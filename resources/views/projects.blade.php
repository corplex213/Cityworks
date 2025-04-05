<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Projects') }}
        </h2>
        @vite(['resources/css/projects.blade.css', 'resources/js/projects.blade.js'])
    </x-slot>
    <div id="projectSelectionSection" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="bg-green-500 text-white p-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Add Project Button -->
                    <div class="flex justify-end mb-4">
                        <button onclick="openModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Add Project
                        </button>
                    </div>

                    <!-- Project List -->
                    <div class="bg-white p-4 shadow rounded-lg">
                        <h3 class="text-lg font-semibold mb-3">Project List</h3>
                        @if($projects->isEmpty())
                            <p class="text-gray-500">No projects available.</p>
                        @else
                        <ul class="divide-y divide-gray-200">
                            @foreach($projects as $project)
                                @if(!$project->archived) <!-- Only show projects that are not archived -->
                                    <li class="py-2 flex justify-between items-center">
                                        <a href="javascript:void(0)" onclick="openProject('{{ $project->proj_name }}', '{{ $project->description }}')" class="text-blue-500 hover:underline">
                                            {{ $project->proj_name }}
                                        </a>
                                        <!-- Options Dropdown -->
                                        <div class="relative">
                                            <button onclick="toggleDropdown({{ $project->id }})" class="bg-gray-300 px-3 py-1 rounded">
                                                ⋯
                                            </button>
                                            <div id="dropdown-{{ $project->id }}" class="hidden absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg">
                                                <!-- Edit -->
                                                <button onclick="openEditModal('{{ $project->id }}', '{{ $project->proj_name }}', '{{ $project->location }}', '{{ $project->description }}')" 
                                                    class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-200">
                                                    Edit
                                                </button>
                                                <!-- Archive -->
                                                <form action="{{ route('projects.archive', $project->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-200">
                                                        Archive
                                                    </button>
                                                </form>                                                                                              
                                                <!-- Delete -->
                                                <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-200">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>                        
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('projectTable')


    <!-- Add Project Modal -->
    <div id="addProjectModal" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-lg font-semibold mb-4">Add Project</h2>
            <form action="{{ route('projects.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm font-medium">Project Name</label>
                    <input type="text" name="proj_name" class="w-full border p-2 rounded" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Location</label>
                    <input type="text" name="location" class="w-full border p-2 rounded" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Description</label>
                    <textarea name="description" class="w-full border p-2 rounded" required></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Project Modal -->
    <div id="editProjectModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-lg font-semibold mb-4">Edit Project</h2>
            <form id="editProjectForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="project_id" id="editProjectId">
                <div class="mb-3">
                    <label class="block text-sm font-medium">Project Name</label>
                    <input type="text" name="proj_name" id="editProjectName" class="w-full border p-2 rounded" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Location</label>
                    <input type="text" name="location" id="editProjectLocation" class="w-full border p-2 rounded" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Description</label>
                    <textarea name="description" id="editProjectDescription" class="w-full border p-2 rounded" required></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">Update</button>
                </div>
            </form>
        </div>
    </div>


    <!-- JavaScript for Modal and Section Toggle -->
    <script>

        function toggleModal(modalId, isVisible) {
        const modal = document.getElementById(modalId);
        if (isVisible) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
        }
        // Usage:
        function openModal() {
            toggleModal('addProjectModal', true);
        }
        function closeModal() {
            toggleModal('addProjectModal', false);
        }
        function openEditModal(id, name, location, description) {
            document.getElementById('editProjectId').value = id;
            document.getElementById('editProjectName').value = name;
            document.getElementById('editProjectLocation').value = location;
            document.getElementById('editProjectDescription').value = description;
            document.getElementById('editProjectForm').action = `/projects/${id}`;
            toggleModal('editProjectModal', true);
        }
        function closeEditModal() {
            toggleModal('editProjectModal', false);
        }

        function openProject(name, description) {
            // Hide project selection section
            document.getElementById('projectSelectionSection').classList.add('hidden');

            // Show project overview section
            document.getElementById('projectOverviewSection').classList.remove('hidden');

            // Set project name and description
            document.getElementById('projectName').textContent = name;
            document.getElementById('projectDescription').textContent = description;
        }
        function showMainTable() {
            // Show the main table section
            document.getElementById('mainTableSection').classList.remove('hidden');
            // Hide the calendar section
            document.getElementById('calendarSection').classList.add('hidden');
        }
        function showCalendar() {
            // Show the calendar section
            document.getElementById('calendarSection').classList.remove('hidden');
            // Hide the main table section
            document.getElementById('mainTableSection').classList.add('hidden');
        }
    </script>
</x-app-layout>
