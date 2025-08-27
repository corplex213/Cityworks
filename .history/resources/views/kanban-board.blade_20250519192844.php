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
                onclick="addKanbanTask('{{ $userId }}', 'To Do')">
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
                onclick="addKanbanTask('{{ $userId }}', 'In Progress')">
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
                onclick="addKanbanTask('{{ $userId }}', 'Review')">
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
                onclick="addKanbanTask('{{ $userId }}', 'Done')">
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

<style>
.kanban-column {
    display: flex;
    flex-direction: column;
    height: 100%;
}
.kanban-add-card input,
.kanban-add-card select {
    background: #f3f4f6;
    border: 1px solid #d1d5db;
}
.kanban-add-card input:focus,
.kanban-add-card select:focus {
    outline: 2px solid #3b82f6;
}
.kanban-container {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

.kanban-container::-webkit-scrollbar {
    height: 8px;
}

.kanban-container::-webkit-scrollbar-track {
    background: transparent;
}

.kanban-container::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 4px;
}

.task-list {
    flex: 1 1 auto;
    min-height: 0;
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

.task-list::-webkit-scrollbar {
    width: 6px;
}

.task-list::-webkit-scrollbar-track {
    background: transparent;
}

.task-list::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 3px;
}

.task-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.task-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.task-card.dragging {
    opacity: 0.5;
    transform: scale(0.95);
}

.column-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.task-list.drag-over {
    background-color: rgba(59, 130, 246, 0.1);
    border: 2px dashed #3b82f6;
}

.subtask-card {
    font-size: 0.95em;
    margin-left: 1rem;
    background: #f9fafb;
    border-left: 3px solid #3b82f6;
}

.subtask-row {
    background: transparent;
    border-left: 3px solid #3b82f6;
    margin-bottom: 2px;
    align-items: center;
}

</style>
