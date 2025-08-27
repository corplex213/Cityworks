@vite(['resources/css/kanban-board.css', 'resources/js/kanban-board.js'])
<!-- Kanban Board Section -->
<div class="kanban-container flex space-x-4 p-4 overflow-x-auto" id="kanban-{{ $userId }}">
        <!-- To Do Column -->
        <div class="kanban-column bg-gray-100 dark:bg-gray-800 rounded-lg shadow-lg w-80 flex-shrink-0">
            <div class="column-header bg-blue-500 text-white p-4 rounded-t-lg">
                <h3 class="text-lg font-semibold">For Checking</h3>
                <span class="task-count text-sm bg-blue-600 px-2 py-1 rounded-full">0</span>
            </div>
            <div class="task-list p-4 space-y-4 min-h-[500px] max-h-[800px] overflow-y-auto" data-status="To Do">
                <!-- Tasks will be dynamically added here -->
            </div>
            <button class="add-item-btn w-full mt-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition"
            data-status="{{ $statusKey ?? '' }}"
            data-user-id="{{ $userId }}"    
            onclick="addKanbanTask('{{ $userId }}','{{ $statusKey ?? 'To Do'}}')">
                + Add Item
            </button>
        </div>

        <!-- In Progress Column -->
        <div class="kanban-column bg-gray-100 dark:bg-gray-800 rounded-lg shadow-lg w-80 flex-shrink-0">
            <div class="column-header bg-yellow-500 text-white p-4 rounded-t-lg">
                <h3 class="text-lg font-semibold">For Revision</h3>
                <span class="task-count text-sm bg-yellow-600 px-2 py-1 rounded-full">0</span>
            </div>
            <div class="task-list p-4 space-y-4 min-h-[500px] max-h-[800px] overflow-y-auto" data-status="In Progress">
                <!-- Tasks will be dynamically added here -->
            </div>
            <button class="add-item-btn w-full mt-4 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition"
            data-status="{{ $statusKey ?? '' }}"
            data-user-id="{{ $userId }}"
            onclick="addKanbanTask('{{ $userId }}', '{{ $statusKey ?? 'In Progress' }}')">
                + Add Item
            </button>
        </div>

        <!-- Review Column -->
        <div class="kanban-column bg-gray-100 dark:bg-gray-800 rounded-lg shadow-lg w-80 flex-shrink-0">
            <div class="column-header bg-red-500 text-white p-4 rounded-t-lg">
                <h3 class="text-lg font-semibold">Deferred</h3>
                <span class="task-count text-sm bg-red-600 px-2 py-1 rounded-full">0</span>
            </div>
            <div class="task-list p-4 space-y-4 min-h-[500px] max-h-[800px] overflow-y-auto" data-status="Review">
                <!-- Tasks will be dynamically added here -->
            </div>
            <button class="add-item-btn w-full mt-4 bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition"
            data-status="{{ $statusKey ?? '' }}"
            data-user-id="{{ $userId }}"    
            onclick="addKanbanTask('{{ $userId }}', '{{ $statusKey ?? 'Review' }}')">
                + Add Item
            </button>
        </div>

        <!-- Done Column -->
        <div class="kanban-column bg-gray-100 dark:bg-gray-800 rounded-lg shadow-lg w-80 flex-shrink-0">
            <div class="column-header bg-green-500 text-white p-4 rounded-t-lg">
                <h3 class="text-lg font-semibold">Completed</h3>
                <span class="task-count text-sm bg-green-600 px-2 py-1 rounded-full">0</span>
            </div>
            <div class="task-list p-4 space-y-4 min-h-[500px] max-h-[800px] overflow-y-auto" data-status="Done">
                <!-- Tasks will be dynamically added here -->
            </div>
            <button class="add-item-btn w-full mt-4 bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition"
            data-status="{{ $statusKey ?? '' }}"
            data-user-id="{{ $userId }}"     
            onclick="addKanbanTask('{{ $userId }}', '{{ $statusKey ?? 'Done' }}')">
                + Add Item
            </button>
        </div>
</div>

<!-- Task Card Template -->
<template id="taskCardTemplate">
    <div class="task-card bg-white dark:bg-gray-700 rounded-lg shadow p-4 cursor-pointer">
        <div class="task-header flex justify-between items-start mb-2">
            <h4 class="task-title text-lg font-semibold text-gray-800 dark:text-gray-200"></h4>
            <span class="priority-badge px-2 py-1 rounded-full text-xs font-medium"></span>
        </div>
        <h4 class="task-title text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2"></h4>
        <div class="task-fields space-y-1 mb-2"></div>
        <button class="toggle-subtasks-btn mt-2 text-xs text-blue-500 hover:underline flex items-center" type="button">
            <span>Show Subtasks</span>
            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="subtasks-list mt-2 hidden"></div>
        <button class="add-kanban-subtask-btn mt-2 text-xs text-blue-600 hover:underline flex items-center" type="button">
            <span>+ Add Subtask</span>
        </button>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-item-btn').forEach(function(btn) {
        if (!window.CAN_CREATE_TASKS) {
            btn.classList.add('cursor-not-allowed', 'opacity-50');
            btn.title = "You do not have permission to add tasks";
            btn.onclick = function(e) {
                e.preventDefault();
                return false;
            };
        } else {
            btn.classList.remove('cursor-not-allowed', 'opacity-50');
            btn.title = "Add Item";
        }
    });
});
</script>