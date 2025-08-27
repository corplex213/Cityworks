<x-app-layout>
    <x-slot name="header">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Project Details') }}
        </h2>
        <div class="py-10" style="margin-bottom: -25px;">
            <a href="{{ route('projects') }}" class="hover:underline text-white px-2 py-2 rounded-lg flex items-center gap-2 ml-[-13px]" style="margin-right: 1155px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to Projects
            </a>
        </div>
        <div class="max-w-8xl mx-[-35px] sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg">
                <div class="p-7 text-gray-900 dark:text-gray-100">
                    <h3 class="text-xl font-semibold mb-4">{{ $project->proj_name }}</h3>
                    <p class="mb-4"><strong>Type of Project:</strong> {{ $project->proj_type}}</p>
                    <p class="mb-4"><strong>Status:</strong> {{ $project->status }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <section class="project-overview">
        <div class="task-table-wrapper border-b dark:border-gray-700 pb-4 mb-0 mx-[25px]">
            <div class="button-table-wrapper flex justify-start space-x-4 relative" style="bottom: -17px;">
                <!-- Buttons -->
                <button id="mainTableBtn" class="tab-button active px-4 py-2 text-lg font-medium text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400" onclick="switchView('mainTable')">
                    Main Table
                </button>
                <button id="kanbanBtn" class="tab-button px-4 py-2 text-lg font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 border-b-2 border-transparent" onclick="switchView('kanban')">
                    Kanban
                </button>
            </div>
        </div>
    </section>
        
    <div id="projectOverviewSection" class="bg-gray-50 dark:bg-gray-900 p-6 rounded-lg shadow-lg mx-6 lg:mx-12" style="margin-left: 0px;margin-right: 0px;">
        <div class="flex justify-between items-center mb-4">
            <!-- Search Input with Icons -->
            <div class="relative">
                <input
                    id="taskSearchInput"
                    type="text"
                    placeholder="Search tasks..."
                    class="w-[650px] border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 pl-10 pr-10 rounded-lg shadow focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    oninput="filterTasks()"
                />
                <!-- Search Icon -->
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none"
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
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 cursor-pointer hover:text-gray-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    onclick="clearSearchBar()"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </div>
        
            <div class="flex space-x-2">
                <!-- Sort Button -->
                <div id="sortBtnContainer">
                    <button 
                        id="sortBtn" 
                        class="flex items-center text-gray-300 hover:bg-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200"
                    >
                        <!-- New Sort Icon: Arrows up/down -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 10a1 1 0 012 0v4h2l-3 3-3-3h2v-4zm14-1a1 1 0 00-2 0v4h-2l3 3 3-3h-2v-4z" clip-rule="evenodd" />
                        </svg>
                        Sort
                    </button>
                </div>
                <button 
                    id="activityLogBtn" 
                    class="flex items-center text-gray-300 hover:bg-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200"
                    onclick="openActivityLogDrawer()"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 2a1 1 0 00-1 1v1H5a2 2 0 00-2 2v1h14V6a2 2 0 00-2-2h-3V3a1 1 0 00-1-1H9zM3 8v8a2 2 0 002 2h10a2 2 0 002-2V8H3zm5 3a1 1 0 100 2h4a1 1 0 100-2H8z" />
                    </svg>
                    Activity Log
                </button>
            </div>
        </div>
        
        
        <!-- Main Table Section -->
        <section id="mainTableSection" class="group-section mt-8">
            @csrf
            <div class="relative bg-white dark:bg-gray-800 p-4 shadow">
                <div id="placeholder" class="flex flex-col items-center justify-center h-64 bg-gray-100 dark:bg-gray-800 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-semibold text-gray-500 dark:text-gray-400">Begin by Assigning an Engineer</h3>
                    <p class="text-base text-gray-400 dark:text-gray-500">Click the "Assign Task to Engineer" button to create a group.</p>
                </div>                
                <div id="dynamicTablesContainer" class="mt-6">
                    <!-- Dynamic tables will be inserted here -->
                </div>
                <div id="noTasksPlaceholder" class="hidden flex flex-col items-center justify-center h-32 bg-gray-100 dark:bg-gray-800 rounded-lg shadow-lg mt-6">
                    <h3 class="text-xl font-semibold text-gray-500 dark:text-gray-400">No tasks found</h3>
                    <p class="text-base text-gray-400 dark:text-gray-500">Try adjusting your search or add a new task.</p>
                </div>
            </div>
            <!-- Add Group Button -->
            <div class="add-group-container mt-4 flex space-x-4">
                <button id="addGroupBtn" type="button" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                    Assign Task to Engineer
                </button>
            </div>
        </section>
        
            <!-- Task Details Drawer -->
            <div id="taskDetailsDrawer" class="fixed inset-y-0 right-0 w-full sm:w-1/2 bg-white dark:bg-gray-800 shadow-lg transform translate-x-full transition-transform duration-200 ease-out z-50" data-current-user-id="{{ auth()->id() }}">
                <div class="p-6 h-full flex flex-col">
                    <!-- Drawer Header -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 id="taskTitle" class="text-xl font-semibold text-gray-800 dark:text-gray-200"></h3>
                        <div class="flex items-center space-x-2">
                            <!-- Delete Task Button -->
                            <button id="deleteTaskButton" onclick="deleteTaskFromDrawer()" class="text-red-500 hover:text-red-700 dark:hover:text-red-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <!-- Close Drawer Button -->
                            <button onclick="closeTaskDetailsDrawer()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
            
                    <!-- Drawer Content -->
                    <div id="taskDetailsContent" class="flex-1 overflow-y-auto space-y-6">
                        <!-- Task Details -->
                        <div>
                            <p class="text-gray-700 dark:text-gray-300"><strong>Start Date:</strong> <span id="startDate" class="text-gray-700 dark:text-gray-300"></span></p>
                            <p class="text-gray-700 dark:text-gray-300"><strong>Due Date:</strong> <span id="dueDate" class="text-gray-700 dark:text-gray-300"></span></p>
                            <p class="text-gray-700 dark:text-gray-300"><strong>Priority:</strong> <span id="priority" class="text-gray-700 dark:text-gray-300"></span></p>
                            <p class="text-gray-700 dark:text-gray-300"><strong>Status:</strong> <span id="status" class="text-gray-700 dark:text-gray-300"></span></p>
                            <p id="completionTimestamp" class="text-gray-700 dark:text-gray-300 hidden"><strong>Completed on:</strong> <span class="text-gray-700 dark:text-gray-300"></span></p>
                        </div>
            
                        <!-- Tabs Navigation -->
                        <div class="border-b border-gray-200 dark:border-gray-700">
                            <nav class="flex space-x-8" aria-label="Tabs">
                                <button id="commentsTab" class="tab-button active px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400" onclick="switchTab('comments')">
                                    Comments (<span id="commentCount">0</span>)
                                </button>
                                <button id="filesTab" class="tab-button px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 border-b-2 border-transparent hover:border-gray-300 dark:hover:border-gray-600" onclick="switchTab('files')">
                                    Files (<span id="fileCount">0</span>)
                                </button>
                            </nav>
                        </div>
            
                        <!-- Tab Content -->
                        <div class="tab-content">
                            <!-- Comments Tab -->
                            <div id="commentsTabContent" class="tab-pane active">
                                <div class="space-y-4">
                                    <!-- Comment Input -->
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white">
                                                    {{ auth()->user()->name[0] }}
                                                </div>
                                            </div>
                                            <div class="flex-grow">
                                                <div class="relative">
                                                    <textarea 
                                                        id="newComment" 
                                                        class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" 
                                                        rows="3"
                                                        placeholder="Write a comment... Use @ to mention team members"
                                                        onkeydown="handleCommentKeydown(event)"></textarea>
                                                    <div id="mentionSuggestions" class="hidden absolute z-10 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto">
                                                        <!-- Mention suggestions will be populated here -->
                                                    </div>
                                                </div>
                                                <div class="mt-2 flex justify-end">
                                                    <button 
                                                        onclick="addComment()" 
                                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                                                        Comment
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Comments List -->
                                    <div id="commentsContainer" class="space-y-4">
                                        <!-- Comments will be dynamically loaded here -->
                                    </div>
                                </div>
                            </div>
            
                            <!-- Files Tab -->
                            <div id="filesTabContent" class="tab-pane hidden">
                                <div class="space-y-4">
                                    <!-- File Upload Area -->
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                                        <div class="flex items-center justify-center w-full">
                                            <label for="fileUpload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                    <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                                    </svg>
                                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (MAX. 10MB)</p>
                                                </div>
                                                <input id="fileUpload" type="file" class="hidden" multiple onchange="handleFileSelect(event)" />
                                            </label>
                                        </div>
                                    </div>
            
                                    <!-- Files List -->
                                    <div id="fileList" class="space-y-2">
                                        <!-- Files will be dynamically loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Log Drawer -->
            <div id="activityLogDrawer" class="fixed inset-y-0 right-0 w-full sm:w-1/2 bg-white dark:bg-gray-800 shadow-lg transform translate-x-full transition-transform duration-200 ease-out z-50">
                <div class="flex flex-col h-full">
                    <!-- Header -->
                    <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Activity Log</h2>
                        <button onclick="closeActivityLogDrawer()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
            
                    <!-- Activity Content -->
                    <div id="activityLogContent" class="flex-1 overflow-y-auto p-6">
                        <!-- Activities will be dynamically inserted here -->
                    </div>
                </div>
            </div>
        <!-- Kanban Section -->
        <div id="kanbanSection" class="hidden">
            @foreach($users as $user)
                <div class="user-kanban-wrapper mb-12" data-user-id="{{ $user->id }}">
                    <div class="flex items-center mb-4">
                        <button
                            type="button"
                            class="toggle-user-kanban mr-2 bg-gray-700 hover:bg-gray-600 text-white rounded-full w-8 h-8 flex items-center justify-center focus:outline-none"
                            aria-label="Toggle Kanban"
                            onclick="toggleUserKanban({{ $user->id }})"
                            id="toggle-btn-{{ $user->id }}"
                        >
                            <svg id="toggle-icon-{{ $user->id }}" class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $user->name }}</h2>
                    </div>
                    <div id="user-kanban-{{ $user->id }}">
                        @include('kanban-board', ['userId' => $user->id])
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- User Selection Modal -->
    <div id="userSelectionModal" 
     class="fixed inset-0 hidden opacity-0 scale-95 transition-all duration-300 ease-out z-50 flex justify-center items-center bg-gray-900 bg-opacity-50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md transform scale-95 transition-transform duration-500 ease-in-out">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Select a User</h3>
            <ul id="userList" class="space-y-2">
                @foreach($allUsers as $user)
                <li>
                    <button data-user-id="{{ $user->id }}" class="w-full text-left bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-lg">
                        {{ $user->name }}
                    </button>
                </li>
                @endforeach
            </ul>
            <div class="mt-4 text-right">
                <button onclick="closeUserModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Close</button>
            </div>
        </div>
    </div>
    <!-- Sorting Modal -->
    <div id="sortingModal" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Sort Tasks</h3>
            <form id="sortingForm">
                <!-- Column Selection -->
                <label for="sortColumn" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Choose Column</label>
                <select id="sortColumn" class="w-full mt-2 mb-4 p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-200">
                    <option value="0">Task</option>
                    <option value="1">Start Date</option>
                    <option value="2">Due Date</option>
                    <option value="3">Priority</option>
                    <option value="4">Status</option>
                    <option value="5">Budget</option>
                </select>

                <!-- Sorting Order -->
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order</label>
                <div class="flex items-center space-x-4 mt-2 mb-4">
                    <label class="flex items-center">
                        <input type="radio" name="sortOrder" value="asc" class="mr-2" checked>
                        Ascending
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="sortOrder" value="desc" class="mr-2">
                        Descending
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="resetSorting()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">Reset</button>
                    <button type="button" onclick="closeSortingModal()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-8 border-t border-gray-300 dark:border-gray-700 pt-4">
        <p class="text-center text-gray-500 dark:text-gray-400">&copy; 2024 City Engineering Office. All rights reserved.</p>
    </footer>


    <style>
        .tab-button {
            transition: color 0.3s ease, border-color 0.3s ease;
        }
    
        .tab-button:hover {
            color: #2563eb; /* Tailwind's blue-600 */
        }
    
        .tab-button.active {
            color: #2563eb; /* Tailwind's blue-600 */
            border-color: #2563eb; /* Tailwind's blue-600 */
        }
    </style>

    <style>
        /* Hide the toggle, subtask, and delete-subtask buttons by default */
        .task-column .subtask-toggle-btn,
        .task-column .subtask-btn,
        .task-column .delete-subtask-btn {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease-in-out;
        }

        /* Show the buttons when hovering over the task column */
        .task-column:hover .subtask-toggle-btn,
        .task-column:hover .subtask-btn,
        .task-column:hover .delete-subtask-btn {
            opacity: 1;
            pointer-events: auto;
        }
    </style>
<script>
    window.PROJECT_ID = {{ $project->id }};
    window.projectType = @json($project->proj_type);
</script>
<script>
function toggleUserKanban(userId) {
    const kanbanDiv = document.getElementById('user-kanban-' + userId);
    const icon = document.getElementById('toggle-icon-' + userId);
    if (!kanbanDiv) return;
    kanbanDiv.classList.toggle('hidden');
    // Rotate the arrow icon
    if (kanbanDiv.classList.contains('hidden')) {
        icon.classList.add('rotate-180');
    } else {
        icon.classList.remove('rotate-180');
    }
}
</script>
<script>
    function loadActivityLog() {
        const projectId = {{ $project->id }};
        const container = document.getElementById('activityLogContent');
        
        // Show loading state
        container.innerHTML = `
            <div class="flex justify-center items-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            </div>
        `;
        
        fetch(`/projects/${projectId}/activities`)
            .then(response => response.json())
            .then(data => {
                // Ensure data is an array before sorting
                if (!Array.isArray(data)) {
                    console.error('Expected array of activities but got:', data);
                    throw new Error('Invalid activity data format');
                }
                
                // Sort activities by created_at
                const activities = data.sort((a, b) => 
                    new Date(b.created_at) - new Date(a.created_at)
                );

                if (activities.length === 0) {
                    container.innerHTML = `
                        <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                            No activities found
                        </div>
                    `;
                    return;
                }

                container.innerHTML = activities.map(activity => 
                    createActivityElement(activity)
                ).join('');
            })
            .catch(error => {
                console.error('Error loading activities:', error);
                container.innerHTML = `
                    <div class="text-center text-red-500 py-4">
                        Error loading activities. Please try again.
                    </div>
                `;
            });
    }

    function openActivityLogDrawer() {
        const drawer = document.getElementById('activityLogDrawer');
        if (!drawer) return;
        
        drawer.classList.remove('translate-x-full');
        loadActivityLog();
    }

    function closeActivityLogDrawer() {
        const drawer = document.getElementById('activityLogDrawer');
        if (!drawer) return;
        
        drawer.classList.add('translate-x-full');
    }


    function createActivityElement(activity) {
        return `
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4 mb-4">
                <div class="flex items-start">
                    <div class="flex-grow">
                        <div class="text-gray-700 dark:text-gray-200">
                            ${activity.getFormattedMessage}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            ${new Date(activity.created_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            })}
                        </div>
                    </div>
                    ${getActivityTypeIcon(activity.type)}
                </div>
            </div>
        `;
    }

    function getActivityTypeIcon(type) {
        const icons = {
            created: '<svg class="h-5 w-5 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>',
            updated: '<svg class="h-5 w-5 text-blue-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>',
            deleted: '<svg class="h-5 w-5 text-red-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>',
            'added_engineer': '<svg class="h-5 w-5 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>',
            'removed_engineer': '<svg class="h-5 w-5 text-red-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/></svg>'
        };
        return icons[type] || '';
    }

    function filterActivities() {
        const searchTerm = document.getElementById('activitySearchInput').value.toLowerCase();
        const filter = document.getElementById('activityFilter').value;
        const activities = document.querySelectorAll('#activityLogContent > div');

        activities.forEach(activity => {
            const text = activity.textContent.toLowerCase();
            const type = activity.getAttribute('data-type');
            const matchesSearch = text.includes(searchTerm);
            const matchesFilter = filter === 'all' || type === filter;

            if (matchesSearch && matchesFilter) {
                activity.classList.remove('hidden');
            } else {
                activity.classList.add('hidden');
            }
        });
    }

    function closeActivityLogDrawer() {
        const drawer = document.getElementById('activityLogDrawer');
        drawer.classList.add('translate-x-full');
    }
</script>

<script>
    // Open the sorting modal
    document.getElementById('sortBtn').addEventListener('click', () => {
        document.getElementById('sortingModal').classList.remove('hidden');
    });

    // Close the sorting modal
    function closeSortingModal() {
        document.getElementById('sortingModal').classList.add('hidden');
    }
    function resetSorting() {
            const tables = document.querySelectorAll('#dynamicTablesContainer table');
            if (!tables.length) return;

            // Clear any saved sorting preferences and reset to default state
            fetch('/reset-sorting-view', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(() => {
                // After resetting, reload tasks in their original order
                loadExistingTasks().then(() => {
                    // Clear any stored sorting preferences
                    sessionStorage.removeItem('sortPreferences');
                    sortTable(null, 'asc');
                    updateSortButtonText(null);
                    closeSortingModal();
                });
            })
            .catch(error => {
                console.error('Error resetting sort view:', error);
                loadExistingTasks().then(() => {
                    sortTable(null, 'asc');
                    updateSortButtonText(null);
                    closeSortingModal();
                });
            });
        }
        document.getElementById('sortingForm').addEventListener('submit', (event) => {
            event.preventDefault();

            const columnIndex = parseInt(document.getElementById('sortColumn').value);
            const sortOrder = document.querySelector('input[name="sortOrder"]:checked').value;

            sortTable(columnIndex, sortOrder);
            updateSortButtonText(columnIndex);
            closeSortingModal();
        });

    // Sort the table
    function sortTable(columnIndex = null, sortOrder = 'asc') {
        const tables = document.querySelectorAll('#dynamicTablesContainer table');
        if (!tables.length) return;
        // Define custom sort orders
        const priorityOrder = {
            'High': 1,
            'Normal': 2,
            'Low': 3
        };

        const statusOrder = {
            'For Revision': 1,
            'For Checking': 2,
            'Completed': 3,
            'Deferred': 4
        };

        tables.forEach((table) => {
            const tbody = table.querySelector('tbody');
            if (!tbody) return;

            // Get all rows except the "Add Item" row and Total Budget row
            let rows = Array.from(tbody.querySelectorAll('tr'))
                .filter(row => 
                    !row.classList.contains('add-item-row') &&
                    !row.classList.contains('total-budget-row')
                );

        // Find the "Add Item" row and Total Budget row
        const addItemRow = tbody.querySelector('tr.add-item-row') || tbody.querySelector('tr:last-child');
        const totalBudgetRow = tbody.querySelector('tr.total-budget-row') || tbody.querySelector('tfoot tr');

        // Identify main tasks and their subtasks
        let groupedRows = [];
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            if (!row.classList.contains('subtask-row')) {
                // Main task
                let group = [row];
                // Collect subtasks immediately following
                let j = i + 1;
                while (j < rows.length && rows[j].classList.contains('subtask-row')) {
                    group.push(rows[j]);
                    j++;
                }
                groupedRows.push(group);
                i = j - 1;
            }
        }

        // Default sorting by creation order
        if (columnIndex === null) {
            groupedRows.sort((a, b) => {
                const aIndex = parseInt(a[0].getAttribute('data-task-id')) || 0;
                const bIndex = parseInt(b[0].getAttribute('data-task-id')) || 0;
                return sortOrder === 'asc' ? aIndex - bIndex : bIndex - aIndex;
            });
        } else {
            groupedRows.sort((a, b) => {
                let cellA, cellB, comparison = 0;
                const mainA = a[0], mainB = b[0];

                switch (columnIndex) {
                    case 3: // Priority column
                        cellA = mainA.querySelector('.priority-value')?.textContent.trim() || '';
                        cellB = mainB.querySelector('.priority-value')?.textContent.trim() || '';
                        comparison = (priorityOrder[cellA] || 0) - (priorityOrder[cellB] || 0);
                        break;
                    case 4: // Status column
                        cellA = mainA.querySelector('.status-value')?.textContent.trim() || '';
                        cellB = mainB.querySelector('.status-value')?.textContent.trim() || '';
                        comparison = (statusOrder[cellA] || 0) - (statusOrder[cellB] || 0);
                        break;
                    case 0: // Task column
                        cellA = mainA.querySelector('td:first-child input')?.value.trim() || '';
                        cellB = mainB.querySelector('td:first-child input')?.value.trim() || '';
                        comparison = cellA.localeCompare(cellB, undefined, { numeric: true });
                        break;
                    case 1: // Start Date column
                    case 2: // Due Date column
                        cellA = new Date(mainA.children[columnIndex]?.querySelector('input')?.value || '');
                        cellB = new Date(mainB.children[columnIndex]?.querySelector('input')?.value || '');
                        comparison = cellA - cellB;
                        break;
                    case 5: // Budget column
                        cellA = parseFloat(mainA.children[columnIndex]?.querySelector('input')?.value.replace(/[^0-9.]/g, '') || 0);
                        cellB = parseFloat(mainB.children[columnIndex]?.querySelector('input')?.value.replace(/[^0-9.]/g, '') || 0);
                        comparison = cellA - cellB;
                        break;
                    default:
                        cellA = mainA.children[columnIndex]?.textContent.trim() || '';
                        cellB = mainB.children[columnIndex]?.textContent.trim() || '';
                        comparison = cellA.localeCompare(cellB, undefined, { numeric: true });
                        break;
                }
                return sortOrder === 'asc' ? comparison : -comparison;
            });
        }

        // Clear the tbody and append sorted groups
        groupedRows.forEach(group => group.forEach(row => tbody.appendChild(row)));

        // Re-append the "Add Item" row just before the Total Budget row
        if (addItemRow && totalBudgetRow) {
            tbody.insertBefore(addItemRow, totalBudgetRow);
        } else if (addItemRow) {
            tbody.appendChild(addItemRow);
        }

        // Ensure the Total Budget row is always last
        if (totalBudgetRow) {
            tbody.appendChild(totalBudgetRow);
        }
    });

    updateSortButtonText(columnIndex);
    saveSortingView(columnIndex, sortOrder);
}

    function updateSortButtonText(columnIndex) {
        const sortBtn = document.getElementById('sortBtn');
        const columnNames = ['Task', 'Start Date', 'Due Date', 'Priority', 'Status', 'Budget'];
        const selectedColumn = columnIndex !== null ? columnNames[columnIndex] : 'Creation Order';

        sortBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 10a1 1 0 012 0v4h2l-3 3-3-3h2v-4zm14-1a1 1 0 00-2 0v4h-2l3 3 3-3h-2v-4z" clip-rule="evenodd" />
            </svg>
            Sort: ${selectedColumn}
        `;
    }
    function saveSortingView(columnIndex, sortOrder) {
        const columnNames = ['Task', 'Start Date', 'Due Date', 'Priority', 'Status', 'Budget'];
        const selectedColumn = columnNames[columnIndex];

        fetch('/save-sorting-view', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                column: selectedColumn,
                order: sortOrder
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Sorting preferences saved:', data);
        })
        .catch(error => {
            console.error('Error saving sorting view:', error);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        fetch('/get-sorting-view', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const columnNames = ['Task', 'Start Date', 'Due Date', 'Priority', 'Status', 'Budget'];
            const columnIndex = columnNames.indexOf(data.column);
            const sortOrder = data.order;

            loadExistingTasks().then(() => {
                if (data.column && columnIndex !== -1) {
                    sortTable(columnIndex, sortOrder);
                }
            });
        })
        .catch(error => {
            console.error('Error fetching sorting view:', error);
            loadExistingTasks();
        });
    });
</script>
<script>
    function closeActivityLogDrawer() {
        const drawer = document.getElementById('activityLogDrawer');
        drawer.classList.add('translate-x-full'); // Hide the drawer
    }

    function switchView(view) {
        const mainTableBtn = document.getElementById('mainTableBtn');
        const kanbanBtn = document.getElementById('kanbanBtn');
        const mainTableSection = document.getElementById('mainTableSection');
        const kanbanSection = document.getElementById('kanbanSection');
        const sortBtnContainer = document.getElementById('sortBtnContainer');

        // Reset all buttons and sections
        [mainTableBtn, kanbanBtn].forEach(btn => {
            if (btn) {
                btn.classList.remove('active', 'text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400');
                btn.classList.add('text-gray-500', 'dark:text-gray-400', 'border-transparent');
            }
        });

        // Hide all sections
        [mainTableSection, kanbanSection].forEach(section => {
            if (section) {
                section.classList.add('hidden'); // Ensure all sections are hidden
            }
        });

        // Show selected section and activate button
        switch (view) {
            case 'mainTable':
                if (mainTableSection && mainTableBtn) {
                    mainTableSection.classList.remove('hidden'); // Show the Main Table section
                    mainTableBtn.classList.remove('text-gray-500', 'dark:text-gray-400', 'border-transparent');
                    mainTableBtn.classList.add('active', 'text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400');
                }
                if (sortBtnContainer) sortBtnContainer.style.display = '';
                break;
            case 'kanban':
                if (kanbanSection && kanbanBtn) {
                    kanbanSection.classList.remove('hidden'); // Show the Kanban section
                    kanbanBtn.classList.remove('text-gray-500', 'dark:text-gray-400', 'border-transparent');
                    kanbanBtn.classList.add('active', 'text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400');
                }
                if (sortBtnContainer) sortBtnContainer.style.display = 'none';
                break;
        }
    }


</script>

    <script>
    // Common CSS classes
    const commonClasses = {
        button: 'font-semibold py-2 px-4 rounded-lg shadow transition',
        primaryButton: 'bg-blue-500 hover:bg-blue-600 text-white',
        successButton: 'bg-green-500 hover:bg-green-600 text-white',
        dangerButton: 'bg-red-500 hover:bg-red-600 text-white',
        tableCell: 'px-6 py-4 border-r border-gray-300 dark:border-gray-600',
        input: 'w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2'
    };

    // Color mapping for status and priority
    const colorMapping = {
        // Priority colors
        High: 'bg-red-500',
        Normal: 'bg-yellow-500',
        Low: 'bg-green-500',
        // Status colors
        Completed: 'bg-green-500 text-white',
            'For Checking': 'bg-blue-500 text-white',
            'For Revision': 'bg-yellow-500 text-white',
            'Deferred': 'bg-red-500 text-white'
    };

    function clearSearchBar() {
        const searchInput = document.getElementById('taskSearchInput');
        searchInput.value = ''; // Clear the input field
        filterTasks(); // Reset the table to show all rows
    }
    function filterTasks() {
        const searchInput = document.getElementById('taskSearchInput').value.toLowerCase();
        const tables = document.querySelectorAll('#dynamicTablesContainer > div');
        let anyVisible = false;

            tables.forEach((tableWrapper) => {
            const tableHeader = tableWrapper.querySelector('h3');
            const tableRows = Array.from(tableWrapper.querySelectorAll('table tbody tr'));
            let tableVisible = false;

            if (!searchInput.trim()) {
                tableWrapper.style.display = '';
                tableRows.forEach((row) => row.style.display = '');
                tableVisible = true;
            } else {
                if (tableHeader && tableHeader.textContent.toLowerCase().includes(searchInput)) {
                    tableWrapper.style.display = '';
                    tableVisible = true;
                } else {
                    tableRows.forEach((row) => {
                        const taskCell = row.querySelector('td:first-child input');
                        if (taskCell && taskCell.value.toLowerCase().includes(searchInput)) {
                            row.style.display = '';
                            tableVisible = true;
                        } else {
                            row.style.display = 'none';
                        }
                    });
                    tableWrapper.style.display = tableVisible ? '' : 'none';
                }
            }
            if (tableVisible) anyVisible = true;
        });
        // Show/hide the "no tasks" placeholder
        const placeholder = document.getElementById('noTasksPlaceholder');
        if (placeholder) {
            placeholder.classList.toggle('hidden', anyVisible);
        }
        }

        // Open the user selection modal
        document.getElementById('addGroupBtn').addEventListener('click', function () {
            openUserModal();
        });

        // Close the user selection modal
        function closeUserModal() {
            const modal = document.getElementById('userSelectionModal');
            // Animate out
            modal.classList.add('opacity-0', 'scale-95');
            // Hide after animation completes
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function updateTotalBudget(input) {
            let value = input.value;
            value = value.replace(/[^0-9.]/g, '');
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }
            const rawValue = value;
            
            if (!input.matches(':focus')) {
                const number = parseFloat(value) || 0;
                input.value = number.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            } else {
                input.value = value;
            }
            updateTotalBudgetForTable(input.closest('table'));
        }

        function updateTotalBudgetForTable(table) {
            const allInputs = table.querySelectorAll('tbody tr:not(.add-item-row):not(.total-budget-row) td:nth-child(6) input');
            let total = 0;
            allInputs.forEach((input) => {
                const value = parseFloat(input.value.replace(/[^0-9.-]/g, '')) || 0;
                total += value;
            });
            const totalBudgetElement = table.querySelector('.total-budget');
            if (totalBudgetElement) {
                totalBudgetElement.textContent = `₱${total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            }
        }

        function saveTask(tableWrapper) {
            const table = tableWrapper.querySelector('table');
            const rows = table.querySelectorAll('tbody tr:not(.subtask-row)');
            const projectId = {{ $project->id }};
            const assignedTo = tableWrapper.getAttribute('data-user-id');

            let savePromises = [];

            rows.forEach(row => {
                const taskId = row.getAttribute('data-task-id');
                const taskNameInput = row.querySelector('td:first-child input');
                const startDateInput = row.querySelector('td:nth-child(2) input');
                const dueDateInput = row.querySelector('td:nth-child(3) input');
                const priorityElement = row.querySelector('.priority-value');
                const statusElement = row.querySelector('.status-value');
                const budgetInput = row.querySelector('td:nth-child(6) input');

                let sourceOfFunding = null;
                let otherFundingSource = null;
                if (projectType === 'POW') {
                    const sourceSelect = row.querySelector('td:nth-child(7) select[name="source_of_funding"]');
                    sourceOfFunding = sourceSelect ? sourceSelect.value : null;
                    const otherInput = row.querySelector('td:nth-child(7) input[name="other_funding_source"]');
                    otherFundingSource = otherInput ? otherInput.value : null;
                }


                if (!taskNameInput || !startDateInput || !dueDateInput || !priorityElement || !statusElement || !budgetInput) {
                    console.error('Missing required elements in row:', row);
                    return;
                }

                const budgetValue = parseFloat(budgetInput.value.replace(/[^0-9.]/g, '')) || 0;

                const subtasks = [];
                let nextRow = row.nextElementSibling;
                while (nextRow && nextRow.classList.contains('subtask-row')) {
                    let subtaskSourceOfFunding = null;
                    let subtaskOtherFundingSource = null;
                    if (projectType === 'POW') {
                        const subtaskSourceSelect = nextRow.querySelector('td:nth-child(7) select[name="source_of_funding"]');
                        subtaskSourceOfFunding = subtaskSourceSelect ? subtaskSourceSelect.value : null;
                        const subtaskOtherInput = nextRow.querySelector('td:nth-child(7) input[name="other_funding_source"]');
                        subtaskOtherFundingSource = subtaskOtherInput ? subtaskOtherInput.value : null;
                    }
                    const subtaskId = nextRow.getAttribute('data-task-id');
                    const subtaskNameInput = nextRow.querySelector('td:first-child input');
                    const subtaskStartDateInput = nextRow.querySelector('td:nth-child(2) input');
                    const subtaskDueDateInput = nextRow.querySelector('td:nth-child(3) input');
                    const subtaskPriorityElement = nextRow.querySelector('.priority-value');
                    const subtaskStatusElement = nextRow.querySelector('.status-value');
                    const subtaskBudgetInput = nextRow.querySelector('td:nth-child(6) input');

                    if (!subtaskNameInput || !subtaskStartDateInput || !subtaskDueDateInput || !subtaskPriorityElement || !subtaskStatusElement || !subtaskBudgetInput) {
                        console.error('Missing required elements in subtask row:', nextRow);
                        nextRow = nextRow.nextElementSibling;
                        continue;
                    }

                    const subtaskBudgetValue = parseFloat(subtaskBudgetInput.value.replace(/[^0-9.]/g, '')) || 0;

                    // Get the old values from the current state
                    const oldValues = {
                        task_name: subtaskNameInput.getAttribute('data-old-value') || subtaskNameInput.value,
                        start_date: subtaskStartDateInput.getAttribute('data-old-value') || subtaskStartDateInput.value,
                        due_date: subtaskDueDateInput.getAttribute('data-old-value') || subtaskDueDateInput.value,
                        priority: subtaskPriorityElement.getAttribute('data-old-value') || subtaskPriorityElement.textContent,
                        status: subtaskStatusElement.getAttribute('data-old-value') || subtaskStatusElement.textContent,
                        budget: subtaskBudgetInput.getAttribute('data-old-value') || subtaskBudgetValue
                    };

                    // Store current values as old values for next update
                    subtaskNameInput.setAttribute('data-old-value', subtaskNameInput.value);
                    subtaskStartDateInput.setAttribute('data-old-value', subtaskStartDateInput.value);
                    subtaskDueDateInput.setAttribute('data-old-value', subtaskDueDateInput.value);
                    subtaskPriorityElement.setAttribute('data-old-value', subtaskPriorityElement.textContent);
                    subtaskStatusElement.setAttribute('data-old-value', subtaskStatusElement.textContent);
                    subtaskBudgetInput.setAttribute('data-old-value', subtaskBudgetValue);

                    subtasks.push({
                        id: subtaskId,
                        task_name: subtaskNameInput.value,
                        start_date: subtaskStartDateInput.value,
                        due_date: subtaskDueDateInput.value,
                        priority: subtaskPriorityElement.textContent,
                        status: subtaskStatusElement.textContent,
                        budget: subtaskBudgetValue,
                        project_id: projectId,
                        assigned_to: assignedTo,
                        source_of_funding: subtaskSourceOfFunding,
                        other_funding_source: subtaskOtherFundingSource,
                        changes: {
                            task_name: { old: oldValues.task_name, new: subtaskNameInput.value },
                            start_date: { old: oldValues.start_date, new: subtaskStartDateInput.value },
                            due_date: { old: oldValues.due_date, new: subtaskDueDateInput.value },
                            priority: { old: oldValues.priority, new: subtaskPriorityElement.textContent },
                            status: { old: oldValues.status, new: subtaskStatusElement.textContent },
                            budget: { old: oldValues.budget, new: subtaskBudgetValue }
                        }
                    });

                    nextRow = nextRow.nextElementSibling;
                }

                const taskData = {
                    project_id: projectId,
                    task_name: taskNameInput.value,
                    start_date: startDateInput.value,
                    due_date: dueDateInput.value,
                    priority: priorityElement.textContent,
                    status: statusElement.textContent,
                    budget: budgetValue,
                    assigned_to: assignedTo,
                    subtasks: subtasks
                };
                if (projectType === 'POW') {
                    taskData.source_of_funding = sourceOfFunding;
                    taskData.other_funding_source = otherFundingSource;
                }

                const url = taskId ? `/tasks/${taskId}` : '/tasks';
                const method = taskId ? 'PUT' : 'POST';

                // Add the fetch promise to the array
                savePromises.push(
                    fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(taskData)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Success:', data);
                        if (!taskId && data.task) {
                            row.setAttribute('data-task-id', data.task.id);

                            // Show the delete button after first save
                            const deleteBtn = tableWrapper.querySelector(`#deleteTableBtn-${assignedTo}`);
                            if (deleteBtn) deleteBtn.style.display = '';

                            if (data.subtasks && data.subtasks.length > 0) {
                                let subtaskRow = row.nextElementSibling;
                                data.subtasks.forEach((subtask, index) => {
                                    if (subtaskRow && subtaskRow.classList.contains('subtask-row')) {
                                        subtaskRow.setAttribute('data-task-id', subtask.id);
                                        subtaskRow = subtaskRow.nextElementSibling;
                                    }
                                });
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        let errorMessage = 'An error occurred while saving the task.';
                        if (error.errors) {
                            errorMessage = Object.values(error.errors).flat().join('\n');
                        } else if (error.message) {
                            errorMessage = error.message;
                        }
                        alert(errorMessage);
                    })
                );
            });

            // Wait for all save operations to complete
            Promise.all(savePromises)
                .then(() => {
                    alert('All tasks in this table have been saved successfully!');
                })
                .catch(error => {
                    console.error('Error saving tasks:', error);
                });
        }

        function deleteTask(row) {
            if (!confirm('Are you sure you want to delete this task?')) {
                return;
            }

            const taskId = row.getAttribute('data-task-id');
            if (!taskId) {
                row.remove();
                return;
            }

            fetch(`/tasks/${taskId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                row.remove();

                // Remove from Kanban
                const kanbanCard = document.querySelector(`[data-task-id="${taskId}"]`);
                if (kanbanCard) {
                    kanbanCard.remove();
                } else if (data.parent_task_id) {
                    // If it's a subtask, update the parent Kanban card's subtasks list
                    const parentKanbanCard = document.querySelector(`.task-card[data-task-id="${data.parent_task_id}"]`);
                    if (parentKanbanCard && typeof window.loadExistingTasks === 'function') {
                        window.loadExistingTasks();
                    }
                }

                if (typeof updateTaskCounts === 'function') updateTaskCounts();
                alert('Task deleted successfully!');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the task.');
            });
        }

        function deleteTaskFromDrawer() {
            const drawer = document.getElementById('taskDetailsDrawer');
            const taskId = drawer.getAttribute('data-current-row');
            if (!confirm('Are you sure you want to delete this task?')) {
                return;
            }

            // Find the row in the table (main task or subtask)
            const row = document.querySelector(`tr[data-task-id="${taskId}"]`);

            // If it's a main task, check if it's the last row in the table
            let isMainTask = row && !row.classList.contains('subtask-row');
            if (isMainTask) {
                const tbody = row.closest('tbody');
                if (tbody && Array.from(tbody.children).filter(r => !r.classList.contains('subtask-row')).length <= 1) {
                    alert('Cannot delete the last row. At least one task must remain.');
                    return;
                }
            }

            if (!taskId) {
                if (row) row.remove();
                const kanbanCard = document.querySelector(`[data-task-id="${taskId}"]`);
                if (kanbanCard) kanbanCard.remove();
                closeTaskDetailsDrawer();
                return;
            }

            fetch(`/tasks/${taskId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (row) row.remove();
                const kanbanCard = document.querySelector(`[data-task-id="${taskId}"]`);
                if (kanbanCard) kanbanCard.remove();
                if (typeof updateTaskCounts === 'function') updateTaskCounts();
                closeTaskDetailsDrawer();
                alert('Task deleted successfully!');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the task.');
            });
        }

        const selectedUsers = new Set();
        function addUserTable(userName, userId) {
            createTableForUser(userName, userId, []);
            const tableWrapper = document.querySelector(`[data-user-id="${userId}"]`);
            const tbody = tableWrapper.querySelector('tbody');
            addNewRow(tbody);
        }

        function editPriority(cell) {
            const priorityValue = cell.querySelector('.priority-value'); // Ensure this exists
            if (!priorityValue) {
                console.error('Priority value element not found in cell:', cell);
                return;
            }

            const select = document.createElement('select');
            select.className = 'w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2';
            select.innerHTML = `
                <option value="High" ${priorityValue.textContent === 'High' ? 'selected' : ''}>High</option>
                <option value="Normal" ${priorityValue.textContent === 'Normal' ? 'selected' : ''}>Normal</option>
                <option value="Low" ${priorityValue.textContent === 'Low' ? 'selected' : ''}>Low</option>
            `;

            select.addEventListener('change', function () {
                const newValue = this.value;
                priorityValue.textContent = newValue;
                cell.className = `priority-cell w-full ${getColor('priority', newValue)} text-white rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition font-medium cursor-pointer`;
                cell.setAttribute('data-old-value', newValue);
                cell.innerHTML = `<span class="priority-value">${newValue}</span>`;
                cell.onclick = function () {
                    editPriority(this);
                };
            });

            cell.innerHTML = '';
            cell.appendChild(select);
            select.focus();
        }

        function validateBudget(input) {
            // Allow only numbers and a single decimal point while typing
            let value = input.value.replace(/[^0-9.]/g, '');

            // Ensure only one decimal point
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            // Limit to 2 decimal places
            if (parts.length === 2 && parts[1].length > 2) {
                value = parts[0] + '.' + parts[1].substring(0, 2);
            }

            // Set the cleaned value back
            input.value = value;

            // Update the total budget
            updateTotalBudgetForTable(input.closest('table'));
        }

        // Generic modal handler
        const modalHandler = {
            open: (modalId, callback) => {
                const modal = document.getElementById(modalId);
                modal.classList.remove('hidden');
                setTimeout(() => modal.classList.remove('opacity-0', 'scale-95'), 10);
                if (callback) callback();
            },
            close: (modalId, callback) => {
                const modal = document.getElementById(modalId);
                modal.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    if (callback) callback();
                }, 300);
            }
        };

        function openUserModal() {
            modalHandler.open('userSelectionModal', updateUserButtonStates);
        }

        function closeUserModal() {
            modalHandler.close('userSelectionModal');
        }

        function updateUserButtonStates() {
            document.querySelectorAll('#userList button').forEach(button => {
                const userId = button.getAttribute('data-user-id');
                if (selectedUsers.has(userId)) {
                    button.disabled = true;
                    button.classList.add('opacity-50', 'cursor-not-allowed');
                    button.classList.remove('hover:bg-gray-200', 'dark:hover:bg-gray-600');
                } else {
                    button.disabled = false;
                    button.classList.remove('opacity-50', 'cursor-not-allowed');
                    button.classList.add('hover:bg-gray-200', 'dark:hover:bg-gray-600');
                }
            });
        }

        function enableColumnResizing(table) {
        const headers = table.querySelectorAll('th');
        headers.forEach((header, index) => {
            const resizer = document.createElement('div');
            resizer.style.cssText = `
                width: 5px;
                cursor: col-resize;
                position: absolute;
                right: 0;
                top: 0;
                bottom: 0;
                z-index: 1;
            `;
            header.style.position = 'relative';
            header.appendChild(resizer);

            resizer.addEventListener('mousedown', (e) => {
                const startX = e.pageX;
                const startWidth = header.offsetWidth;

                const onMouseMove = (e) => {
                    const newWidth = startWidth + (e.pageX - startX);
                    header.style.width = `${newWidth}px`;
                    table.querySelectorAll('tr').forEach((row) => {
                        const cell = row.children[index];
                        if (cell) cell.style.width = `${newWidth}px`;
                    });
                };

                const stopResizing = () => {
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', stopResizing);
                };

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', stopResizing);
            });
        });
    }

    function editStatus(cell) {
        const oldValue = cell.getAttribute('data-old-value');
        const statusValue = cell.querySelector('.status-value'); // Ensure this exists
        if (!statusValue) {
            console.error('Status value element not found in cell:', cell);
            return;
        }

        const select = document.createElement('select');
        select.className = 'w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2';
        select.innerHTML = `
            <option value="Completed" ${statusValue.textContent === 'Completed' ? 'selected' : ''}>Completed</option>
            <option value="For Checking" ${statusValue.textContent === 'For Checking' ? 'selected' : ''}>For Checking</option>
            <option value="For Revision" ${statusValue.textContent === 'For Revision' ? 'selected' : ''}>For Revision</option>
            <option value="Deferred" ${statusValue.textContent === 'Deferred' ? 'selected' : ''}>Deferred</option>
        `;

        select.addEventListener('change', function () {
            const newValue = this.value;
            statusValue.textContent = newValue;
            cell.className = `status-cell w-full ${getColor('status', newValue)} text-white rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition cursor-pointer`;
            cell.setAttribute('data-old-value', newValue);
            cell.innerHTML = `<span class="status-value">${newValue}</span>`;
            cell.onclick = function () {
                editStatus(this);
            };
        });

        cell.innerHTML = '';
        cell.appendChild(select);
        select.focus();
    }

    // Update the user list items to pass both name and ID
    document.querySelectorAll('#userList button').forEach(button => {
        const userId = button.getAttribute('data-user-id');
        const userName = button.textContent;
        button.onclick = () => {
            if (!selectedUsers.has(userId)) {
                selectedUsers.add(userId);
                addUserTable(userName, userId);
                updateUserButtonStates();
                closeUserModal();
            }
        };
    });

        // Function to load existing tasks
        window.loadExistingTasks = function() {
        const projectId = {{ $project->id }};
            return fetch(`/projects/${projectId}/tasks`)
                .then(response => response.json())
                .then(tasks => {
                    const container = document.getElementById('dynamicTablesContainer');
                    container.innerHTML = '';
                    selectedUsers.clear();

                    Object.entries(tasks).forEach(([userId, userTasks]) => {
                        const user = userTasks[0].assigned_user;
                        selectedUsers.add(userId);
                        createTableForUser(user.name, userId, userTasks);

                        if (window.loadTasksIntoKanban) {
                            window.loadTasksIntoKanban(userTasks, userId);
                        }
                    });

                    updateUserButtonStates();

                    // Only apply sorting if there are active sorting preferences
                    return fetch('/get-sorting-view')
                        .then(response => response.json())
                        .then(sortingPrefs => {
                            if (sortingPrefs.column && sortingPrefs.order) {
                                const columnNames = ['Task', 'Start Date', 'Due Date', 'Priority', 'Status', 'Budget'];
                                const columnIndex = columnNames.indexOf(sortingPrefs.column);
                                if (columnIndex !== -1) {
                                    sortTable(columnIndex, sortingPrefs.order);
                                }
                            }
                        });
                })
                .catch(error => {
                    console.error('Error loading tasks:', error);
                });
        }

    function calculateTotalBudget(tasks) {
        let total = 0;
        tasks.forEach(task => {
            // Add main task budget (convert to number and handle NaN)
            const mainBudget = parseFloat(task.budget.toString().replace(/[^0-9.-]+/g, '')) || 0;
            total += mainBudget;

            // Add subtask budgets
            if (task.subtasks && task.subtasks.length > 0) {
                task.subtasks.forEach(subtask => {
                    const subtaskBudget = parseFloat(subtask.budget.toString().replace(/[^0-9.-]+/g, '')) || 0;
                    total += subtaskBudget;
                });
            }
        });
        return total;
    }
    function handleSourceOfFundingChange(select) {
        const row = select.closest('tr');
        const otherFundingDiv = row.querySelector('.other-funding-source');
        const otherInput = otherFundingDiv?.querySelector('input[name="other_funding_source"]');
        if (select.value === 'Others') {
            select.style.display = 'none';
            otherFundingDiv.classList.remove('hidden');
            if (otherInput) {
                otherInput.focus();
                otherInput.ondblclick = function () {
                    select.style.display = '';
                    otherFundingDiv.classList.add('hidden');
                    select.value = '';
                };
            }
        } else {
            otherFundingDiv.classList.add('hidden');
            select.style.display = '';
        }
    }
    const projectType = @json($project->proj_type);
    // Function to create table for a user with their tasks
    function createTableForUser(userName, userId, tasks) {
        const container = document.getElementById('dynamicTablesContainer');
        const placeholder = document.getElementById('placeholder');
        placeholder?.remove();

        const tableWrapper = document.createElement('div');
        tableWrapper.className = 'mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden';
        tableWrapper.setAttribute('data-user-id', userId);
        
        // Create the table header
        const header = document.createElement('div');
        header.className = 'flex justify-between items-center text-2xl font-semibold text-center text-gray-800 dark:text-gray-200 px-6 py-4 bg-gray-100 dark:bg-gray-700';

        const headerTitle = document.createElement('h3');
        headerTitle.textContent = userName;

        const buttonContainer = document.createElement('div');
        buttonContainer.className = 'flex items-center space-x-4';

        // Create save button
        const saveButton = document.createElement('button');
        saveButton.className = 'text-green-500 hover:text-green-700 transition-colors duration-150';
        saveButton.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        `;
        saveButton.onclick = () => saveTask(tableWrapper);

        const deleteButton = document.createElement('button');
        deleteButton.id = `deleteTableBtn-${userId}`;
        deleteButton.className = 'text-red-500 hover:text-red-700 transition-colors duration-150';
        deleteButton.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        `;
        deleteButton.onclick = () => {
            if (confirm(`Are you sure you want to delete the task table for ${userName}?`)) {
                const projectId = {{ $project->id }};
                
                // First log the activity
                fetch(`/projects/${projectId}/activities`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        type: 'removed_engineer',
                        description: `Removed task table for ${userName}`,
                        changes: {
                            target_user_name: userName,
                            target_user_id: userId
                        }
                    })
                })
                .then(response => response.json())
                .then(() => {
                    // Then delete the tasks
                    return fetch(`/projects/${projectId}/users/${userId}/tasks`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        tableWrapper.remove();
                        selectedUsers.delete(userId);
                        updateUserButtonStates();
                        loadActivityLog(); // Refresh the activity log

                        // Check if there are any remaining tables
                        const container = document.getElementById('dynamicTablesContainer');
                        if (container.children.length === 0) {
                            // Add placeholder back if no tables remain
                            container.innerHTML = `
                                <div id="placeholder" class="flex flex-col items-center justify-center h-64 bg-gray-100 dark:bg-gray-800 rounded-lg shadow-lg">
                                    <h3 class="text-2xl font-semibold text-gray-500 dark:text-gray-400">Begin by Assigning an Engineer</h3>
                                    <p class="text-base text-gray-400 dark:text-gray-500">Click the "Assign Task to Engineer" button to create a group.</p>
                                </div>
                            `;
                        }
                    } else {
                        throw new Error(data.message || 'Failed to delete tasks');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the table.');
                });
            }
        };

        if (!tasks || tasks.length === 0) {
            deleteButton.style.display = 'none';
        }

        // Add buttons to container
        buttonContainer.appendChild(saveButton);
        buttonContainer.appendChild(deleteButton);

        // Add elements to header
        header.appendChild(headerTitle);
        header.appendChild(buttonContainer);
        // Create the scrollable container
        const scrollableContainer = document.createElement('div');
        scrollableContainer.className = 'w-full overflow-x-auto relative';


        // Create the table
        const table = document.createElement('table');
        table.className = 'min-w-[900px] w-full bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 border border-gray-300 dark:border-gray-600';

        // Create the table header row
        const thead = document.createElement('thead');
        thead.className = 'bg-gray-100 dark:bg-gray-700';
        thead.innerHTML = `
            <tr>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[450px]">
                    Task
                </th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[120px]">
                    Start Date
                </th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[120px]">
                    Due Date
                </th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[220px]">
                    Priority
                </th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[220px]">
                    Status
                </th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[220px]">
                    Budget
                </th>
                ${projectType === 'POW' ? `
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[220px]">
                    Source of Funding
                </th>
                ` : ''}
            </tr>
        `;

        // Create the table body
        const tbody = document.createElement('tbody');
        tbody.className = 'bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700';

        // Add existing tasks to the table
        tasks.forEach(task => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 cursor-pointer';
            row.setAttribute('data-task-id', task.id);
            row.innerHTML = `
                    <td class="task-column px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[450px] cursor-pointer"
                    onclick="if(!event.target.closest('input') && !event.target.closest('.subtask-btn') && !event.target.closest('.delete-subtask-btn')) openTaskDetails(this.closest('tr'))">
                    <div class="flex items-center justify-between">
                        <input
                            type="text"
                            class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2"
                            value="${task.task_name}">
                        <div class="flex items-center ml-2">
                            <button class="subtask-toggle-btn mr-2 text-gray-500 hover:text-gray-400 transition-colors duration-150"
                                    onclick="toggleSubtasks(this.closest('tr'), event)"
                                    title="Toggle Subtasks">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform rotate-180 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button class="subtask-btn text-blue-500 hover:text-blue-700 transition-colors duration-150" onclick="addSubtask(this.closest('tr'))" title="Add Subtask">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                    <input
                        type="date" 
                        class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2"
                        value="${task.start_date}">
                </td>
                <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                    <input
                        type="date" 
                        class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2"
                        value="${task.due_date}">
                </td>
                <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                    <div
                        class="priority-cell w-full ${getColor('priority', task.priority)} text-white rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition font-medium cursor-pointer"
                        onclick="editPriority(this)">
                        <span class="priority-value">${task.priority}</span>
                    </div>
                </td>
                <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                    <div
                        class="status-cell w-full ${getColor('status', task.status)} text-white rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition cursor-pointer"
                        onclick="editStatus(this)">
                        <span class="status-value">${task.status}</span>
                    </div>
                </td>
                <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                    <input
                        type="text"
                        inputmode="decimal"
                        placeholder="Enter Budget"
                        value="${task.budget}"
                        oninput="updateTotalBudget(this)"
                        onblur="updateTotalBudget(this)"
                        class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2"
                    >
                </td>
                ${projectType === 'POW' ? `
                <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                    <select name="source_of_funding"
                        class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                        onchange="handleSourceOfFundingChange(this)">
                        <option value="">Select</option>
                        <option value="DRRM-F">DRRM-F</option>
                        <option value="LDF">LDF</option>
                        <option value="NTA">NTA</option>
                        <option value="For funding">For funding</option>
                        <option value="Others">Others</option>
                    </select>
                    <div class="other-funding-source hidden mt-2">
                        <input type="text" name="other_funding_source" class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2 p-2 rounded-lg" placeholder="Please specify">
                    </div>
                </td>
                ` : ''}
            `;
            if (projectType === 'POW' && task.source_of_funding) {
                const sourceSelect = row.querySelector('td:nth-child(7) select[name="source_of_funding"]');
                if (sourceSelect) sourceSelect.value = task.source_of_funding;
                if (task.source_of_funding === 'Others' && task.other_funding_source) {
                    const otherDiv = row.querySelector('td:nth-child(7) .other-funding-source');
                    if (otherDiv) {
                        otherDiv.classList.remove('hidden');
                        const otherInput = otherDiv.querySelector('input[name="other_funding_source"]');
                        if (otherInput) otherInput.value = task.other_funding_source;
                    }
                }
            }
            tbody.appendChild(row);

            // Add subtasks if they exist
            if (task.subtasks && task.subtasks.length > 0) {
                task.subtasks.forEach(subtask => {
                    const subtaskRow = document.createElement('tr');
                    subtaskRow.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 subtask-row hidden';
                    subtaskRow.setAttribute('data-task-id', subtask.id);
                    subtaskRow.innerHTML = `
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[450px] cursor-pointer" onclick="if(!event.target.closest('input') && !event.target.closest('.subtask-btn') && !event.target.closest('.delete-subtask-btn')) openTaskDetails(this.closest('tr'))">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center w-full">
                                    <span class="text-gray-400 mr-2">└─</span>
                                    <input 
                                        type="text" 
                                        class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2" 
                                        value="${subtask.task_name}"
                                        data-old-value="${subtask.task_name}"
                                        placeholder="Enter Subtask">
                                </div>
                                <button class="delete-subtask-btn ml-2 text-red-500 hover:text-red-700 transition-colors duration-150" onclick="deleteSubtask(this.closest('tr'))" title="Delete Subtask">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                            <input 
                                type="date" 
                                class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2"
                                value="${subtask.start_date}"
                                data-old-value="${subtask.start_date}"
                                onchange="updateOldValue(this)">
                        </td>
                        <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                            <input
                                type="date" 
                                class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2"
                                value="${subtask.due_date}"
                                data-old-value="${subtask.due_date}"
                                onchange="updateOldValue(this)">
                        </td>
                        <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                            <div
                                class="priority-cell w-full ${getColor('priority', subtask.priority)} text-white rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition font-medium cursor-pointer"
                                onclick="editPriority(this)"
                                data-old-value="${subtask.priority}">
                                <span class="priority-value">${subtask.priority}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                            <div
                                class="status-cell w-full ${getColor('status', subtask.status)} text-white rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition cursor-pointer"
                                onclick="editStatus(this)"
                                data-old-value="${subtask.status}">
                                <span class="status-value">${subtask.status}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 ">
                            <input 
                                type="text"
                                inputmode="decimal"
                                placeholder="Enter Budget" 
                                value="${subtask.budget}"
                                data-old-value="${subtask.budget}"
                                oninput="updateTotalBudget(this); updateOldValue(this)" 
                                class="w-full bg-transparent outline-none text-inherit">
                        </td>
                        ${projectType === 'POW' ? `
                    <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                        <select name="source_of_funding"
                            class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                            onchange="handleSourceOfFundingChange(this)">
                            <option value="">Select</option>
                            <option value="DRRM-F">DRRM-F</option>
                            <option value="LDF">LDF</option>
                            <option value="NTA">NTA</option>
                            <option value="For funding">For funding</option>
                            <option value="Others">Others</option>
                        </select>
                        <div class="other-funding-source hidden mt-2">
                            <input type="text" name="other_funding_source" class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2 p-2 rounded-lg" placeholder="Please specify">
                        </div>
                    </td>
                    ` : ''}
                    `;
                    if (projectType === 'POW' && subtask.source_of_funding) {
                        const subtaskSourceSelect = subtaskRow.querySelector('td:nth-child(7) select[name="source_of_funding"]');
                        if (subtaskSourceSelect) subtaskSourceSelect.value = subtask.source_of_funding;
                        if (subtask.source_of_funding === 'Others' && subtask.other_funding_source) {
                            const subtaskOtherDiv = subtaskRow.querySelector('td:nth-child(7) .other-funding-source');
                            if (subtaskOtherDiv) {
                                subtaskOtherDiv.classList.remove('hidden');
                                const subtaskOtherInput = subtaskOtherDiv.querySelector('input[name="other_funding_source"]');
                                if (subtaskOtherInput) subtaskOtherInput.value = subtask.other_funding_source;
                            }
                        }
                    }
                    tbody.appendChild(subtaskRow);
                });
            }
        });
        const addItemRow = document.createElement('tr');
        addItemRow.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 cursor-pointer';
        addItemRow.onclick = () => addNewRow(tbody);
        addItemRow.innerHTML = `
            <td colspan="6" class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">
                <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Add Item</span>
                </div>
            </td>
        `;
        tbody.appendChild(addItemRow);
        const totalBudget = calculateTotalBudget(tasks);
        // Create the table footer
        const tfoot = document.createElement('tfoot');
        const totalColumns = projectType === 'POW' ? 7 : 6;
        const budgetLabelColspan = projectType === 'POW' ? 6 : 5;
        tfoot.className = 'bg-gray-100 dark:bg-gray-700';
        tfoot.innerHTML = `
            <tr>
                <td colspan="${budgetLabelColspan}" class="px-6 py-4 text-right font-semibold text-gray-800 dark:text-gray-200 border-t border-gray-300 dark:border-gray-600">
                    Total Budget:
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-t border-gray-300 dark:border-gray-600">
                    <span class="total-budget font-semibold">₱${totalBudget.toLocaleString('en-US', { minimumFractionDigits: 2 })}</span>
                </td>
            </tr>
        `;

        // Append all elements
        table.appendChild(thead);
        table.appendChild(tbody);
        table.appendChild(tfoot);
        scrollableContainer.appendChild(table);
        tableWrapper.appendChild(header);
        tableWrapper.appendChild(scrollableContainer);
        container.appendChild(tableWrapper);
    }

    // Helper function to get color based on type and value
    function getColor(type, value) {
        return colorMapping[value] || (type === 'priority' ? 'bg-yellow-500' : 'bg-gray-500');
    }

    // Function to add a new row
    function addNewRow(tbody) {
        const addItemRow = tbody.querySelector('tr:last-child');
        const totalColumns = projectType === 'POW' ? 7 : 6; // 7 columns if POW, else 6

        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150';

        // Build the main columns
        let rowHtml = `
            <td class="task-column px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[450px] cursor-pointer" 
                onclick="if(!event.target.closest('input') && !event.target.closest('.subtask-btn') && !event.target.closest('.delete-subtask-btn')) openTaskDetails(this.closest('tr'))">
                <div class="flex items-center justify-between">
                    <input 
                        type="text" 
                        class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2" 
                        placeholder="Enter Task">
                    <div class="flex items-center ml-2">
                        <button class="subtask-toggle-btn mr-2 text-gray-500 hover:text-gray-400 transition-colors duration-150" 
                                onclick="toggleSubtasks(this.closest('tr'), event)" 
                                title="Toggle Subtasks">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform rotate-180 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button class="subtask-btn text-blue-500 hover:text-blue-700 transition-colors duration-150" onclick="addSubtask(this.closest('tr'))" title="Add Subtask">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                <input 
                    type="date" 
                    class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2">
            </td>
            <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                <input
                    type="date" 
                    class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2">
            </td>
            <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                <div
                    class="priority-cell w-full bg-yellow-500 text-white rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition font-medium cursor-pointer"
                    onclick="editPriority(this)">
                    <span class="priority-value">Normal</span>
                </div>
            </td>
            <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                <div
                    class="status-cell w-full bg-blue-500 text-white rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition cursor-pointer"
                    onclick="editStatus(this)">
                    <span class="status-value">For Checking</span>
                </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 ">
                <input 
                    type="text"
                    inputmode="decimal"
                    placeholder="Enter Budget" 
                    oninput="updateTotalBudget(this)" 
                    onblur="updateTotalBudget(this)"
                    class="w-full bg-transparent outline-none text-inherit">
            </td>
        `;

        // Add Source of Funding column if POW
        if (projectType === 'POW') {
            rowHtml += `
            <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                <select name="source_of_funding"
                    class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                    onchange="handleSourceOfFundingChange(this)">
                    <option value="">Select</option>
                    <option value="DRRM-F">DRRM-F</option>
                    <option value="LDF">LDF</option>
                    <option value="NTA">NTA</option>
                    <option value="For funding">For funding</option>
                    <option value="Others">Others</option>
                </select>
                <div class="other-funding-source hidden mt-2">
                    <input type="text" name="other_funding_source" class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2 p-2 rounded-lg" placeholder="Please specify">
                </div>
            </td>
            `;
        }

        row.innerHTML = rowHtml;
        tbody.appendChild(row);

        // Update Add Item Row colspan
        addItemRow.innerHTML = `
            <td colspan="${totalColumns}" class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">
                <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Add Item</span>
                </div>
            </td>
        `;
        tbody.appendChild(addItemRow);
    }
    window.addNewRow = addNewRow;

    // Function to add a subtask
    function addSubtask(parentRow) {
        // Check if the parent row is already a subtask
        if (parentRow.classList.contains('subtask-row')) {
            alert('Cannot add subtasks to subtasks');
            return;
        }

        // Create a new row for the subtask
        const subtaskRow = document.createElement('tr');
        subtaskRow.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 subtask-row';

        // Get the parent task's values
        const parentStartDate = parentRow.querySelector('td:nth-child(2) input').value;
        const parentDueDate = parentRow.querySelector('td:nth-child(3) input').value;
        const parentPriority = parentRow.querySelector('.priority-value').textContent;
        const parentStatus = parentRow.querySelector('.status-value').textContent;

        // Create the subtask row with indentation and similar values
        subtaskRow.innerHTML = `
            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[450px] cursor-pointer" onclick="if(!event.target.closest('input') && !event.target.closest('.subtask-btn') && !event.target.closest('.delete-subtask-btn')) openTaskDetails(this.closest('tr'))">
                <div class="flex items-center justify-between">
                    <div class="flex items-center w-full">
                        <span class="text-gray-400 mr-2">└─</span>
                        <input 
                            type="text" 
                            class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2" 
                            placeholder="Enter Subtask">
                    </div>
                    <button class="delete-subtask-btn ml-2 text-red-500 hover:text-red-700 transition-colors duration-150" onclick="deleteSubtask(this.closest('tr'))" title="Delete Subtask">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </td>
            <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                <input 
                    type="date" 
                    class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2"
                    value="${parentStartDate}">
            </td>
            <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                <input
                    type="date" 
                    class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2"
                    value="${parentDueDate}">
            </td>
            <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                <div
                    class="priority-cell w-full ${getColor('priority', parentPriority)} text-white rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition font-medium cursor-pointer"
                    onclick="editPriority(this)">
                    <span class="priority-value">${parentPriority}</span>
                </div>
            </td>
            <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                <div
                    class="status-cell w-full ${getColor('status', parentStatus)} text-white rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition cursor-pointer"
                    onclick="editStatus(this)">
                    <span class="status-value">${parentStatus}</span>
                </div>
            </td>
            <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600 text-sm text-gray-900 dark:text-gray-200">
                <input 
                    type="text"
                    inputmode="decimal"
                    placeholder="Enter Budget" 
                    oninput="updateTotalBudget(this)" 
                    onblur="updateTotalBudget(this)"
                    class="w-full bg-transparent outline-none text-inherit">
            </td>
            ${projectType === 'POW' ? `
            <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                <select name="source_of_funding"
                    class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                    onchange="handleSourceOfFundingChange(this)">
                    <option value="">Select</option>
                    <option value="DRRM-F">DRRM-F</option>
                    <option value="LDF">LDF</option>
                    <option value="NTA">NTA</option>
                    <option value="For funding">For funding</option>
                    <option value="Others">Others</option>
                </select>
                <div class="other-funding-source hidden mt-2">
                    <input type="text" name="other_funding_source" class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2 p-2 rounded-lg" placeholder="Please specify">
                </div>
            </td>
            ` : ''}
        `;

        // Insert the subtask row after the parent row
        parentRow.parentNode.insertBefore(subtaskRow, parentRow.nextSibling);
    }

    // Function to delete a subtask
    function deleteSubtask(row) {
        if (!confirm('Are you sure you want to delete this subtask?')) {
            return;
        }

        const taskId = row.getAttribute('data-task-id');
        if (!taskId) {
            row.remove();
            return;
        }

        fetch(`/tasks/${taskId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            console.log('Success:', data);
            row.remove();
            alert('Subtask deleted successfully!');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the subtask.');
        });
    }

    // Load existing tasks when the page loads
    document.addEventListener('DOMContentLoaded', () => {
        loadExistingTasks();
        updateUserButtonStates();
    });

    // Task drawer handler functions
    const taskDrawerHandler = {
        open: (row) => {
            const taskId = row.getAttribute('data-task-id');
            const taskName = row.querySelector('td:first-child input').value;
            const startDate = row.querySelector('td:nth-child(2) input').value;
            const dueDate = row.querySelector('td:nth-child(3) input').value;
            const priority = row.querySelector('.priority-value').textContent;
            const status = row.querySelector('.status-value').textContent;

            const drawer = document.getElementById('taskDetailsDrawer');
            drawer.setAttribute('data-current-row', taskId || '');

            // Update drawer content
            document.getElementById('taskTitle').textContent = taskName;
            document.getElementById('startDate').textContent = startDate;
            document.getElementById('dueDate').textContent = dueDate;
            document.getElementById('priority').textContent = priority;
            document.getElementById('status').textContent = status;

            // Handle completion timestamp
            const completionTimestampElement = document.getElementById('completionTimestamp');
            if (completionTimestampElement) {
                if (status === 'Completed' && taskId) {
                    // Fetch completion timestamp from server
                    fetch(`/tasks/${taskId}/completion-time`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.completion_time) {
                                completionTimestampElement.querySelector('span').textContent = 
                                    new Date(data.completion_time).toLocaleString('en-US', {
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    });
                                completionTimestampElement.classList.remove('hidden');
                            } else {
                                completionTimestampElement.classList.add('hidden');
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching completion time:', error);
                            completionTimestampElement.classList.add('hidden');
                        });
                } else {
                    completionTimestampElement.classList.add('hidden');
                }
            }
                
            // Load additional content
            loadComments();
            loadFiles();

            // Show drawer
            drawer.classList.remove('translate-x-full');
        },
        close: () => {
            const drawer = document.getElementById('taskDetailsDrawer');
            drawer.classList.add('translate-x-full');
        }
    };

    function openTaskDetailsFromKanban(task) {
        const drawer = document.getElementById('taskDetailsDrawer');
        drawer.setAttribute('data-current-row', task.id || '');

        // Update drawer content
        document.getElementById('taskTitle').textContent = task.task_name || '';
        document.getElementById('startDate').textContent = task.start_date || '';
        document.getElementById('dueDate').textContent = task.due_date || '';
        document.getElementById('priority').textContent = task.priority || '';
        document.getElementById('status').textContent = task.status || '';

        // Handle completion timestamp
        const completionTimestampElement = document.getElementById('completionTimestamp');
        if (completionTimestampElement) {
            if ((task.status === 'Completed' || task.status === 'completed') && task.completion_time) {
                completionTimestampElement.querySelector('span').textContent =
                    new Date(task.completion_time).toLocaleString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                completionTimestampElement.classList.remove('hidden');
            } else {
                completionTimestampElement.classList.add('hidden');
            }
        }

        // Load comments and files for this task
        loadComments();
        loadFiles();

        // Show drawer
        drawer.classList.remove('translate-x-full');
    }

    const openTaskDetails = (row) => taskDrawerHandler.open(row);
    const closeTaskDetailsDrawer = () => taskDrawerHandler.close();

    function loadComments() {
        const taskId = document.getElementById('taskDetailsDrawer').getAttribute('data-current-row');
        if (!taskId) {
            console.error('No task ID found');
            return;
        }

        const commentsContainer = document.getElementById('commentsContainer');
        commentsContainer.innerHTML = ''; // Clear existing comments

        fetch(`/tasks/${taskId}/comments`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(comments => {
                if (Array.isArray(comments)) {
                    comments.forEach(comment => {
                        const commentElement = createCommentElement(comment);
                        commentsContainer.appendChild(commentElement);
                    });

                    // Update the comment count
                    document.getElementById('commentCount').textContent = comments.length;
                } else {
                    console.error('Expected array of comments, got:', comments);
                }
            })
            .catch(error => {
                console.error('Error loading comments:', error);
                commentsContainer.innerHTML = '<div class="text-red-500 p-4">Error loading comments. Please try again.</div>';
            });
    }

    function createCommentElement(comment, isReply = false) {
        const commentDiv = document.createElement('div');
        // All replies will have the same indentation level (ml-12)
        commentDiv.className = `bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-4 ${isReply ? 'ml-12' : ''}`;
        commentDiv.setAttribute('data-comment-id', comment.id);

        const isCurrentUser = comment.user_id === {{ auth()->id() }};

        commentDiv.innerHTML = `
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white">
                        ${comment.user.name.charAt(0)}
                    </div>
                </div>
                <div class="flex-grow">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">${comment.user.name}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">
                                ${formatDate(comment.created_at)}
                                ${comment.edited ? '<span class="text-xs text-gray-500 dark:text-gray-400 ml-2 italic">(Edited)</span>' : ''}
                            </span>
                        </div>
                        <div class="flex items-center space-x-2">
                            ${!isReply ? `
                                <button onclick="showReplyForm(${comment.id})" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            ` : ''}
                           ${isCurrentUser ? `
                            <button onclick="editComment(${comment.id})" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>
                            <button onclick="deleteComment(${comment.id})" class="text-red-500 hover:text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2H4a1 1 0 000-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        ` : ''}
                        </div>
                    </div>
                    <div class="mt-2 text-gray-700 dark:text-gray-300 whitespace-pre-wrap">${comment.content}</div>
                    
                    <!-- Reply Form (Hidden by default) -->
                    <div id="replyForm-${comment.id}" class="hidden mt-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm">
                                    {{ auth()->user()->name[0] }}
                                </div>
                            </div>
                            <div class="flex-grow">
                                <textarea 
                                    id="replyText-${comment.id}" 
                                    class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" 
                                    rows="2"
                                    placeholder="Write a reply..."></textarea>
                                <div class="mt-2 flex justify-end space-x-2">
                                    <button onclick="cancelReply(${comment.id})" class="px-3 py-1 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                                        Cancel
                                    </button>
                                    <button onclick="submitReply(${comment.id})" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded-lg">
                                        Reply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Replies Container -->
                    ${!isReply && comment.replies && comment.replies.length > 0 ? `
                        <div class="mt-4">
                            <div class="flex items-center space-x-2 mb-2">
                                <button onclick="toggleReplies(${comment.id})" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 flex items-center">
                                    <svg id="toggleIcon-${comment.id}" class="h-4 w-4 transform transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-1 text-sm">${comment.replies.length} ${comment.replies.length === 1 ? 'reply' : 'replies'}</span>
                                </button>
                            </div>
                            <div id="replies-${comment.id}" class="space-y-4 hidden">
                                ${comment.replies.map(reply => createCommentElement(reply, true).outerHTML).join('')}
                            </div>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
        return commentDiv;
    }

    function handleCommentKeydown(event) {
        if (event.key === '@') {
            showMentionSuggestions();
        } else if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            addComment();
        }
    }

    function showMentionSuggestions() {
        const suggestionsDiv = document.getElementById('mentionSuggestions');
        suggestionsDiv.classList.remove('hidden');
        // Fetch and populate team members
        fetch('/api/team-members')
            .then(response => response.json())
            .then(members => {
                suggestionsDiv.innerHTML = members.map(member => `
                    <div class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer" onclick="insertMention('${member.name}')">
                        ${member.name}
                    </div>
                `).join('');
            });
    }

    function insertMention(name) {
        const textarea = document.getElementById('newComment');
        const cursorPos = textarea.selectionStart;
        const text = textarea.value;
        const beforeCursor = text.substring(0, cursorPos);
        const afterCursor = text.substring(cursorPos);
        
        // Find the last @ symbol before cursor
        const lastAtIndex = beforeCursor.lastIndexOf('@');
        if (lastAtIndex !== -1) {
            textarea.value = beforeCursor.substring(0, lastAtIndex) + `@${name} ` + afterCursor;
            document.getElementById('mentionSuggestions').classList.add('hidden');
        }
    }

    function addComment() {
        const newComment = document.getElementById('newComment').value;
        if (!newComment.trim()) return;

        const taskId = document.getElementById('taskDetailsDrawer').getAttribute('data-current-row');
        
        fetch(`/tasks/${taskId}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                task_id: taskId,
                content: newComment
            })
        })
        .then(response => response.json())
        .then(data => {
            const commentsContainer = document.getElementById('commentsContainer');
            const commentElement = createCommentElement(data.comment);
            commentsContainer.appendChild(commentElement); // Append to the bottom instead of inserting at the top
            document.getElementById('newComment').value = ''; // Clear input
        })
        .catch(error => console.error('Error adding comment:', error));
    }

    function deleteComment(commentId) {
        if (!confirm('Are you sure you want to delete this comment?')) return;

        fetch(`/comments/${commentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
            if (commentElement) commentElement.remove();
        })
        .catch(error => console.error('Error deleting comment:', error));
    }

    // Add helper function to format dates
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function loadFiles() {
        const taskId = document.getElementById('taskDetailsDrawer').getAttribute('data-current-row');
        if (!taskId) {
            console.error('No task ID found');
            return;
        }

        const fileList = document.getElementById('fileList');
        fileList.innerHTML = ''; // Clear existing files

        fetch(`/tasks/${taskId}/files`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(files => {
                if (Array.isArray(files)) {
                    files.forEach(file => {
                        const fileElement = createFileElement(file);
                        fileList.appendChild(fileElement);
                    });

                    // Update the file count
                    document.getElementById('fileCount').textContent = files.length;
                } else {
                    console.error('Expected array of files, got:', files);
                }
            })
            .catch(error => {
                console.error('Error loading files:', error);
                fileList.innerHTML = '<div class="text-red-500 p-4">Error loading files. Please try again.</div>';
            });
    }

    function createFileElement(file) {
        const fileDiv = document.createElement('div');
        fileDiv.className = 'bg-white dark:bg-gray-800 rounded-lg shadow p-4';
        fileDiv.setAttribute('data-file-id', file.id);
        
        const fileExtension = file.name.split('.').pop().toLowerCase();
        const fileIcon = getFileIcon(fileExtension);
        const fileSize = formatFileSize(file.size);
        
        fileDiv.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        ${fileIcon}
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${file.name}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            ${fileSize} • Uploaded by ${file.user.name} • ${formatDate(file.created_at)}
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="downloadFile(${file.id})" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    ${file.user_id === {{ auth()->id() }} ? `
                        <button onclick="deleteFile(${file.id})" class="text-red-500 hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2H4a1 1 0 000-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
        
        return fileDiv;
    }

    function getFileIcon(extension) {
        const iconClass = 'h-8 w-8 text-gray-500 dark:text-gray-400';
        
        switch (extension) {
            case 'pdf':
                return `<svg class="${iconClass}" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path></svg>`;
            case 'doc':
            case 'docx':
                return `<svg class="${iconClass}" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path></svg>`;
            case 'xls':
            case 'xlsx':
                return `<svg class="${iconClass}" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path></svg>`;
            case 'jpg':
            case 'jpeg':
            case 'png':
                return `<svg class="${iconClass}" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"></path></svg>`;
            default:
                return `<svg class="${iconClass}" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path></svg>`;
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function handleFileSelect(event) {
        const files = event.target.files;
        if (!files.length) return;
        
        const taskId = document.getElementById('taskDetailsDrawer').getAttribute('data-current-row');
        const formData = new FormData();
        
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }
        
        fetch(`/tasks/${taskId}/files`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Reload the files list
            loadFiles();
            // Clear the file input
            event.target.value = '';
        })
        .catch(error => console.error('Error uploading files:', error));
    }

    function downloadFile(fileId) {
        window.location.href = `/files/${fileId}/download`;
    }

    function deleteFile(fileId) {
        if (!confirm('Are you sure you want to delete this file?')) return;
        
        fetch(`/files/${fileId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const fileElement = document.querySelector(`[data-file-id="${fileId}"]`);
            if (fileElement) fileElement.remove();
        })
        .catch(error => console.error('Error deleting file:', error));
    }

    // Add these new functions for tab switching
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.add('hidden');
        });
        
        // Remove active class from all tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active', 'text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400');
            button.classList.add('text-gray-500', 'dark:text-gray-400', 'border-transparent');
        });
        
        // Show selected tab content and activate its button
        document.getElementById(tabName + 'TabContent').classList.remove('hidden');
        document.getElementById(tabName + 'Tab').classList.add('active', 'text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400');
        document.getElementById(tabName + 'Tab').classList.remove('text-gray-500', 'dark:text-gray-400', 'border-transparent');
    }

    function editComment(commentId) {
        const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
        if (!commentElement) return;
        
        const contentElement = commentElement.querySelector('.text-gray-700');
        const currentContent = contentElement.textContent;
        
        // Create edit form
        const editForm = document.createElement('div');
        editForm.className = 'mt-2';
        editForm.innerHTML = `
            <textarea class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" rows="3">${currentContent}</textarea>
            <div class="mt-2 flex justify-end space-x-2">
                <button onclick="cancelEdit(${commentId})" class="px-3 py-1 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                    Cancel
                </button>
                <button onclick="saveEdit(${commentId})" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded-lg">
                    Save
                </button>
            </div>
        `;
        
        // Replace content with edit form
        contentElement.replaceWith(editForm);
        
        // Focus the textarea
        editForm.querySelector('textarea').focus();
    }

    function cancelEdit(commentId) {
        const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
        if (!commentElement) return;
        
        // Reload the comment to restore original state
        loadComments();
    }

    function saveEdit(commentId) {
        const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
        if (!commentElement) return;

        const textarea = commentElement.querySelector('textarea');
        const newContent = textarea.value.trim();

        if (!newContent) return;

        fetch(`/comments/${commentId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                content: newContent
            })
        })
        .then(response => response.json())
        .then(data => {
            // Find the content element dynamically
            const contentElement = commentElement.querySelector('.text-gray-700');
            if (!contentElement) {
                console.error('Content element not found for comment:', commentId);
                return;
            }

            // Update the comment content
            contentElement.textContent = newContent;

            // Replace the edit form with the updated content
            const editForm = commentElement.querySelector('textarea').parentElement;
            editForm.replaceWith(contentElement);
        })
        .catch(error => console.error('Error updating comment:', error));
    }

    function replyToComment(commentId, userName) {
        const textarea = document.getElementById('newComment');
        textarea.value = `@${userName} `;
        textarea.focus();
    }

    function showReplyForm(commentId) {
        // Hide all other reply forms
        document.querySelectorAll('[id^="replyForm-"]').forEach(form => {
            form.classList.add('hidden');
        });
        
        // Show the selected reply form
        const replyForm = document.getElementById(`replyForm-${commentId}`);
        replyForm.classList.remove('hidden');
        
        // Focus the textarea
        const textarea = document.getElementById(`replyText-${commentId}`);
        textarea.focus();
    }

    function cancelReply(commentId) {
        const replyForm = document.getElementById(`replyForm-${commentId}`);
        replyForm.classList.add('hidden');
        
        // Clear the textarea
        const textarea = document.getElementById(`replyText-${commentId}`);
        textarea.value = '';
    }

    function submitReply(commentId) {
        const textarea = document.getElementById(`replyText-${commentId}`);
        const content = textarea.value.trim();
        
        if (!content) return;
        
        const taskId = document.getElementById('taskDetailsDrawer').getAttribute('data-current-row');
        
        fetch(`/tasks/${taskId}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                content: content,
                parent_id: commentId
            })
        })
        .then(response => response.json())
        .then(data => {
            // Get or create the replies container
            let repliesContainer = document.getElementById(`replies-${commentId}`);
            if (!repliesContainer) {
                // Create the replies container if it doesn't exist
                const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
                const repliesWrapper = document.createElement('div');
                repliesWrapper.className = 'mt-4';
                
                // Create the toggle button and count
                const toggleButton = document.createElement('div');
                toggleButton.className = 'flex items-center space-x-2 mb-2';
                toggleButton.innerHTML = `
                    <button onclick="toggleReplies(${commentId})" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 flex items-center">
                        <svg id="toggleIcon-${commentId}" class="h-4 w-4 transform transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-1 text-sm">1 reply</span>
                    </button>
                `;
                
                // Create the replies container
                repliesContainer = document.createElement('div');
                repliesContainer.id = `replies-${commentId}`;
                repliesContainer.className = 'space-y-4';
                
                // Append everything to the comment
                repliesWrapper.appendChild(toggleButton);
                repliesWrapper.appendChild(repliesContainer);
                commentElement.querySelector('.flex-grow').appendChild(repliesWrapper);
            } else {
                // Update the reply count for existing container
                const countSpan = document.querySelector(`#toggleIcon-${commentId}`).nextElementSibling;
                const currentCount = parseInt(countSpan.textContent);
                countSpan.textContent = `${currentCount + 1} ${currentCount + 1 === 1 ? 'reply' : 'replies'}`;
            }
            
            // Add the reply to the container
            const replyElement = createCommentElement(data.comment, true);
            repliesContainer.appendChild(replyElement);
            
            // Clear and hide the reply form
            textarea.value = '';
            cancelReply(commentId);
        })
        .catch(error => console.error('Error adding reply:', error));
    }

    function toggleReplies(commentId) {
        const repliesContainer = document.getElementById(`replies-${commentId}`);
        const toggleIcon = document.getElementById(`toggleIcon-${commentId}`);
        
        if (repliesContainer.classList.contains('hidden')) {
            repliesContainer.classList.remove('hidden');
            toggleIcon.classList.remove('rotate-180');
        } else {
            repliesContainer.classList.add('hidden');
            toggleIcon.classList.add('rotate-180');
        }
    }

    // Function to toggle subtasks visibility
    function toggleSubtasks(parentRow, event) {
        if (event) {
            event.stopPropagation();
        }

        const toggleBtn = parentRow.querySelector('.subtask-toggle-btn svg');
        let nextRow = parentRow.nextElementSibling;
        let hasVisibleSubtasks = false;

        // Toggle the arrow rotation
        toggleBtn.classList.toggle('rotate-180');

        // Toggle visibility of all subtasks
        while (nextRow && nextRow.classList.contains('subtask-row')) {
            nextRow.classList.toggle('hidden');
            if (!nextRow.classList.contains('hidden')) {
                hasVisibleSubtasks = true;
            }
            nextRow = nextRow.nextElementSibling;
        }

        // Update the toggle button color based on visibility
        const toggleBtnContainer = parentRow.querySelector('.subtask-toggle-btn');
        if (hasVisibleSubtasks) {
            toggleBtnContainer.classList.remove('text-gray-500');
            toggleBtnContainer.classList.add('text-blue-500');
        } else {
            toggleBtnContainer.classList.remove('text-blue-500');
            toggleBtnContainer.classList.add('text-gray-500');
        }
    }

    // Add this function at the end of the file
    function updateOldValue(input) {
        input.setAttribute('data-old-value', input.value);
    }
</script>

    <script>
        function switchView(view) {
            const mainTableBtn = document.getElementById('mainTableBtn');
            const kanbanBtn = document.getElementById('kanbanBtn');
            const mainTableSection = document.getElementById('mainTableSection');
            const kanbanSection = document.getElementById('kanbanSection');
            const sortBtnContainer = document.getElementById('sortBtnContainer');

            // Reset all buttons and sections
            [mainTableBtn, kanbanBtn].forEach(btn => {
                if (btn) {
                    btn.classList.remove('active', 'text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400');
                    btn.classList.add('text-gray-500', 'dark:text-gray-400', 'border-transparent');
                }
            });

            // Hide all sections
            [mainTableSection, kanbanSection].forEach(section => {
                if (section) {
                    section.classList.add('hidden');
                }
            });

            // Show selected section and activate button
            switch (view) {
                case 'mainTable':
                    if (mainTableSection && mainTableBtn) {
                        mainTableSection.classList.remove('hidden');
                        mainTableBtn.classList.remove('text-gray-500', 'dark:text-gray-400', 'border-transparent');
                        mainTableBtn.classList.add('active', 'text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400');
                    }
                    if (sortBtnContainer) sortBtnContainer.style.display = '';
                    break;
                case 'kanban':
                    if (kanbanSection && kanbanBtn) {
                        kanbanSection.classList.remove('hidden');
                        kanbanBtn.classList.remove('text-gray-500', 'dark:text-gray-400', 'border-transparent');
                        kanbanBtn.classList.add('active', 'text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400');
                    }
                    if (sortBtnContainer) sortBtnContainer.style.display = 'none';
                    break;
            }
        }
    </script>
</x-app-layout>