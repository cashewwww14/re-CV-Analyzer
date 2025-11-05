<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV Analysis Preview - {{ $cvAnalysis->user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header Bar (No Print) -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg no-print">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <h1 class="text-xl font-bold">CV Analysis Preview</h1>
            <div class="flex items-center space-x-4">
                <button onclick="window.print()" class="bg-white text-blue-600 hover:bg-gray-100 px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Print Analysis
                </button>
                <button onclick="window.close()" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <!-- User Info Card -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="flex items-center space-x-4 mb-4">
                <div class="bg-indigo-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $cvAnalysis->user->name }}</h2>
                    <p class="text-gray-600">{{ $cvAnalysis->user->email }}</p>
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-semibold text-gray-700">CV Filename:</span>
                    <span class="text-gray-600">{{ $cvAnalysis->cv_filename }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Analysis Date:</span>
                    <span class="text-gray-600">{{ $cvAnalysis->created_at->format('d M Y, H:i') }}</span>
                </div>
                @if($cvAnalysis->jobMatches->isNotEmpty() && $cvAnalysis->jobMatches->first()->jobDescription)
                <div class="md:col-span-2">
                    <span class="font-semibold text-gray-700">Job Position:</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 ml-2">
                        {{ $cvAnalysis->jobMatches->first()->jobDescription->job_title }}
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Scores Section -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <!-- Overall Score -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 text-center">Overall Score</h3>
                <div class="flex justify-center">
                    <canvas id="scoreChart" width="200" height="200"></canvas>
                </div>
            </div>

            <!-- Job Match -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 text-center">Job Match</h3>
                <div class="flex justify-center">
                    <canvas id="matchChart" width="200" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">📝 Summary</h3>
            <p class="text-gray-700 leading-relaxed">{{ $analysis['summary'] }}</p>
        </div>

        <!-- Strengths & Weaknesses -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <!-- Strengths -->
            @if(isset($analysis['strengths']) && !empty($analysis['strengths']))
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-green-800 mb-4">💪 Strengths</h3>
                <ul class="space-y-2">
                    @foreach($analysis['strengths'] as $strength)
                        <li class="flex items-start">
                            <span class="bg-green-100 text-green-800 rounded-full px-2 py-1 text-xs font-medium mr-2 mt-0.5 flex-shrink-0">✓</span>
                            <span class="text-gray-700">{{ $strength }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Weaknesses -->
            @if(isset($analysis['weaknesses']) && !empty($analysis['weaknesses']))
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-red-800 mb-4">⚠️ Weaknesses</h3>
                <ul class="space-y-2">
                    @foreach($analysis['weaknesses'] as $weakness)
                        <li class="flex items-start">
                            <span class="bg-red-100 text-red-800 rounded-full px-2 py-1 text-xs font-medium mr-2 mt-0.5 flex-shrink-0">!</span>
                            <span class="text-gray-700">{{ $weakness }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        <!-- Missing Skills -->
        @if(isset($analysis['missing_skills']) && !empty($analysis['missing_skills']))
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h3 class="text-xl font-bold text-orange-800 mb-4">🎯 Missing Key Skills</h3>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($analysis['missing_skills'] as $skill)
                    <div class="bg-orange-100 px-4 py-3 rounded-lg flex items-center justify-center min-h-[60px]">
                        <p class="text-orange-800 font-medium text-center">{{ $skill }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recommendations -->
        @if(isset($analysis['recommendations']))
        <div class="space-y-6">
            <!-- Immediate Improvements -->
            @if(isset($analysis['recommendations']['immediate_improvements']))
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-blue-800 mb-4">🚀 Immediate CV Improvements</h3>
                <ul class="space-y-3">
                    @foreach($analysis['recommendations']['immediate_improvements'] as $improvement)
                        <li class="flex items-start">
                            <span class="bg-blue-100 text-blue-800 rounded-full px-2 py-1 text-xs font-medium mr-3 mt-1 flex-shrink-0">{{ $loop->iteration }}</span>
                            <span class="text-gray-700">{{ $improvement }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Skill Development -->
            @if(isset($analysis['recommendations']['skill_development']))
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-purple-800 mb-4">📚 Skill Development</h3>
                <ul class="space-y-3">
                    @foreach($analysis['recommendations']['skill_development'] as $skill)
                        <li class="flex items-start">
                            <span class="bg-purple-100 text-purple-800 rounded-full px-2 py-1 text-xs font-medium mr-3 mt-1 flex-shrink-0">{{ $loop->iteration }}</span>
                            <span class="text-gray-700">{{ $skill }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Career Advice -->
            @if(isset($analysis['recommendations']['career_advice']))
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-indigo-800 mb-4">💼 Career Advice</h3>
                <ul class="space-y-3">
                    @foreach($analysis['recommendations']['career_advice'] as $advice)
                        <li class="flex items-start">
                            <span class="bg-indigo-100 text-indigo-800 rounded-full px-2 py-1 text-xs font-medium mr-3 mt-1 flex-shrink-0">{{ $loop->iteration }}</span>
                            <span class="text-gray-700">{{ $advice }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        @endif

        <!-- User Feedback (if exists) -->
        @if(!empty($cvAnalysis->user_notes))
        <div class="bg-white rounded-xl shadow-md p-6 mt-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">💬 User Feedback</h3>
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                <p class="text-gray-700 whitespace-pre-line">{{ $cvAnalysis->user_notes }}</p>
            </div>
        </div>
        @endif
    </div>

    <script>
        // Overall Score Chart
        const scoreCtx = document.getElementById('scoreChart').getContext('2d');
        const score = {{ $analysis['overall_score'] ?? 0 }};
        new Chart(scoreCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [score, 100 - score],
                    backgroundColor: ['#3B82F6', '#E5E7EB'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            }
        });

        // Add score text in center
        const scoreText = document.createElement('div');
        scoreText.className = 'absolute inset-0 flex items-center justify-center';
        scoreText.innerHTML = `<div class="text-center"><div class="text-3xl font-bold text-blue-600">${score}</div><div class="text-sm text-gray-500">/ 100</div></div>`;
        scoreCtx.canvas.parentElement.style.position = 'relative';

        // Job Match Chart
        const matchCtx = document.getElementById('matchChart').getContext('2d');
        const matchScore = {{ $analysis['match_percentage'] ?? 0 }};
        new Chart(matchCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [matchScore, 100 - matchScore],
                    backgroundColor: ['#10B981', '#E5E7EB'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            }
        });

        // Add match text in center
        const matchText = document.createElement('div');
        matchText.className = 'absolute inset-0 flex items-center justify-center';
        matchText.innerHTML = `<div class="text-center"><div class="text-3xl font-bold text-green-600">${matchScore}%</div><div class="text-sm text-gray-500">Match</div></div>`;
        matchCtx.canvas.parentElement.style.position = 'relative';
    </script>
</body>
</html>
