let lastFilterBy = 'filterUser';
let lastFilterValue = '';
if (localStorage.getItem('lastFilterBy')) {
    lastFilterBy = localStorage.getItem('lastFilterBy');
}
if (localStorage.getItem('lastFilterValue')) {
    try {
        lastFilterValue = JSON.parse(localStorage.getItem('lastFilterValue'));
    } catch (e) {
        lastFilterValue = localStorage.getItem('lastFilterValue');
    }
}

// Define filter options and their input types
const filterOptions = {
    filterUser: { label: 'User', type: 'user-select' },
    filterStartDate: { label: 'Start Date', type: 'date' },
    filterDueDate: { label: 'Due Date', type: 'date' },
    filterPriority: { label: 'Priority', type: 'select', options: ['', 'High', 'Normal', 'Low'] },
    filterStatus: { label: 'Status', type: 'select', options: ['', 'For Revision', 'For Checking', 'Completed', 'Deferred'] },
    filterBudget: { label: 'Budget', type: 'number', placeholder: 'Enter budget...' },
    filterFunding: { 
        label: 'Source of Funding', 
        type: 'select', 
        options: ['', 'DRRM-F', 'LDF', 'NTA', 'For funding', 'Others']
    }
};

// Only show funding for POW projects
if (window.projectType !== 'POW') {
    delete filterOptions.filterFunding;
    delete filterOptions.filterBudget;
}

// Render the correct filter field
function renderFilterField(selected) {
    const container = document.getElementById('filterFieldContainer');
    container.innerHTML = '';
    const opt = filterOptions[selected];
    if (!opt) return;

    // Show a single date input for Start Date and Due Date filters
    if (selected === 'filterStartDate' || selected === 'filterDueDate') {
        container.innerHTML = `
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">${opt.label}</label>
            <input type="date" id="filterDate" class="w-full mb-3 p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-200">
        `;
        return;
    }

    // Show two number inputs for Budget range
    if (selected === 'filterBudget') {
        container.innerHTML = `
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Budget Range</label>
            <div class="flex space-x-2">
                <input type="number" id="budgetMin" placeholder="Min" class="w-1/2 mb-3 p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-200">
                <input type="number" id="budgetMax" placeholder="Max" class="w-1/2 mb-3 p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-200">
            </div>
        `;
        return;
    }

    let input;
    if (opt.type === 'select') {
        input = document.createElement('select');
        input.className = 'w-full mt-1 mb-4 p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-200';
        opt.options.forEach(val => {
            const option = document.createElement('option');
            option.value = val;
            option.textContent = val || 'All';
            input.appendChild(option);
        });
    } else if (opt.type === 'user-select') {
        input = document.createElement('select');
        input.className = 'w-full mt-1 mb-4 p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-200';
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'All Users';
        input.appendChild(defaultOption);
        (window.usersWithTasks || []).forEach(user => {
            const option = document.createElement('option');
            option.value = user.id;
            option.textContent = user.name;
            input.appendChild(option);
        });
    } else {
        input = document.createElement('input');
        input.type = opt.type;
        input.placeholder = opt.placeholder || '';
        input.className = 'w-full mt-1 mb-4 p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-200';
    }
    input.id = 'filterValueInput';
    container.appendChild(input);
}

// Open the filter modal
document.getElementById('filterBtn').addEventListener('click', openFilterModal);

function openFilterModal() {
    const modal = document.getElementById('filterModal');
    const content = document.getElementById('filterModalContent');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0', 'scale-95');
        content.classList.remove('opacity-0', 'scale-95');
        content.classList.add('opacity-100', 'scale-100');
    }, 10);

    // Set filterBy select to last used
    document.getElementById('filterBy').value = lastFilterBy;
    renderFilterField(lastFilterBy);

    // Set filter value to last used
    setTimeout(() => {
        if (lastFilterBy === 'filterPriority' || lastFilterBy === 'filterStatus' || lastFilterBy === 'filterUser' || lastFilterBy === 'filterFunding') {
            document.getElementById('filterValueInput').value = lastFilterValue || '';
        }
        if (lastFilterBy === 'filterStartDate' || lastFilterBy === 'filterDueDate') {
            document.getElementById('filterDate').value = lastFilterValue || '';
        }
        if (lastFilterBy === 'filterBudget' && Array.isArray(lastFilterValue)) {
            document.getElementById('budgetMin').value = lastFilterValue[0] || '';
            document.getElementById('budgetMax').value = lastFilterValue[1] || '';
        }
    }, 0);
}

// Close the filter modal
function closeFilterModal() {
    const modal = document.getElementById('filterModal');
    const content = document.getElementById('filterModalContent');
    modal.classList.add('opacity-0', 'scale-95');
    content.classList.add('opacity-0', 'scale-95');
    content.classList.remove('opacity-100', 'scale-100');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Reset filter field
function resetFilters() {
    // Clear filter input fields as before
    const filterBy = document.getElementById('filterBy').value;

    if (
        filterBy === 'filterPriority' ||
        filterBy === 'filterStatus' ||
        filterBy === 'filterUser' ||
        filterBy === 'filterFunding'
    ) {
        const input = document.getElementById('filterValueInput');
        if (input) input.value = '';
    }
    if (filterBy === 'filterStartDate' || filterBy === 'filterDueDate') {
        const dateInput = document.getElementById('filterDate');
        if (dateInput) dateInput.value = '';
    }
    if (filterBy === 'filterBudget') {
        const min = document.getElementById('budgetMin');
        const max = document.getElementById('budgetMax');
        if (min) min.value = '';
        if (max) max.value = '';
    }
    localStorage.removeItem('lastFilterBy');
    localStorage.removeItem('lastFilterValue');

    // Fetch all tasks (no filters)
    fetch(`/projects/${window.PROJECT_ID}/tasks`)
        .then(r => r.json())
        .then(tasks => {
            renderFilteredTasks(tasks);
        })
        .finally(() => {
            closeFilterModal();
        });
}

// Apply filters (implement your filtering logic here)
function applyFilters() {
    const filterBy = document.getElementById('filterBy').value;
    lastFilterBy = filterBy;
    let filterValue = '';
    let params = {};

    // Priority filter
    if (filterBy === 'filterPriority') {
        filterValue = document.getElementById('filterValueInput').value;
        if (filterValue) params['filter[priority]'] = filterValue; // <-- Add this line
    }

    // Status filter
    if (filterBy === 'filterStatus') {
        filterValue = document.getElementById('filterValueInput').value;
        if (filterValue) params['filter[status]'] = filterValue;
    }

    // Start Date filter
    if (filterBy === 'filterStartDate') {
        filterValue = document.getElementById('filterDate').value;
        if (filterValue) params['filter[start_date]'] = filterValue; // <-- Add this line
    }

    // Due Date filter
    if (filterBy === 'filterDueDate') {
        filterValue = document.getElementById('filterDate').value;
        if (filterValue) params['filter[due_date]'] = filterValue;
    }

    // User filter
    if (filterBy === 'filterUser') {
        filterValue = document.getElementById('filterValueInput').value;
        if (filterValue) params['filter[assigned_to]'] = filterValue;
    }

    // Budget range filter
    if (filterBy === 'filterBudget') {
        const min = document.getElementById('budgetMin').value;
        const max = document.getElementById('budgetMax').value;
        filterValue = [min, max];
        if (min) params['filter[budget_min]'] = min; // <-- Add this line
        if (max) params['filter[budget_max]'] = max; // <-- Add this line
    }

    if (filterBy === 'filterFunding') {
        filterValue = document.getElementById('filterValueInput').value;
        if (filterValue) params['filter[source_of_funding]'] = filterValue;
    }

    lastFilterValue = filterValue;
    localStorage.setItem('lastFilterBy', lastFilterBy);
    localStorage.setItem('lastFilterValue', JSON.stringify(lastFilterValue));
    // Build query string
    const query = new URLSearchParams(params).toString();

    // Fetch filtered tasks from backend
    fetch(`/projects/${window.PROJECT_ID}/tasks?${query}`)
        .then(r => r.json())
        .then(tasks => {
            renderFilteredTasks(tasks);
        })
        .finally(() => {
            closeFilterModal();
        });
}

function renderFilteredTasks(tasks) {
    const container = document.getElementById('dynamicTablesContainer');
    container.innerHTML = '';
    let hasData = false;
    Object.entries(tasks).forEach(([userId, userTasks]) => {
        if (userTasks.length > 0) {
            hasData = true;
            const user = userTasks[0].assigned_user;
            createTableForUser(user.name, userId, userTasks);
        }
    });
    if (!hasData) {
        container.innerHTML = `
            <div class="text-center text-gray-500 py-10">
                <span>No tasks found for the selected filter.</span>
            </div>
        `;
    }
}

// Handle filter type change
document.getElementById('filterBy').addEventListener('change', function(e) {
    renderFilterField(e.target.value);
});

// Handle form submit
document.getElementById('filterForm').addEventListener('submit', function (e) {
    e.preventDefault();
    applyFilters();
});

window.addEventListener('DOMContentLoaded', function () {
    // Set filterBy select and render the correct filter field before applying filters
    document.getElementById('filterBy').value = lastFilterBy;
    renderFilterField(lastFilterBy);

    // Set filter value to last used (copy from openFilterModal)
    setTimeout(() => {
        if (lastFilterBy === 'filterPriority' || lastFilterBy === 'filterStatus' || lastFilterBy === 'filterUser' || lastFilterBy === 'filterFunding') {
            document.getElementById('filterValueInput').value = lastFilterValue || '';
        }
        if (lastFilterBy === 'filterStartDate' || lastFilterBy === 'filterDueDate') {
            document.getElementById('filterDate').value = lastFilterValue || '';
        }
        if (lastFilterBy === 'filterBudget' && Array.isArray(lastFilterValue)) {
            document.getElementById('budgetMin').value = lastFilterValue[0] || '';
            document.getElementById('budgetMax').value = lastFilterValue[1] || '';
        }

        if (localStorage.getItem('lastFilterBy') && localStorage.getItem('lastFilterValue')) {
            applyFilters();
        } else {
            // If no filter, show all tasks
            fetch(`/projects/${window.PROJECT_ID}/tasks`)
                .then(r => r.json())
                .then(tasks => {
                    renderFilteredTasks(tasks);
                });
        }
    }, 0);
});

// Expose functions globally if needed
window.openFilterModal = openFilterModal;
window.closeFilterModal = closeFilterModal;
window.resetFilters = resetFilters;
window.applyFilters = applyFilters;