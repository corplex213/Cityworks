<!-- Project Overview Section -->
<div id="projectOverviewSection" class="hidden bg-white p-6 rounded-lg shadow-lg">
    <section class="project-overview">
        <!-- Project Header -->
        <div class="project-header border-b border-gray-200 pb-4 mb-6">
            <h1 id="projectName" class="text-2xl font-bold text-green-600 editable" contenteditable="true">Project Name</h1>
            <p id="projectDescription" class="text-gray-700 mt-2 editable" contenteditable="true">
                Project description goes here. This is a brief overview of the project objectives and goals.
            </p>
        </div>

        <!-- Task Table Wrapper -->
        <div class="task-table-wrapper">
            <div class="button-table-wrapper flex justify-start space-x-4">
                <!-- Buttons -->
                <button id="mainTableBtn" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded shadow" onclick="showMainTable()">
                    Main Table
                </button>
                <button id="calendarBtn" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded shadow" onclick="showCalendar()">
                    Calendar
                </button>
            </div>
        </div>
    </section>

    <!-- Main Table Section -->
    <section id="mainTableSection" class="group-section mt-8">
        <div class="relative" id="dynamicTablesContainer">
            <!-- Dynamic tables will be appended here -->
        </div>
        <!-- Add Group Button -->
        <div class="add-group-container mt-4">
            <button id="addGroupBtn" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded shadow">
                Add Engineer/Project Manager
            </button>
        </div>
    </section>

    <!-- Calendar Section -->
    <section id="calendarSection" class="calendar-section mt-8 hidden">
        <div class="calendar-header flex items-center justify-between bg-gray-100 p-4 rounded-lg shadow">
            <button id="prevMonth" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded shadow">
                Prev
            </button>
            <span id="monthYearDisplay" class="text-gray-700 font-semibold">Month Year</span>
            <button id="nextMonth" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded shadow">
                Next
            </button>
        </div>
        <div class="calendar-grid mt-4 bg-gray-50 p-4 rounded-lg shadow">
            <!-- Placeholder for calendar grid -->
            <p class="text-gray-500">Calendar content will appear here.</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer mt-8 border-t border-gray-200 pt-4">
        <p class="text-center text-gray-500">&copy; 2024 City Engineering Office. All rights reserved.</p>
    </footer>


    <script>
        // Function to create a table
        const createTable = (tableName = "Default Table") => {
            // Create a new table dynamically
            const tableContainer = document.createElement('div');
            tableContainer.classList.add('table-container', 'mt-4', 'p-4', 'bg-gray-100', 'rounded-lg', 'shadow');
    
            const tableTitle = document.createElement('h2');
            tableTitle.classList.add('text-lg', 'font-bold', 'text-green-600', 'mb-2', 'text-center'); // Centered
            tableTitle.textContent = tableName;
    
            const table = document.createElement('table');
            table.classList.add('min-w-full', 'border-collapse', 'border', 'border-gray-300');
    
            // Add table headers
            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');
            headerRow.classList.add('bg-gray-200');
            headerRow.innerHTML = `
                <th class="border border-gray-300 px-4 py-2">Text</th>
                <th class="border border-gray-300 px-4 py-2">Timeline</th>
                <th class="border border-gray-300 px-2 py-2 w-12"> <!-- Shortened "+" column -->
                    <div class="relative">
                        <button class="bg-green-500 hover:bg-green-600 text-white font-semibold py-1 px-2 rounded shadow dropdown-toggle">
                            +
                        </button>
                        <div class="dropdown-menu hidden absolute bg-white border border-gray-300 rounded shadow mt-1">
                            <button class="dropdown-item px-4 py-2 hover:bg-gray-100" data-field="Key Persons">Key Persons</button>
                            <button class="dropdown-item px-4 py-2 hover:bg-gray-100" data-field="Status">Status</button>
                            <button class="dropdown-item px-4 py-2 hover:bg-gray-100" data-field="Comments">Comments</button>
                            <button class="dropdown-item px-4 py-2 hover:bg-gray-100" data-field="File Upload">File Upload</button>
                            <button class="dropdown-item px-4 py-2 hover:bg-gray-100" data-field="Budget">Budget</button>
                            <button class="dropdown-item px-4 py-2 hover:bg-gray-100" data-field="Timeline">Timeline</button>
                        </div>
                    </div>
                </th>
            `;
            thead.appendChild(headerRow);
            table.appendChild(thead);
    
            // Add table body
            const tbody = document.createElement('tbody');
    
            // Function to create a new row
            const createRow = () => {
                const row = document.createElement('tr');
                Array.from(headerRow.children).forEach((header, index) => {
                    if (index === headerRow.children.length - 1) return; // Skip the "+" button column
                    const newCell = document.createElement('td');
                    newCell.classList.add('border', 'border-gray-300', 'px-4', 'py-2');
    
                    // Add specific behavior based on the header text
                    if (header.textContent === "Text" || header.textContent === "Key Persons" || header.textContent === "Comments") {
                        newCell.setAttribute('contenteditable', 'true');
                    } else if (header.textContent === "Timeline") {
                        const startDateInput = document.createElement('input');
                        startDateInput.type = 'date';
                        startDateInput.classList.add('w-full', 'border', 'border-gray-300', 'rounded', 'px-2', 'py-1');
    
                        const dueDateInput = document.createElement('input');
                        dueDateInput.type = 'date';
                        dueDateInput.classList.add('w-full', 'border', 'border-gray-300', 'rounded', 'px-2', 'py-1', 'mt-2');
    
                        // Event listener to summarize dates
                        const summarizeDates = () => {
                            if (startDateInput.value && dueDateInput.value) {
                                const startDate = new Date(startDateInput.value);
                                const dueDate = new Date(dueDateInput.value);
    
                                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                                newCell.innerHTML = `
                                    <span class="timeline-summary">${startDate.toLocaleDateString(undefined, options)} - ${dueDate.toLocaleDateString(undefined, options)}</span>
                                `;
    
                                // Add click event to make the date pickers available again
                                const summarySpan = newCell.querySelector('.timeline-summary');
                                summarySpan.addEventListener('click', () => {
                                    newCell.innerHTML = '';
                                    newCell.appendChild(startDateInput);
                                    newCell.appendChild(dueDateInput);
                                });
                            }
                        };
    
                        startDateInput.addEventListener('change', summarizeDates);
                        dueDateInput.addEventListener('change', summarizeDates);
    
                        newCell.appendChild(startDateInput);
                        newCell.appendChild(dueDateInput);
                    } else if (header.textContent === "Status") {
                        newCell.innerHTML = `
                            <select class="w-full border border-gray-300 rounded px-2 py-1">
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                            </select>
                        `;
                    } else if (header.textContent === "File Upload") {
                        newCell.innerHTML = `<input type="file" class="w-full border border-gray-300 rounded">`;
                    } else if (header.textContent === "Budget") {
                        newCell.setAttribute('contenteditable', 'true');
                        newCell.addEventListener('input', function () {
                            this.textContent = this.textContent.replace(/[^0-9]/g, ''); // Allow only integers
                        });
                    } else {
                        newCell.setAttribute('contenteditable', 'true');
                    }
    
                    row.appendChild(newCell);
                });
                return row;
            };
    
            // Add initial rows
            tbody.appendChild(createRow());
            tbody.appendChild(createRow());
            table.appendChild(tbody);
    
            // Append title and table to the container
            tableContainer.appendChild(tableTitle);
            tableContainer.appendChild(table);
    
            // Add "Add Item" button
            const addItemBtn = document.createElement('button');
            addItemBtn.textContent = "Add Item";
            addItemBtn.classList.add('bg-green-500', 'hover:bg-green-600', 'text-white', 'font-semibold', 'py-2', 'px-4', 'rounded', 'shadow', 'mt-4', 'block', 'mx-auto'); // Centered
            addItemBtn.addEventListener('click', function () {
                tbody.appendChild(createRow());
            });
            tableContainer.appendChild(addItemBtn);
    
            // Append the container to the dynamic tables section
            document.getElementById('dynamicTablesContainer').appendChild(tableContainer);
    
            // Dropdown menu functionality
            const dropdownToggle = table.querySelector('.dropdown-toggle');
            const dropdownMenu = table.querySelector('.dropdown-menu');
    
            dropdownToggle.addEventListener('click', function () {
                dropdownMenu.classList.toggle('hidden');
            });
    
            dropdownMenu.addEventListener('click', function (event) {
                const fieldType = event.target.getAttribute('data-field');
                if (!fieldType) return;
    
                const newHeader = document.createElement('th');
                newHeader.classList.add('border', 'border-gray-300', 'px-4', 'py-2');
                newHeader.textContent = fieldType;
                headerRow.insertBefore(newHeader, headerRow.lastElementChild);
    
                // Add a new cell to each existing row in the table body
                Array.from(tbody.querySelectorAll('tr')).forEach(row => {
                    const newCell = document.createElement('td');
                    newCell.classList.add('border', 'border-gray-300', 'px-4', 'py-2');
    
                    // Add specific behavior based on the field type
                    if (fieldType === "Key Persons" || fieldType === "Comments") {
                        newCell.setAttribute('contenteditable', 'true');
                    } else if (fieldType === "Timeline") {
                        const startDateInput = document.createElement('input');
                        startDateInput.type = 'date';
                        startDateInput.classList.add('w-full', 'border', 'border-gray-300', 'rounded', 'px-2', 'py-1');
    
                        const dueDateInput = document.createElement('input');
                        dueDateInput.type = 'date';
                        dueDateInput.classList.add('w-full', 'border', 'border-gray-300', 'rounded', 'px-2', 'py-1', 'mt-2');
    
                        // Event listener to summarize dates
                        const summarizeDates = () => {
                            if (startDateInput.value && dueDateInput.value) {
                                const startDate = new Date(startDateInput.value);
                                const dueDate = new Date(dueDateInput.value);
    
                                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                                newCell.innerHTML = `
                                    <span class="timeline-summary">${startDate.toLocaleDateString(undefined, options)} - ${dueDate.toLocaleDateString(undefined, options)}</span>
                                `;
    
                                // Add click event to make the date pickers available again
                                const summarySpan = newCell.querySelector('.timeline-summary');
                                summarySpan.addEventListener('click', () => {
                                    newCell.innerHTML = '';
                                    newCell.appendChild(startDateInput);
                                    newCell.appendChild(dueDateInput);
                                });
                            }
                        };
    
                        startDateInput.addEventListener('change', summarizeDates);
                        dueDateInput.addEventListener('change', summarizeDates);
    
                        newCell.appendChild(startDateInput);
                        newCell.appendChild(dueDateInput);
                    } else if (fieldType === "Status") {
                        newCell.innerHTML = `
                            <select class="w-full border border-gray-300 rounded px-2 py-1">
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                            </select>
                        `;
                    } else if (fieldType === "File Upload") {
                        newCell.innerHTML = `<input type="file" class="w-full border border-gray-300 rounded">`;
                    } else if (fieldType === "Budget") {
                        newCell.setAttribute('contenteditable', 'true');
                        newCell.addEventListener('input', function () {
                            this.textContent = this.textContent.replace(/[^0-9]/g, ''); // Allow only integers
                        });
                    } else {
                        newCell.setAttribute('contenteditable', 'true');
                    }
    
                    row.appendChild(newCell);
                });
    
                // Hide the dropdown menu after selection
                dropdownMenu.classList.add('hidden');
            });
        };
    
        // Automatically create a default table when the project is initialized
        document.addEventListener('DOMContentLoaded', function () {
            createTable("Default Project Table");
        });
    
        // Add event listener for the "Add Group" button
        document.getElementById('addGroupBtn').addEventListener('click', function () {
            const tableName = prompt("Please enter the name for the table:");
            if (!tableName) {
                alert("Invalid input. Please enter a valid table name.");
                return;
            }
            createTable(tableName);
        });
    </script>