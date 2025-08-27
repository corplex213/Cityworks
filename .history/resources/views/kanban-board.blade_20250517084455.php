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
        </div>

        <!-- Review Column -->
        <div class="kanban-column bg-gray-100 dark:bg-gray-800 rounded-lg shadow-lg w-80 flex-shrink-0">
            <div class="column-header bg-red-500 text-white p-4 rounded-t-lg">
                <h3 class="text-lg font-semibold">Deffered</h3>
                <span class="task-count text-sm bg-red-600 px-2 py-1 rounded-full">0</span>
            </div>
            <div class="task-list p-4 space-y-4 min-h-[500px] max-h-[800px] overflow-y-auto" data-status="Review">
                <!-- Tasks will be dynamically added here -->
            </div>
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
        </div>
    </div>


<!-- Task Card Template -->
<template id="taskCardTemplate">
    <div class="task-card bg-white dark:bg-gray-700 rounded-lg shadow p-4 cursor-move" draggable="true">
        <div class="task-header flex justify-between items-start mb-2">
            <h4 class="task-title text-lg font-semibold text-gray-800 dark:text-gray-200"></h4>
            <span class="priority-badge px-2 py-1 rounded-full text-xs font-medium"></span>
        </div>
        <div class="task-details text-sm text-gray-600 dark:text-gray-400">
            <div class="due-date mb-2">
                <i class="far fa-calendar-alt mr-1"></i>
                <span class="due-date-text"></span>
            </div>
            <div class="assigned-to flex items-center">
                <div class="avatar w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs mr-2"></div>
                <span class="assigned-name"></span>
            </div>
        </div>
    </div>
</template>

<style>
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Kanban functionality
    initializeKanban();
});

function initializeKanban() {
    // Make task lists droppable
    document.querySelectorAll('.task-list').forEach(list => {
        list.addEventListener('dragover', e => {
            e.preventDefault();
            list.classList.add('drag-over');
        });

        list.addEventListener('dragleave', () => {
            list.classList.remove('drag-over');
        });

        list.addEventListener('drop', e => {
            e.preventDefault();
            list.classList.remove('drag-over');
            
            const taskId = e.dataTransfer.getData('text/plain');
            const taskCard = document.querySelector(`[data-task-id="${taskId}"]`);
            const newStatus = list.getAttribute('data-status');
            
            if (taskCard) {
                // Update task status in the database
                updateTaskStatus(taskId, newStatus);
                
                // Move the task card to the new list
                list.appendChild(taskCard);
                
                // Update task counts
                updateTaskCounts();
            }
        });
    });
}

function updateTaskStatus(taskId, newStatus) {
    fetch(`/tasks/${taskId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Task status updated:', data);
    })
    .catch(error => {
        console.error('Error updating task status:', error);
    });
}

function updateTaskCounts(containerSelector = '') {
    document.querySelectorAll(`${containerSelector} .task-list`).forEach(list => {
        const count = list.children.length;
        const countElement = list.previousElementSibling.querySelector('.task-count');
        countElement.textContent = count;
    });
}

// Function to load tasks into Kanban board
window.loadTasksIntoKanban = function(tasks, userId) {
    // Only keep tasks for this user
    const userTasks = tasks.filter(task => task.assigned_user && String(task.assigned_user.id) === String(userId));

    // Clear existing tasks in this user's Kanban
    document.querySelectorAll(`#kanban-${userId} .task-list`).forEach(list => {
        list.innerHTML = '';
    });

    // Status mapping
    function mapStatus(status) {
        switch (status) {
            case 'For Checking': return 'To Do';
            case 'For Revision': return 'In Progress';
            case 'Deferred': return 'Review';
            case 'Completed': return 'Done';
            default: return 'To Do';
        }
    }

    // Add tasks to appropriate columns
    userTasks.forEach(task => {
        const mappedStatus = mapStatus(task.status);
        const taskCard = createTaskCard({ ...task, status: mappedStatus });
        const targetList = document.querySelector(`#kanban-${userId} .task-list[data-status="${mappedStatus}"]`);
        if (targetList) {
            targetList.appendChild(taskCard);
        }
    });

    // Update task counts for this user's Kanban
    updateTaskCounts(`#kanban-${userId}`);
};

function createTaskCard(task) {
    const template = document.getElementById('taskCardTemplate');
    const taskCard = template.content.cloneNode(true).querySelector('.task-card');
    
    // Set task data
    taskCard.setAttribute('data-task-id', task.id);
    taskCard.querySelector('.task-title').textContent = task.task_name;
    
    // Set priority badge
    const priorityBadge = taskCard.querySelector('.priority-badge');
    priorityBadge.textContent = task.priority;
    getPriorityColor(task.priority).split(' ').forEach(cls => priorityBadge.classList.add(cls));
    
    // Set due date
    const dueDate = new Date(task.due_date);
    taskCard.querySelector('.due-date-text').textContent = dueDate.toLocaleDateString();
    
    // Set assigned user
    const avatar = taskCard.querySelector('.avatar');
    const assignedName = taskCard.querySelector('.assigned-name');
    if (task.assigned_user) {
        avatar.textContent = task.assigned_user.name.charAt(0);
        assignedName.textContent = task.assigned_user.name;
    }
    
    // Add drag functionality
    taskCard.addEventListener('dragstart', e => {
        e.dataTransfer.setData('text/plain', task.id);
        taskCard.classList.add('dragging');
    });
    
    taskCard.addEventListener('dragend', () => {
        taskCard.classList.remove('dragging');
    });
    
    // Add click handler to open task details
    taskCard.addEventListener('click', () => {
        openTaskDetails(task);
    });
    
    return taskCard;
}

function getPriorityColor(priorityOrStatus) {
    switch ((priorityOrStatus || '').toLowerCase()) {
        case 'high':
            return 'bg-red-500 text-white';
        case 'normal':
            return 'bg-yellow-500 text-white';
        case 'low':
            return 'bg-green-500 text-white';
        // Status colors
        case 'completed':
            return 'bg-green-500 text-white';
        case 'for checking':
            return 'bg-blue-500 text-white';
        case 'for revision':
            return 'bg-yellow-500 text-white';
        case 'deferred':
            return 'bg-red-500 text-white';
        default:
            return 'bg-gray-500 text-white';
    }
}

</script> 