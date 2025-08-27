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
            saveButton.className = 'flex items-center text-green-500 hover:text-green-700 font-medium mr-2 text-base transition-colors duration-150';
            saveButton.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M17 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h10zm-1 2H8v2h8V5zm-1 4H9v10h6V9z" fill="currentColor"/>
                </svg>
                Save
            `;

            const deleteButton = document.createElement('button');
            deleteButton.id = `deleteTableBtn-${userId}`;
            deleteButton.className = 'flex items-center text-red-500 hover:text-red-700 font-medium text-base transition-colors duration-150';
            deleteButton.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M3 6h18M8 6v12a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V6m-6 0V4a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Delete
            `;
        deleteButton.onclick = () => {
            if (confirm(`Are you sure you want to delete the task table for ${userName}?`)) {
                const projectId = window.PROJECT_ID;
                
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
                        loadActivityLog();

                        const container = document.getElementById('dynamicTablesContainer');
                        if (container.children.length === 0) {
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
                    <td class="task-column px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[450px] cursor-pointer group"
                        ${task.id ? `onclick="if(!event.target.closest('input') && !event.target.closest('.subtask-btn') && !event.target.closest('.delete-subtask-btn')) openTaskDetails(this.closest('tr'))"` : ''}
                    >
                        <div class="flex items-center justify-between">
                            <input
                                type="text"
                                class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2"
                                value="${task.task_name}">
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
                    const sourceSelect = row.querySelector('td:nth-child(7) select[name="source_of_funding"]');
                    if (otherDiv) {
                        otherDiv.classList.remove('hidden');
                        const otherInput = otherDiv.querySelector('input[name="other_funding_source"]');
                        if (otherInput) {
                            otherInput.value = task.other_funding_source;
                            // Add double-click functionality
                            otherInput.placeholder = "Please specify (double-click to change)";
                            otherInput.style.cursor = 'pointer';
                            otherInput.title = 'Double-click to return to dropdown menu';
                            
                            otherInput.ondblclick = () => {
                                sourceSelect.style.display = '';
                                otherDiv.classList.add('hidden');
                                sourceSelect.value = '';
                                sourceSelect.focus();
                            };
                        }
                        if (sourceSelect) sourceSelect.style.display = 'none';
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
                                class="w-full bg-transparent rounded-lg outline-none text-inherit">
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
                        const subtaskSourceSelect = subtaskRow.querySelector('td:nth-child(7) select[name="source_of_funding"]');
                        if (subtaskOtherDiv) {
                            subtaskOtherDiv.classList.remove('hidden');
                            const subtaskOtherInput = subtaskOtherDiv.querySelector('input[name="other_funding_source"]');
                            if (subtaskOtherInput) {
                                subtaskOtherInput.value = subtask.other_funding_source;
                                // Add double-click functionality
                                subtaskOtherInput.placeholder = "Please specify (double-click to change)";
                                subtaskOtherInput.style.cursor = 'pointer';
                                subtaskOtherInput.title = 'Double-click to return to dropdown menu';
                                
                                subtaskOtherInput.ondblclick = () => {
                                    subtaskSourceSelect.style.display = '';
                                    subtaskOtherDiv.classList.add('hidden');
                                    subtaskSourceSelect.value = '';
                                    subtaskSourceSelect.focus();
                                };
                            }
                            if (subtaskSourceSelect) subtaskSourceSelect.style.display = 'none';
                        }
                    }
                    }
                    tbody.appendChild(subtaskRow);
                });
            }
        });
        const totalColumns = projectType === 'POW' ? 7 : 6;
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
        const totalBudget = calculateTotalBudget(tasks);
        // Create the table footer
        const tfoot = document.createElement('tfoot');
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

// Function to add a new row
    function addNewRow(tbody) {
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
            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 ">
                <input 
                    type="text"
                    inputmode="decimal"
                    placeholder="Enter Budget" 
                    oninput="updateTotalBudget(this)" 
                    onblur="updateTotalBudget(this)"
                    class="w-full bg-transparent outline-none rounded-lg text-inherit">
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
    subtaskRow.innerHTML = `
        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[450px] cursor-pointer">
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
                class="w-full bg-transparent outline-none rounded-lg text-inherit">
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

    // Insert the subtask row after the last subtask or after the parent row
    if (insertAfter.nextElementSibling) {
        insertAfter.parentNode.insertBefore(subtaskRow, insertAfter.nextElementSibling);
    } else {
        insertAfter.parentNode.appendChild(subtaskRow);
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

    // Show spinner, hide placeholder and clear tables
    if (loadingSpinner) loadingSpinner.style.display = '';
    if (placeholder) placeholder.style.display = 'none';
    if (container) container.innerHTML = '';

    // Fetch tasks and sorting preferences in parallel
    return Promise.all([
        fetch(`/projects/${projectId}/tasks`).then(r => r.json()),
        fetch('/get-sorting-view').then(r => r.json())
    ])
    .then(([tasks, sortingPrefs]) => {
        // Hide spinner after loading
        if (loadingSpinner) loadingSpinner.style.display = 'none';

        container.innerHTML = '';
        selectedUsers.clear();

        // If no tasks, show placeholder
        if (Object.keys(tasks).length === 0) {
            if (placeholder) placeholder.style.display = '';
            return;
        }

        // Hide placeholder if tasks exist
        if (placeholder) placeholder.style.display = 'none';

        // Sort and render tables (existing logic)
        if (sortingPrefs.column && sortingPrefs.order) {
            const columnKeyMap = {
                'Task': 'task_name',
                'Start Date': 'start_date',
                'Due Date': 'due_date',
                'Priority': 'priority',
                'Status': 'status',
                'Budget': 'budget'
            };
            const key = columnKeyMap[sortingPrefs.column];
            Object.entries(tasks).forEach(([userId, userTasks]) => {
                userTasks.sort((a, b) => {
                    let valA = a[key] || '';
                    let valB = b[key] || '';
                    if (key === 'budget') {
                        valA = parseFloat(valA) || 0;
                        valB = parseFloat(valB) || 0;
                    }
                    if (valA < valB) return sortingPrefs.order === 'asc' ? -1 : 1;
                    if (valA > valB) return sortingPrefs.order === 'asc' ? 1 : -1;
                    return 0;
                });
            });
        }

        Object.entries(tasks).forEach(([userId, userTasks]) => {
            const user = userTasks[0].assigned_user;
            selectedUsers.add(userId);
            createTableForUser(user.name, userId, userTasks);

            if (window.loadTasksIntoKanban) {
                window.loadTasksIntoKanban(userTasks, userId);
            }
        });

        updateUserButtonStates();
    })
    .catch(error => {
        // Hide spinner and show placeholder on error
        if (loadingSpinner) loadingSpinner.style.display = 'none';
        if (placeholder) placeholder.style.display = '';
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

// Add missing deleteSubtask implementation and attach to window
function deleteSubtask(subtaskRow) {
    if (!subtaskRow.classList.contains('subtask-row')) return;
    const table = subtaskRow.closest('table');
    subtaskRow.remove();
    if (table) updateTotalBudgetForTable(table);
}
window.deleteSubtask = deleteSubtask;

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
    deleteSubtask,
    handleSourceOfFundingChange,
    updateTotalBudget,
    updateTotalBudgetForTable,
    toggleSubtasks,
    updateOldValue,
    loadExistingTasks
});