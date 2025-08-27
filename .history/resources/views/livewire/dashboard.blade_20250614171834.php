<div>
    <!-- You can optionally add wire:poll.10s to auto-refresh -->
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
                <!-- ...existing code for other cards, charts, and tables... -->
            </div>
            <!-- ...existing code for charts, trends, activities, deadlines, etc... -->
        </div>
    </div>
    <!-- You can keep your dashboard.js and window.dashboardData logic in your layout or add as needed -->
</div>
