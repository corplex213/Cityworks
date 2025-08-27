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
    taskCard.setAttribute('draggable', 'true');

    taskCard.addEventListener('dragstart', (e) => {
        e.dataTransfer.setData('text/plain', task.id);
        taskCard.classList.add('dragging');
        
        // Add highlight class to valid drop targets
        document.querySelectorAll('.task-list').forEach(list => {
            list.classList.add('valid-drop-target');
        });
    });

    taskCard.addEventListener('dragend', () => {
        taskCard.classList.remove('dragging');
        
        // Remove highlight from drop targets
        document.querySelectorAll('.task-list').forEach(list => {
            list.classList.remove('valid-drop-target');
        });
    });

    taskCard.addEventListener('click', (e) => {
        // If you want some visual feedback when clicking
        taskCard.classList.add('task-selected');
        setTimeout(() => {
            taskCard.classList.remove('task-selected');
        }, 200);
        
        // Prevent opening the drawer
        e.stopPropagation();
    });

    // Set priority badge
    const priorityBadge = taskCard.querySelector('.priority-badge');
    priorityBadge.textContent = task.priority;
    getPriorityColor(task.priority).split(' ').forEach(cls => priorityBadge.classList.add(cls));


    // Render fields as labeled rows (except status)
        const fieldsDiv = taskCard.querySelector('.task-fields');
        fieldsDiv.innerHTML = `
        <div class="flex justify-between items-center">
            <span class="text-xs text-gray-400">Start Date</span>
            <span class="text-sm text-gray-200">${task.start_date ? new Date(task.start_date).toLocaleDateString() : ''}</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-xs text-gray-400">Due Date</span>
            <span class="text-sm text-gray-200">${task.due_date ? new Date(task.due_date).toLocaleDateString() : ''}</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-xs text-gray-400">Budget</span>
            <span class="text-sm text-gray-200 budget-value">
                ₱${getTotalBudget(task).toLocaleString('en-US', {minimumFractionDigits: 2})}
            </span>
        </div>
        ${window.projectType === 'POW' ? `
        <div class="flex justify-between items-center">
            <span class="text-xs text-gray-400">Source of Funding</span>
            <span class="text-sm text-gray-200">${task.source_of_funding ?? ''}${task.source_of_funding === 'Others' && task.other_funding_source ? ' (' + task.other_funding_source + ')' : ''}</span>
        </div>
        ` : ''}
    `;

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
                <span class="flex-1 text-sm font-bold text-white">${subtask.task_name}</span>
                <span class="text-xs px-2 py-1 rounded-full ${getPriorityColor(subtask.priority)}">${subtask.priority}</span>
                <span class="text-xs text-gray-500">${subtask.due_date ? new Date(subtask.due_date).toLocaleDateString() : ''}</span>
            `;
            subtaskDiv.addEventListener('click', function(e) {
                e.stopPropagation();
                openTaskDetailsFromKanban(subtask);
            });
            subtasksList.appendChild(subtaskDiv);
        });
        subtaskDiv.addEventListener('dragstart', (e) => {
            e.preventDefault();
            e.stopPropagation();
        });
    }
    // Initial render
    renderSubtasks(task.subtasks);
    updateBudgetDisplay(taskCard, task);

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
        form.className = 'kanban-subtask-form p-2 mt-2 flex flex-col space-y-2 bg-gray-800 border-0 shadow-none rounded';
        form.innerHTML = `
            <input type="text" class="subtask-title-input w-full p-2 rounded border bg-gray-900 text-white" placeholder="Subtask name" required>
            <input type="date" class="subtask-start-date-input w-full p-2 rounded border bg-gray-900 text-white" placeholder="Start Date">
            <input type="date" class="subtask-due-date-input w-full p-2 rounded border bg-gray-900 text-white" placeholder="Due Date">
            <select class="subtask-priority-input w-full p-2 rounded border bg-gray-900 text-white">
                <option value="High">High</option>
                <option value="Normal" selected>Normal</option>
                <option value="Low">Low</option>
            </select>
            <input type="text" class="subtask-budget-input w-full p-2 rounded border bg-gray-900 text-white" placeholder="Budget" inputmode="decimal">
            ${window.projectType === 'POW' ? `
            <div>
                <label class="block text-xs mb-1 text-gray-400">Source of Funding</label>
                <select class="source-of-funding-input w-full p-2 rounded border mb-2 bg-gray-900 text-white">
                    <option value="">Select</option>
                    <option value="DRRM-F">DRRM-F</option>
                    <option value="LDF">LDF</option>
                    <option value="NTA">NTA</option>
                    <option value="For funding">For funding</option>
                    <option value="Others">Others</option>
                </select>
                <div class="other-funding-source hidden mt-2">
                    <input type="text" name="other_funding_source" class="w-full p-2 rounded border bg-gray-900 text-white" placeholder="Please specify">
                </div>
            </div>
            ` : ''}
            <div class="flex justify-end space-x-2">
                <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded">Save</button>
                <button type="button" class="cancel-subtask-btn bg-gray-400 text-white px-3 py-1 rounded">Cancel</button>
            </div>
        `;
        form.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        form.querySelectorAll('input, select, button').forEach(el => {
            el.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
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
            // Get values
            const title = form.querySelector('.subtask-title-input').value.trim();
            const startDate = form.querySelector('.subtask-start-date-input').value;
            const dueDate = form.querySelector('.subtask-due-date-input').value;
            const priority = form.querySelector('.subtask-priority-input').value;
            const budget = form.querySelector('.subtask-budget-input').value.trim();

            // Validate required fields
            if (!title || !startDate || !dueDate || !priority || !budget) {
                alert('Please fill in all required subtask fields.');
                return;
            }
            // Validate date logic
            if (new Date(dueDate) < new Date(startDate)) {
                alert('Due date cannot be before start date.');
                return;
            }
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
                task_name: title,
                start_date: startDate,
                due_date: dueDate,
                priority: priority,
                status: backendStatus,
                budget: budget
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
                updateBudgetDisplay(taskCard, task);
            })
            .catch(err => alert('Error saving subtask!'));
        };
    });
    return taskCard;
}

function updateBudgetDisplay(taskCard, task) {
    const budgetSpan = taskCard.querySelector('.budget-value');
    if (budgetSpan) {
        budgetSpan.textContent = '₱' + getTotalBudget(task).toLocaleString('en-US', { minimumFractionDigits: 2 });
    }
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

    // --- Use the same form markup as Add Subtask ---
    const form = document.createElement('form');
    form.className = 'kanban-add-card kanban-subtask-form p-2 mt-2 flex flex-col space-y-2 bg-gray-800 border-0 shadow-none rounded';
    form.innerHTML = `
        <input type="text" class="subtask-title-input w-full p-2 rounded border bg-gray-900 text-white" placeholder="Task name" required>
        <input type="date" class="subtask-start-date-input w-full p-2 rounded border bg-gray-900 text-white">
        <input type="date" class="subtask-due-date-input w-full p-2 rounded border bg-gray-900 text-white">
        <select class="subtask-priority-input w-full p-2 rounded border bg-gray-900 text-white">
            <option value="High">High</option>
            <option value="Normal" selected>Normal</option>
            <option value="Low">Low</option>
        </select>
        <input type="text" class="subtask-budget-input w-full p-2 rounded border bg-gray-900 text-white" placeholder="Budget" inputmode="decimal">
        ${window.projectType === 'POW' ? `
        <div>
            <label class="block text-xs mb-1 text-gray-400">Source of Funding</label>
            <select class="source-of-funding-input w-full p-2 rounded border mb-2 bg-gray-900 text-white">
                <option value="">Select</option>
                <option value="DRRM-F">DRRM-F</option>
                <option value="LDF">LDF</option>
                <option value="NTA">NTA</option>
                <option value="For funding">For funding</option>
                <option value="Others">Others</option>
            </select>
            <div class="other-funding-source hidden mt-2">
                <input type="text" name="other_funding_source" class="w-full p-2 rounded border bg-gray-900 text-white" placeholder="Please specify">
            </div>
        </div>
        ` : ''}
        <div class="flex justify-end space-x-2">
            <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded">Save</button>
            <button type="button" class="cancel-subtask-btn bg-gray-400 text-white px-3 py-1 rounded">Cancel</button>
        </div>
    `;

    // Prevent click bubbling
    form.addEventListener('click', function(e) { e.stopPropagation(); });
    form.querySelectorAll('input, select, button').forEach(el => {
        el.addEventListener('click', function(e) { e.stopPropagation(); });
    });

    // Budget validation
    form.querySelector('.subtask-budget-input').addEventListener('input', function() {
        let value = this.value.replace(/[^0-9.]/g, '');
        const parts = value.split('.');
        if (parts.length > 2) value = parts[0] + '.' + parts.slice(1).join('');
        if (parts.length === 2 && parts[1].length > 2) value = parts[0] + '.' + parts[1].substring(0, 2);
        this.value = value;
    });

    // Source of funding handler for POW
    if (window.projectType === 'POW') {
        const sourceSelect = form.querySelector('.source-of-funding-input');
        const otherDiv = form.querySelector('.other-funding-source');
        sourceSelect.addEventListener('change', function() {
            if (this.value === 'Others') {
                this.style.display = 'none';
                otherDiv.classList.remove('hidden');
                const otherInput = otherDiv.querySelector('input[name="other_funding_source"]');
                otherInput.focus();
                otherInput.ondblclick = () => {
                    sourceSelect.style.display = '';
                    otherDiv.classList.add('hidden');
                    sourceSelect.value = '';
                };
            } else {
                otherDiv.classList.add('hidden');
                sourceSelect.style.display = '';
            }
        });
    }

    // Cancel handler
    form.querySelector('.cancel-subtask-btn').onclick = function() { form.remove(); };

    // Save handler
    form.onsubmit = function(ev) {
        ev.preventDefault();
        // Get values
        const title = form.querySelector('.subtask-title-input').value.trim();
        const startDate = form.querySelector('.subtask-start-date-input').value;
        const dueDate = form.querySelector('.subtask-due-date-input').value;
        const priority = form.querySelector('.subtask-priority-input').value;
        const budget = form.querySelector('.subtask-budget-input').value.trim();

        // Validate required fields
        if (!title || !startDate || !dueDate || !priority || !budget) {
            alert('Please fill in all required fields.');
            return;
        }
        // Validate date logic
        if (new Date(dueDate) < new Date(startDate)) {
            alert('Due date cannot be before start date.');
            return;
        }
        // Map statusKey to backend status
        let backendStatus = '';
        switch (statusKey) {
            case 'To Do': backendStatus = 'For Checking'; break;
            case 'In Progress': backendStatus = 'For Revision'; break;
            case 'Review': backendStatus = 'Deferred'; break;
            case 'Done': backendStatus = 'Completed'; break;
            default: backendStatus = 'For Checking';
        }
        const taskData = {
            project_id: PROJECT_ID,
            assigned_to: userId,
            task_name: title,
            start_date: startDate,
            due_date: dueDate,
            priority: priority,
            status: backendStatus,
            budget: budget
        };
        if (window.projectType === 'POW') {
            taskData.source_of_funding = form.querySelector('.source-of-funding-input').value;
            taskData.other_funding_source = form.querySelector('.other-funding-source input')?.value || null;
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

    taskList.prepend(form);
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

function filterKanbanTasks() {
    const searchInput = document.getElementById('taskSearchInput');
    if (!searchInput) return;
    const searchTerm = searchInput.value.toLowerCase();

    // For each user's kanban
    document.querySelectorAll('.kanban-container').forEach(container => {
        // For each column in the kanban
        container.querySelectorAll('.task-list').forEach(list => {
            let visibleCount = 0;
            list.querySelectorAll('.task-card').forEach(card => {
                const title = card.querySelector('.task-title')?.textContent.toLowerCase() || '';
                if (title.includes(searchTerm)) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            // Optionally, update the task count badge
            const countElement = list.previousElementSibling.querySelector('.task-count');
            if (countElement) countElement.textContent = visibleCount;
        });
    });
}

function getTotalBudget(task) {
    let total = parseFloat(task.budget) || 0;
    if (Array.isArray(task.subtasks)) {
        for (const sub of task.subtasks) {
            total += parseFloat(sub.budget) || 0;
        }
    }
    return total;
}


// Attach to input event
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('taskSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', filterKanbanTasks);
    }
});

window.addKanbanTask = addKanbanTask;
window.handleKanbanSourceOfFundingChange = handleKanbanSourceOfFundingChange;
// Add others as needed