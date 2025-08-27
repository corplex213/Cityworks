<x-app-layout>
    @vite(['resources/js/dashboard.js'])
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Welcome User!') }}
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
                <!-- ...existing dashboard Blade content... -->
                @include('dashboard')
            </div>
        </div>
    </div>
<script>
    window.dashboardData = {
        inProgressProjects: @js($inProgressProjects),
        completedProjects: @js($completedProjects),
        deferredProjects: @js($deferredProjects),
        totalProjects: @js($totalProjects),
        projectTypes: @js($projectTypes),
        projectTaskData: @js($projectTaskData),
        allProjects: @js($allProjects),
        priorityHistory: @js($priorityHistory),
        statusHistory: @js($statusHistory),
        userAssignment: @js($userAssignment),
        taskTrends: @js($taskTrends),
        priorityHistory: @js($priorityHistory),
    };
</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
    // ...existing exportDashboardToPDF and exportDashboardToCSV functions...
    </script>
</x-app-layout>
