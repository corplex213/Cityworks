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
    <div class="task-card bg-white dark:bg-gray-700 rounded-lg shadow p-4 cursor-move" draggable="true">
        <h4 class="task-title text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2"></h4>
        <div class="task-fields space-y-1 mb-2"></div>
        <button class="toggle-subtasks-btn mt-2 text-xs text-blue-500 hover:underline flex items-center" type="button">
            <span>Show Subtasks</span>
            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="subtasks-list mt-2 hidden"></div>
        <button class="add-kanban-subtask-btn mt-2 text-xs text-green-600 hover:underline flex items-center" type="button">
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
.subtask-checkbox {
    accent-color: #3b82f6;
}
</style>

<script>
    const PROJECT_ID = {{ $project->id }};
    window.projectType = @json($project->proj_type);
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
    const userTasks = tasks.filter(task => task.assigned_user && String(task.assigned_user.id) === String(userId) && !task.parent_task_id);

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

    // --- Subtasks Section ---
    const subtasksList = taskCard.querySelector('.subtasks-list');
    let subtasksVisible = false;
    
    function renderSubtasks(subtasks) {
        subtasksList.innerHTML = '';
        if (!subtasks || !Array.isArray(subtasks) || subtasks.length === 0) {
            subtasksList.innerHTML = '<div class="text-xs text-gray-400 ml-4">No subtasks yet.</div>';
            return;
        }
        subtasks.forEach(subtask => {
            const subtaskDiv = document.createElement('div');
            subtaskDiv.className = 'flex items-center space-x-2 subtask-row py-1 px-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800';
            subtaskDiv.innerHTML = `
                <input type="checkbox" class="subtask-checkbox" ${subtask.status === 'Completed' ? 'checked' : ''} disabled>
                <span class="flex-1 text-sm ${subtask.status === 'Completed' ? 'line-through text-gray-400' : ''}">${subtask.task_name}</span>
                <span class="text-xs px-2 py-1 rounded-full ${getPriorityColor(subtask.priority)}">${subtask.priority}</span>
                <span class="text-xs text-gray-500">${subtask.due_date ? new Date(subtask.due_date).toLocaleDateString() : ''}</span>
            `;
            subtasksList.appendChild(subtaskDiv);
        });
    }
    // Initial render
    renderSubtasks(task.subtasks);
    // Toggle subtasks
    const toggleBtn = taskCard.querySelector('.toggle-subtasks-btn');
    const updateToggleBtn = () => {
        const count = task.subtasks && Array.isArray(task.subtasks) ? task.subtasks.length : 0;
        toggleBtn.querySelector('span').textContent = (subtasksVisible ? 'Hide Subtasks' : 'Show Subtasks') + (count ? ` (${count})` : '');
    };
    toggleBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        subtasksVisible = !subtasksVisible;
        subtasksList.classList.toggle('hidden', !subtasksVisible);
        updateToggleBtn();
    });
    // Start hidden if no subtasks
    subtasksVisible = false;
    subtasksList.classList.add('hidden');
    updateToggleBtn();

    // --- Add Subtask Inline Form ---
    const addSubtaskBtn = taskCard.querySelector('.add-kanban-subtask-btn');
    addSubtaskBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (taskCard.querySelector('.kanban-subtask-form')) return;
        const form = document.createElement('form');
        form.className = 'kanban-subtask-form bg-gray-50 dark:bg-gray-900 p-3 rounded mt-2 flex flex-col space-y-2 border border-gray-300 dark:border-gray-700 ml-4';
        form.innerHTML = `
            <input type="text" class="subtask-title-input w-full p-2 rounded border" placeholder="Subtask name" required>
            <input type="date" class="subtask-start-date-input w-full p-2 rounded border" placeholder="Start Date">
            <input type="date" class="subtask-due-date-input w-full p-2 rounded border" placeholder="Due Date">
            <select class="subtask-priority-input w-full p-2 rounded border">
                <option value="High">High</option>
                <option value="Normal" selected>Normal</option>
                <option value="Low">Low</option>
            </select>
            <input type="text" class="subtask-budget-input w-full p-2 rounded border" placeholder="Budget" inputmode="decimal">
            <div class="flex justify-end space-x-2">
                <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded">Save</button>
                <button type="button" class="cancel-subtask-btn bg-gray-400 text-white px-3 py-1 rounded">Cancel</button>
            </div>
        `;
        subtasksList.parentNode.insertBefore(form, addSubtaskBtn);
        // Cancel handler
        form.querySelector('.cancel-subtask-btn').onclick = function() { form.remove(); };
        // Budget validation
        form.querySelector('.subtask-budget-input').addEventListener('input', function() {
            let value = this.value.replace(/[^0-9.]/g, '');
            const parts = value.split('.');
            if (parts.length > 2) value = parts[0] + '.' + parts.slice(1).join('');
            if (parts.length === 2 && parts[1].length > 2) value = parts[0] + '.' + parts[1].substring(0, 2);
            this.value = value;
        });
        // Save handler
        form.onsubmit = function(ev) {
            ev.preventDefault();
            // Map Kanban status (display) to backend status value
            let backendStatus = '';
            switch (task.status) {
                case 'To Do': backendStatus = 'For Checking'; break;
                case 'In Progress': backendStatus = 'For Revision'; break;
                case 'Review': backendStatus = 'Deferred'; break;
                case 'Done': backendStatus = 'Completed'; break;
                default: backendStatus = 'For Checking';
            }
            const subtaskData = {
                parent_task_id: task.id,
                project_id: PROJECT_ID,
                assigned_to: task.assigned_user?.id || null,
                task_name: form.querySelector('.subtask-title-input').value,
                start_date: form.querySelector('.subtask-start-date-input').value,
                due_date: form.querySelector('.subtask-due-date-input').value,
                priority: form.querySelector('.subtask-priority-input').value,
                status: backendStatus,
                budget: form.querySelector('.subtask-budget-input').value
            };
            if (window.projectType === 'POW') {
                subtaskData.source_of_funding = task.source_of_funding || null;
                subtaskData.other_funding_source = task.other_funding_source || null;
            }
            fetch('/tasks', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(subtaskData)
            })
            .then(res => res.json())            .then(data => {
                // Add new subtask to the list and re-render
                if (!task.subtasks) task.subtasks = [];
                const subtask = data.task ? data.task : data;
                task.subtasks.push(subtask);
                renderSubtasks(task.subtasks);
                subtasksVisible = true;
                subtasksList.classList.remove('hidden');
                updateToggleBtn();
                form.remove();
            })
            .catch(err => alert('Error saving subtask!'));
        };
    });
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

// Add a new task card in the Kanban column
function addKanbanTask(userId, statusKey) {
    const taskList = document.querySelector(`#kanban-${userId} .task-list[data-status="${statusKey}"]`);
    if (!taskList) return;

    // Prevent multiple add forms
    if (taskList.querySelector('.kanban-add-card')) return;

    // Build the card HTML (no Status field)
    let cardHtml = `
        <div class="kanban-add-card bg-white dark:bg-gray-700 rounded-lg shadow p-4 mb-2 flex flex-col space-y-2">
            <input type="text" class="task-title-input w-full p-2 rounded border mb-2" placeholder="Enter Task">
            <input type="date" class="start-date-input w-full p-2 rounded border mb-2" placeholder="Start Date">
            <input type="date" class="due-date-input w-full p-2 rounded border mb-2" placeholder="Due Date">
            <div class="flex space-x-2">
                <div class="flex-1">
                    <label class="block text-xs mb-1">Priority</label>
                    <select class="priority-input w-full p-2 rounded border">
                        <option value="High">High</option>
                        <option value="Normal" selected>Normal</option>
                        <option value="Low">Low</option>
                    </select>
                </div>
            </div>
            <input type="text" class="budget-input w-full p-2 rounded border mb-2" placeholder="Enter Budget" inputmode="decimal">
            ${window.projectType === 'POW' ? `
            <div>
                <label class="block text-xs mb-1">Source of Funding</label>
                <select class="source-of-funding-input w-full p-2 rounded border mb-2" onchange="handleKanbanSourceOfFundingChange(this)">
                    <option value="">Select</option>
                    <option value="DRRM-F">DRRM-F</option>
                    <option value="LDF">LDF</option>
                    <option value="NTA">NTA</option>
                    <option value="For funding">For funding</option>
                    <option value="Others">Others</option>
                </select>
                <div class="other-funding-source hidden mt-2">
                    <input type="text" name="other_funding_source" class="w-full p-2 rounded border" placeholder="Please specify">
                </div>
            </div>
            ` : ''}
            <div class="flex justify-end space-x-2 mt-2">
                <button class="save-btn bg-green-500 text-white px-3 py-1 rounded">Save</button>
                <button class="cancel-btn bg-gray-400 text-white px-3 py-1 rounded">Cancel</button>
            </div>
        </div>
    `;

    // Create the card element
    const card = document.createElement('div');
    card.innerHTML = cardHtml;
    const cardEl = card.firstElementChild;

    // Budget validation (same as main table)
    cardEl.querySelector('.budget-input').addEventListener('input', function() {
        let value = this.value.replace(/[^0-9.]/g, '');
        const parts = value.split('.');
        if (parts.length > 2) value = parts[0] + '.' + parts.slice(1).join('');
        if (parts.length === 2 && parts[1].length > 2) value = parts[0] + '.' + parts[1].substring(0, 2);
        this.value = value;
    });

    // Source of funding handler for POW
    if (window.projectType === 'POW') {
        cardEl.querySelector('.source-of-funding-input').addEventListener('change', function() {
            const otherDiv = cardEl.querySelector('.other-funding-source');
            if (this.value === 'Others') {
                this.style.display = 'none';
                otherDiv.classList.remove('hidden');
                const otherInput = otherDiv.querySelector('input[name="other_funding_source"]');
                otherInput.focus();
                otherInput.ondblclick = () => {
                    this.style.display = '';
                    otherDiv.classList.add('hidden');
                    this.value = '';
                };
            } else {
                otherDiv.classList.add('hidden');
                this.style.display = '';
            }
        });
    }

    // Save handler
    cardEl.querySelector('.save-btn').onclick = function() {
        // Map statusKey to status value
        let statusValue = '';
        switch (statusKey) {
            case 'To Do': statusValue = 'For Checking'; break;
            case 'In Progress': statusValue = 'For Revision'; break;
            case 'Review': statusValue = 'Deferred'; break;
            case 'Done': statusValue = 'Completed'; break;
            default: statusValue = 'For Checking';
        }

        const taskData = {
            project_id: PROJECT_ID,
            assigned_to: userId,
            task_name: cardEl.querySelector('.task-title-input').value,
            start_date: cardEl.querySelector('.start-date-input').value,
            due_date: cardEl.querySelector('.due-date-input').value,
            priority: cardEl.querySelector('.priority-input').value,
            status: statusValue,
            budget: cardEl.querySelector('.budget-input').value
        };
        if (window.projectType === 'POW') {
            taskData.source_of_funding = cardEl.querySelector('.source-of-funding-input').value;
            taskData.other_funding_source = cardEl.querySelector('.other-funding-source input')?.value || null;
        }

        fetch('/tasks', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(taskData)
        })
        .then(res => res.json())
        .then(data => {
            if (typeof loadExistingTasks === 'function') loadExistingTasks();
        })
        .catch(err => alert('Error saving task!'));
    };

    // Cancel handler
    cardEl.querySelector('.cancel-btn').onclick = function() {
        cardEl.remove();
    };

    taskList.prepend(cardEl);
}

// Helper for POW source of funding in Kanban
function handleKanbanSourceOfFundingChange(select) {
    const card = select.closest('.kanban-add-card');
    const otherDiv = card.querySelector('.other-funding-source');
    if (select.value === 'Others') {
        select.style.display = 'none';
        otherDiv.classList.remove('hidden');
        const otherInput = otherDiv.querySelector('input[name="other_funding_source"]');
        otherInput.focus();
        otherInput.ondblclick = function () {
            select.style.display = '';
            otherDiv.classList.add('hidden');
            select.value = '';
        };
    } else {
        otherDiv.classList.add('hidden');
        select.style.display = '';
    }
}
</script>