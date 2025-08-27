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
            <div id="taskDetailsDrawer" class="fixed inset-y-0 right-0 w-full sm:w-1/2 bg-gray-900 dark:bg-gray-900 shadow-lg transform translate-x-full transition-transform duration-200 ease-out z-50" data-current-user-id="{{ auth()->id() }}">
                <div class="p-4 h-full flex flex-col">
                    <!-- Drawer Header -->
                    <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-700">
                        <h3 id="taskTitle" class="text-xl font-semibold text-white break-words" style="word-wrap: break-word; max-width: calc(100% - 60px);"></h3>
                        <div class="flex items-center space-x-2">
                            <!-- Delete Task Button -->
                            <button id="deleteTaskButton" onclick="deleteTaskFromDrawer()" class="text-red-400 hover:text-red-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <!-- Close Drawer Button -->
                            <button onclick="closeTaskDetailsDrawer()" class="text-gray-400 hover:text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Task Details Card -->
                    <div class="bg-gray-800 p-5 rounded-lg mb-4 shadow-md">
                        <!-- Task details grid layout -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Dates section -->
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs">Start Date</p>
                                    <p id="startDate" class="text-gray-200 font-medium"></p>
                                </div>
                            </div>

                            <!-- Due Date section -->
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-red-500/20 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs">Due Date</p>
                                    <p id="dueDate" class="text-gray-200 font-medium"></p>
                                </div>
                            </div>

                            <!-- Priority section -->
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs">Priority</p>
                                    <p id="priority" class="text-gray-200 font-medium"></p>
                                </div>
                            </div>

                            <!-- Status section -->
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-500/20 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 2a6 6 0 100 12 6 6 0 000-12z" clip-rule="evenodd" />
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs">Status</p>
                                    <p id="status" class="text-gray-200 font-medium"></p>
                                </div>
                            </div>

                            <!-- Assigned by section -->
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs">Assigned by</p>
                                    <p id="assignedBy" class="text-gray-200 font-medium"></p>
                                </div>
                            </div>

                            <!-- Assigned to section -->
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-indigo-500/20 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs">Assigned to</p>
                                    <p id="assignedTo" class="text-gray-200 font-medium"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline information - spans full width -->
                        <div class="mt-4 pt-4 border-t border-gray-700">
                        <div id="assignmentTimestamp" class="flex items-center space-x-3 mb-2">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-400 text-xs">Assigned on</p>
                                <!-- Important: This paragraph needs to exist for JS queries -->
                                <p class="text-gray-200 font-medium assignment-date"></p>
                            </div>
                        </div>
                        <div id="completionTimestamp" class="flex items-center space-x-3 hidden">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-500/20 rounded-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-400 text-xs">Completed on</p>
                                <!-- Important: This paragraph needs to exist for JS queries -->
                                <p class="text-gray-200 font-medium completion-date"></p>
                            </div>
                        </div>
                    </div>
                    </div>
                    
                    <!-- Drawer Content -->
                    <div id="taskDetailsContent" class="flex-1 overflow-y-auto space-y-4">
                        <!-- Tabs Navigation -->
                        <div class="border-b border-gray-700">
                            <nav class="flex space-x-8" aria-label="Tabs">
                                <button id="commentsTab" class="tab-button active px-3 py-2 text-sm font-medium text-blue-400 border-b-2 border-blue-400" onclick="switchTab('comments')">
                                    Comments
                                </button>
                                <button id="filesTab" class="tab-button px-3 py-2 text-sm font-medium text-gray-400 hover:text-gray-300 border-b-2 border-transparent" onclick="switchTab('files')">
                                    Files
                                </button>
                            </nav>
                        </div>
                        
                        <!-- Tab Content -->
                        <div class="tab-content mt-4">
                            <!-- Comments Tab -->
                            <div id="commentsTabContent" class="tab-pane active">
                                <div class="space-y-5">
                                    <!-- Comment Input -->
                                    <div class="bg-gray-800 rounded-lg p-4 shadow-md">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-medium shadow-md">
                                                    {{ auth()->user()->name[0] }}
                                                </div>
                                            </div>
                                            <div class="flex-grow">
                                                <div class="relative">
                                                    <textarea 
                                                        id="newComment" 
                                                        class="w-full p-3 border border-gray-600 rounded-lg bg-gray-700 text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none shadow-sm" 
                                                        rows="3"
                                                        placeholder="Write a comment... Use @ to mention team members"
                                                        onkeydown="handleCommentKeydown(event)"></textarea>
                                                    <div id="mentionSuggestions" class="hidden absolute z-10 w-full bg-gray-700 border border-gray-600 rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto">
                                                        <!-- Mention suggestions will be populated here -->
                                                    </div>
                                                </div>
                                                <div class="mt-3 flex justify-end">
                                                    <button 
                                                        onclick="addComment()" 
                                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center shadow-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                        Post Comment
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Comment List Header -->
                                    <div class="flex items-center space-x-2 px-2">
                                        <div class="flex-shrink-0 w-6 h-6 bg-indigo-500/20 rounded-md flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <h3 class="text-gray-300 font-medium">Comments (<span id="commentCount">0</span>)</h3>
                                    </div>
                                    
                                    <!-- Comments List with improved design -->
                                    <div id="commentsContainer" class="space-y-4">
                                        <!-- Comments will be loaded here -->
                                        <!-- Example of how a comment should look (will be generated by JS): -->
                                        <div class="comment bg-gray-800 rounded-lg p-4 shadow-md">
                                            <div class="flex space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white shadow-sm">
                                                        U
                                                    </div>
                                                </div>
                                                <div class="flex-grow">
                                                    <div class="flex items-center justify-between">
                                                        <p class="font-medium text-gray-200">Username</p>
                                                        <p class="text-xs text-gray-400">May 20, 2025 at 2:30 PM</p>
                                                    </div>
                                                    <div class="mt-2 text-gray-300">
                                                        <p>This is an example comment that shows the new design. Comments will include proper formatting and support for mentions.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Empty State -->
                                    <div id="noCommentsMessage" class="hidden text-center py-8">
                                        <div class="w-16 h-16 bg-gray-700/50 rounded-full mx-auto flex items-center justify-center mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <h4 class="text-gray-400 font-medium">No comments yet</h4>
                                        <p class="text-gray-500 text-sm mt-1">Be the first to add a comment to this task</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Files Tab -->
                            <div id="filesTabContent" class="tab-pane hidden">
                                <div class="space-y-5">
                                    <!-- File Upload Area -->
                                    <div class="bg-gray-800 rounded-lg p-4">
                                        <div class="flex items-center justify-center w-full">
                                            <label for="fileUpload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-600 border-dashed rounded-lg cursor-pointer bg-gray-700 hover:bg-gray-600">
                                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                    <svg class="w-8 h-8 mb-4 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                                    </svg>
                                                    <p class="mb-2 text-sm text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                                    <p class="text-xs text-gray-400">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (MAX. 10MB)</p>
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