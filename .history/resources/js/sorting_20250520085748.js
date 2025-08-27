// Sorting-related functions
document.getElementById('sortBtn').addEventListener('click', () => {
    document.getElementById('sortingModal').classList.remove('hidden');
});
function closeSortingModal() {
    document.getElementById('sortingModal').classList.add('hidden');
}
function resetSorting() {
    const tables = document.querySelectorAll('#dynamicTablesContainer table');
    if (!tables.length) return;
    fetch('/reset-sorting-view', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(() => {
        loadExistingTasks().then(() => {
            sessionStorage.removeItem('sortPreferences');
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
document.getElementById('sortingForm').addEventListener('submit', (event) => {
    event.preventDefault();
    const columnIndex = parseInt(document.getElementById('sortColumn').value);
    const sortOrder = document.querySelector('input[name="sortOrder"]:checked').value;
    sortTable(columnIndex, sortOrder);
    updateSortButtonText(columnIndex);
    closeSortingModal();
});
function sortTable(columnIndex = null, sortOrder = 'asc') {
    // ...existing code for sortTable...
}
function updateSortButtonText(columnIndex) {
    // ...existing code for updateSortButtonText...
}
function saveSortingView(columnIndex, sortOrder) {
    // ...existing code for saveSortingView...
}
document.addEventListener('DOMContentLoaded', () => {
    fetch('/get-sorting-view', {
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
            if (data.column && columnIndex !== -1) {
                sortTable(columnIndex, sortOrder);
            }
        });
    })
    .catch(error => {
        console.error('Error fetching sorting view:', error);
        loadExistingTasks();
    });
});
