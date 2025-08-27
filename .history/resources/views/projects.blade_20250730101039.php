<!-- This blade is the Activities tab -->
<x-app-layout>
    <x-slot name="header">
        @vite(['resources/js/projects.js'])
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Activities') }}
        </h2>
        
    </x-slot>

    <div id="projectSelectionSection" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if(session('success'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2" class="fixed top-4 right-4 z-50 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg">
                        {{ session('success') }}
                    </div>
                    @endif
                
                    <div class="flex justify-between items-center mb-6 w-full gap-4 flex-wrap">
                        <!-- Left group: Search + Sort -->
                        <div class="flex items-center gap-2 w-full max-w-xl">
                            <!-- Search Bar -->
                            <div class="relative flex-1">
                                <form method="GET" action="{{ route('projects') }}">
                                    <input id="searchInput" name="search" type="text" placeholder="Search projects..." 
                                        value="{{ request('search') }}"
                                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 pl-10 pr-10 rounded-lg shadow focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                        oninput="filterProjects()">
                                    <!-- Search Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <!-- Clear Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-3 top-3 h-5 w-5 text-gray-400 cursor-pointer hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" onclick="clearSearchBar()">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </form>
                            </div>
                            <!-- Sort Dropdown -->
                            <div class="relative">
                                <button id="sortDropdownButton" onclick="toggleSortDropdown()" 
                                        class="flex items-center justify-between border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg shadow focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    Sort Options
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div id="sortDropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg transition-all duration-300 ease-out opacity-0 transform scale-95 z-50">
                                    <form method="GET" action="{{ route('projects') }}" class="p-2">
                                            <input type="hidden" name="search" value="{{ request('search') }}">
                                            <button type="submit" name="sort" value="proj_name" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 {{ request('sort') == 'proj_name' ? 'font-bold' : '' }}">
                                                Project Name
                                            </button>
                                            <button type="submit" name="sort" value="proj_type" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 {{ request('sort') == 'proj_type' ? 'font-bold' : '' }}">
                                                Type of Project
                                            </button>
                                            <button type="submit" name="sort" value="status" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 {{ request('sort') == 'status' ? 'font-bold' : '' }}">
                                                Status
                                            </button>
                                            <button type="submit" name="sort" value="created_at" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 {{ request('sort') == 'created_at' ? 'font-bold' : '' }}">
                                                Created At
                                            </button>
                                        </form>
                                </div>
                            </div>
                        </div>
                        <!-- Right group: Export + Add -->
                        <div class="flex items-center gap-2">
                            <button onclick="exportProjectsToPDF()" class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg shadow">
                                Export PDF
                            </button>
                            <button onclick="exportProjectsToCSV()" class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg shadow">
                                Export CSV
                            </button>
                            <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg shadow whitespace-nowrap">
                                Add Project
                            </button>
                        </div>
                    </div>

                <!-- Project Tabs -->
                <div class="relative">
                    <div class="flex justify-start space-x-4 mb-6 border-b border-gray-300 dark:border-gray-600">
                        <a href="{{ route('projects', ['search' => request('search')]) }}" 
                        class="tab-link px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 {{ !request('proj_type') ? 'active-tab' : '' }}">
                            All Projects
                        </a>
                        <a href="{{ route('projects', ['proj_type' => 'POW', 'search' => request('search')]) }}" 
                        class="tab-link px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 {{ request('proj_type') == 'POW' ? 'active-tab' : '' }}">
                            POW
                        </a>
                        <a href="{{ route('projects', ['proj_type' => 'Investigation', 'search' => request('search')]) }}" 
                        class="tab-link px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 {{ request('proj_type') == 'Investigation' ? 'active-tab' : '' }}">
                            Investigation
                        </a>
                        <a href="{{ route('projects', ['proj_type' => 'MTS', 'search' => request('search')]) }}" 
                        class="tab-link px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 {{ request('proj_type') == 'MTS' ? 'active-tab' : '' }}">
                            MTS
                        </a>
                        <a href="{{ route('projects', ['proj_type' => 'Communication', 'search' => request('search')]) }}" 
                        class="tab-link px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 {{ request('proj_type') == 'Communication' ? 'active-tab' : '' }}">
                            Communication
                        </a>
                        <a href="{{ route('projects', ['proj_type' => 'R&D', 'search' => request('search')]) }}" 
                        class="tab-link px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 {{ request('proj_type') == 'R&D' ? 'active-tab' : '' }}">
                            R&D
                        </a>
                    </div>
                    <!-- Animated Underline -->
                    <div id="tabIndicator" class="absolute bottom-0 h-0.5 bg-blue-500 transition-all duration-300"></div>
                </div>
                    <!-- Project Table -->
                    <div class="overflow-x-auto rounded-lg shadow-lg">
                        <table class="min-w-full bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <!-- Checkbox for Select All -->
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200">
                                        <input type="checkbox" id="selectAllCheckbox" 
                                               class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                               onclick="toggleSelectAll(this)">
                                    </th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 rounded-tl-lg">
                                        <a href="{{ route('projects', ['sort' => 'proj_name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}">
                                            Project Name
                                            @if(request('sort') == 'proj_name')
                                                <span>{{ request('direction') == 'asc' ? '▲' : '▼' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200">
                                        <a href="{{ route('projects', ['sort' => 'proj_type', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}">
                                            Type of Project
                                            @if(request('sort') == 'proj_type')
                                                <span>{{ request('direction') == 'asc' ? '▲' : '▼' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200">
                                        <a href="{{ route('projects', ['sort' => 'status', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}">
                                            Status
                                            @if(request('sort') == 'status')
                                                <span>{{ request('direction') == 'asc' ? '▲' : '▼' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200">
                                        <a href="{{ route('projects', ['sort' => 'created_at', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}">
                                            Created At
                                            @if(request('sort') == 'created_at')
                                                <span>{{ request('direction') == 'asc' ? '▲' : '▼' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="projectTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($projects as $project)
                                <tr class="project-row hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150" 
                                    data-project-id="{{ $project->id }}">
                                    <!-- Checkbox for Individual Selection -->
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        <input type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800 project-checkbox">
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 project-name">
                                        <a href="{{ route('projects.show', $project->id) }}" 
                                           class="font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                            {{ $project->proj_name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $project->proj_type }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="relative">
                                            <button onclick="toggleStatusDropdown({{ $project->id }})" 
                                                    class="flex items-center px-2 py-1 rounded-lg focus:outline-none 
                                                    {{ $project->status === 'In Progress' ? 'bg-yellow-100 text-yellow-800' : ($project->status === 'Completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                                <span class="inline-block w-3 h-3 rounded-full mr-2 
                                                    {{ $project->status === 'In Progress' ? 'bg-yellow-500' : ($project->status === 'Completed' ? 'bg-green-500' : 'bg-red-500') }}">
                                                </span>
                                                {{ $project->status }}
                                            </button>
                                            <div id="statusDropdown-{{ $project->id }}" 
                                                class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg shadow-lg z-50 transition-all duration-300 ease-in-out opacity-0 transform scale-95">
                                               <form action="{{ route('projects.updateStatus', $project->id) }}" method="POST">
                                                   @csrf
                                                   @method('PUT')
                                                   <button type="submit" name="status" value="In Progress" 
                                                           class="block w-full text-left px-4 py-2 text-sm text-yellow-400 hover:bg-yellow-100 dark:hover:bg-yellow-700">
                                                       In Progress
                                                   </button>
                                                   <button type="submit" name="status" value="Completed" 
                                                           class="block w-full text-left px-4 py-2 text-sm text-green-400 hover:bg-green-100 dark:hover:bg-green-700">
                                                       Completed
                                                   </button>
                                                   <button type="submit" name="status" value="Deferred" 
                                                           class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-red-100 dark:hover:bg-red-700">
                                                       Deferred
                                                   </button>
                                               </form>
                                           </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $project->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No projects available.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $projects->appends(['search' => request('search')])->links('pagination::tailwind') }}
                        </div>
                    </div>
                    <div id="toolbar" class="fixed bottom-0 left-0 w-full bg-gray-100 dark:bg-gray-800 shadow-lg border-t border-gray-300 dark:border-gray-700 z-50 transition-transform duration-300 ease-in-out translate-y-full">
                        <div class="flex justify-between items-center px-6 py-3">
                            <!-- Selected Count -->
                            <div class="flex items-center space-x-2">
                                <span id="selectedCount" class="text-sm font-medium text-gray-800 dark:text-gray-200">0</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">project(s) selected</span>
                            </div>
                    
                            <!-- Action Buttons -->
                            <div class="flex space-x-2">
                                <button id="deleteSelectedButton" 
                                        class="flex items-center space-x-2 bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-3 rounded-lg shadow transition disabled:opacity-50 disabled:cursor-not-allowed"
                                        onclick="deleteSelectedProjects()" disabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M9 6v12m6-12v12M4 6l1.5 14.5a2 2 0 002 1.5h9a2 2 0 002-1.5L20 6" />
                                    </svg>
                                    <span>Delete</span>
                                </button>
                                <button id="archiveSelectedButton" 
                                        class="flex items-center space-x-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-3 rounded-lg shadow transition disabled:opacity-50 disabled:cursor-not-allowed"
                                        onclick="confirmArchiveSelectedProjects()" disabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                    </svg>
                                    <span>Archive</span>
                                </button>
                                <button id="editSelectedButton" 
                                        class="flex items-center space-x-2 bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-3 rounded-lg shadow transition disabled:opacity-50 disabled:cursor-not-allowed"
                                        onclick="editSelectedProjects()" disabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span>Edit</span>
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
            </div>
        </div>
    </div>

    <!-- Add Project Modal -->
    <div id="addProjectModal" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 flex justify-center items-center transition-opacity duration-300 ease-out">
        <div id="addProjectContent"
            class="opacity-0 scale-95 transform transition-all duration-300 ease-out bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-full max-w-md">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Add Project</h2>
            <form action="{{ route('projects.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Project Name</label>
                    <input type="text" name="proj_name"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Type of Project</label>
                    <select name="proj_type"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        required>
                        <option value="POW">POW</option>
                        <option value="Investigation">Investigation</option>
                        <option value="MTS">MTS</option>
                        <option value="Communication">Communication</option>
                        <option value="R&D">R&D</option>
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeModal()"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg mr-2 transition">Cancel</button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">Create</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Edit Project Modal -->
    <div id="editProjectModal" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 flex justify-center items-center transition-opacity duration-300 ease-out">
        <div class="opacity-0 scale-95 transform transition-all duration-300 ease-out bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-full max-w-md">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Edit Project</h2>
            <form id="editProjectForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="project_id" id="editProjectId">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Project Name</label>
                    <input type="text" name="proj_name" id="editProjectName"
                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Type of Project</label>
                    <select name="proj_type" id="editProjectType"
                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        required>
                        <option value="POW">POW</option>
                        <option value="Investigation">Investigation</option>
                        <option value="MTS">MTS</option>
                        <option value="Communication">Communication</option>
                        <option value="R&D">R&D</option>
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeEditModal()"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg mr-2 transition">Cancel</button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmationModal" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 flex justify-center items-center transition-opacity duration-300 ease-out">
        <div id="deleteConfirmationContent" class="opacity-0 scale-95 transform transition-all duration-300 ease-out bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-full max-w-md">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Confirm Deletion</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to delete the selected projects? This action cannot be undone.</p>
            <div class="flex justify-end">
                <button type="button" onclick="closeDeleteModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg mr-2 transition">Cancel</button>
                <form id="deleteProjectForm" method="POST" action="{{ route('projects.bulkDelete') }}">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Archive Confirmation Modal -->
    <div id="archiveConfirmationModal" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 flex justify-center items-center transition-opacity duration-300 ease-out">
        <div id="archiveConfirmationContent" class="opacity-0 scale-95 transform transition-all duration-300 ease-out bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-full max-w-md">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Confirm Archiving</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to archive the selected projects? This action cannot be undone.</p>
            <div class="flex justify-end">
                <button type="button" onclick="closeArchiveModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg mr-2 transition">Cancel</button>
                <form id="archiveProjectForm" method="POST" action="{{ route('projects.bulkArchive') }}">
                    @csrf
                    @method('POST')
                    <div id="archiveProjectIdsContainer"></div>
                    <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition">Archive</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>