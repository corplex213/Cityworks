import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

document.addEventListener('DOMContentLoaded', function () {
    const toolbar = document.getElementById('toolbar');
    const deleteButton = document.getElementById('deleteSelectedButton');
    const restoreButton = document.getElementById('restoreSelectedButton');
    const selectedCount = document.getElementById('selectedCount');

    function closeToolbar() {
        toolbar.classList.add('translate-y-full');
        selectedCount.textContent = 0;
        deleteButton.disabled = true;
        restoreButton.disabled = true;
        const checkboxes = document.querySelectorAll('.project-checkbox');
        checkboxes.forEach(checkbox => { checkbox.checked = false; });
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        if (selectAllCheckbox) selectAllCheckbox.checked = false;
    }

    function filterArchivedProjects() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const projectRows = document.querySelectorAll('#archivedProjectTableBody .archived-project-row');
        projectRows.forEach((row) => {
            const projectName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const status = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            if (projectName.includes(searchInput) || status.includes(searchInput)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function clearArchivedSearchBar() {
        const searchInput = document.getElementById('searchInput');
        searchInput.value = '';
        filterArchivedProjects();
    }

    function updateToolbar() {
        const checkboxes = document.querySelectorAll('.project-checkbox');
        const selectedCheckboxes = Array.from(checkboxes).filter(checkbox => checkbox.checked);
        if (selectedCheckboxes.length > 0) {
            toolbar.classList.remove('translate-y-full');
            selectedCount.textContent = selectedCheckboxes.length;
            deleteButton.disabled = false;
            restoreButton.disabled = false;
        } else {
            toolbar.classList.add('translate-y-full');
            selectedCount.textContent = 0;
            deleteButton.disabled = true;
            restoreButton.disabled = true;
        }
    }

    function toggleSelectAll(selectAllCheckbox) {
        const checkboxes = document.querySelectorAll('.project-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        updateToolbar();
    }

    document.querySelectorAll('.project-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateToolbar);
    });

    document.getElementById('selectAllCheckbox').addEventListener('change', function () {
        toggleSelectAll(this);
    });

    function deleteSelectedProjects() {
        const selectedIds = Array.from(document.querySelectorAll('.project-checkbox:checked'))
            .map(checkbox => checkbox.closest('tr').dataset.projectId);

        if (selectedIds.length > 0) {
            if (confirm('Are you sure you want to delete the selected projects?')) {
                fetch(deleteRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ ids: selectedIds })
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Failed to delete selected projects.');
                    }
                });
            }
        } else {
            alert('No projects selected.');
        }
    }

    function restoreSelectedProjects() {
        const selectedIds = Array.from(document.querySelectorAll('.project-checkbox:checked'))
            .map(checkbox => checkbox.closest('tr').dataset.projectId);

        if (selectedIds.length > 0) {
            if (confirm('Are you sure you want to restore the selected projects?')) {
                fetch(restoreRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ ids: selectedIds })
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Failed to restore selected projects.');
                    }
                });
            }
        } else {
            alert('No projects selected.');
        }
    }

    function confirmDeleteSelectedProjects() {
        const selectedIds = Array.from(document.querySelectorAll('.project-checkbox:checked'))
            .map(checkbox => checkbox.closest('tr').dataset.projectId);

        if (selectedIds.length > 0) {
            openDeleteModal(selectedIds);
        } else {
            alert('No projects selected.');
        }
    }

    function confirmRestoreSelectedProjects() {
        const selectedIds = Array.from(document.querySelectorAll('.project-checkbox:checked'))
            .map(checkbox => checkbox.closest('tr').dataset.projectId);

        if (selectedIds.length > 0) {
            openRestoreModal(selectedIds);
        } else {
            alert('No projects selected.');
        }
    }

    function openRestoreModal(selectedIds) {
        const modal = document.getElementById('restoreConfirmationModal');
        const content = document.getElementById('restoreConfirmationContent');
        const form = document.getElementById('restoreProjectForm');
        const container = document.getElementById('restoreProjectIdsContainer');
        container.innerHTML = '';
        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            container.appendChild(input);
        });
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('opacity-0', 'scale-95');
        }, 10);
    }

    function closeRestoreModal() {
        const modal = document.getElementById('restoreConfirmationModal');
        const content = document.getElementById('restoreConfirmationContent');
        content.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function openDeleteModal(selectedIds) {
        const modal = document.getElementById('deleteConfirmationModal');
        const content = document.getElementById('deleteConfirmationContent');
        const form = document.getElementById('deleteProjectForm');
        form.action = deleteRoute;
        form.querySelector('input[name="ids"]').value = JSON.stringify(selectedIds);
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('opacity-0', 'scale-95');
        }, 10);
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteConfirmationModal');
        const content = document.getElementById('deleteConfirmationContent');
        content.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function exportArchivedToPDF() {
        const table = document.querySelector('table');
        if (!table) return alert('No table found.');
        html2canvas(table).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF('l', 'pt', 'a4');
            const pageWidth = pdf.internal.pageSize.getWidth();
            const imgWidth = pageWidth - 40;
            const imgHeight = canvas.height * imgWidth / canvas.width;
            pdf.addImage(imgData, 'PNG', 20, 20, imgWidth, imgHeight);
            pdf.save('archived-activities.pdf');
        });
    }

    function exportArchivedToCSV() {
        const table = document.querySelector('table');
        if (!table) return alert('No table found.');
        let csv = [];
        const rows = table.querySelectorAll('tr');
        for (let row of rows) {
            let cols = Array.from(row.querySelectorAll('th,td')).map(col => `"${col.innerText.replace(/"/g, '""')}"`);
            csv.push(cols.join(','));
        }
        const csvContent = "data:text/csv;charset=utf-8," + csv.join('\n');
        const link = document.createElement('a');
        link.setAttribute('href', encodeURI(csvContent));
        link.setAttribute('download', 'archived-projects.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Expose functions to window for inline onclick
    window.closeToolbar = closeToolbar;
    window.filterArchivedProjects = filterArchivedProjects;
    window.clearArchivedSearchBar = clearArchivedSearchBar;
    window.toggleSelectAll = toggleSelectAll;
    window.deleteSelectedProjects = deleteSelectedProjects;
    window.restoreSelectedProjects = restoreSelectedProjects;
    window.confirmDeleteSelectedProjects = confirmDeleteSelectedProjects;
    window.confirmRestoreSelectedProjects = confirmRestoreSelectedProjects;
    window.openRestoreModal = openRestoreModal;
    window.closeRestoreModal = closeRestoreModal;
    window.openDeleteModal = openDeleteModal;
    window.closeDeleteModal = closeDeleteModal;
    window.exportArchivedToPDF = exportArchivedToPDF;
    window.exportArchivedToCSV = exportArchivedToCSV;

    // CSRF and route variables (set in Blade)
    window.deleteRoute = window.deleteRoute || '';
    window.restoreRoute = window.restoreRoute || '';
    window.csrfToken = window.csrfToken || '';
});