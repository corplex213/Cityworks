<x-app-layout>
    @vite(['resources/css/project-management.css', 'resources/js/activityLog.js', 'resources/js/sortingTask.js', 'resources/js/project-managementUI.js'])
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
    <div id="sortingModal"
     class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 transition-all duration-300 ease-in-out opacity-0 scale-95">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6 transform transition-all duration-300 ease-in-out scale-95 opacity-0"
         id="sortingModalContent">
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
                    <label class="flex items-center text-white">
                        <input type="radio" name="sortOrder" value="asc" class="mr-2" checked>
                        Ascending
                    </label>
                    <label class="flex items-center text-white">
                        <input type="radio" name="sortOrder" value="desc" class="mr-2">
                        Descending
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="resetSorting()" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">Reset</button>
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

<script>
    window.PROJECT_ID = {{ $project->id }};
    window.projectType = @json($project->proj_type);
</script>

<script>
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
                closeTaskDetailsDrawer();
                alert('Task deleted successfully!');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the task.');
            });
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
            row.remove();

            // --- Remove subtask from Kanban view ---
            if (data.parent_task_id && typeof window.loadExistingTasks === 'function') {
                window.loadExistingTasks();
            }

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
</script>
</x-app-layout>