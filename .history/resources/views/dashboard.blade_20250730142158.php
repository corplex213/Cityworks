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
                    <div id = "recent-activities" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                        <div class="p-8 h-full flex flex-col justify-between">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Recent Activities</h3>
                            <div class="space-y-4">
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
                                            <p class="text-sm text-gray-900 dark:text-white">{{ $activity->description }}</p>
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
                        <div id = "upcoming-deadlines" class="flex-1 flex flex-col p-8">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Upcoming Deadlines</h3>
                            <div class="flex justify-center w-full">
                                <div class="overflow-x-auto">
                                    <table class="min-w-[400px] max-w-full divide-y divide-gray-200 dark:divide-gray-700 exportable-table">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Activity</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Task</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Deadline</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Priority</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @forelse($upcomingDeadlines as $task)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $task->proj_name }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $task->task_name }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            {{ $task->status === 'For Checking' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : 
                                                            ($task->status === 'Completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 
                                                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300') }}">
                                                            {{ $task->status }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
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

                    <!-- Task Assignment Breakdown -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                        <div id="task-assignment" class="p-8 h-full flex flex-col justify-between">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Task Assignment Breakdown</h3>
                            <div class="h-64">
                                <canvas id="userAssignmentChart"
                                    aria-label="Task Assignment Breakdown by User"
                                    role="img"
                                    tabindex="0"
                                    style="outline:none"></canvas>

                                    @if(empty($userAssignment) || count($userAssignment) === 0)
                                        <div class="flex flex-col items-center justify-center h-48">
                                            <svg class="w-10 h-10 mb-2 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-gray-500 dark:text-gray-400">No task assignments.</span>
                                        </div>
                                    @else
                                        <div class="h-64">
                                            <canvas id="userAssignmentChart"
                                                aria-label="Task Assignment Breakdown by User"
                                                role="img"
                                                tabindex="0"
                                                style="outline:none"></canvas>
                                        </div>
                                    @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                    <!-- Task Completion & Overdue Rate Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div id="task-completion" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                            <div class="p-8 h-full flex flex-col justify-between">
                                <div class="flex items-center">
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
                        </div>
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                            <div id="overdue-task-rate" class="p-8 h-full flex flex-col justify-between">
                                <div class="flex items-center">
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

                    <!-- Average Time to Complete Tasks Card -->
                    <div class="bg-white dark:bg-gray-800 mb-4 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                        <div class="p-8 h-full flex flex-col justify-between">
                            <div id="average-time" class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg. Time to Complete Task</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $averageTimeHours }} hrs</p>
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
                            <div class="flex justify-center w-full flex-1">
                                <div class="overflow-x-auto w-full">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 exportable-table">
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
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $task->task_name }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $task->project->proj_name ?? '' }}</td>
                                                <td class="px-4 py-2 text-sm text-red-600 dark:text-red-400">{{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $task->status }}</td>
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


                    <!-- Activity (Project) Status -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                        <div class="p-8 h-full flex flex-col justify-between">
                            <div class="flex justify-between items-center mb-10">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Activity Status</h3>
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
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Activity Types</h3>
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
                        <div class="p-8 h-full flex flex-col justify-between">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-8 flex items-center">
                                Task Creation & Completion Trends
                            </h3>
                            <div class="flex flex-col h-full">
                                <div class="flex justify-center mb-6">
                                    <div id="taskTrendsChartLegend" class="flex space-x-8"></div>
                                </div>
                                <div class="h-64">
                                    <canvas id="taskTrendsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aging Tasks -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                        <div class="p-8 h-full flex flex-col">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
                                    Aging Tasks (Oldest Open)
                                </h3>
                            </div>
                            <div class="overflow-x-auto w-full">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 exportable-table">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Task</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Project</th>
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

                    <!-- Task Priority Distribution -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg h-full flex flex-col justify-between">
                        <div class="p-8 h-full flex flex-col justify-between">
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center space-x-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Task Priorities</h3>
                                    <select id="taskPriorityProject" class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                        <option value="all">All Projects</option>
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
                        <div class="p-8 h-full flex flex-col justify-between">
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center space-x-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Task Status</h3>
                                    <select id="taskStatusProject" class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                        <option value="all">All Projects</option>
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
                        <div class="p-8 h-full flex flex-col justify-between">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Task Priority Trends</h3>
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
                        <div class="p-8 h-full flex flex-col justify-between">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Task Status Trends</h3>
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
    };
</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
    function exportDashboardToPDF(containerId, filename) {
        const container = document.getElementById(containerId);
        const tables = container.querySelectorAll('table.exportable-table');
        if (tables.length === 0) {
            alert('No exportable tables found.');
            return;
        }
        // Find unique parent containers for each exportable table
        const sections = [];
        tables.forEach((table) => {
            let section = table.closest('.overflow-x-auto') || table.parentElement;
            if (section && !sections.includes(section)) {
                sections.push(section);
            }
        });
        if (sections.length === 0) {
            alert('No exportable containers found.');
            return;
        }
        const pdf = new jspdf.jsPDF('l', 'pt', 'a4');
        let addPage = false;
        // Helper to capture each section and add to PDF
        const processSection = (idx) => {
            if (idx >= sections.length) {
                pdf.save(filename);
                return;
            }
            const section = sections[idx];
            // Create a wrapper to force light background for export
            const tempWrapper = document.createElement('div');
            tempWrapper.classList.add('pdf-export-style');
            tempWrapper.style.background = 'white';
            tempWrapper.style.color = 'black';
            tempWrapper.style.padding = '24px';
            tempWrapper.style.width = section.offsetWidth + 'px';
            tempWrapper.style.maxWidth = '100vw';
            tempWrapper.style.overflow = 'auto';
            // Clone the section for screenshotting
            tempWrapper.appendChild(section.cloneNode(true));
            document.body.appendChild(tempWrapper);
            // Wait for fonts/images to load
            setTimeout(() => {
                html2canvas(tempWrapper, {backgroundColor: '#fff', scale: 2}).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const pageWidth = pdf.internal.pageSize.getWidth();
                    const pageHeight = pdf.internal.pageSize.getHeight();
                    // Fit image to page width, keep aspect ratio
                    let imgWidth = pageWidth - 40;
                    let imgHeight = canvas.height * imgWidth / canvas.width;
                    if (imgHeight > pageHeight - 60) {
                        imgHeight = pageHeight - 60;
                        imgWidth = canvas.width * imgHeight / canvas.height;
                    }
                    if (addPage) pdf.addPage();
                    // Add table title
                    pdf.setFontSize(16);
                    pdf.text(`Table ${idx + 1}`, 30, 40);
                    pdf.addImage(imgData, 'PNG', 20, 50, imgWidth, imgHeight);
                    document.body.removeChild(tempWrapper);
                    addPage = true;
                    processSection(idx + 1);
                });
            }, 100); // Give time for rendering
        };
        processSection(0);
    }

    // Export dashboard to CSV (basic: grabs all tables inside the container)
    function exportDashboardToCSV(containerId, filename) {
        const container = document.getElementById(containerId);
        let csv = [];
        // Only select tables with the exportable-table class
        const tables = container.querySelectorAll('table.exportable-table');
        tables.forEach((table, idx) => {
            csv.push(`Table ${idx + 1}`);
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

