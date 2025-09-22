const priorityOrder = {'High': 1, 'Normal': 2, 'Low': 3};
const statusOrder = {'For Revision': 1, 'For Checking': 2, 'Completed': 3,'Deferred': 4};
// Dynamically build columnNames based on project type
let columnNames = ['Task', 'Start Date', 'Due Date', 'Priority', 'Status'];
let currentSortColumnIndex = null;
let currentSortOrder = 'asc';
if (window.projectType === 'POW') {
    columnNames.push('Budget', 'Source of Funding');
}
    
    // Open the sorting modal
    document.getElementById('sortBtn').addEventListener('click', openSortingModal);

    function openSortingModal() {
        const modal = document.getElementById('sortingModal');
        const content = document.getElementById('sortingModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0', 'scale-95');
            content.classList.remove('opacity-0', 'scale-95');
            content.classList.add('opacity-100', 'scale-100');
        }, 10);

        // Dynamically populate the sorting select options
        const sortColumnSelect = document.getElementById('sortColumn');
        if (sortColumnSelect) {
            sortColumnSelect.innerHTML = '';
            columnNames.forEach((name, idx) => {
                const option = document.createElement('option');
                option.value = idx;
                option.textContent = name;
                sortColumnSelect.appendChild(option);
            });

            // Use the current sort state, fallback to 0/"asc"
            let selectedIndex = currentSortColumnIndex !== null ? currentSortColumnIndex : 0;
            let selectedOrder = currentSortOrder || 'asc';

            sortColumnSelect.value = selectedIndex;

            // Set radio button for order
            const ascRadio = document.querySelector('input[name="sortOrder"][value="asc"]');
            const descRadio = document.querySelector('input[name="sortOrder"][value="desc"]');
            if (ascRadio && descRadio) {
                if (selectedOrder === 'desc') {
                    descRadio.checked = true;
                } else {
                    ascRadio.checked = true;
                }
            }
        }
    }

    // Close the sorting modal
    function closeSortingModal() {
        const modal = document.getElementById('sortingModal');
        const content = document.getElementById('sortingModalContent');
        modal.classList.add('opacity-0', 'scale-95');
        content.classList.add('opacity-0', 'scale-95');
        content.classList.remove('opacity-100', 'scale-100');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
    document.getElementById('sortingForm').addEventListener('submit', async (event) => {
        event.preventDefault();

        const columnIndex = parseInt(document.getElementById('sortColumn').value);
        const sortOrder = document.querySelector('input[name="sortOrder"]:checked').value;

        await sortTable(columnIndex, sortOrder);
        updateSortButtonText(columnIndex);
        closeSortingModal();
    });

    // Sort the table
    function sortTable(columnIndex = null, sortOrder = 'asc') {
        currentSortColumnIndex = columnIndex;
        currentSortOrder = sortOrder;
        const tables = document.querySelectorAll('#dynamicTablesContainer table');
        if (!tables.length) return;
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
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                if (!row.classList.contains('subtask-row')) {
                    // Main task
                    let group = [row];
                    // Collect subtasks immediately following
                    let j = i + 1;
                    while (j < rows.length && rows[j].classList.contains('subtask-row')) {
                        group.push(rows[j]);
                        j++;
                    }
                    // --- SORT SUBTASKS WITHIN THIS GROUP ---
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
                                case 6: // Source of Funding (only if POW)
                                if (window.projectType === 'POW') {
                                    cellA = a.children[columnIndex]?.querySelector('input, select')?.value.trim() || a.children[columnIndex]?.textContent.trim() || '';
                                    cellB = b.children[columnIndex]?.querySelector('input, select')?.value.trim() || b.children[columnIndex]?.textContent.trim() || '';
                                    comparison = cellA.localeCompare(cellB, undefined, { numeric: true });
                                } else {
                                    comparison = 0;
                                }
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
                    // --- END SUBTASK SORT ---
                    groupedRows.push(group);
                    i = j - 1;
                }
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
                    case 6: // Source of Funding (only if POW)
                        if (window.projectType === 'POW') {
                            cellA = mainA.children[columnIndex]?.querySelector('input, select')?.value.trim() || mainA.children[columnIndex]?.textContent.trim() || '';
                            cellB = mainB.children[columnIndex]?.querySelector('input, select')?.value.trim() || mainB.children[columnIndex]?.textContent.trim() || '';
                            comparison = cellA.localeCompare(cellB, undefined, { numeric: true });
                        } else {
                            comparison = 0;
                        }
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
        const fragment = document.createDocumentFragment();
        groupedRows.forEach(group => group.forEach(row => fragment.appendChild(row)));
        tbody.innerHTML = ''; 
        tbody.appendChild(fragment);

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
        return saveSortingView(columnIndex, sortOrder);
    }
    return Promise.resolve();
}

    function updateSortButtonText(columnIndex) {
        const sortBtn = document.getElementById('sortBtn');
        const selectedColumn = columnIndex !== null ? columnNames[columnIndex] : 'Creation Order';

        sortBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 10a1 1 0 012 0v4h2l-3 3-3-3h2v-4zm14-1a1 1 0 00-2 0v4h-2l3 3 3-3h-2v-4z" clip-rule="evenodd" />
            </svg>
            Sort: ${selectedColumn}
        `;
    }
    function saveSortingView(columnIndex, sortOrder) {
        const selectedColumn = columnNames[columnIndex];
        const projectId = window.PROJECT_ID;
        const cacheKey = `sortingView_${projectId}`;

        return fetch('/save-sorting-view', {
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
            // Update the cache with the new sorting preferences
            sessionStorage.setItem(cacheKey, JSON.stringify({
                project_id: projectId,
                column: selectedColumn,
                order: sortOrder
            }));
            console.log('Sorting preferences saved:', data);
        })
        .catch(error => {
            console.error('Error saving sorting view:', error);
        });
    }

    async function resetSorting() {
        const projectId = window.PROJECT_ID;
        const cacheKey = `sortingView_${projectId}`;
        const tables = document.querySelectorAll('#dynamicTablesContainer table');
        if (!tables.length) return;

        try {
            await fetch('/reset-sorting-view', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ project_id: projectId })
            });
            // Clear the cached sorting preferences
            sessionStorage.removeItem(cacheKey);

            await loadExistingTasks();
            await sortTable(null, 'asc');
            updateSortButtonText(null);
            closeSortingModal();
        } catch (error) {
            console.error('Error resetting sort view:', error);
            sessionStorage.removeItem(cacheKey);

            await loadExistingTasks();
            await sortTable(null, 'asc');
            updateSortButtonText(null);
            closeSortingModal();
        }
    }

    document.addEventListener('DOMContentLoaded', async () => {
    const projectId = window.PROJECT_ID;
    const cacheKey = `sortingView_${projectId}`;
    let cached = sessionStorage.getItem(cacheKey);

    async function applySorting(data) {
        // Defensive: handle missing or invalid data
        if (!data || !data.column || columnNames.indexOf(data.column) === -1 || data.project_id != window.PROJECT_ID) {
            await sortTable(null, 'asc');
            return;
        }
        const columnIndex = columnNames.indexOf(data.column);
        const sortOrder = data.order || 'asc';
        await sortTable(columnIndex, sortOrder);
    }

    try {
        let sortingPromise;
        if (cached) {
            sortingPromise = Promise.resolve(JSON.parse(cached));
        } else {
            sortingPromise = fetch(`/get-sorting-view?project_id=${projectId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                sessionStorage.setItem(cacheKey, JSON.stringify(data));
                return data;
            });
        }

        // Start loading tasks and sorting preferences in parallel
        const [_, sortingData] = await Promise.all([
            window.loadExistingTasks(),
            sortingPromise
        ]);
        await applySorting(sortingData);
        window.tasksLoaded = true;
    } catch (e) {
        await sortTable(null, 'asc');
    } finally {
        if (typeof window.hideSpinner === 'function') {
            window.hideSpinner();
        }
    }
});
window.resetSorting = resetSorting;
window.closeSortingModal = closeSortingModal;
window.sortTable = sortTable;
window.updateSortButtonText = updateSortButtonText;