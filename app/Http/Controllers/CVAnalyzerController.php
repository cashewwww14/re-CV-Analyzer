<?php

namespace App\Http\Controllers;

use App\Models\CvAnalysis;
use Illuminate\Support\Facades\Auth;
use App\Services\GeminiAIService;
use App\Services\PDFProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class CVAnalyzerController extends Controller
{
    private $aiService;
    private $pdfService;

    public function __construct(GeminiAIService $aiService, PDFProcessingService $pdfService)
    {
        $this->aiService = $aiService;
        $this->pdfService = $pdfService;
    }

    public function index()
    {
        $jobTitles = \App\Models\JobDescription::where('status', 'active')
            ->orderBy('job_title')
            ->get(['id', 'job_title', 'department']);
        
        return view('cv-analyzer', compact('jobTitles'));
    }

    public function showDetail($id)
    {
        $cvAnalysis = CvAnalysis::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $parsedData = json_decode($cvAnalysis->parsed_data, true) ?? [];
        $summary = json_decode($cvAnalysis->ai_summary, true) ?? $cvAnalysis->ai_summary;
        $recommendations = json_decode($cvAnalysis->career_recommendations, true) ?? [];

        if (is_array($summary)) {
            $summary = implode(" ", $summary);
        }

        $analysis = [
            'overall_score'         => $parsedData['overall_score'] ?? 0,
            'match_percentage'      => $parsedData['match_percentage'] ?? 0,
            'summary'               => $summary,
            'strengths'             => $parsedData['strengths'] ?? [],
            'weaknesses'            => $parsedData['weaknesses'] ?? [],
            'missing_skills'        => $parsedData['missing_skills'] ?? [],
            'detailed_analysis'     => $parsedData['detailed_analysis'] ?? [],
            'recommendations'       => $recommendations ?? [],
            'interview_preparation' => $parsedData['interview_preparation'] ?? [],
            'action_plan'           => $parsedData['action_plan'] ?? [],
        ];

        return view('cv-detail', compact('analysis', 'cvAnalysis'));
    }

    public function analyze(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cv_file' => 'required|file|mimes:pdf|mimetypes:application/pdf|max:1024',
            'job_title' => 'required_without:new_job_title|string|max:255',
            'new_job_title' => 'required_without:job_title|string|max:255',
            'new_job_description' => 'required_with:new_job_title|string|max:5000',
            'job_requirements' => 'nullable|string|max:3000'
        ], [
            'cv_file.required' => 'Please upload your CV (PDF format)',
            'cv_file.mimes' => 'CV must be in PDF format',
            'cv_file.mimetypes' => 'Invalid file format. Only PDF files are allowed.',
            'cv_file.max' => 'CV file size must not exceed 1MB',
            'job_title.required_without' => 'Please select a job position or add a new one',
            'new_job_title.required_without' => 'Please enter a new job title',
            'new_job_title.string' => 'Job title must be a text string',
            'new_job_description.required_with' => 'Please enter the job description when adding a new job',
            'new_job_description.string' => 'Job description must be a text string',
            'new_job_description.max' => 'Job description must not exceed 5000 characters',
        ]);

        if ($validator->fails()) {
            Log::warning('CV Analysis validation failed', [
                'errors' => $validator->errors()->toArray(),
                'user_id' => Auth::id()
            ]);
            return back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('cv_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Get or create job description
            $jobTitle = '';
            $jobDescription = null;
            $jobDescriptionId = null;
            
            if ($request->filled('new_job_title')) {
                // User entered new job title + description
                $jobTitle = trim($request->new_job_title);
                $newDescription = trim($request->new_job_description);
                
                // Check if job title already exists
                $existingJob = \App\Models\JobDescription::where('job_title', $jobTitle)->first();
                
                if ($existingJob) {
                    // Job already exists, use it
                    $jobDescription = $existingJob;
                    $jobDescriptionId = $existingJob->id;
                } else {
                    // Create new job description
                    try {
                        $jobDescription = \App\Models\JobDescription::create([
                            'job_title' => $jobTitle,
                            'department' => 'General',
                            'description' => $newDescription,
                            'requirements' => $request->job_requirements ?? '',
                            'status' => 'active',
                            'created_by' => Auth::id(),
                        ]);
                        $jobDescriptionId = $jobDescription->id;
                    } catch (\Exception $e) {
                        // If unique constraint error, fetch existing
                        $jobDescription = \App\Models\JobDescription::where('job_title', $jobTitle)->first();
                        $jobDescriptionId = $jobDescription->id;
                    }
                }
            } else {
                // User selected existing job from dropdown
                $jobTitle = trim($request->job_title);
                $jobDescription = \App\Models\JobDescription::where('job_title', $jobTitle)->first();
                
                if ($jobDescription) {
                    $jobDescriptionId = $jobDescription->id;
                }
            }
            
            // Store CV file to storage
            $cvFilePath = $file->storeAs('cvs', $filename, 'public');
            
            Log::info('Starting CV analysis', [
                'filename' => $filename,
                'cv_file_path' => $cvFilePath,
                'user_id' => Auth::id(),
                'file_size' => $file->getSize(),
                'job_title' => $jobTitle
            ]);

            // Process PDF
            $pdfBase64 = $this->pdfService->convertToBase64($file);
            
            // Build job requirements context
            $jobRequirementsContext = "";
            
            // Add additional requirements as disclaimer/priority at top if provided
            if ($request->job_requirements) {
                $jobRequirementsContext .= "⚠️ ADDITIONAL REQUIREMENTS (High Priority):\n{$request->job_requirements}\n\n";
                $jobRequirementsContext .= "---\n\n";
            }
            
            $jobRequirementsContext .= "Job Title: {$jobTitle}\n\n";
            
            if ($jobDescription && $jobDescription->description) {
                $jobRequirementsContext .= "Job Description:\n{$jobDescription->description}\n\n";
            }
            
            if ($jobDescription && $jobDescription->requirements) {
                $jobRequirementsContext .= "Standard Requirements:\n{$jobDescription->requirements}\n\n";
            }

            Log::info('PDF converted to base64, calling AI service');

            // Analyze with AI
            $analysis = $this->aiService->analyzeCV($pdfBase64, $jobRequirementsContext);

            Log::info('AI analysis completed successfully');

            // Process analysis data
            $parsedData = $analysis['parsed_data'] ?? [];

            $parsedData = [
                'overall_score'        => $parsedData['overall_score'] ?? ($analysis['overall_score'] ?? 0),
                'match_percentage'     => $parsedData['match_percentage'] ?? ($analysis['match_percentage'] ?? 0),
                'strengths'            => $parsedData['strengths'] ?? ($analysis['strengths'] ?? []),
                'weaknesses'           => $parsedData['weaknesses'] ?? ($analysis['weaknesses'] ?? []),
                'missing_skills'       => $parsedData['missing_skills'] ?? ($analysis['missing_skills'] ?? []),
                'detailed_analysis'    => $parsedData['detailed_analysis'] ?? ($analysis['detailed_analysis'] ?? []),
                'action_plan'          => $parsedData['action_plan'] ?? ($analysis['action_plan'] ?? []),
                'interview_preparation'=> $parsedData['interview_preparation'] ?? ($analysis['interview_preparation'] ?? []),
            ];

            $summary = $analysis['summary'] ?? '';
            if (is_array($summary)) {
                $summary = implode(" ", $summary);
            }

            $recommendations = $analysis['recommendations'] ?? [];
            if (!is_array($recommendations)) {
                $recommendations = [$recommendations];
            }

            // Save to database with CV file path
            $cvAnalysis = CvAnalysis::create([
                'user_id' => Auth::id(),
                'cv_filename' => $filename,
                'cv_file_path' => $cvFilePath,
                'extracted_text' => is_array($analysis['extracted_text'] ?? null)
                    ? json_encode($analysis['extracted_text'])
                    : ($analysis['extracted_text'] ?? null),
                'parsed_data' => json_encode($parsedData),
                'ai_summary' => $summary,
                'career_recommendations' => json_encode($recommendations),
                'status' => 'completed',
            ]);

            Log::info('CV analysis saved to database', [
                'cv_analysis_id' => $cvAnalysis->id,
                'user_id' => Auth::id()
            ]);

            // Create job match record if we have a job description
            if ($jobDescriptionId) {
                \App\Models\CvJobMatch::create([
                    'cv_analysis_id' => $cvAnalysis->id,
                    'job_description_id' => $jobDescriptionId,
                    'match_score' => $parsedData['match_percentage'] ?? 0,
                    'matching_skills' => $parsedData['strengths'] ?? [],
                    'missing_skills' => $parsedData['missing_skills'] ?? [],
                    'match_analysis' => $summary,
                    'recommendations' => json_encode($recommendations),
                ]);
            }

            return view('cv-results', [
                'analysis' => array_merge($parsedData, [
                    'summary' => $summary,
                    'recommendations' => $recommendations
                ]),
                'cvAnalysis' => $cvAnalysis,
                'saved' => true
            ]);

        } catch (Exception $e) {
            Log::error('CV Analysis failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            return back()
                ->with('error', 'Analysis failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function history()
    {
        $analyses = CvAnalysis::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('cv-history', compact('analyses'));
    }

    public function saveNotes(Request $request, $cv)
    {
        try {
            $cvAnalysis = CvAnalysis::where('id', $cv)
                ->where('user_id', Auth::id())
                ->firstOrFail();
                
            $cvAnalysis->user_notes = $request->input('user_notes');
            $cvAnalysis->save();

            Log::info('Notes saved successfully', [
                'cv_analysis_id' => $cv,
                'user_id' => Auth::id()
            ]);

            return response()->json(['message' => 'Notes saved successfully!']);
            
        } catch (Exception $e) {
            Log::error('Failed to save notes', [
                'error' => $e->getMessage(),
                'cv_analysis_id' => $cv,
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'message' => 'Failed to save notes: ' . $e->getMessage()
            ], 500);
        }
    }
}