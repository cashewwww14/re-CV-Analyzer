<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring - CV Analyzer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold">CV Analyzer - Admin</h1>
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
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 text-gray-700 hover:bg-gray-100 px-4 py-3 rounded-lg font-semibold transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.job-desc') }}" class="flex items-center space-x-3 text-gray-700 hover:bg-gray-100 px-4 py-3 rounded-lg font-semibold transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Job Desc Management</span>
                    </a>
                    <a href="{{ route('admin.monitoring') }}" class="flex items-center space-x-3 bg-blue-50 text-blue-700 px-4 py-3 rounded-lg font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Monitoring</span>
                    </a>
                    <a href="{{ route('admin.analytics') }}" class="flex items-center space-x-3 text-gray-700 hover:bg-gray-100 px-4 py-3 rounded-lg font-semibold transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Analytics</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">System Monitoring</h2>
                <p class="text-gray-600">Monitor CV analysis activity and system performance</p>
            </div>

            <!-- Real-time Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-gray-500 text-sm font-semibold mb-2">Today's Analysis</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ $todayAnalysis }}</p>
                    <p class="{{ $percentageChange >= 0 ? 'text-green-600' : 'text-red-600' }} text-sm mt-2">
                        {{ $percentageChange >= 0 ? '↑' : '↓' }} {{ abs($percentageChange) }}% from yesterday
                    </p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-gray-500 text-sm font-semibold mb-2">Active Users</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ $activeUsers }}</p>
                    <p class="text-blue-600 text-sm mt-2">Last 24 hours</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-gray-500 text-sm font-semibold mb-2">API Response Time</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ $avgResponseTime }}ms</p>
                    <p class="text-gray-500 text-sm mt-2">Average response time</p>
                </div>
            </div>

            <!-- Recent Activity Log -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Recent Activity Log</h3>
                @if($activityLog->isEmpty())
                    <div class="text-center py-12 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-lg">No activity recorded</p>
                        <p class="text-sm mt-2">Activity will appear here when users analyze CVs</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Time</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Action</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Job Position</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($activityLog as $log)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $log->created_at->format('H:i:s') }}<br>
                                            <span class="text-xs text-gray-400">{{ $log->created_at->format('M d, Y') }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                            {{ $log->user->name }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-semibold">
                                                CV Analysis
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            @if($log->jobMatches->isNotEmpty() && $log->jobMatches->first()->jobDescription)
                                                {{ $log->jobMatches->first()->jobDescription->job_title }}
                                            @else
                                                General
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
