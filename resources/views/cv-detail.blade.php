<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV Analysis Details - CV Analyzer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold">CV Analyzer</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">Welcome, {{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg text-sm font-semibold transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white min-h-screen shadow-lg">
            <div class="p-6">
                <nav class="space-y-2">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3 text-gray-700 hover:bg-gray-100 px-4 py-3 rounded-lg font-semibold transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Analyze CV</span>
                    </a>
                    <a href="{{ route('cv.history') }}" class="flex items-center space-x-3 bg-blue-50 text-blue-700 px-4 py-3 rounded-lg font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Analysis History</span>
                    </a>
                    <a href="{{ route('profile.show') }}" class="flex items-center space-x-3 text-gray-700 hover:bg-gray-100 px-4 py-3 rounded-lg font-semibold transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>My Profile</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 mb-2">CV Analysis Details</h2>
                        <p class="text-gray-600">Detailed insights from your CV analysis</p>
                    </div>
                    <a href="{{ route('cv.history') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold transition">
                        ← Back to History
                    </a>
                </div>
            </div>

            @if(isset($analysis['error']))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <h3 class="font-bold">Analysis Error:</h3>
                    <p>{{ $analysis['error'] }}</p>
                </div>
            @endif

            <!-- Score Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-gray-500 text-sm font-semibold mb-2">Overall Score</h3>
                    <div class="flex items-center justify-center mb-4">
                        <canvas id="scoreChart" width="200" height="200"></canvas>
                    </div>
                    <div class="text-center">
                        <span class="text-3xl font-bold text-blue-600">{{ $analysis['overall_score'] ?? 0 }}/100</span>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-gray-500 text-sm font-semibold mb-2">Job Match</h3>
                    <div class="flex items-center justify-center mb-4">
                        <canvas id="matchChart" width="200" height="200"></canvas>
                    </div>
                    <div class="text-center">
                        <span class="text-3xl font-bold text-green-600">{{ $analysis['match_percentage'] ?? 0 }}%</span>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            @if(isset($analysis['summary']))
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Summary</h3>
                <p class="text-gray-700">{{ $analysis['summary'] }}</p>
            </div>
            @endif

            <!-- Strengths & Weaknesses -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                @if(isset($analysis['strengths']))
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-bold text-green-800 mb-4">✅ Strengths</h3>
                    <ul class="space-y-2">
                        @foreach($analysis['strengths'] as $strength)
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">•</span>
                                <span class="text-gray-700">{{ $strength }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(isset($analysis['weaknesses']))
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-bold text-red-800 mb-4">⚠️ Areas to Improve</h3>
                    <ul class="space-y-2">
                        @foreach($analysis['weaknesses'] as $weakness)
                            <li class="flex items-start">
                                <span class="text-red-500 mr-2">•</span>
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

                <!-- Courses & Bootcamps -->
                @if(isset($analysis['recommendations']['courses_bootcamps']) && !empty($analysis['recommendations']['courses_bootcamps']))
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-bold text-purple-800 mb-4">🎓 Recommended Courses & Bootcamps</h3>
                    <div class="grid gap-4">
                        @foreach($analysis['recommendations']['courses_bootcamps'] as $course)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-semibold text-gray-800">{{ $course['name'] ?? 'Course' }}</h4>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ ($course['priority'] ?? 'Medium') === 'High' ? 'bg-red-100 text-red-800' :
                                           (($course['priority'] ?? 'Medium') === 'Low' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $course['priority'] ?? 'Medium' }} Priority
                                    </span>
                                </div>
                                @if(isset($course['provider']))
                                    <p class="text-sm text-gray-600 mb-1"><strong>Provider:</strong> {{ $course['provider'] }}</p>
                                @endif
                                @if(isset($course['focus']))
                                    <p class="text-sm text-gray-600 mb-1"><strong>Focus:</strong> {{ $course['focus'] }}</p>
                                @endif
                                @if(isset($course['duration']))
                                    <p class="text-sm text-gray-600"><strong>Duration:</strong> {{ $course['duration'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Project Recommendations -->
                @if(isset($analysis['recommendations']['projects']) && !empty($analysis['recommendations']['projects']))
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-bold text-green-800 mb-4">💻 Suggested Projects</h3>
                    <div class="space-y-4">
                        @foreach($analysis['recommendations']['projects'] as $project)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-semibold text-gray-800">{{ $project['title'] ?? 'Project' }}</h4>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ ($project['difficulty'] ?? 'Intermediate') === 'Beginner' ? 'bg-green-100 text-green-800' :
                                           (($project['difficulty'] ?? 'Intermediate') === 'Advanced' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                        {{ $project['difficulty'] ?? 'Intermediate' }}
                                    </span>
                                </div>
                                @if(isset($project['description']))
                                    <p class="text-gray-600 mb-3">{{ $project['description'] }}</p>
                                @endif
                                @if(isset($project['technologies']) && !empty($project['technologies']))
                                    <div class="mb-2">
                                        <span class="text-sm font-medium text-gray-700">Technologies: </span>
                                        @foreach($project['technologies'] as $tech)
                                            <span class="inline-block bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs mr-1 mb-1">{{ $tech }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                @if(isset($project['impact']))
                                    <p class="text-sm text-blue-600"><strong>Impact:</strong> {{ $project['impact'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- User Feedback Section -->
            <div class="bg-white rounded-xl shadow-md p-6 mt-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">💬 User Feedback</h3>
                
                @if(!empty($cvAnalysis->user_notes))
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                        <h4 class="font-semibold text-gray-800 mb-2">Your Saved Notes:</h4>
                        <p class="text-gray-700 whitespace-pre-line">{{ $cvAnalysis->user_notes }}</p>
                    </div>
                @else
                    <p class="text-gray-500 italic mb-4">No feedback saved yet.</p>
                @endif

                <!-- Action Buttons (Always visible) -->
                <div class="flex flex-wrap gap-3 mt-4">
                    @if($cvAnalysis->cv_file_path)
                    <a href="{{ Storage::url($cvAnalysis->cv_file_path) }}" target="_blank" class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        View CV
                    </a>
                    @endif
                    <button onclick="window.print()" class="inline-flex items-center bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg font-semibold transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Analysis
                    </button>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-center space-x-4 mt-12">
                <a href="{{ route('home') }}" class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-600 hover:to-indigo-700 transition">
                    Analyze New CV
                </a>
                <a href="{{ route('cv.history') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition">
                    Back to History
                </a>
            </div>
        </main>
    </div>

    <script>
        // Create score chart
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
                responsive: false,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Create match chart
        const matchCtx = document.getElementById('matchChart').getContext('2d');
        const matchPercentage = {{ $analysis['match_percentage'] ?? 0 }};

        new Chart(matchCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [matchPercentage, 100 - matchPercentage],
                    backgroundColor: ['#10B981', '#E5E7EB'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>