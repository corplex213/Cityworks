<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Projects') }}
        </h2>
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

    <!-- Project Overview Section -->
<div id="projectOverviewSection" class="hidden bg-white p-6 rounded-lg shadow-lg">
    <section class="project-overview">
        <!-- Project Header -->
        <div class="project-header border-b border-gray-200 pb-4 mb-6">
            <h1 id="projectName" class="text-2xl font-bold text-green-600 editable" contenteditable="true">Project Name</h1>
            <p id="projectDescription" class="text-gray-700 mt-2 editable" contenteditable="true">
                Project description goes here. This is a brief overview of the project objectives and goals.
            </p>
        </div>

        <!-- Task Table Wrapper -->
        <div class="task-table-wrapper">
            <div class="button-table-wrapper flex justify-start space-x-4">
                <!-- Buttons -->
                <button id="mainTableBtn" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded shadow" onclick="showMainTable()">
                    Main Table
                </button>
                <button id="calendarBtn" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded shadow" onclick="showCalendar()">
                    Calendar
                </button>
            </div>
        </div>
    </section>

    <!-- Main Table Section -->
    <section id="mainTableSection" class="group-section mt-8">
        <div class="group-container bg-gray-50 p-4 rounded-lg shadow">
            <!-- Placeholder for group content -->
            <p class="text-gray-500">No groups available. Add a new group to get started.</p>
        </div>
        <div class="add-group-container mt-4">
            <button id="addGroupBtn" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded shadow">
                Add New Group
            </button>
        </div>
    </section>

    <!-- Calendar Section -->
    <section id="calendarSection" class="calendar-section mt-8 hidden">
        <div class="calendar-header flex items-center justify-between bg-gray-100 p-4 rounded-lg shadow">
            <button id="prevMonth" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded shadow">
                Prev
            </button>
            <span id="monthYearDisplay" class="text-gray-700 font-semibold">Month Year</span>
            <button id="nextMonth" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded shadow">
                Next
            </button>
        </div>
        <div class="calendar-grid mt-4 bg-gray-50 p-4 rounded-lg shadow">
            <!-- Placeholder for calendar grid -->
            <p class="text-gray-500">Calendar content will appear here.</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer mt-8 border-t border-gray-200 pt-4">
        <p class="text-center text-gray-500">&copy; 2024 City Engineering Office. All rights reserved.</p>
    </footer>
</div>
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
        function openModal() {
            document.getElementById('addProjectModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('addProjectModal').classList.add('hidden');
        }

        function toggleDropdown(projectId) {
            document.getElementById('dropdown-' + projectId).classList.toggle('hidden');
        }

        function openEditModal(id, name, location, description) {
            document.getElementById('editProjectId').value = id;
            document.getElementById('editProjectName').value = name;
            document.getElementById('editProjectLocation').value = location;
            document.getElementById('editProjectDescription').value = description;

            // Update form action dynamically
            document.getElementById('editProjectForm').action = `/projects/${id}`;
            document.getElementById('editProjectModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editProjectModal').classList.add('hidden');
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
