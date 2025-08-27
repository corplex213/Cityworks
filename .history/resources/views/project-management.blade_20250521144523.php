<x-app-layout>
    @vite(['resources/css/project-management.css', 'resources/js/project-table.js', 'resources/js/activityLog.js', 'resources/js/sortingTask.js', 'resources/js/project-managementUI.js', 'resources/js/managementCRUD.js', 'resources/js/taskDrawer.js'])
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
                    <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
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
                        <div class="bg-blue-50 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
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
                                        <div class="bg-blue-50 dark:bg-gray-700 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-600">
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
                                                            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" 
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
    window.CURRENT_USER_ID = {{ auth()->id() }};
    window.CURRENT_USER_NAME = @json(auth()->user()->name);
</script>
</x-app-layout>