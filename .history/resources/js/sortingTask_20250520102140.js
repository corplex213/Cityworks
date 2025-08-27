    // Open the sorting modal
    document.getElementById('sortBtn').addEventListener('click', () => {
        document.getElementById('sortingModal').classList.remove('hidden');
    });

    // Close the sorting modal
    function closeSortingModal() {
        document.getElementById('sortingModal').classList.add('hidden');
    }
    document.getElementById('sortingForm').addEventListener('submit', (event) => {
        event.preventDefault();

        const columnIndex = parseInt(document.getElementById('sortColumn').value);
        const sortOrder = document.querySelector('input[name="sortOrder"]:checked').value;

        sortTable(columnIndex, sortOrder);
        updateSortButtonText(columnIndex);
        closeSortingModal();
    });

    // Sort the table
    function sortTable(columnIndex = null, sortOrder = 'asc') {
        const tables = document.querySelectorAll('#dynamicTablesContainer table');
        if (!tables.length) return;
        // Define custom sort orders
        const priorityOrder = {
            'High': 1,
            'Normal': 2,
            'Low': 3
        };

        const statusOrder = {
            'For Revision': 1,
            'For Checking': 2,
            'Completed': 3,
            'Deferred': 4
        };

        tables.forEach((table) => {
            const tbody = table.querySelector('tbody');
            if (!tbody) return;

            // Get all rows except the "Add Item" row and Total Budget row
            let rows = Array.from(tbody.querySelectorAll('tr'))
                .filter(row => 
                    !row.classList.contains('add-item-row') &&
                    !row.classList.contains('total-budget-row')
                );

        // Find the "Add Item" row and Total Budget row
        const addItemRow = tbody.querySelector('tr.add-item-row') || tbody.querySelector('tr:last-child');
        const totalBudgetRow = tbody.querySelector('tr.total-budget-row') || tbody.querySelector('tfoot tr');

        // Identify main tasks and their subtasks
        let groupedRows = [];
        if (group.length > 1) {
            let subtasks = group.slice(1);
            subtasks.sort((a, b) => {
                let cellA, cellB, comparison = 0;
                switch (columnIndex) {
                    case 3: // Priority
                        cellA = a.querySelector('.priority-value')?.textContent.trim() || '';
                        cellB = b.querySelector('.priority-value')?.textContent.trim() || '';
                        comparison = (priorityOrder[cellA] || 0) - (priorityOrder[cellB] || 0);
                        break;
                    case 4: // Status
                        cellA = a.querySelector('.status-value')?.textContent.trim() || '';
                        cellB = b.querySelector('.status-value')?.textContent.trim() || '';
                        comparison = (statusOrder[cellA] || 0) - (statusOrder[cellB] || 0);
                        break;
                    case 0: // Task
                        cellA = a.querySelector('td:first-child input')?.value.trim() || '';
                        cellB = b.querySelector('td:first-child input')?.value.trim() || '';
                        comparison = cellA.localeCompare(cellB, undefined, { numeric: true });
                        break;
                    case 1: // Start Date
                    case 2: // Due Date
                        cellA = new Date(a.children[columnIndex]?.querySelector('input')?.value || '');
                        cellB = new Date(b.children[columnIndex]?.querySelector('input')?.value || '');
                        comparison = cellA - cellB;
                        break;
                    case 5: // Budget
                        cellA = parseFloat(a.children[columnIndex]?.querySelector('input')?.value.replace(/[^0-9.]/g, '') || 0);
                        cellB = parseFloat(b.children[columnIndex]?.querySelector('input')?.value.replace(/[^0-9.]/g, '') || 0);
                        comparison = cellA - cellB;
                        break;
                    default:
                        cellA = a.children[columnIndex]?.textContent.trim() || '';
                        cellB = b.children[columnIndex]?.textContent.trim() || '';
                        comparison = cellA.localeCompare(cellB, undefined, { numeric: true });
                        break;
                }
                return sortOrder === 'asc' ? comparison : -comparison;
            });
            group = [group[0], ...subtasks];
        }

        // Default sorting by creation order
        if (columnIndex === null) {
            groupedRows.sort((a, b) => {
                const aIndex = parseInt(a[0].getAttribute('data-task-id')) || 0;
                const bIndex = parseInt(b[0].getAttribute('data-task-id')) || 0;
                return sortOrder === 'asc' ? aIndex - bIndex : bIndex - aIndex;
            });
        } else {
            groupedRows.sort((a, b) => {
                let cellA, cellB, comparison = 0;
                const mainA = a[0], mainB = b[0];

                switch (columnIndex) {
                    case 3: // Priority column
                        cellA = mainA.querySelector('.priority-value')?.textContent.trim() || '';
                        cellB = mainB.querySelector('.priority-value')?.textContent.trim() || '';
                        comparison = (priorityOrder[cellA] || 0) - (priorityOrder[cellB] || 0);
                        break;
                    case 4: // Status column
                        cellA = mainA.querySelector('.status-value')?.textContent.trim() || '';
                        cellB = mainB.querySelector('.status-value')?.textContent.trim() || '';
                        comparison = (statusOrder[cellA] || 0) - (statusOrder[cellB] || 0);
                        break;
                    case 0: // Task column
                        cellA = mainA.querySelector('td:first-child input')?.value.trim() || '';
                        cellB = mainB.querySelector('td:first-child input')?.value.trim() || '';
                        comparison = cellA.localeCompare(cellB, undefined, { numeric: true });
                        break;
                    case 1: // Start Date column
                    case 2: // Due Date column
                        cellA = new Date(mainA.children[columnIndex]?.querySelector('input')?.value || '');
                        cellB = new Date(mainB.children[columnIndex]?.querySelector('input')?.value || '');
                        comparison = cellA - cellB;
                        break;
                    case 5: // Budget column
                        cellA = parseFloat(mainA.children[columnIndex]?.querySelector('input')?.value.replace(/[^0-9.]/g, '') || 0);
                        cellB = parseFloat(mainB.children[columnIndex]?.querySelector('input')?.value.replace(/[^0-9.]/g, '') || 0);
                        comparison = cellA - cellB;
                        break;
                    default:
                        cellA = mainA.children[columnIndex]?.textContent.trim() || '';
                        cellB = mainB.children[columnIndex]?.textContent.trim() || '';
                        comparison = cellA.localeCompare(cellB, undefined, { numeric: true });
                        break;
                }
                return sortOrder === 'asc' ? comparison : -comparison;
            });
        }

        // Clear the tbody and append sorted groups
        groupedRows.forEach(group => group.forEach(row => tbody.appendChild(row)));

        // Re-append the "Add Item" row just before the Total Budget row
        if (addItemRow && totalBudgetRow) {
            tbody.insertBefore(addItemRow, totalBudgetRow);
        } else if (addItemRow) {
            tbody.appendChild(addItemRow);
        }

        // Ensure the Total Budget row is always last
        if (totalBudgetRow) {
            tbody.appendChild(totalBudgetRow);
        }
    });

    updateSortButtonText(columnIndex);
    if (columnIndex !== null) {
        saveSortingView(columnIndex, sortOrder);
    }
}

    function updateSortButtonText(columnIndex) {
        const sortBtn = document.getElementById('sortBtn');
        const columnNames = ['Task', 'Start Date', 'Due Date', 'Priority', 'Status', 'Budget'];
        const selectedColumn = columnIndex !== null ? columnNames[columnIndex] : 'Creation Order';

        sortBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 10a1 1 0 012 0v4h2l-3 3-3-3h2v-4zm14-1a1 1 0 00-2 0v4h-2l3 3 3-3h-2v-4z" clip-rule="evenodd" />
            </svg>
            Sort: ${selectedColumn}
        `;
    }
    function saveSortingView(columnIndex, sortOrder) {
        const columnNames = ['Task', 'Start Date', 'Due Date', 'Priority', 'Status', 'Budget'];
        const selectedColumn = columnNames[columnIndex];
        const projectId = window.PROJECT_ID;

        fetch('/save-sorting-view', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                project_id: projectId,
                column: selectedColumn,
                order: sortOrder
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Sorting preferences saved:', data);
        })
        .catch(error => {
            console.error('Error saving sorting view:', error);
        });
    }

    function resetSorting() {
        const projectId = window.PROJECT_ID;
        const tables = document.querySelectorAll('#dynamicTablesContainer table');
        if (!tables.length) return;

        fetch('/reset-sorting-view', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ project_id: projectId })
        })
        .then(response => response.json())
        .then(() => {
            loadExistingTasks().then(() => {
                sortTable(null, 'asc');
                updateSortButtonText(null);
                closeSortingModal();
            });
        })
        .catch(error => {
            console.error('Error resetting sort view:', error);
            loadExistingTasks().then(() => {
                sortTable(null, 'asc');
                updateSortButtonText(null);
                closeSortingModal();
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const projectId = window.PROJECT_ID;
        fetch(`/get-sorting-view?project_id=${projectId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const columnNames = ['Task', 'Start Date', 'Due Date', 'Priority', 'Status', 'Budget'];
            const columnIndex = columnNames.indexOf(data.column);
            const sortOrder = data.order;

            loadExistingTasks().then(() => {
                // Only apply saved sort if it exists for this project
                if (data.column && columnIndex !== -1 && data.project_id == projectId) {
                    sortTable(columnIndex, sortOrder);
                } else {
                    // Default: creation order
                    sortTable(null, 'asc');
                    updateSortButtonText(null);
                }
            });
        })
        .catch(error => {
            console.error('Error fetching sorting view:', error);
            loadExistingTasks().then(() => {
                sortTable(null, 'asc');
                updateSortButtonText(null);
            });
        });
    });

window.resetSorting = resetSorting;
window.closeSortingModal = closeSortingModal;
window.sortTable = sortTable;
window.updateSortButtonText = updateSortButtonText;