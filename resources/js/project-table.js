const selectedUsers = new Set();
window.selectedUsers = selectedUsers; 

function addUserTable(userName, userId) {
    createTableForUser(userName, userId, []);
    const tableWrapper = document.querySelector(`[data-user-id="${userId}"]`);
    const tbody = tableWrapper.querySelector('tbody');
    addNewRow(tbody);
}
const PRIORITY_OPTIONS = `
    <option value="High">High</option>
    <option value="Normal">Normal</option>
    <option value="Low">Low</option>
`;

const STATUS_OPTIONS = `
    <option value="Completed">Completed</option>
    <option value="For Checking">For Checking</option>
    <option value="For Revision">For Revision</option>
    <option value="Deferred">Deferred</option>
`;

const FUNDING_OPTIONS = `
    <option value="">Select</option>
    <option value="DRRM-F">DRRM-F</option>
    <option value="LDF">LDF</option>
    <option value="NTA">NTA</option>
    <option value="For funding">For funding</option>
    <option value="Others">Others</option>
`;

const projectType = window.projectType;
// Function to create table for a user with their tasks
    function createTableForUser(userName, userId, tasks) {
        const container = document.getElementById('dynamicTablesContainer');
        const placeholder = document.getElementById('placeholder');
        placeholder?.remove();

        const tableWrapper = document.createElement('div');
        tableWrapper.className = 'user-table-wrapper mb-12 mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden';
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
            saveButton.className = 'flex items-center text-green-500 hover:text-green-700 font-medium mr-2 text-base transition-colors duration-150';
            saveButton.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M17 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h10zm-1 2H8v2h8V5zm-1 4H9v10h6V9z" fill="currentColor"/>
                </svg>
                Save
            `;
            saveButton.onclick = () => saveTask(tableWrapper);

            const deleteButton = document.createElement('button');
                deleteButton.id = `deleteTableBtn-${userId}`;
                deleteButton.className = 'flex items-center text-red-500 hover:text-red-700 font-medium text-base transition-colors duration-150';
                // Add not-allowed cursor and opacity if no permission
                if (!window.CAN_DELETE_TASKS) {
                    deleteButton.classList.add('cursor-not-allowed', 'opacity-50');
                    deleteButton.style.pointerEvents = 'none';
                }
                deleteButton.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M3 6h18M8 6v12a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V6m-6 0V4a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Delete
                `;
                deleteButton.onclick = () => {
                    if (!window.CAN_DELETE_TASKS) return; // Prevent action if no permission
                    if (confirm(`Are you sure you want to delete the task table for ${userName}?`)) {
                        const projectId = window.PROJECT_ID;
                        fetch(`/projects/${projectId}/users/${userId}/tasks`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
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
                                location.reload();
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
            <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[450px]">Task</th>
            <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[120px]">Start Date</th>
            <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[120px]">Due Date</th>
            <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[220px]">Priority</th>
            <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[220px]">Status</th>
            ${(projectType === 'POW' && window.CURRENT_USER_ROLE !== 'Staff') ? `
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[220px]">Budget</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[220px]">Source of Funding</th>
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

        let rowHtml = `
            <td class="task-column px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[450px] cursor-pointer group"
                ${task.id ? `onclick="if(!event.target.closest('input') && !event.target.closest('.subtask-btn') && !event.target.closest('.delete-subtask-btn')) openTaskDetails(this.closest('tr'))"` : ''}>
                <div class="flex items-center justify-between">
                    <input type="text" class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2" value="${task.task_name}">
                    <div class="flex items-center ml-2">
                        ${task.subtasks && task.subtasks.length > 0 ? `
                            <button class="subtask-toggle-btn mr-2 text-gray-500 hover:text-gray-400 transition-colors duration-150 opacity-0 group-hover:opacity-100"
                                    onclick="toggleSubtasks(this.closest('tr'), event)"
                                    title="Toggle Subtasks"
                                    style="transition: opacity 0.2s;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform rotate-180 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        ` : ''}
                        <button class="subtask-btn text-blue-500 hover:text-blue-700 transition-colors duration-150" onclick="addSubtask(this.closest('tr'))" title="Add Subtask">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                <input type="date" class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2" value="${task.start_date}">
            </td>
            <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                <input type="date" class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2" value="${task.due_date}">
            </td>
            <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                <div class="priority-cell w-full ${getColor('priority', task.priority)} text-white rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition font-medium cursor-pointer" onclick="editPriority(this)">
                    <span class="priority-value">${task.priority}</span>
                </div>
            </td>
            <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                <div class="status-cell w-full ${getColor('status', task.status)} text-white rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition cursor-pointer" onclick="editStatus(this)">
                    <span class="status-value">${task.status}</span>
                </div>
            </td>
        `;
        if (projectType === 'POW' && window.CURRENT_USER_ROLE !== 'Staff') {
            rowHtml += `
            <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                <input type="text" inputmode="decimal" placeholder="Enter Budget" value="${task.budget}" oninput="updateTotalBudget(this)" onblur="updateTotalBudget(this)" class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2">
            </td>
            <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                <select name="source_of_funding" class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition" onchange="handleSourceOfFundingChange(this)">
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
            const taskNameInput = row.querySelector('td:first-child input');
            const startDateInput = row.querySelector('td:nth-child(2) input');
            const dueDateInput = row.querySelector('td:nth-child(3) input');
            const priorityElement = row.querySelector('.priority-value');
            const statusElement = row.querySelector('.status-value');
            const budgetInput = row.querySelector('td:nth-child(6) input');
            if (taskNameInput) taskNameInput.setAttribute('data-old-value', taskNameInput.value);
            if (startDateInput) startDateInput.setAttribute('data-old-value', startDateInput.value);
            if (dueDateInput) dueDateInput.setAttribute('data-old-value', dueDateInput.value);
            if (priorityElement) priorityElement.setAttribute('data-old-value', priorityElement.textContent);
            if (statusElement) statusElement.setAttribute('data-old-value', statusElement.textContent);
            if (budgetInput) budgetInput.setAttribute('data-old-value', budgetInput.value);
            if (projectType === 'POW' && window.CURRENT_USER_ROLE !== 'Staff') {
                const sourceSelect = row.querySelector('td:nth-child(7) select[name="source_of_funding"]');
                const otherInput = row.querySelector('td:nth-child(7) input[name="other_funding_source"]');
                // Set the select value from the saved data
                if (sourceSelect && typeof task.source_of_funding !== 'undefined') {
                    sourceSelect.value = task.source_of_funding || '';
                    // If "Others" is selected, show the input
                    if (task.source_of_funding === 'Others' && otherInput && typeof task.other_funding_source !== 'undefined') {
                        sourceSelect.style.display = 'none';
                        const otherDiv = row.querySelector('.other-funding-source');
                        if (otherDiv) otherDiv.classList.remove('hidden');
                        otherInput.value = task.other_funding_source || '';
                        // Attach double-click handler after reload
                        otherInput.ondblclick = () => {
                            sourceSelect.style.display = '';
                            otherDiv.classList.add('hidden');
                            sourceSelect.value = '';
                            sourceSelect.focus();
                        };
                        // Optional: add a tooltip
                        otherInput.title = "Double-click to change Source of Funding";
                    }
                }
                row.setAttribute('data-old-source_of_funding', sourceSelect ? sourceSelect.value : '');
                row.setAttribute('data-old-other_funding_source', otherInput ? otherInput.value : '');
            }

            // Add subtasks if they exist
            if (task.subtasks && task.subtasks.length > 0) {
            task.subtasks.forEach(subtask => {
                const subtaskRow = document.createElement('tr');
                subtaskRow.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 subtask-row hidden';
                subtaskRow.setAttribute('data-task-id', subtask.id);

                let subtaskHtml = `
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
                `;
                if (projectType === 'POW' && window.CURRENT_USER_ROLE !== 'Staff') {
                    subtaskHtml += `
                    <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600 text-sm text-gray-900 dark:text-gray-200">
                        <input 
                            type="text"
                            inputmode="decimal"
                            placeholder="Enter Budget" 
                            value="${subtask.budget}"
                            data-old-value="${subtask.budget}"
                            oninput="updateTotalBudget(this); updateOldValue(this)" 
                            class="w-full bg-transparent rounded-lg outline-none text-inherit">
                    </td>
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
                subtaskRow.innerHTML = subtaskHtml;
                tbody.appendChild(subtaskRow);
                    const subtaskNameInput = subtaskRow.querySelector('td:first-child input');
                    const subtaskStartDateInput = subtaskRow.querySelector('td:nth-child(2) input');
                    const subtaskDueDateInput = subtaskRow.querySelector('td:nth-child(3) input');
                    const subtaskPriorityElement = subtaskRow.querySelector('.priority-value');
                    const subtaskStatusElement = subtaskRow.querySelector('.status-value');
                    const subtaskBudgetInput = subtaskRow.querySelector('td:nth-child(6) input');
                    if (subtaskNameInput) subtaskNameInput.setAttribute('data-old-value', subtaskNameInput.value);
                    if (subtaskStartDateInput) subtaskStartDateInput.setAttribute('data-old-value', subtaskStartDateInput.value);
                    if (subtaskDueDateInput) subtaskDueDateInput.setAttribute('data-old-value', subtaskDueDateInput.value);
                    if (subtaskPriorityElement) subtaskPriorityElement.setAttribute('data-old-value', subtaskPriorityElement.textContent);
                    if (subtaskStatusElement) subtaskStatusElement.setAttribute('data-old-value', subtaskStatusElement.textContent);
                    if (subtaskBudgetInput) subtaskBudgetInput.setAttribute('data-old-value', subtaskBudgetInput.value);
                    if (projectType === 'POW' && window.CURRENT_USER_ROLE !== 'Staff') {
                        const subtaskSourceSelect = subtaskRow.querySelector('td:nth-child(7) select[name="source_of_funding"]');
                        const subtaskOtherInput = subtaskRow.querySelector('td:nth-child(7) input[name="other_funding_source"]');
                        // Set the select value from the saved data
                        if (subtaskSourceSelect && typeof subtask.source_of_funding !== 'undefined') {
                            subtaskSourceSelect.value = subtask.source_of_funding || '';
                            if (subtask.source_of_funding === 'Others' && subtaskOtherInput && typeof subtask.other_funding_source !== 'undefined') {
                                subtaskSourceSelect.style.display = 'none';
                                const otherDiv = subtaskRow.querySelector('.other-funding-source');
                                if (otherDiv) otherDiv.classList.remove('hidden');
                                subtaskOtherInput.value = subtask.other_funding_source || '';
                                // Attach double-click handler after reload
                                subtaskOtherInput.ondblclick = () => {
                                    subtaskSourceSelect.style.display = '';
                                    otherDiv.classList.add('hidden');
                                    subtaskSourceSelect.value = '';
                                    subtaskSourceSelect.focus();
                                };
                                // Optional: add a tooltip
                                subtaskOtherInput.title = "Double-click to change Source of Funding";
                            }
                        }
                        subtaskRow.setAttribute('data-old-source_of_funding', subtaskSourceSelect ? subtaskSourceSelect.value : '');
                        subtaskRow.setAttribute('data-old-other_funding_source', subtaskOtherInput ? subtaskOtherInput.value : '');
                    }
                });
            }
        });
        const totalColumns = projectType === 'POW' ? 7 : 5;
            if (window.CAN_CREATE_TASKS) {
                const addItemRow = document.createElement('tr');
                addItemRow.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 cursor-pointer';
                addItemRow.onclick = () => addNewRow(tbody);
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
        // Only create and append the footer if POW
        if (projectType === 'POW' && window.CURRENT_USER_ROLE !== 'Staff') {
            const totalBudget = calculateTotalBudget(tasks);
            const tfoot = document.createElement('tfoot');
            tfoot.className = 'bg-gray-100 dark:bg-gray-700';
            tfoot.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-4 text-right font-semibold text-gray-800 dark:text-gray-200 border-t border-gray-300 dark:border-gray-600">
                        Total Budget:
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-t border-gray-300 dark:border-gray-600">
                        <span class="total-budget font-semibold">₱${totalBudget.toLocaleString('en-US', { minimumFractionDigits: 2 })}</span>
                    </td>
                </tr>
            `;
            table.appendChild(tfoot);
        }

        table.appendChild(thead);
        table.appendChild(tbody);
        
        scrollableContainer.appendChild(table);
        tableWrapper.appendChild(header);
        tableWrapper.appendChild(scrollableContainer);
        container.appendChild(tableWrapper);
        if (!window.CAN_EDIT_TASKS) {
            tableWrapper.querySelectorAll('input, select').forEach(el => {
                el.disabled = true;
                el.classList.add('bg-gray-200', 'cursor-not-allowed');
            });
            // Disable priority/status editing
            tableWrapper.querySelectorAll('.priority-cell, .status-cell').forEach(cell => {
                cell.onclick = null;
                cell.classList.add('cursor-not-allowed', 'opacity-70');
            });
        }
    }

// Function to add a new row
    function addNewRow(tbody) {
        if (!window.CAN_CREATE_TASKS) {
            return;
        }
        const addItemRow = tbody.querySelector('tr:last-child');
        const totalColumns = projectType === 'POW' ? 7 : 6;

        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150';

        // Build the main columns
        let rowHtml = `
            <td class="task-column px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[450px] cursor-pointer">
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
            ${(projectType === 'POW' && window.CURRENT_USER_ROLE !== 'Staff') ? `
    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 ">
        <input 
            type="text"
            inputmode="decimal"
            placeholder="Enter Budget" 
            oninput="updateTotalBudget(this)" 
            onblur="updateTotalBudget(this)"
            class="w-full bg-transparent outline-none rounded-lg text-inherit">
    </td>
    ` : ''}
`;

        // Add Source of Funding column if POW
        if (projectType === 'POW' && window.CURRENT_USER_ROLE !== 'Staff') {
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
        const taskNameInput = row.querySelector('td:first-child input');
        const startDateInput = row.querySelector('td:nth-child(2) input');
        const dueDateInput = row.querySelector('td:nth-child(3) input');
        const priorityElement = row.querySelector('.priority-value');
        const statusElement = row.querySelector('.status-value');
        const budgetInput = row.querySelector('td:nth-child(6) input');
        if (taskNameInput) taskNameInput.setAttribute('data-old-value', taskNameInput.value);
        if (startDateInput) startDateInput.setAttribute('data-old-value', startDateInput.value);
        if (dueDateInput) dueDateInput.setAttribute('data-old-value', dueDateInput.value);
        if (priorityElement) priorityElement.setAttribute('data-old-value', priorityElement.textContent);
        if (statusElement) statusElement.setAttribute('data-old-value', statusElement.textContent);
        if (budgetInput) budgetInput.setAttribute('data-old-value', budgetInput.value);
        if (projectType === 'POW' && window.CURRENT_USER_ROLE !== 'Staff') {
            const sourceSelect = row.querySelector('td:nth-child(7) select[name="source_of_funding"]');
            const otherInput = row.querySelector('td:nth-child(7) input[name="other_funding_source"]');
            row.setAttribute('data-old-source_of_funding', sourceSelect ? sourceSelect.value : '');
            row.setAttribute('data-old-other_funding_source', otherInput ? otherInput.value : '');
        }

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
    if (!window.CAN_CREATE_TASKS) {
        alert('You do not have permission to add subtasks.');
        return;
    }
    // Check if the parent row is already a subtask
    if (parentRow.classList.contains('subtask-row')) {
        alert('Cannot add subtasks to subtasks');
        return;
    }
    
    // Check if there's already an empty subtask input
    let existingEmptySubtask = null;
    let nextRow = parentRow.nextElementSibling;
    let hasHiddenSubtasks = false;
    
    // Check all existing subtasks of this parent for empty ones
    // Also check if there are hidden subtasks
    while (nextRow && nextRow.classList.contains('subtask-row')) {
        if (nextRow.classList.contains('hidden')) {
            hasHiddenSubtasks = true;
        }
        
        const subtaskInput = nextRow.querySelector('td:first-child input');
        if (subtaskInput && (!subtaskInput.value || subtaskInput.value.trim() === '')) {
            existingEmptySubtask = subtaskInput;
        }
        nextRow = nextRow.nextElementSibling;
    }
    
    // If subtasks are hidden, show them first
    if (hasHiddenSubtasks) {
        const toggleBtn = parentRow.querySelector('.subtask-toggle-btn svg');
        if (toggleBtn && toggleBtn.classList.contains('rotate-180')) {
            // Force toggle subtasks to be visible
            toggleSubtasks(parentRow);
        }
    }
    
    // If there's an empty subtask input, focus on that instead of creating a new one
    if (existingEmptySubtask) {
        existingEmptySubtask.focus();
        return;
    }

    // Get the parent task's values
    const parentStartDate = parentRow.querySelector('td:nth-child(2) input').value;
    const parentDueDate = parentRow.querySelector('td:nth-child(3) input').value;
    const parentPriority = parentRow.querySelector('.priority-value').textContent;
    const parentStatus = parentRow.querySelector('.status-value').textContent;

    // Find the right insertion position - after the last subtask if any exist
    let insertAfter = parentRow;
    let hasExistingSubtasks = false;
    
    // Reset nextRow pointer
    nextRow = parentRow.nextElementSibling;
    
    // Find the last subtask row for this parent
    while (nextRow && nextRow.classList.contains('subtask-row')) {
        hasExistingSubtasks = true;
        insertAfter = nextRow;
        nextRow = nextRow.nextElementSibling;
    }

    // Create a new row for the subtask
    const subtaskRow = document.createElement('tr');
    subtaskRow.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 subtask-row';
    
    // Create the subtask row with indentation and similar values
    let subtaskHtml = `
        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[450px] cursor-pointer">
            <div class="flex items-center justify-between">
                <div class="flex items-center w-full">
                    <span class="text-gray-400 mr-2">└─</span>
                    <input 
                        type="text" 
                        class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2" 
                        placeholder="Enter Subtask">
                </div>
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
    `;

    if (projectType === 'POW' && window.CURRENT_USER_ROLE !== 'Staff') {
        subtaskHtml += `
        <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600 text-sm text-gray-900 dark:text-gray-200">
            <input 
                type="text"
                inputmode="decimal"
                placeholder="Enter Budget" 
                oninput="updateTotalBudget(this)" 
                onblur="updateTotalBudget(this)"
                class="w-full bg-transparent outline-none rounded-lg text-inherit">
        </td>
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
    subtaskRow.innerHTML = subtaskHtml;
    // Insert the subtask row after the last subtask or after the parent row
    if (insertAfter.nextElementSibling) {
        insertAfter.parentNode.insertBefore(subtaskRow, insertAfter.nextElementSibling);
    } else {
        insertAfter.parentNode.appendChild(subtaskRow);
    }
    const subtaskNameInput = subtaskRow.querySelector('td:first-child input');
    const subtaskStartDateInput = subtaskRow.querySelector('td:nth-child(2) input');
    const subtaskDueDateInput = subtaskRow.querySelector('td:nth-child(3) input');
    const subtaskPriorityElement = subtaskRow.querySelector('.priority-value');
    const subtaskStatusElement = subtaskRow.querySelector('.status-value');
    const subtaskBudgetInput = subtaskRow.querySelector('td:nth-child(6) input');
    if (subtaskNameInput) subtaskNameInput.setAttribute('data-old-value', subtaskNameInput.value);
    if (subtaskStartDateInput) subtaskStartDateInput.setAttribute('data-old-value', subtaskStartDateInput.value);
    if (subtaskDueDateInput) subtaskDueDateInput.setAttribute('data-old-value', subtaskDueDateInput.value);
    if (subtaskPriorityElement) subtaskPriorityElement.setAttribute('data-old-value', subtaskPriorityElement.textContent);
    if (subtaskStatusElement) subtaskStatusElement.setAttribute('data-old-value', subtaskStatusElement.textContent);
    if (subtaskBudgetInput) subtaskBudgetInput.setAttribute('data-old-value', subtaskBudgetInput.value);
    if (projectType === 'POW' && window.CURRENT_USER_ROLE !== 'Staff') {
        const subtaskSourceSelect = subtaskRow.querySelector('td:nth-child(7) select[name="source_of_funding"]');
        const subtaskOtherInput = subtaskRow.querySelector('td:nth-child(7) input[name="other_funding_source"]');
        subtaskRow.setAttribute('data-old-source_of_funding', subtaskSourceSelect ? subtaskSourceSelect.value : '');
        subtaskRow.setAttribute('data-old-other_funding_source', subtaskOtherInput ? subtaskOtherInput.value : '');
    }
    
    // Make sure the toggle subtask button is visible in the parent row
    let toggleBtnContainer = parentRow.querySelector('.subtask-toggle-btn');
    if (!toggleBtnContainer) {
        // Create toggle button if it doesn't exist yet
        const buttonContainer = parentRow.querySelector('td:first-child .flex.items-center.ml-2');
        
        if (buttonContainer) {
            toggleBtnContainer = document.createElement('button');
            toggleBtnContainer.className = 'subtask-toggle-btn mr-2 text-blue-500 hover:text-gray-400 transition-colors duration-150';
            toggleBtnContainer.setAttribute('onclick', 'toggleSubtasks(this.closest(\'tr\'), event)');
            toggleBtnContainer.setAttribute('title', 'Toggle Subtasks');
            toggleBtnContainer.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            `;
            
            // Insert before the add subtask button
            buttonContainer.insertBefore(toggleBtnContainer, buttonContainer.firstChild);
        }
    }
    
    // Update toggle button style to always show active state when adding a subtask
    if (toggleBtnContainer) {
        // Make sure it's blue (visible)
        toggleBtnContainer.classList.remove('text-gray-500');
        toggleBtnContainer.classList.add('text-blue-500');
        toggleBtnContainer.classList.remove('opacity-0');
        toggleBtnContainer.classList.add('opacity-100');
        
        // Ensure the arrow is pointing down (open state)
        const toggleArrow = toggleBtnContainer.querySelector('svg');
        if (toggleArrow) {
            toggleArrow.classList.remove('rotate-180');
        }
    }

    // Make sure all subtasks are visible - this is crucial
    let currentRow = parentRow.nextElementSibling;
    while (currentRow && currentRow.classList.contains('subtask-row')) {
        currentRow.classList.remove('hidden');
        currentRow = currentRow.nextElementSibling;
    }

    // Focus on the newly added subtask input
    setTimeout(() => {
        const input = subtaskRow.querySelector('td:first-child input');
        if (input) input.focus();
    }, 50);
}

function editCell(cell, type) {
    const valueSpan = cell.querySelector(`.${type}-value`);
    const currentValue = valueSpan.textContent;
    const options = type === 'priority' ? PRIORITY_OPTIONS : STATUS_OPTIONS;
    const select = document.createElement('select');
    select.innerHTML = options;
    select.value = currentValue;
    select.className = 'w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2';
    valueSpan.replaceWith(select);
    select.focus();
    select.onblur = () => {
        const newValue = select.value;
        const newSpan = document.createElement('span');
        newSpan.className = `${type}-value`;
        newSpan.textContent = newValue;
        select.replaceWith(newSpan);
        cell.className = `${type}-cell w-full ${getColor(type, newValue)} text-white rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition font-medium cursor-pointer`;
    };
    select.onchange = () => {
        select.blur();
    };
}

function editPriority(cell) {
    editCell(cell, 'priority');
}

function editStatus(cell) {
    editCell(cell, 'status');
}

function parseAndFormatBudget(value) {
    let cleaned = value.replace(/[^0-9.]/g, '');
    const parts = cleaned.split('.');
    if (parts.length > 2) {
        cleaned = parts[0] + '.' + parts.slice(1).join('');
    }
    if (parts.length === 2 && parts[1].length > 2) {
        cleaned = parts[0] + '.' + parts[1].substring(0, 2);
    }
    return cleaned;
}


function updateTotalBudget(input) {
    let value = parseAndFormatBudget(input.value);
    input.value = value; 
    if (!input.matches(':focus')) {
        const number = parseFloat(value) || 0;
        input.value = number.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    updateTotalBudgetForTable(input.closest('table'));
}

function updateTotalBudgetForTable(table) {
    const allInputs = table.querySelectorAll('tbody tr:not(.add-item-row):not(.total-budget-row) td:nth-child(6) input');
    let total = 0;
    allInputs.forEach((input) => {
        const value = parseFloat(parseAndFormatBudget(input.value)) || 0;
        total += value;
    });
    const totalBudgetElement = table.querySelector('.total-budget');
    if (totalBudgetElement) {
        totalBudgetElement.textContent = `₱${total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }
}

// Function to load existing tasks
    window.loadExistingTasks = function() {
        const projectId = window.PROJECT_ID;
        const loadingSpinner = document.getElementById('mainTableLoading');
        const placeholder = document.getElementById('placeholder');
        const container = document.getElementById('dynamicTablesContainer');

        if (loadingSpinner) loadingSpinner.style.display = '';
        if (placeholder) placeholder.style.display = 'none';
        if (container) container.innerHTML = '';

        // Fetch tasks first
        return fetch(`/projects/${projectId}/tasks`)
            .then(r => r.json())
            .then(tasks => {
                container.innerHTML = '';
                selectedUsers.clear();

                if (Object.keys(tasks).length === 0) {
                    if (placeholder) placeholder.style.display = '';
                    if (loadingSpinner) loadingSpinner.style.display = 'none';
                    if (window.hideGlobalLoading) window.hideGlobalLoading();
                    return;
                }

                if (placeholder) placeholder.style.display = 'none';

                Object.entries(tasks).forEach(([userId, userTasks]) => {
                    const user = userTasks[0].assigned_user;
                    selectedUsers.add(userId);
                    createTableForUser(user.name, userId, userTasks);

                    if (window.loadTasksIntoKanban) {
                        window.loadTasksIntoKanban(userTasks, userId);
                    }
                });

                updateUserButtonStates();

                // HIDE THE TABLE SPINNER IMMEDIATELY AFTER TABLE IS RENDERED
                if (loadingSpinner) loadingSpinner.style.display = 'none';

                // Fetch sorting preferences in the background (optional)
                return fetch('/get-sorting-view')
                    .then(r => r.json())
                    .then(sortingPrefs => {
                        // Optionally, re-sort and re-render if needed
                    })
                    .catch(() => {});
            })
            .finally(() => {
                // Only hide the global spinner here
                if (window.hideGlobalLoading) window.hideGlobalLoading();
            })
            .catch(() => {
                if (loadingSpinner) loadingSpinner.style.display = 'none';
                if (placeholder) placeholder.style.display = '';
                if (window.hideGlobalLoading) window.hideGlobalLoading();
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
            otherInput.placeholder = "Please specify";
            otherInput.style.cursor = 'pointer';
            
            otherInput.ondblclick = () => {
                select.style.display = '';
                otherFundingDiv.classList.add('hidden');
                select.value = '';
                select.focus();
            };
        }
    } else {
        otherFundingDiv.classList.add('hidden');
        select.style.display = '';
    }
}

    // Task drawer handler functions
    const taskDrawerHandler = {
        open: (row) => {
            const taskId = row.getAttribute('data-task-id');
            const taskName = row.querySelector('td:first-child input').value;
            const startDateRaw = row.querySelector('td:nth-child(2) input').value;
            const dueDateRaw = row.querySelector('td:nth-child(3) input').value;
            const priority = row.querySelector('.priority-value').textContent;
            const status = row.querySelector('.status-value').textContent;

            const formatDate = (dateString) => {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    month: 'long',
                    day: 'numeric'
                });
            };

            const drawer = document.getElementById('taskDetailsDrawer');
            drawer.setAttribute('data-current-row', taskId || '');
            fetch(`/tasks/${taskId}`)
                .then(res => res.json())
                .then(task => {
                    if (window.quill && task.task_description !== undefined) {
                        window.quill.root.innerHTML = task.task_description || '';
                    }
                    if (window.showTaskDescriptionReadonly) {
                        window.showTaskDescriptionReadonly(task.task_description);
                    }
                });


            // Update drawer content
            document.getElementById('taskTitle').textContent = taskName;
            document.getElementById('startDate').textContent = formatDate(startDateRaw);
            document.getElementById('dueDate').textContent = formatDate(dueDateRaw);
            document.getElementById('priority').textContent = priority;
            document.getElementById('status').textContent = status;
            
            // Set assigned by and to information 
            const tableWrapper = row.closest('[data-user-id]');
            if (tableWrapper) {
                const userId = tableWrapper.getAttribute('data-user-id');
                // Get user name from the table header
                const userName = tableWrapper.querySelector('h3')?.textContent || 'Unassigned';
                
                // Use the current user's name instead of "System"
                document.getElementById('assignedBy').textContent = window.CURRENT_USER_NAME || 'System';
                document.getElementById('assignedTo').textContent = userName;
            } else {
                document.getElementById('assignedBy').textContent = window.CURRENT_USER_NAME || 'System';
                document.getElementById('assignedTo').textContent = 'Unassigned';
            }
            
            // Handle completion timestamp
            const assignmentTimestampElement = document.getElementById('assignmentTimestamp');
            if (assignmentTimestampElement && taskId) {
                // Fetch task details to get the creation date and creator
                fetch(`/tasks/${taskId}`)
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        return response.json();
                    })
                    .then(taskDetails => {
                        // Update the assigned by field with the actual creator's name
                        if (taskDetails.creator && taskDetails.creator.name) {
                            document.getElementById('assignedBy').textContent = taskDetails.creator.name;
                        } else {
                            document.getElementById('assignedBy').textContent = window.CURRENT_USER_NAME || 'System';
                        }
                        
                        if (taskDetails && taskDetails.created_at) {
                            const dateElement = assignmentTimestampElement.querySelector('span') || 
                                            assignmentTimestampElement.querySelector('.assignment-date') ||
                                            assignmentTimestampElement.querySelector('p:nth-child(2)');
                            
                            if (dateElement) {
                                const date = new Date(taskDetails.created_at);
                                dateElement.textContent = date.toLocaleDateString('en-US', {
                                    month: 'long',
                                    day: 'numeric'
                                }) + ', ' + date.toLocaleTimeString('en-US', {
                                    hour: 'numeric',
                                    minute: '2-digit',
                                    hour12: true
                                });
                                assignmentTimestampElement.classList.remove('hidden');
                            } else {
                                console.warn('Could not find date element in assignment timestamp');
                                assignmentTimestampElement.classList.add('hidden');
                            }
                        } else {
                            assignmentTimestampElement.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching task details:', error);
                        assignmentTimestampElement.classList.add('hidden');
                    });
            } else if (assignmentTimestampElement) {
                assignmentTimestampElement.classList.add('hidden');
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

// Ensure taskDrawerHandler is available globally for openTaskDetails
window.taskDrawerHandler = taskDrawerHandler;

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

function setupEditableCellBorders(table) {
    // For all inputs/selects in the table
    table.querySelectorAll('input, select').forEach(el => {
        const td = el.closest('td');
        if (td) td.classList.add('no-border');
        el.addEventListener('focus', () => {
            if (td) td.classList.remove('no-border');
        });
        el.addEventListener('blur', () => {
            if (td) td.classList.add('no-border');
        });
    });
}

Object.assign(window, {
    addUserTable,
    createTableForUser,
    addNewRow,
    addSubtask,
    editPriority,
    editStatus,
    handleSourceOfFundingChange,
    updateTotalBudget,
    updateTotalBudgetForTable,
    toggleSubtasks,
    updateOldValue,
    loadExistingTasks
});