<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Projects') }}
        </h2>
        @vite(['resources/css/projects.blade.css'])
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
                
                    <!-- Add Project Button -->
                    <div class="flex justify-between items-center mb-6 w-full gap-4">
                        <div class="relative w-full max-w-lg">
                            <form method="GET" action="{{ route('projects') }}">
                                <input id="searchInput" name="search" type="text" placeholder="Search projects..." 
                                       value="{{ request('search') }}"
                                       class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 pl-10 rounded-lg shadow focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </form>
                        </div>
                        <div class="relative">
                            <button id="sortDropdownButton" onclick="toggleSortDropdown()" 
                                    class="flex items-center justify-between w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg shadow focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                Sort Options
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="sortDropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg transition-all duration-300 ease-out opacity-0 transform scale-95 z-50">
                                <form method="GET" action="{{ route('projects') }}" id="sortForm">
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                    <button type="submit" name="sort" value="created_at" 
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Sort by Created At
                                    </button>
                                    <button type="submit" name="sort" value="proj_name" 
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Sort by Project Name
                                    </button>
                                    <button type="submit" name="sort" value="location" 
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Sort by Location
                                    </button>
                                    <button type="submit" name="sort" value="status" 
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Sort by Status
                                    </button>
                                </form>
                            </div>
                        </div>
                        <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg shadow whitespace-nowrap">
                            Add Project
                        </button>
                    </div>

                    <!-- Project Table -->
                    <div class="overflow-x-auto rounded-lg shadow-lg">
                        <table class="min-w-full bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 rounded-tl-lg">
                                        <a href="{{ route('projects', ['sort' => 'proj_name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}">
                                            Project Name
                                            @if(request('sort') == 'proj_name')
                                                <span>{{ request('direction') == 'asc' ? '▲' : '▼' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200">
                                        <a href="{{ route('projects', ['sort' => 'location', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}">
                                            Location
                                            @if(request('sort') == 'location')
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
                                    <th class="px-6 py-3 text-center text-sm font-medium text-gray-800 dark:text-gray-200 rounded-tr-lg">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="projectTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($projects->where('archived', false) as $project)
                                <tr class="project-row hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 project-name">
                                        <a href="{{ route('projects.show', $project->id) }}" 
                                           class="font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                            {{ $project->proj_name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $project->location }}
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
                                            <div id="statusDropdown-{{ $project->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg shadow-lg z-50">
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
                                                    <button type="submit" name="status" value="Delayed" 
                                                            class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-red-100 dark:hover:bg-red-700">
                                                        Delayed
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $project->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        <div class="flex items-center justify-center space-x-4">
                                            <!-- Edit Icon -->
                                            <button onclick="openEditModal('{{ $project->id }}', '{{ $project->proj_name }}', '{{ $project->location }}', '{{ $project->description }}')" 
                                                    class="flex items-center justify-center text-blue-500 hover:text-blue-700 transition duration-300 ease-in-out h-8 w-8">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M16 5l3 3m-9 6l3-3m-3 3l-3-3" />
                                                </svg>
                                            </button>

                                            <!-- Archive Icon -->
                                            <div class="flex items-center justify-center h-8 w-8">
                                                <form action="{{ route('projects.archive', $project->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="flex items-center justify-center text-yellow-500 hover:text-yellow-700 transition duration-300 ease-in-out h-8 w-8">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 19H5V6h14v13zM12 2v4m4 10H8" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                            <!-- Delete Icon -->
                                            <button onclick="openDeleteModal({{ $project->id }}, '{{ route('projects.destroy', $project->id) }}')" 
                                                    class="flex items-center justify-center text-red-500 hover:text-red-700 transition duration-300 ease-in-out h-8 w-8">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
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
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Location</label>
                    <input type="text" name="location"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="description"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        required></textarea>
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
                    <input type="text" name="proj_name" id="editProjectName" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Location</label>
                    <input type="text" name="location" id="editProjectLocation" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="description" id="editProjectDescription" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg mr-2 transition">Cancel</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">Update</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editProjectModal" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 flex justify-center items-center transition-opacity duration-300 ease-out">
        <div class="opacity-0 scale-95 transform transition-all duration-300 ease-out bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-full max-w-md">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Edit Project</h2>
            <form id="editProjectForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="project_id" id="editProjectId">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Project Name</label>
                    <input type="text" name="proj_name" id="editProjectName" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Location</label>
                    <input type="text" name="location" id="editProjectLocation" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="description" id="editProjectDescription" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg mr-2 transition">Cancel</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
        <div id="deleteConfirmationModal" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 flex justify-center items-center transition-opacity duration-300 ease-out">
            <div id="deleteConfirmationContent" class="opacity-0 scale-95 transform transition-all duration-300 ease-out bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-full max-w-md">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Confirm Deletion</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to delete this project? This action cannot be undone.</p>
                <div class="flex justify-end">
                    <button type="button" onclick="closeDeleteModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg mr-2 transition">Cancel</button>
                    <form id="deleteProjectForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">Delete</button>
                    </form>
                </div>
            </div>
        </div>

    <!-- JavaScript for Modal and Dropdown -->
    <script>
        function toggleModal(modalId, isVisible) {
            const modal = document.getElementById(modalId);
            if (isVisible) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }
        function openModal() {
            const modal = document.getElementById('addProjectModal');
            const content = document.getElementById('addProjectContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('opacity-0', 'scale-95');
            }, 10);
        }

        function closeModal() {
            const modal = document.getElementById('addProjectModal');
            const content = document.getElementById('addProjectContent');
            content.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
        function openEditModal(id, name, location, description) {
            const modal = document.getElementById('editProjectModal');
            const content = modal.querySelector('.bg-white');
            const form = document.getElementById('editProjectForm');

            // Set the form action dynamically
            form.action = `/projects/${id}`;

            // Populate the form fields
            document.getElementById('editProjectId').value = id;
            document.getElementById('editProjectName').value = name;
            document.getElementById('editProjectLocation').value = location;
            document.getElementById('editProjectDescription').value = description;

            // Show the modal with animation
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('opacity-0', 'scale-95');
            }, 10);
        }

        function closeEditModal() {
            const modal = document.getElementById('editProjectModal');
            const content = modal.querySelector('.bg-white');

            // Hide the modal with animation
            content.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
        function toggleDropdown(projectId) {
            document.getElementById('dropdown-' + projectId).classList.toggle('hidden');
        }
        function toggleStatusDropdown(projectId) {
        const dropdown = document.getElementById(`statusDropdown-${projectId}`);
        dropdown.classList.toggle('hidden');
        }
        function openProject(name, description) {
            document.getElementById('projectSelectionSection').classList.add('hidden');
            document.getElementById('projectOverviewSection').classList.remove('hidden');
            document.getElementById('projectName').textContent = name;
            document.getElementById('projectDescription').textContent = description;
        }
        function openDeleteModal(projectId, deleteUrl) {
        const modal = document.getElementById('deleteConfirmationModal');
        const content = document.getElementById('deleteConfirmationContent');
        const form = document.getElementById('deleteProjectForm');

        // Set the form action to the delete URL
        form.action = deleteUrl;

        // Show the modal with animation
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('opacity-0', 'scale-95');
        }, 10);
        }
        function closeDeleteModal() {
            const modal = document.getElementById('deleteConfirmationModal');
            const content = document.getElementById('deleteConfirmationContent');

            // Hide the modal with animation
            content.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
        function toggleSortDropdown() {
            const dropdown = document.getElementById('sortDropdownMenu');
            const button = document.getElementById('sortDropdownButton');

            // Toggle dropdown visibility
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                setTimeout(() => {
                    dropdown.classList.remove('opacity-0', 'scale-95');
                    dropdown.classList.add('opacity-100', 'scale-100');
                }, 10);

                // Add event listener to close dropdown when clicking outside
                document.addEventListener('click', closeDropdownOnClickOutside);
            } else {
                closeDropdown();
            }

            function closeDropdown() {
                dropdown.classList.remove('opacity-100', 'scale-100');
                dropdown.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    dropdown.classList.add('hidden');
                }, 300);
                // Remove the event listener
                document.removeEventListener('click', closeDropdownOnClickOutside);
            }

            function closeDropdownOnClickOutside(event) {
                if (!dropdown.contains(event.target) && !button.contains(event.target)) {
                    closeDropdown();
                }
            }
        }
    </script>
</x-app-layout>