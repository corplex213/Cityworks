<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            @vite(['resources/js/archiveProjects.js'])
            {{ __('Archived Activities') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="bg-green-500 text-white p-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Search Options -->
                    <div class="flex justify-between items-center mb-6 w-full gap-4">
                        <div class="relative w-full max-w-lg">
                            <form method="GET" action="{{ route('archiveProjects') }}">
                                <input id="searchInput" name="search" type="text" placeholder="Search archived activities..." 
                                       value="{{ request('search') }}"
                                       class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 pl-10 pr-10 rounded-lg shadow focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                       oninput="filterArchivedProjects()">
                                <!-- Search Icon -->
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="absolute left-3 top-3 h-5 w-5 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    />
                                </svg>
                                <!-- Clear Icon -->
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="absolute right-3 top-3 h-5 w-5 text-gray-400 cursor-pointer hover:text-gray-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    onclick="clearArchivedSearchBar()"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </form>
                        </div>
                        <!-- Export as PDF or CSV -->
                        <div class="flex items-center gap-2">
                            <button onclick="exportArchivedToPDF()" class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg shadow">
                                Export PDF
                            </button>
                            <button onclick="exportArchivedToCSV()" class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg shadow">
                                Export CSV
                            </button>
                        </div>
                    </div>

                    <!-- Archived Project Table -->
                    <div class="overflow-x-auto rounded-lg shadow-lg">
                        <table class="min-w-full bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <!-- Checkbox for Select All -->
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200">
                                        <input type="checkbox" id="selectAllCheckbox" 
                                               class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                               onclick="toggleSelectAll(this)">
                                    </th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 rounded-tl-lg">
                                        Project Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="archivedProjectTableBody" class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($archivedProjects as $project)
                                    <tr class="archived-project-row" data-project-id="{{ $project->id }}">
                                        <!-- Checkbox for Individual Selection -->
                                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                            <input type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800 project-checkbox">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $project->proj_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $project->status }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No archived activity found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $archivedProjects->appends(['search' => request('search')])->links('pagination::tailwind') }}
                        </div>
                    </div>
                    
                    <div id="toolbar" class="fixed bottom-0 left-0 w-full bg-gray-100 dark:bg-gray-800 shadow-lg border-t border-gray-300 dark:border-gray-700 z-50 transition-transform duration-300 ease-in-out translate-y-full">
                        <div class="flex justify-between items-center px-6 py-3">
                            <!-- Selected Count -->
                            <div class="flex items-center space-x-2">
                                <span id="selectedCount" class="text-sm font-medium text-gray-800 dark:text-gray-200">0</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Activity selected</span>
                            </div>
                    
                            <!-- Action Buttons -->
                            <div class="flex space-x-2">
                                <button id="deleteSelectedButton" 
                                        class="flex items-center space-x-2 bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-3 rounded-lg shadow transition disabled:opacity-50 disabled:cursor-not-allowed"
                                        onclick="confirmDeleteSelectedProjects()" disabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M9 6v12m6-12v12M4 6l1.5 14.5a2 2 0 002 1.5h9a2 2 0 002-1.5L20 6" />
                                    </svg>
                                    <span>Delete</span>
                                </button>
                                <button id="restoreSelectedButton" 
                                        class="flex items-center space-x-2 bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-3 rounded-lg shadow transition disabled:opacity-50 disabled:cursor-not-allowed"
                                        onclick="confirmRestoreSelectedProjects()" disabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                    </svg>
                                    <span>Restore</span>
                                </button>
                    
                                <!-- Transparent Exit Button -->
                                <button id="closeToolbarButton" 
                                        class="flex items-center justify-center bg-transparent text-gray-800 dark:text-gray-200 font-medium py-2 px-3 rounded-lg transition hover:bg-gray-200 dark:hover:bg-gray-700"
                                        onclick="closeToolbar()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="restoreConfirmationModal" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 flex justify-center items-center transition-opacity duration-300 ease-out">
                        <div id="restoreConfirmationContent" class="opacity-0 scale-95 transform transition-all duration-300 ease-out bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-full max-w-md">
                            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Confirm Restoration</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to restore the selected projects? This action cannot be undone.</p>
                            <div class="flex justify-end">
                                <button type="button" onclick="closeRestoreModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg mr-2 transition">Cancel</button>
                                <form id="restoreProjectForm" method="POST" action="{{ route('projects.bulkRestore') }}">
                                    @csrf
                                    @method('POST')
                                    <div id="restoreProjectIdsContainer"></div>
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">Restore</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="deleteConfirmationModal" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 flex justify-center items-center transition-opacity duration-300 ease-out">
                        <div id="deleteConfirmationContent" class="opacity-0 scale-95 transform transition-all duration-300 ease-out bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-full max-w-md">
                            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Confirm Deletion</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to delete the selected projects? This action cannot be undone.</p>
                            <div class="flex justify-end">
                                <button type="button" onclick="closeDeleteModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg mr-2 transition">Cancel</button>
                                <form id="deleteProjectForm" method="POST">
                                    @csrf
                                    <input type="hidden" name="ids">
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.deleteRoute = "{{ route('projects.bulkDelete') }}";
        window.restoreRoute = "{{ route('projects.bulkRestore') }}";
        window.csrfToken = "{{ csrf_token() }}";
    </script>
</x-app-layout>