<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobDescription;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalCVsAnalyzed = \App\Models\CvAnalysis::count();
        $totalUsers = \App\Models\User::where('role', 'user')->count();
        $activeJobDescs = \App\Models\JobDescription::where('status', 'active')->count();
        
        // Calculate average match score
        $avgMatchScore = \App\Models\CvJobMatch::avg('match_score');
        $avgMatchScore = $avgMatchScore ? round($avgMatchScore, 1) : 0;
        
        // Recent activities (last 10) - dengan jobMatches
        $recentActivities = \App\Models\CvAnalysis::with(['user', 'jobMatches.jobDescription'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Today's analysis count
        $todayAnalysis = \App\Models\CvAnalysis::whereDate('created_at', today())->count();
        
        // Yesterday's analysis for comparison
        $yesterdayAnalysis = \App\Models\CvAnalysis::whereDate('created_at', today()->subDay())->count();
        
        // Get all user feedbacks (with notes)
        $userFeedbacks = \App\Models\CvAnalysis::with(['user', 'jobMatches.jobDescription'])
            ->whereNotNull('user_notes')
            ->where('user_notes', '!=', '')
            ->orderBy('updated_at', 'desc')
            ->get();
        
        return view('admin.dashboard', compact(
            'totalCVsAnalyzed',
            'totalUsers',
            'activeJobDescs',
            'avgMatchScore',
            'recentActivities',
            'todayAnalysis',
            'yesterdayAnalysis',
            'userFeedbacks'
        ));
    }

    public function jobDescManagement()
    {
        $jobDescriptions = JobDescription::with('creator')->latest()->get();
        return view('admin.job-desc-management', compact('jobDescriptions'));
    }

    public function createJobDesc()
    {
        return view('admin.job-desc-create');
    }

    public function storeJobDesc(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'skills_required' => 'nullable|string',
            'experience_level' => 'nullable|string|max:255',
            'education_level' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        JobDescription::create([
            'job_title' => $request->job_title,
            'department' => $request->department,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'responsibilities' => $request->responsibilities,
            'skills_required' => $request->skills_required,
            'experience_level' => $request->experience_level,
            'education_level' => $request->education_level,
            'status' => $request->status,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.job-desc')->with('success', 'Job description created successfully!');
    }

    public function editJobDesc($id)
    {
        $jobDescription = JobDescription::findOrFail($id);
        return view('admin.job-desc-edit', compact('jobDescription'));
    }

    public function updateJobDesc(Request $request, $id)
    {
        $jobDescription = JobDescription::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'job_title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'skills_required' => 'nullable|string',
            'experience_level' => 'nullable|string|max:255',
            'education_level' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $jobDescription->update($request->all());

        return redirect()->route('admin.job-desc')->with('success', 'Job description updated successfully!');
    }

    public function deleteJobDesc($id)
    {
        $jobDescription = JobDescription::findOrFail($id);
        $jobDescription->delete();

        return redirect()->route('admin.job-desc')->with('success', 'Job description deleted successfully!');
    }

    public function monitoring()
    {
        // Today's analysis
        $todayAnalysis = \App\Models\CvAnalysis::whereDate('created_at', today())->count();
        $yesterdayAnalysis = \App\Models\CvAnalysis::whereDate('created_at', today()->subDay())->count();
        
        // Calculate percentage change
        $percentageChange = 0;
        if ($yesterdayAnalysis > 0) {
            $percentageChange = round((($todayAnalysis - $yesterdayAnalysis) / $yesterdayAnalysis) * 100, 1);
        }
        
        // Active users (users who analyzed CV in last 24 hours)
        $activeUsers = \App\Models\CvAnalysis::where('created_at', '>=', now()->subDay())
            ->distinct('user_id')
            ->count('user_id');
        
        // Recent activity log (last 50) - dengan jobMatches
        $activityLog = \App\Models\CvAnalysis::with(['user', 'jobMatches.jobDescription'])
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();
        
        // Average API response time (simulated for now)
        $avgResponseTime = rand(800, 1500);
        
        return view('admin.monitoring', compact(
            'todayAnalysis',
            'percentageChange',
            'activeUsers',
            'activityLog',
            'avgResponseTime'
        ));
    }

    public function analytics()
    {
        // Total statistics
        $totalCVsAnalyzed = \App\Models\CvAnalysis::count();
        $totalUsers = \App\Models\User::where('role', 'user')->count();
        $totalJobDescs = \App\Models\JobDescription::count();
        
        // Analysis by date (last 7 days)
        $analysisData = [];
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $labels[] = $date->format('M d');
            $analysisData[] = \App\Models\CvAnalysis::whereDate('created_at', $date)->count();
        }
        
        // Top job descriptions by analysis count
        $topJobDescs = \App\Models\CvJobMatch::select('job_description_id', \DB::raw('count(*) as total'))
            ->groupBy('job_description_id')
            ->orderBy('total', 'desc')
            ->take(5)
            ->with('jobDescription')
            ->get();
        
        // Match score distribution
        $matchScoreRanges = [
            '0-20' => \App\Models\CvJobMatch::whereBetween('match_score', [0, 20])->count(),
            '21-40' => \App\Models\CvJobMatch::whereBetween('match_score', [21, 40])->count(),
            '41-60' => \App\Models\CvJobMatch::whereBetween('match_score', [41, 60])->count(),
            '61-80' => \App\Models\CvJobMatch::whereBetween('match_score', [61, 80])->count(),
            '81-100' => \App\Models\CvJobMatch::whereBetween('match_score', [81, 100])->count(),
        ];
        
        // Average match score by job description
        $avgScoreByJob = \App\Models\CvJobMatch::select('job_description_id', \DB::raw('AVG(match_score) as avg_score'))
            ->groupBy('job_description_id')
            ->orderBy('avg_score', 'desc')
            ->take(5)
            ->with('jobDescription')
            ->get();
        
        // User activity statistics
        $userStats = \App\Models\User::where('role', 'user')
            ->withCount('cvAnalyses')
            ->orderBy('cv_analyses_count', 'desc')
            ->take(10)
            ->get();
        
        return view('admin.analytics', compact(
            'totalCVsAnalyzed',
            'totalUsers',
            'totalJobDescs',
            'analysisData',
            'labels',
            'topJobDescs',
            'matchScoreRanges',
            'avgScoreByJob',
            'userStats'
        ));
    }

    public function viewCvDetail($id)
    {
        // Admin bisa lihat semua CV analysis detail
        $cvAnalysis = \App\Models\CvAnalysis::with(['user', 'jobMatches.jobDescription'])
            ->findOrFail($id);

        $parsedData = json_decode($cvAnalysis->parsed_data, true) ?? [];
        $summary = json_decode($cvAnalysis->ai_summary, true) ?? $cvAnalysis->ai_summary;
        $recommendations = json_decode($cvAnalysis->career_recommendations, true) ?? [];

        if (is_array($summary)) {
            $summary = implode(" ", $summary);
        }

        $analysis = [
            'overall_score'         => $parsedData['overall_score'] ?? 0,
            'match_percentage'      => $parsedData['match_percentage'] ?? 0,
            'strengths'             => $parsedData['strengths'] ?? [],
            'weaknesses'            => $parsedData['weaknesses'] ?? [],
            'missing_skills'        => $parsedData['missing_skills'] ?? [],
            'summary'               => $summary,
            'recommendations'       => $recommendations,
        ];

        return view('cv-detail', compact('cvAnalysis', 'analysis'));
    }

    public function previewAnalysis($id)
    {
        // Admin preview CV analysis - Halaman terpisah tanpa sidebar user
        $cvAnalysis = \App\Models\CvAnalysis::with(['user', 'jobMatches.jobDescription'])
            ->findOrFail($id);

        $parsedData = json_decode($cvAnalysis->parsed_data, true) ?? [];
        $summary = json_decode($cvAnalysis->ai_summary, true) ?? $cvAnalysis->ai_summary;
        $recommendations = json_decode($cvAnalysis->career_recommendations, true) ?? [];

        if (is_array($summary)) {
            $summary = implode(" ", $summary);
        }

        $analysis = [
            'overall_score'         => $parsedData['overall_score'] ?? 0,
            'match_percentage'      => $parsedData['match_percentage'] ?? 0,
            'strengths'             => $parsedData['strengths'] ?? [],
            'weaknesses'            => $parsedData['weaknesses'] ?? [],
            'missing_skills'        => $parsedData['missing_skills'] ?? [],
            'summary'               => $summary,
            'recommendations'       => $recommendations,
        ];

        return view('admin.cv-analysis-preview', compact('cvAnalysis', 'analysis'));
    }
}
