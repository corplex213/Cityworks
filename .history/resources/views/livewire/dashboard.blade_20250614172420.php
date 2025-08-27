<div>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css">
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            <!-- Project Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <!-- Total Projects Card -->
                <div id="total-projects-card" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Projects</p>
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
                <!-- In Progress Projects Card -->
                <div id="in-progress-projects-card" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18M3 12h18M3 21h18" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">In Progress Projects</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $inProgressProjects }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Completed Projects Card -->
                <div id="completed-projects-card" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4 -4m2 2a9 9 0 11-18 0a9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Completed Projects</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $completedProjects }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Deferred Projects Card -->
                <div id="deferred-projects-card" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18M3 12h18M3 21h18" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Deferred Projects</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $deferredProjects }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Charts and Trends Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                <!-- Budget vs Actual Chart -->
                <div id="budget-vs-actual-chart" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Budget vs Actual</h3>
                        <div class="chart-container">
                            <!-- Your chart.js or other chart library code here -->
                        </div>
                    </div>
                </div>
                <!-- Projects by Type Chart -->
                <div id="projects-by-type-chart" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Projects by Type</h3>
                        <div class="chart-container">
                            <!-- Your chart.js or other chart library code here -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Recent Activities and Deadlines Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                <!-- Recent Activities Table -->
                <div id="recent-activities-table" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activities</h3>
                        <div class="table-responsive">
                            <!-- Your Livewire table or other table code here -->
                        </div>
                    </div>
                </div>
                <!-- Upcoming Deadlines Table -->
                <div id="upcoming-deadlines-table" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Upcoming Deadlines</h3>
                        <div class="table-responsive">
                            <!-- Your Livewire table or other table code here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    window.dashboardData = {
        inProgressProjects: @json($inProgressProjects),
        completedProjects: @json($completedProjects),
        deferredProjects: @json($deferredProjects),
        totalProjects: @json($totalProjects),
        projectTypes: @json($projectTypes),
        projectTaskData: @json($projectTaskData),
        cumulativeBudget: @json($cumulativeBudget),
        cumulativeCompletedBudget: @json($cumulativeCompletedBudget ?? []),
        cumulativeOngoingBudget: @json($cumulativeOngoingBudget ?? []),
        plannedBudget: @json($plannedBudget ?? []),
        actualBudget: @json($actualBudget ?? []),
        forecastBudget: @json($forecastBudget ?? []),
        totalBudget: @json($totalBudget),
        allProjects: @json($allProjects),
        priorityHistory: @json($priorityHistory),
        statusHistory: @json($statusHistory),
        userAssignment: @json($userAssignment),
    };
    </script>
</div>
