<x-app-layout>
    @vite(['resources/js/dashboard.js'])
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Welcome {{ Auth::user()->name }}!
        </h2>
    </x-slot>


    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css">
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>

   <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        // Make jsPDF available globally for autoTable
        window.jsPDF = window.jspdf.jsPDF;
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>


    <div class="py-8">
        <div class="flex justify-end mb-4 space-x-2">
            <button onclick="exportDashboardToCSV('dashboard-content', 'dashboard.csv')" class="px-3 py-2 bg-blue-600 text-white rounded text-xs">Export Dashboard as CSV</button>
            <button onclick="exportDashboardToPDF('dashboard-content', 'dashboard.pdf')" class="px-3 py-2 bg-red-600 text-white rounded text-xs">Export Dashboard as PDF</button>
        </div>
        <div id="dashboard-content">
            <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
                <!-- Activities Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <!-- Total Activities (Projects) Card -->
                    <div id="total-projects-card" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Activities</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalProjects }}</p>
                                    <p class="text-xs mt-1 flex items-center">
                                        @if($totalProjectsTrend > 0)
                                            <span class="text-green-600 dark:text-green-400">&#9650; {{ $totalProjectsTrend }}%</span>
                                        @elseif($totalProjectsTrend < 0)
                                            <span class="text-red-600 dark:text-red-400">&#9660; {{ abs($totalProjectsTrend) }}%</span>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">0%</span>
                                        @endif
                                        <span class="ml-1 text-gray-400">vs last week</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- In Progress Card -->
                    <div id="in-progress-card" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">In Progress</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $inProgressProjects }}</p>
                                    <p class="text-xs mt-1 flex items-center">
                                        @if($inProgressTrend > 0)
                                            <span class="text-green-600 dark:text-green-400">&#9650; {{ $inProgressTrend }}%</span>
                                        @elseif($inProgressTrend < 0)
                                            <span class="text-red-600 dark:text-red-400">&#9660; {{ abs($inProgressTrend) }}%</span>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">0%</span>
                                        @endif
                                        <span class="ml-1 text-gray-400">vs last week</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Card -->
                    <div id="completed-card" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Completed</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $completedProjects }}</p>
                                    <p class="text-xs mt-1 flex items-center">
                                        @if($completedTrend > 0)
                                            <span class="text-green-600 dark:text-green-400">&#9650; {{ $completedTrend }}%</span>
                                        @elseif($completedTrend < 0)
                                            <span class="text-red-600 dark:text-red-400">&#9660; {{ abs($completedTrend) }}%</span>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">0%</span>
                                        @endif
                                        <span class="ml-1 text-gray-400">vs last week</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deferred Card -->
                    <div id="deferred-card" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
                                    <svg class="w-6 h-6 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Deferred</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $deferredProjects }}</p>
                                    <p class="text-xs mt-1 flex items-center">
                                        @if($deferredTrend > 0)
                                            <span class="text-green-600 dark:text-green-400">&#9650; {{ $deferredTrend }}%</span>
                                        @elseif($deferredTrend < 0)
                                            <span class="text-red-600 dark:text-red-400">&#9660; {{ abs($deferredTrend) }}%</span>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">0%</span>
                                        @endif
                                        <span class="ml-1 text-gray-400">vs last week</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities, Upcoming Deadlines, and Task Assignment Breakdown -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                    <!-- Recent Activities -->
                    <div id="recent-activities" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                        <div class="p-8 h-full flex flex-col justify-between">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activities</h3>
                            <div class="space-y-8">
                                @forelse($recentActivities as $activity)
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-900 dark:text-white">
                                                <span class="font-semibold">
                                                    {{ $activity->causer ? $activity->causer->name : 'System' }}
                                                </span>
                                                {{ $activity->description }}
                                                @if($activity->subject)
                                                    @php
                                                        // Try to get a display name for the subject
                                                        $subject = $activity->subject;
                                                        $subjectDisplay = method_exists($subject, 'getDisplayName')
                                                            ? $subject->getDisplayName()
                                                            : ($subject->name ?? $subject->title ?? $subject->task_name ?? $subject->file_name ?? null);
                                                    @endphp
                                                    @if($subjectDisplay)
                                                        <span class="text-blue-500 dark:text-blue-300 font-semibold">"{{ $subjectDisplay }}"</span>
                                                    @endif
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="flex flex-col items-center justify-center h-48">
                                        <svg class="w-10 h-10 mb-2 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-gray-500 dark:text-gray-400">No recent activities.</span>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <!-- Upcoming Deadlines -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col">
                        <div id="upcoming-deadlines" class="flex-1 flex flex-col p-8 items-center justify-center text-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Upcoming Deadlines</h3>
                            <div class="flex-1 flex flex-col justify-center items-center w-full">
                                <div class="overflow-x-auto w-full flex justify-center" style="max-height: 320px; overflow-y: auto;">
                                    <table class="min-w-[400px] max-w-full divide-y divide-gray-200 dark:divide-gray-700 exportable-table mx-auto text-center">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Activity</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Task</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Deadline</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Priority</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @forelse($upcomingDeadlines as $task)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-center">{{ $task->proj_name }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-center">{{ $task->task_name }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-700 dark:text-red-300 text-center">
                                                        {{ \Carbon\Carbon::parse($task->due_date)->format('F d, Y') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            {{ $task->status === 'For Checking' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : 
                                                            ($task->status === 'Completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 
                                                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300') }}">
                                                            {{ $task->status }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            {{ $task->priority === 'High' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 
                                                            ($task->priority === 'Normal' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : 
                                                            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300') }}">
                                                            {{ $task->priority }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="px-6 py-16 text-center text-gray-500 dark:text-gray-400">
                                                        <div class="flex flex-col items-center justify-center">
                                                            <svg class="w-10 h-10 mb-2 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            No upcoming deadlines.
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Task Completion Rate and Overdue Task Rate as separate cards -->
                    <div class="flex flex-col gap-4 h-full">
                        <!-- Task Completion Rate Card -->
                        <div id="task-completion" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg flex-1 flex items-center">
                            <div class="p-6 flex items-center w-full">
                                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Task Completion Rate</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $completionRate }}%</p>
                                </div>
                            </div>
                        </div>
                        <!-- Overdue Task Rate Card -->
                        <div id="overdue-task-rate" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg flex-1 flex items-center">
                            <div class="p-6 flex items-center w-full">
                                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
                                    <svg class="w-6 h-6 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Overdue Task Rate</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $overdueRate }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- User Progress Bars --}}
                    @php
                        $usersWithRates = collect($userTasksByAssignee)->map(function($user) {
                            $total = $user['total'] ?? 0;
                            $completed = $user['completed'] ?? 0;
                            $percent = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
                            $user['percent'] = $percent;
                            return $user;
                        });
                        $sortedUsers = $usersWithRates->sortByDesc('completed')->values();
                        $topUsers = $sortedUsers->take(3);
                        $otherUsers = $sortedUsers->slice(3);
                    @endphp
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-6">
                        <div class="p-8">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">User Task Progress</h2>
                                <div class="flex items-center gap-2">
                                    <label for="userProgressFilter" class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter by User:</label>
                                    <select id="userProgressFilter" class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                        <option value="all">All Users</option>
                                        @foreach($usersWithRates as $user)
                                            <option value="{{ $user['name'] }}">{{ $user['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="user-progress-bars">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                                    <svg class="w-6 h-6 text-yellow-500 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17l-5 3 1.9-5.6L4 10.5l5.7-.4L12 5l2.3 5.1 5.7.4-4.9 3.9L17 20z"/>
                                    </svg>
                                    Top 3 Performers
                                </h3>
                                <div class="space-y-5 mb-8">
                                    @foreach($topUsers as $user)
                                        @php
                                            $total = $user['total'] ?? 0;
                                            $completed = $user['completed'] ?? 0;
                                            $percent = $user['percent'];
                                            $avatar = $user['avatar'] ?? null;
                                            $initials = collect(explode(' ', $user['name']))->map(fn($w) => strtoupper(mb_substr($w,0,1)))->join('');
                                        @endphp
                                        <div class="rounded-lg bg-gray-800 dark:bg-gray-900/30">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="flex items-center gap-2 font-semibold text-gray-100 dark:text-gray-100">
                                                    @if($avatar)
                                                        <img src="{{ $avatar }}" alt="{{ $user['name'] }}" class="w-6 h-6 rounded-full object-cover border border-gray-300 dark:border-gray-700" />
                                                    @else
                                                        <span class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-bold">
                                                            {{ $initials }}
                                                        </span>
                                                    @endif
                                                    {{ $user['name'] }}
                                                    <span class="ml-2 px-2 py-0.5 rounded text-xs font-bold bg-green-400 text-white dark:bg-green-500 dark:text-green-900">Top Performer</span>
                                                </span>
                                                <span class="text-xs text-gray-300 dark:text-gray-400">{{ $completed }} / {{ $total }} completed ({{ $percent }}%)</span>
                                            </div>
                                            <div class="w-full h-6 bg-gray-700 dark:bg-gray-700 rounded-full overflow-hidden">
                                                <div class="progress-bar h-6 rounded-full flex items-center justify-center"
                                                    data-percent="{{ $percent }}"
                                                    style="width:0%; background-color: {{ $percent >= 80 ? '#22c55e' : ($percent >= 50 ? '#facc15' : '#ef4444') }};">
                                                    <span class="text-xs font-bold text-white drop-shadow" style="width:100%; text-align:center;">
                                                        {{ $percent }}%
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <svg class="w-6 h-6 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8" />
                                    </svg>
                                    Other Users
                                </h3>
                                <div class="space-y-5">
                                    @foreach($otherUsers as $user)
                                        @php
                                            $total = $user['total'] ?? 0;
                                            $completed = $user['completed'] ?? 0;
                                            $percent = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
                                            $avatar = $user['avatar'] ?? null;
                                            $initials = collect(explode(' ', $user['name']))->map(fn($w) => strtoupper(mb_substr($w,0,1)))->join('');
                                        @endphp
                                        <div>
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="flex items-center gap-2 font-semibold text-gray-800 dark:text-gray-100">
                                                    @if($avatar)
                                                        <img src="{{ $avatar }}" alt="{{ $user['name'] }}" class="w-6 h-6 rounded-full object-cover border border-gray-300 dark:border-gray-700" />
                                                    @else
                                                        <span class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-bold">
                                                            {{ $initials }}
                                                        </span>
                                                    @endif
                                                    {{ $user['name'] }}
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $completed }} / {{ $total }} completed ({{ $percent }}%)
                                                </span>
                                            </div>
                                            <div class="w-full h-6 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                                <div class="progress-bar h-6 rounded-full flex items-center justify-center"
                                                    data-percent="{{ $percent }}"
                                                    style="width:0%; background-color: {{ $percent >= 80 ? '#22c55e' : ($percent >= 50 ? '#facc15' : '#ef4444') }};">
                                                    <span class="text-xs font-bold text-white drop-shadow" style="width:100%; text-align:center;">
                                                        {{ $percent }}%
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div id="user-task-details" class="mt-8 hidden">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Task Details</h3>
                                <div class="overflow-y-auto" style="max-height: 400px;">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 rounded-lg bg-white dark:bg-gray-800" id="userTaskTable">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-bold text-indigo-700 dark:text-indigo-300 uppercase">Activity</th>
                                                <th class="px-4 py-2 text-left text-xs font-bold text-blue-700 dark:text-blue-300 uppercase">Task</th>
                                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">Status</th>
                                                <th class="px-4 py-2 text-left text-xs font-bold text-pink-700 dark:text-pink-300 uppercase">Priority</th>
                                                <th class="px-4 py-2 text-left text-xs font-bold text-red-700 dark:text-red-300 uppercase">Due Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="userTaskTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            <!-- JS will populate rows here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                    <!-- Task Assignment Breakdown -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                        <div id="task-assignment" class="p-8 h-full flex flex-col">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Task Assignment Breakdown</h3>
                            <div class="flex flex-col flex-1 justify-center items-center">
                                <div class="flex-shrink-0 mb-4" style="height: 240px;">
                                    <canvas id="userAssignmentChart"
                                        aria-label="Task Assignment Breakdown by User"
                                        role="img"
                                        tabindex="0"
                                        style="outline:none; width:100%; height:100%;"></canvas>
                                </div>
                                @if(empty($userAssignment) || !is_countable($userAssignment) || count($userAssignment) === 0)
                                    <div class="flex flex-col items-center justify-center h-48">
                                        <svg class="w-10 h-10 mb-2 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-gray-500 dark:text-gray-400">No task assignments.</span>
                                    </div>
                                @else
                                    @php
                                        $totalCount = is_array($userAssignment)
                                            ? max(array_sum(array_column($userAssignment, 'count')), 1)
                                            : max($userAssignment->sum('count'), 1);
                                        $chartColors = [
                                            '#ffc107', '#28a745', '#dc3545', '#36a2eb',
                                            '#9966ff', '#ffce56', '#4bc0c0', '#ff6384',
                                        ];
                                    @endphp
                                    <div class="mt-4 flex flex-col items-center">
                                        @foreach($userAssignment as $i => $user)
                                            <div class="flex items-center space-x-2 mb-2">
                                                <span class="inline-block w-3 h-3 rounded-full"
                                                    style="background-color: {{ $user['color'] ?? $chartColors[$i % count($chartColors)] ?? '#888' }}"></span>
                                                <span class="text-sm text-gray-900 dark:text-white">{{ $user['user'] }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    ({{ number_format($user['count'] / $totalCount * 100, 1) }}%)
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Source of Funding Breakdown Card -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                        <div class="p-8 h-full flex flex-col justify-between">
                            <h3 id="funding-breakdown" class="text-lg font-medium text-gray-900 dark:text-white mb-4">Source of Funding Breakdown</h3>
                            <div class="h-64">
                                <canvas id="fundingSourceChart"></canvas>
                            </div>
                            <div class="mt-4">
                                @php $totalFunding = array_sum($fundingSources); @endphp
                                <div class="mb-2 text-center text-sm text-gray-400 dark:text-gray-300 font-semibold">
                                    Total Funding Source: {{ $totalFunding }}
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 exportable-table">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Source</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Count</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $totalFunding = array_sum($fundingSources); @endphp
                                            @forelse($fundingSources as $source => $count)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $source ?? 'Unknown' }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $count }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">
                                                    {{ $totalFunding > 0 ? round(($count / $totalFunding) * 100, 1) : 0 }}%
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">No Funding Sources</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Overdue Tasks -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col">
                        <div id="overdue-task" class="p-8 flex flex-col h-full">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Overdue Tasks</h3>
                            </div>
                            <div class="flex-1 flex flex-col">
                                <!-- Add overflow-x-auto and set a min-width for the table -->
                                <div class="overflow-x-auto w-full" style="max-height: 320px; overflow-y: auto;">
                                    <table class="min-w-[700px] max-w-full divide-y divide-gray-200 dark:divide-gray-700 exportable-table">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Task</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Activity</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-red-600 dark:text-red-400 uppercase">Due Date</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($overdueTasks as $task)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white whitespace-nowrap">{{ $task->task_name }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white whitespace-nowrap">{{ $task->project->proj_name ?? '' }}</td>
                                                <td class="px-4 py-2 text-sm text-red-600 dark:text-red-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white whitespace-nowrap">{{ $task->status }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500 text-base font-medium">
                                                    <div class="flex flex-col items-center justify-center">
                                                        <svg class="w-10 h-10 mb-2 text-red-300 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-1.414 1.414M6.343 17.657l-1.414-1.414M5.636 5.636l1.414 1.414M17.657 17.657l1.414-1.414M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        No overdue tasks.
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subtask Analytics -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                        <div id="subtask-analytics" class="p-8 h-full flex flex-col justify-between">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-8 flex items-center">
                                Subtask Analytics
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8 items-start">
                                <!-- Stats -->
                                <div class="space-y-8">
                                    <div class="flex items-center space-x-4">
                                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Subtasks</p>
                                            <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $totalSubtasks }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Avg. Subtasks per Task</p>
                                            <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $averageSubtasks }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                                            <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Subtask Completion Rate</p>
                                            <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $subtaskCompletionRate }}%</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Top Tasks with Most Subtasks -->
                                <div>
                                    <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-4">Tasks with Most Subtasks</h4>
                                    <div class="overflow-y-auto" style="max-height: 240px;">
                                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @forelse($topTasksWithSubtasks as $task)
                                                <li class="py-3 flex items-center justify-between">
                                                    <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $task->task_name }}</span>
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300">
                                                        {{ $task->subtasks_count }} {{ Str::plural('subtask', $task->subtasks_count) }}
                                                    </span>
                                                </li>
                                            @empty
                                                <li class="text-gray-500 dark:text-gray-400 py-2">No main tasks with subtasks.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Activity (Project) Status -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                        <div class="p-8 h-full flex flex-col justify-between">
                            <div class="flex justify-between items-center mb-10">
                                <h3 id="activity-status" class="text-lg font-medium text-gray-900 dark:text-white">Activity Status</h3>
                                <div class="flex space-x-2">
                                    <select id="projectStatusTimeFilter" class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                        <option value="all">All Time</option>
                                        <option value="week">This Week</option>
                                        <option value="month">This Month</option>
                                        <option value="year">This Year</option>
                                    </select>
                                    <select id="projectStatusChartType" class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                        <option value="pie">Pie</option>
                                        <option value="bar">Bar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="h-72">
                                <canvas id="projectStatusChart"></canvas>
                            </div>
                            <div class="mt-12 grid grid-cols-3 gap-6">
                                <div class="text-center">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">In Progress</p>
                                    <p class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">{{ $inProgressProjects }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $totalProjects > 0 ? round(($inProgressProjects / $totalProjects) * 100, 1) : 0 }}%</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Completed</p>
                                    <p class="text-lg font-semibold text-green-600 dark:text-green-400">{{ $completedProjects }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 1) : 0 }}%</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Deferred</p>
                                    <p class="text-lg font-semibold text-red-600 dark:text-red-400">{{ $deferredProjects }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $totalProjects > 0 ? round(($deferredProjects / $totalProjects) * 100, 1) : 0 }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity (Project) Type Distribution -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                        <div class="p-8 h-full flex flex-col justify-between">
                            <div class="flex justify-between items-center mb-4">
                                <h3 id="activity-types" class="text-lg font-medium text-gray-900 dark:text-white">Activity Types</h3>
                                <div class="flex space-x-2">
                                    <select id="projectTypeTimeFilter" class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                        <option value="all">All Time</option>
                                        <option value="week">This Week</option>
                                        <option value="month">This Month</option>
                                        <option value="year">This Year</option>
                                    </select>
                                    <select id="projectTypeChartType" class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                        <option value="bar">Bar</option>
                                        <option value="line">Line</option>
                                        <option value="pie">Pie</option>
                                        <option value="radar">Radar</option>
                                    </select>
                                </div>
                            </div>
                            <div id="projectTypeChartContainer" class="h-64">
                                <canvas id="projectTypeChart"></canvas>
                            </div>
                            <div class="mt-4">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 exportable-table">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Count</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @forelse($projectTypes as $type => $count)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $type }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $count }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $totalProjects > 0 ? round(($count / $totalProjects) * 100, 1) : 0 }}%</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">No Project Types</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Task Creation & Completion Trends -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                        <div id="task-trends" class="p-8 h-full flex flex-col justify-between">
                            <div class="flex justify-between items-center mb-8">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Task Creation & Completion Trends</h3>
                                <select id="trendsPeriodJump" class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    <option value="weekly">Weekly</option>
                                    <!-- JS will populate months here -->
                                </select>
                            </div>
                            <div class="flex justify-center mb-6">
                                <div id="taskTrendsChartLegend" class="flex space-x-8"></div>
                            </div>
                            <div class="h-64">
                                <canvas id="taskTrendsChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Aging Tasks -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col">
                        <div id="aging-tasks" class="p-8 flex flex-col h-full">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
                                    Aging Tasks (Oldest Open)
                                </h3>
                            </div>
                            <div class="flex-1 flex flex-col">
                                <div class="overflow-x-auto w-full" style="max-height: 320px; overflow-y: auto;">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 exportable-table">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Task</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Activity</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Opened</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($agingTasks as $task)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $task->task_name }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $task->project->proj_name ?? '' }}</td>
                                                <td class="px-4 py-2 text-sm text-purple-600 dark:text-purple-400">
                                                    {{ \Carbon\Carbon::parse($task->start_date ?? $task->created_at)->format('M d, Y') }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $task->status }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500 text-base font-medium">
                                                    <div class="flex flex-col items-center justify-center">
                                                        <svg class="w-10 h-10 mb-2 text-purple-300 dark:text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        No aging tasks.
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Task Priority Distribution -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                        <div id="task-priorities" class="p-8 h-full flex flex-col justify-between">
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center space-x-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Task Priorities</h3>
                                    <select id="taskPriorityProject"
                                        class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                        style="max-width: 12rem;">
                                        <option value="all">All Activities</option>
                                        @foreach($projectTaskData as $projectId => $data)
                                            <option value="{{ $projectId }}">{{ $data['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <select id="taskPriorityChartType" class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    <option value="pie">Pie</option>
                                    <option value="doughnut">Doughnut</option>
                                </select>
                            </div>
                            <div class="h-64">
                                <canvas id="taskPriorityChart"></canvas>
                            </div>
                            <div class="mt-4">
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="text-center">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">High Priority</p>
                                        <p class="text-lg font-semibold text-red-600 dark:text-red-400" id="highPriorityCount">0</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400" id="highPriorityPercent">0%</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Normal Priority</p>
                                        <p class="text-lg font-semibold text-yellow-600 dark:text-yellow-400" id="normalPriorityCount">0</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400" id="normalPriorityPercent">0%</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Low Priority</p>
                                        <p class="text-lg font-semibold text-green-600 dark:text-green-400" id="lowPriorityCount">0</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400" id="lowPriorityPercent">0%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Task Status Distribution -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                        <div id="task-status" class="p-8 h-full flex flex-col justify-between">
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center space-x-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Task Status</h3>
                                    <select id="taskStatusProject"
                                        class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                        style="max-width: 12rem;">
                                        <option value="all">All Activities</option>
                                        @foreach($projectTaskData as $projectId => $data)
                                            <option value="{{ $projectId }}">{{ $data['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <select id="taskStatusChartType" class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    <option value="stackedColumn">Stacked Column</option>
                                    <option value="bar">Bar</option>
                                    <option value="pie">Pie</option>
                                </select>
                            </div>
                            <div class="h-64">
                                <canvas id="taskStatusChart"></canvas>
                            </div>
                            <div class="mt-4">
                                <div class="grid grid-cols-4 gap-4">
                                    <div class="text-center">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Completed</p>
                                        <p class="text-lg font-semibold text-green-600 dark:text-green-400" id="completedStatusCount">0</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400" id="completedStatusPercent">0%</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">For Checking</p>
                                        <p class="text-lg font-semibold text-yellow-600 dark:text-yellow-400" id="checkingStatusCount">0</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400" id="checkingStatusPercent">0%</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">For Revision</p>
                                        <p class="text-lg font-semibold text-red-600 dark:text-red-400" id="revisionStatusCount">0</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400" id="revisionStatusPercent">0%</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Deferred</p>
                                        <p class="text-lg font-semibold text-gray-600 dark:text-gray-400" id="deferredStatusCount">0</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400" id="deferredStatusPercent">0%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Historical Task Priority Trends -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                        <div id="priority-trends" class="p-8 h-full flex flex-col justify-between">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Task Priority Trends</h3>
                                    <select id="priorityTrendsPeriodJump" class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    <option value="weekly">Weekly</option>
                                    <!-- JS will populate months here -->
                                </select>
                            </div>
                            <div class="h-64">
                                <canvas id="taskPriorityHistoryChart"
                                    aria-label="Task Priority Trends Over Time"
                                    role="img"
                                    tabindex="0"
                                    style="outline:none"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Historical Task Status Trends -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                        <div id="status-trends" class="p-8 h-full flex flex-col justify-between">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Task Status Trends</h3>
                                <select id="statusTrendsPeriodJump" class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    <option value="weekly">Weekly</option>
                                    <!-- JS will populate months here -->
                                </select>
                            </div>
                            <div class="h-64">
                                <canvas id="taskStatusHistoryChart"
                                    aria-label="Task Status Trends Over Time"
                                    role="img"
                                    tabindex="0"
                                    style="outline:none"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal for Funding Source Tasks -->
        <div id="fundingTasksModal" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 flex justify-center items-center transition-opacity duration-300 ease-out">
            <div id="fundingTasksContent"
                class="opacity-0 scale-95 transform transition-all duration-300 ease-out bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h2 id="fundingTasksTitle" class="text-xl font-semibold text-gray-800 dark:text-gray-100"></h2>
                    <button type="button" onclick="closeFundingTasksModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-100 text-2xl font-bold">&times;</button>
                </div>
                <ul id="fundingTasksList" class="mb-2 text-gray-700 dark:text-gray-200 list-disc pl-5" style="max-height: 500px; overflow-y: auto;"></ul>
            </div>
        </div>
    </div>
<script>
    window.dashboardData = {
        inProgressProjects: {{ $inProgressProjects }},
        completedProjects: {{ $completedProjects }},
        deferredProjects: {{ $deferredProjects }},
        totalProjects: {{ $totalProjects }},
        projectTypes: {!! json_encode($projectTypes) !!},
        projectTaskData: {!! json_encode($projectTaskData) !!},
        allProjects: {!! $allProjects->toJson() !!},
        priorityHistory: {!! json_encode($priorityHistory) !!},
        statusHistory: {!! json_encode($statusHistory) !!},
        userAssignment: {!! json_encode($userAssignment) !!},
        taskTrends: {!! json_encode($taskTrends) !!},
        priorityHistory: {!! json_encode($priorityHistory) !!},
        fundingSources: {!! json_encode($fundingSources) !!},
        projectsByStatus: {
                "In Progress": {!! \App\Models\Project::where('status', 'In Progress')->get()->map(function($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->proj_name,
                        'status' => $p->status,
                        'created_at' => $p->created_at,
                    ];
                })->toJson() !!},
                "Completed": {!! \App\Models\Project::where('status', 'Completed')->get()->map(function($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->proj_name,
                        'status' => $p->status,
                        'created_at' => $p->created_at,
                    ];
                })->toJson() !!},
                "Deferred": {!! \App\Models\Project::where('status', 'Deferred')->get()->map(function($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->proj_name,
                        'status' => $p->status,
                        'created_at' => $p->created_at,
                    ];
                })->toJson() !!}
            }
        };
    window.dashboardData.projectsByType = {!! $projectsByType->toJson() !!};    
    window.dashboardData.tasksByFundingSource = @json($tasksByFundingSource);
    window.dashboardData.userTasksByAssignee = @json($userTasksByAssignee);
</script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.progress-bar').forEach(function(bar) {
                setTimeout(function() {
                    bar.style.transition = 'width 0.7s cubic-bezier(.4,0,.2,1)';
                    bar.style.width = bar.dataset.percent + '%';
                }, 100); // Delay for smooth effect
            });

           function renderUserTasks(userName) {
                const progressBars = document.getElementById('user-progress-bars');
                const taskDetails = document.getElementById('user-task-details');
                const tbody = document.getElementById('userTaskTableBody');
                if (userName === 'all') {
                    progressBars.style.display = '';
                    taskDetails.style.display = 'none';
                    return;
                }
                progressBars.style.display = 'none';
                taskDetails.style.display = 'block';
                tbody.innerHTML = '';
                let allTasks = [];
                const users = window.dashboardData.userTasksByAssignee || [];
                const user = users.find(u => u.name === userName);
                if (user && user.tasks) {
                    allTasks = user.tasks.map(task => ({...task, user: user.name}));
                }
                if (allTasks.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">No tasks found.</td></tr>`;
                    return;
                }
                allTasks.forEach(task => {
                    // Format due date as "Month Day, Year"
                    let formattedDueDate = '';
                    if (task.due_date) {
                        const dateObj = new Date(task.due_date);
                        const options = { year: 'numeric', month: 'long', day: 'numeric' };
                        formattedDueDate = dateObj.toLocaleDateString(undefined, options);
                    }
                    tbody.innerHTML += `
                        <tr class="hover:bg-blue-50 dark:hover:bg-gray-900 transition">
                            <td class="px-4 py-2 text-sm text-indigo-700 dark:text-indigo-300">${task.project_name || ''}</td>
                            <td class="px-4 py-2 text-sm font-semibold text-gray-900 dark:text-white">${task.task_name || ''}</td>
                            <td class="px-4 py-2 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-bold
                                    ${task.status === 'Completed' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' :
                                    task.status === 'For Checking' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300' :
                                    task.status === 'For Revision' ? 'bg-pink-100 text-pink-700 dark:bg-pink-900 dark:text-pink-300' :
                                    'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300'}">
                                    ${task.status || ''}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-bold
                                    ${task.priority === 'High' ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' :
                                    task.priority === 'Normal' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300' :
                                    'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'}">
                                    ${task.priority || ''}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm font-semibold text-red-700 dark:text-red-300">${formattedDueDate}</td>
                        </tr>
                    `;
                });
            }

            // Initial render
            renderUserTasks('all');

            // Filter event
            document.getElementById('userProgressFilter').addEventListener('change', function(e) {
                renderUserTasks(e.target.value);
            });

            // List all filter/select element IDs you want to persist
            const persistentFilters = [
                'userProgressFilter',
                'projectStatusTimeFilter',
                'projectStatusChartType',
                'projectTypeTimeFilter',
                'projectTypeChartType',
                'trendsPeriodJump',
                'taskPriorityProject',
                'taskPriorityChartType',
                'taskStatusProject',
                'taskStatusChartType',
                'priorityTrendsPeriodJump',
                'statusTrendsPeriodJump'
            ];

            // Restore saved values on load
            persistentFilters.forEach(id => {
                const el = document.getElementById(id);
                if (el && localStorage.getItem('dashboard_' + id)) {
                    el.value = localStorage.getItem('dashboard_' + id);
                    // Optionally, trigger change event if your JS relies on it
                    el.dispatchEvent(new Event('change'));
                }
                // Save value on change
                if (el) {
                    el.addEventListener('change', function() {
                        localStorage.setItem('dashboard_' + id, el.value);
                    });
                }
            });


        });
    function exportDashboardToPDF(containerId, filename) {
        const container = document.getElementById(containerId);
        const tables = container.querySelectorAll('table.exportable-table');
        if (tables.length === 0) {
            alert('No exportable tables found.');
            return;
        }
        const pdf = new jsPDF('l', 'pt', 'a4');
        tables.forEach((table, idx) => {
            if (idx > 0) pdf.addPage();
            pdf.setFontSize(16);

            // Enhanced: Find the nearest heading above the table, even if wrapped in divs
            let title = `Table ${idx + 1}`;
            let heading = null;
            let el = table;
            while (el && !heading) {
                // Check all previous siblings for a heading
                let prev = el.previousElementSibling;
                while (prev) {
                    if (/H[1-4]/.test(prev.tagName)) {
                        heading = prev;
                        break;
                    }
                    // If the sibling is a div, check its last child recursively
                    if (prev.tagName === 'DIV') {
                        let last = prev.lastElementChild;
                        while (last) {
                            if (/H[1-4]/.test(last.tagName)) {
                                heading = last;
                                break;
                            }
                            last = last.previousElementSibling;
                        }
                        if (heading) break;
                    }
                    prev = prev.previousElementSibling;
                }
                el = el.parentElement;
            }
            if (heading) {
                title = heading.textContent.trim();
            }

            pdf.text(title, 40, 40);
            pdf.autoTable({
                html: table,
                startY: 60,
                theme: 'grid',
                styles: { fontSize: 10 },
                headStyles: { fillColor: [41, 128, 185] },
                margin: { left: 40, right: 40 }
            });
        });
        pdf.save(filename);
    }

    // Export dashboard to CSV (basic: grabs all tables inside the container)
    function exportDashboardToCSV(containerId, filename) {
        const container = document.getElementById(containerId);
        let csv = [];
        const tables = container.querySelectorAll('table.exportable-table');
        tables.forEach((table, idx) => {
            // --- Find the nearest heading above the table ---
            let title = `Table ${idx + 1}`;
            let heading = null;
            let el = table;
            while (el && !heading) {
                let prev = el.previousElementSibling;
                while (prev) {
                    if (/H[1-4]/.test(prev.tagName)) {
                        heading = prev;
                        break;
                    }
                    if (prev.tagName === 'DIV') {
                        let last = prev.lastElementChild;
                        while (last) {
                            if (/H[1-4]/.test(last.tagName)) {
                                heading = last;
                                break;
                            }
                            last = last.previousElementSibling;
                        }
                        if (heading) break;
                    }
                    prev = prev.previousElementSibling;
                }
                el = el.parentElement;
            }
            if (heading) {
                title = heading.textContent.trim();
            }
            // --- Add the heading as the table name in CSV ---
            csv.push(title);
            for (let row of table.rows) {
                let rowData = [];
                for (let cell of row.cells) {
                    rowData.push('"' + cell.innerText.replace(/"/g, '""') + '"');
                }
                csv.push(rowData.join(','));
            }
            csv.push(''); // Empty line between tables
        });
        if (csv.length === 0) {
            alert('No exportable tables found.');
            return;
        }
        const csvString = csv.join('\n');
        const blob = new Blob([csvString], { type: 'text/csv' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
    }
    </script>
</x-app-layout>

