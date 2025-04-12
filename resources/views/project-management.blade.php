<x-app-layout>
    <x-slot name="header">
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
                    <p class="mb-4"><strong>Location:</strong> {{ $project->location }}</p>
                    <p class="mb-4"><strong>Description:</strong> {{ $project->description }}</p>
                    <p class="mb-4"><strong>Status:</strong> {{ $project->status }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <section class="project-overview">
        <div class="task-table-wrapper border-b dark:border-gray-700 pb-4 mb-0 mx-[25px]">
            <div class="button-table-wrapper flex justify-start space-x-4">
                <!-- Buttons -->
                <button id="mainTableBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition" style="margin-left: 15px;">
                    Main Table
                </button>
                <button id="calendarBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                    Calendar
                </button>
                <button id="kanbanBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                    Kanban
                </button>
            </div>
        </div>
    </section>
        
    <div id="projectOverviewSection" class="bg-gray-50 dark:bg-gray-900 p-6 rounded-lg shadow-lg mx-6 lg:mx-12" style="margin-left: 0px;margin-right: 0px;">
        <div class="relative mb-4">
            <input
                id="taskSearchInput"
                type="text"
                placeholder="Search tasks..."
                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 pl-10 pr-10 rounded-lg shadow focus:ring-2 focus:ring-blue-500 focus:outline-none"
                oninput="filterTasks()"
            />
            <!-- Search Icon -->
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="absolute left-3 top-3 h-5 w-5 text-gray-400"
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
                class="absolute right-3 top-3 h-5 w-5 text-gray-400 cursor-pointer hover:text-gray-600"
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
            </div>
            <!-- Add Group Button -->
            <div class="add-group-container mt-4 flex space-x-4">
                <button id="addGroupBtn" type="button" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                    Assign Task to Engineer
                </button>
            </div>
        </section>
        
        <!-- Calendar Section -->
        <section id="calendarSection" class="calendar-section mt-8 hidden">
            <div class="calendar-header flex items-center justify-between bg-gray-100 dark:bg-gray-800 p-4 rounded-lg shadow">
                <button id="prevMonth" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                    Prev
                </button>
                <span id="monthYearDisplay" class="text-gray-800 dark:text-gray-200 font-semibold">Month Year</span>
                <button id="nextMonth" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                    Next
                </button>
            </div>
            <div class="calendar-grid mt-4 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow">
                <!-- Placeholder for calendar grid -->
                <p class="text-gray-500 dark:text-gray-400">Calendar content will appear here.</p>
            </div>
        </section>
    </div>

    <!-- User Selection Modal -->
    <div id="userSelectionModal" 
     class="fixed inset-0 hidden opacity-0 scale-95 transition-all duration-300 ease-out z-50 flex justify-center items-center bg-gray-900 bg-opacity-50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md transform scale-95 transition-transform duration-500 ease-in-out">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Select a User</h3>
            <ul id="userList" class="space-y-2">
                @foreach($users as $user)
                <li>
                    <button onclick="addUserTable('{{ $user->name }}')" class="w-full text-left bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-lg">
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


    <!-- Footer -->
    <footer class="footer mt-8 border-t border-gray-300 dark:border-gray-700 pt-4">
        <p class="text-center text-gray-500 dark:text-gray-400">&copy; 2024 City Engineering Office. All rights reserved.</p>
    </footer>

    <script>
        function clearSearchBar() {
            const searchInput = document.getElementById('taskSearchInput');
            searchInput.value = ''; // Clear the input field
            filterTasks(); // Reset the table to show all rows
        }
    function filterTasks() {
        const searchInput = document.getElementById('taskSearchInput').value.toLowerCase();
        const tables = document.querySelectorAll('#dynamicTablesContainer > div'); // Select all table wrappers

        tables.forEach((tableWrapper) => {
            const tableHeader = tableWrapper.querySelector('h3'); // Get the table header (user name)
            const tableRows = Array.from(tableWrapper.querySelectorAll('table tbody tr')); // Get all rows in the table

            let tableVisible = false;

            // If the search input is empty, show all tables and rows
            if (!searchInput.trim()) {
                tableWrapper.style.display = ''; // Show the table
                tableRows.forEach((row) => {
                    row.style.display = ''; // Show all rows
                });
                } else {
                    // Check if the table header or any task field matches the search input
                    if (tableHeader && tableHeader.textContent.toLowerCase().includes(searchInput)) {
                        tableWrapper.style.display = ''; // Show the table
                        tableVisible = true;
                    } else {
                        // Check if any row in the table matches the search input
                        tableRows.forEach((row) => {
                            const taskCell = row.querySelector('td:first-child input'); // Assuming the first column contains the task input field
                            if (taskCell && taskCell.value.toLowerCase().includes(searchInput)) {
                                row.style.display = ''; // Show the row
                                tableVisible = true;
                            } else {
                                row.style.display = 'none'; // Hide the row
                            }
                        });

                        // Hide the table if no rows match
                        tableWrapper.style.display = tableVisible ? '' : 'none';
                    }
                }
            });
        }

        // Open the user selection modal
        document.getElementById('addGroupBtn').addEventListener('click', function () {
            openUserModal();
        });

        // Close the user selection modal
        function closeUserModal() {
            const modal = document.getElementById('userSelectionModal');
            // Animate out
            modal.classList.add('opacity-0', 'scale-95');
            // Hide after animation completes
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Add a new table for the selected user
        function addUserTable(userName) {
            const container = document.getElementById('dynamicTablesContainer');
            const placeholder = document.getElementById('placeholder');

            // Remove the placeholder if it exists
            if (placeholder) {
                placeholder.remove();
            }

            // Create the table wrapper
            const tableWrapper = document.createElement('div');
            tableWrapper.className = 'mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden';

            // Create the table header
            const header = document.createElement('h3');
            header.className = 'text-2xl font-semibold text-center text-gray-800 dark:text-gray-200 px-6 py-4 bg-gray-100 dark:bg-gray-700';
            header.textContent = userName;

            // Create the scrollable container
            const scrollableContainer = document.createElement('div');
            scrollableContainer.className = 'overflow-x-auto';

            // Create the table
            const table = document.createElement('table');
            table.className = 'w-full min-w-[1000px] bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 border border-gray-300 dark:border-gray-600';

            // Create the table header row
            const thead = document.createElement('thead');
            thead.className = 'bg-gray-100 dark:bg-gray-700';
            thead.innerHTML = `
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[450px]">
                        Task
                    </th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600">
                        Start Date
                    </th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600">
                        Due Date
                    </th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600">
                        Key Persons
                    </th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600">
                        Priority
                    </th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200">
                        Budget
                    </th>
                </tr>
            `;

            // Create the table body
            const tbody = document.createElement('tbody');
            const tfoot = document.createElement('tfoot');
            tbody.className = 'bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700';
            tbody.innerHTML = `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[450px]">
                        <input 
                            id="taskInput" 
                            type="text" 
                            class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2" 
                            placeholder="Enter Task">
                    </td>
                    <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                        <input 
                            id="startDateInput"
                            type="date" 
                            class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2">
                    </td>
                    <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                        <input
                            id="dueDateInput" 
                            type="date" 
                            class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2" style= "cursor:pointer;">
                    </td>
                    <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                        <button 
                            id="addKeyPersonsBtn" 
                            class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2 font-semibold shadow transition hover:bg-gray-200 dark:hover:bg-gray-700" 
                            onclick="openKeyPersonsModal(this)">
                            Add Key Persons
                        </button>
                        <div id="keyPersonsContainer" class="key-persons-container flex flex-wrap gap-2 mt-2"></div>
                    </td>
                    <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                        <div
                            class="priority-cell w-full bg-yellow-500 text-white rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition font-medium cursor-pointer"
                            onclick="editPriority(this)"
                        >
                            <span class="priority-value">Normal</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                        <div
                            id="status"
                            class="status-cell w-full bg-gray-500 text-white rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition cursor-pointer"
                            onclick="editStatus(this)"
                        >
                            <span class="status-value">Pending</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[450px]">
                        <input 
                        id="budgetInput" 
                        type="text" 
                        placeholder="Enter number" 
                        oninput="validateBudget(this); updateTotalBudget(this)" 
                        class="w-full bg-transparent outline-none text-inherit">
                    </td>
                </tr>
            `;


            tfoot.className = 'bg-gray-100 dark:bg-gray-700';
            tfoot.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-4 text-right font-semibold text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600">
                        Total Budget:
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">
                        <span id="totalBudget" class="font-semibold">0</span>
                    </td>
                </tr>
            `;
            // Append the header and body to the table
            table.appendChild(thead);
            table.appendChild(tbody);
            table.appendChild(tfoot);

            // Append the table to the scrollable container
            scrollableContainer.appendChild(table);

            // Create the "Add Item" button
            const addItemButton = document.createElement('button');
            addItemButton.className = 'mt-4 mb-5 mx-auto bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition block';
            addItemButton.textContent = 'Add Item';

            // Add event listener to the "Add Item" button
            addItemButton.addEventListener('click', function () {
                const newRow = document.createElement('tr');
                newRow.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150';
                newRow.innerHTML = `
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[450px]">
                        <input 
                            id="taskInput" 
                            type="text" 
                            class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2" 
                            placeholder="Enter Task">
                    </td>
                    <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                        <input 
                            id="startDateInput"
                            type="date" 
                            class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2">
                    </td>
                    <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                        <input
                            id="dueDateInput" 
                            type="date" 
                            class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2">
                    </td>
                    <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                        <button 
                            id="addKeyPersonsBtn" 
                            class="w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2 font-semibold shadow transition hover:bg-gray-200 dark:hover:bg-gray-700" 
                            onclick="openKeyPersonsModal(this)">
                            Add Key Persons
                        </button>
                        <div id="keyPersonsContainer" class="key-persons-container flex flex-wrap gap-2 mt-2"></div>
                    </td>
                   <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                        <div
                            class="priority-cell w-full bg-yellow-500 text-white rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition font-medium cursor-pointer"
                            onclick="editPriority(this)"
                        >
                            <span class="priority-value">Normal</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-600">
                        <div
                            class="status-cell w-full bg-gray-500 text-white rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition cursor-pointer"
                            onclick="editStatus(this)"
                        >
                            <span class="status-value">Pending</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 min-w-[450px]">
                        <input 
                        id="budgetInput" 
                        type="text" 
                        placeholder="Enter number" 
                        oninput="validateBudget(this); updateTotalBudget(this)" 
                        class="w-full bg-transparent outline-none text-inherit">
                    </td>
                `;
                tbody.appendChild(newRow);
            });

            // Append the header, scrollable container, and button to the wrapper
            tableWrapper.appendChild(header);
            tableWrapper.appendChild(scrollableContainer);
            tableWrapper.appendChild(addItemButton);

            // Append the wrapper to the container
            container.appendChild(tableWrapper);

            // Close the modal
            closeUserModal();

            // Enable column resizing
            enableColumnResizing(table);
        }

        function editPriority(cell) {
            const priorityValueElement = cell.querySelector('.priority-value');
            if (!priorityValueElement) {
                console.error("Priority value element not found in the cell.");
                return;
            }

            const currentValue = priorityValueElement.textContent;
            const options = ['High', 'Normal', 'Low'];
            const colors = {
                High: 'bg-red-500 text-white',
                Normal: 'bg-yellow-500 text-white',
                Low: 'bg-green-500 text-white'
            };

            const select = document.createElement('select');
            select.className = 'w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition font-medium';

            options.forEach(option => {
                const opt = document.createElement('option');
                opt.value = option;
                opt.textContent = option;
                if (option === currentValue) opt.selected = true;
                select.appendChild(opt);
            });

            // Define a helper function to update the cell appearance and rebind onclick
        function applyPriorityChange(selectedValue) {
                // Remove old background and text color classes
                cell.classList.remove('bg-red-500', 'bg-yellow-500', 'bg-green-500', 'text-white');

                // Add new color classes
                const [bgClass, textClass] = colors[selectedValue].split(' ');
                cell.classList.add(bgClass, textClass);

                // Set the new content and rebind the click event
                cell.innerHTML = `<span class="priority-value">${selectedValue}</span>`;
                cell.onclick = () => editPriority(cell);
            }

            select.addEventListener('change', () => {
                applyPriorityChange(select.value);
            });

            // Clear the cell and insert the dropdown
            cell.innerHTML = '';
            cell.appendChild(select);
            select.focus();
        }

        function updateTotalBudget(input) {
            // Format the input value with commas and Peso sign
            const value = parseFloat(input.value.replace(/,/g, '')) || 0;
            input.value = value.toLocaleString('en-US', { minimumFractionDigits: 0 });

            // Find the closest table
            const table = input.closest('table');
            if (!table) return;

            // Select all budget inputs in the table body
            const budgetInputs = table.querySelectorAll('tbody input[id="budgetInput"]');
            let total = 0;

            // Sum all budget input values
            budgetInputs.forEach((budgetInput) => {
                const budgetValue = parseFloat(budgetInput.value.replace(/,/g, '')) || 0;
                total += budgetValue;
            });

            // Update the total budget in the footer with Peso sign and formatted value
            const totalBudgetElement = table.querySelector('#totalBudget');
            if (totalBudgetElement) {
                totalBudgetElement.textContent = `₱${total.toLocaleString('en-US', { minimumFractionDigits: 0 })}`;
            }
        }

        // Function to validate the Budget field
        function validateBudget(input) {
            input.value = input.value.replace(/[^0-9.]/g, '');
        }

        function openUserModal() {
            const modal = document.getElementById('userSelectionModal');

            // Show the modal
            modal.classList.remove('hidden');

            // Allow the browser to register the visibility change before animating
            setTimeout(() => {
                modal.classList.remove('opacity-0', 'scale-95');
            }, 10);
        }

        function enableColumnResizing(table) {
        const headers = table.querySelectorAll('th');
        headers.forEach((header, index) => {
            const resizer = document.createElement('div');
            resizer.style.cssText = `
                width: 5px;
                cursor: col-resize;
                position: absolute;
                right: 0;
                top: 0;
                bottom: 0;
                z-index: 1;
            `;
            header.style.position = 'relative';
            header.appendChild(resizer);

            resizer.addEventListener('mousedown', (e) => {
                const startX = e.pageX;
                const startWidth = header.offsetWidth;

                const onMouseMove = (e) => {
                    const newWidth = startWidth + (e.pageX - startX);
                    header.style.width = `${newWidth}px`;
                    table.querySelectorAll('tr').forEach((row) => {
                        const cell = row.children[index];
                        if (cell) cell.style.width = `${newWidth}px`;
                    });
                };

                const stopResizing = () => {
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', stopResizing);
                };

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', stopResizing);
            });
        });
    }

    function editStatus(cell) {
        const statusValueElement = cell.querySelector('.status-value');
        if (!statusValueElement) {
            console.error("Error: .status-value element not found in the cell.");
            return; // Exit the function if the element is not found
        }

        const currentValue = statusValueElement.textContent;
        const options = ['Pending', 'In Progress', 'Completed', 'Behind Schedule', 'On Schedule', 'Suspended', 'Terminated', 'Ahead of Schedule'];
        const colors = {
            Pending: 'bg-gray-500 text-white',
            'In Progress': 'bg-blue-500 text-white',
            Completed: 'bg-green-500 text-white',
            'Behind Schedule': 'bg-red-500 text-white',
            'On Schedule': 'bg-yellow-500 text-white',
            Suspended: 'bg-purple-500 text-white',
            Terminated: 'bg-black text-white',
            'Ahead of Schedule': 'bg-teal-500 text-white'
        };

        const select = document.createElement('select');
        select.className = 'w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none transition font-medium';

        options.forEach(option => {
            const opt = document.createElement('option');
            opt.value = option;
            opt.textContent = option;
            if (option === currentValue) opt.selected = true;
            select.appendChild(opt);
        });

        select.addEventListener('change', () => {
            const selectedValue = select.value;

            // Remove old color classes
            cell.className = cell.className.replace(/bg-\w+-500 text-\w+/g, '');

            // Add new color classes
            cell.className += ` ${colors[selectedValue]}`;

            cell.innerHTML = `<span class="status-value">${selectedValue}</span>`;
            cell.onclick = () => editStatus(cell);
        });

        cell.innerHTML = '';
        cell.appendChild(select);
        select.focus();
    }
    </script>
</x-app-layout>