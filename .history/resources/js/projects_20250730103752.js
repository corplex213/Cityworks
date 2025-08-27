import '../css/projects.css';
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';
document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.tab-link');
            const indicator = document.getElementById('tabIndicator');
    
            function updateIndicator() {
                const activeTab = document.querySelector('.tab-link.active-tab');
                if (activeTab) {
                    const tabRect = activeTab.getBoundingClientRect();
                    const parentRect = activeTab.parentElement.getBoundingClientRect();
                    indicator.style.width = `${tabRect.width}px`;
                    indicator.style.left = `${tabRect.left - parentRect.left}px`;
                }
            }
    
            // Update the indicator on page load
            updateIndicator();
    
            // Add click event to update the indicator when switching tabs
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.remove('active-tab'));
                    tab.classList.add('active-tab');
                    updateIndicator();
                });
            });
    
            // Update the indicator on window resize
            window.addEventListener('resize', updateIndicator);

    // JavaScript for Modal and Dropdown
        function closeToolbar() {
        // Hide the toolbar
        toolbar.classList.add('translate-y-full');

        // Reset the selected count
        selectedCount.textContent = 0;

        // Disable buttons
        deleteButton.disabled = true;
        archiveButton.disabled = true;
        editButton.disabled = true;

        // Uncheck all checkboxes
        const checkboxes = document.querySelectorAll('.project-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });

        // Uncheck the "Select All" checkbox
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = false;
        }
    }
        function toggleModal(modalId, isVisible) {
            const modal = document.getElementById(modalId);
            if (isVisible) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }
        function openModal() {
            const modal = document.getElementById('addProjectModal');
            const content = document.getElementById('addProjectContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('opacity-0', 'scale-95');
            }, 10);
        }

        function closeModal() {
            const modal = document.getElementById('addProjectModal');
            const content = document.getElementById('addProjectContent');
            content.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
        function openEditModal(id, name, projType) {
            const modal = document.getElementById('editProjectModal');
            const form = document.getElementById('editProjectForm');

            form.action = `/projects/${id}`;
            document.getElementById('editProjectId').value = id;
            document.getElementById('editProjectName').value = name;
            document.getElementById('editProjectType').value = projType;
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.querySelector('.bg-white').classList.remove('opacity-0', 'scale-95');
            }, 10);
        }

        function closeEditModal() {
            const modal = document.getElementById('editProjectModal');
            const content = modal.querySelector('.bg-white');

            // Hide the modal with animation
            content.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
        function toggleDropdown(projectId) {
            document.getElementById('dropdown-' + projectId).classList.toggle('hidden');
        }
        function toggleStatusDropdown(projectId) {
            const dropdown = document.getElementById(`statusDropdown-${projectId}`);
            if (!dropdown) {
                console.error(`Dropdown with ID statusDropdown-${projectId} not found.`);
                return;
            }

            if (dropdown.classList.contains('hidden')) {
                // Show the dropdown with animation
                dropdown.classList.remove('hidden');
                setTimeout(() => {
                    dropdown.classList.remove('opacity-0', 'scale-95');
                    dropdown.classList.add('opacity-100', 'scale-100');
                }, 10);
            } else {
                // Hide the dropdown with animation
                dropdown.classList.remove('opacity-100', 'scale-100');
                dropdown.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    dropdown.classList.add('hidden');
                }, 300); // Match the duration of the animation
            }
        }
        function openProject(name, description) {
            document.getElementById('projectSelectionSection').classList.add('hidden');
            document.getElementById('projectOverviewSection').classList.remove('hidden');
            document.getElementById('projectName').textContent = name;
            document.getElementById('projectDescription').textContent = description;
        }
        function openDeleteModal(projectId, deleteUrl) {
            const modal = document.getElementById('deleteConfirmationModal');
            const content = document.getElementById('deleteConfirmationContent');
            const form = document.getElementById('deleteProjectForm');

            // Set the form action to the delete URL
            form.action = deleteUrl;

            // Show the modal with animation
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('opacity-0', 'scale-95');
            }, 10);
        }
        function closeDeleteModal() {
            const modal = document.getElementById('deleteConfirmationModal');
            const content = document.getElementById('deleteConfirmationContent');

            // Hide the modal with animation
            content.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
        function toggleSortDropdown() {
            const dropdown = document.getElementById('sortDropdownMenu');
            const button = document.getElementById('sortDropdownButton');

            // Toggle dropdown visibility
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                setTimeout(() => {
                    dropdown.classList.remove('opacity-0', 'scale-95');
                    dropdown.classList.add('opacity-100', 'scale-100');
                }, 10);

                // Add event listener to close dropdown when clicking outside
                document.addEventListener('click', closeDropdownOnClickOutside);
            } else {
                closeDropdown();
            }

            function closeDropdown() {
                dropdown.classList.remove('opacity-100', 'scale-100');
                dropdown.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    dropdown.classList.add('hidden');
                }, 300);
                // Remove the event listener
                document.removeEventListener('click', closeDropdownOnClickOutside);
            }

            function closeDropdownOnClickOutside(event) {
                if (!dropdown.contains(event.target) && !button.contains(event.target)) {
                    closeDropdown();
                }
            }
        }
        function clearSearchBar() {
            const searchInput = document.getElementById('searchInput');
            searchInput.value = ''; // Clear the input field
            filterProjects(); // Reset the table to show all rows
        }
    
        function filterProjects() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const projectRows = document.querySelectorAll('#projectTableBody .project-row');

            projectRows.forEach((row) => {
                const projectNameElement = row.querySelector('.project-name a');
                const locationElement = row.querySelector('td:nth-child(3)');
                const statusElement = row.querySelector('td:nth-child(4) button');

                // Ensure the elements exist before accessing their text content
                const projectName = projectNameElement ? projectNameElement.textContent.toLowerCase() : '';
                const location = locationElement ? locationElement.textContent.toLowerCase() : '';
                const status = statusElement ? statusElement.textContent.toLowerCase() : '';

                if (
                    projectName.includes(searchInput) ||
                    location.includes(searchInput) ||
                    status.includes(searchInput)
                ) {
                    row.style.display = ''; // Show the row
                } else {
                    row.style.display = 'none'; // Hide the row
                }
            });
        }
        function toggleSelectAll(selectAllCheckbox) {
            const checkboxes = document.querySelectorAll('.project-checkbox');
            checkboxes.forEach((checkbox) => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        }
        function deleteSelectedProjects() {
            const selectedIds = Array.from(document.querySelectorAll('.project-checkbox:checked'))
                .map(checkbox => checkbox.closest('tr').dataset.projectId);

            if (selectedIds.length > 0) {
                // Open the delete confirmation modal
                const modal = document.getElementById('deleteConfirmationModal');
                const content = document.getElementById('deleteConfirmationContent');
                const form = document.getElementById('deleteProjectForm');

                // Set the selected IDs as a hidden input in the form
                let idsInput = form.querySelector('input[name="ids"]');
                if (!idsInput) {
                    idsInput = document.createElement('input');
                    idsInput.type = 'hidden';
                    idsInput.name = 'ids';
                    form.appendChild(idsInput);
                }
                idsInput.value = JSON.stringify(selectedIds);

                // Show the modal with animation
                modal.classList.remove('hidden');
                setTimeout(() => {
                    content.classList.remove('opacity-0', 'scale-95');
                }, 10);
            } else {
                alert('No projects selected.');
            }
        }
        function archiveSelectedProjects() {
            const selectedIds = Array.from(document.querySelectorAll('.project-checkbox:checked'))
                .map(checkbox => checkbox.closest('tr').dataset.projectId);

            if (selectedIds.length > 0) {
                if (confirm('Are you sure you want to archive the selected projects?')) {
                    // Send a request to archive the selected projects
                    fetch('{{ route("projects.bulkArchive") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ ids: selectedIds })
                    }).then(response => {
                        if (response.ok) {
                            location.reload(); // Reload the page after archiving
                        } else {
                            alert('Failed to archive selected projects.');
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
                openDeleteModal(null, '{{ route("projects.bulkDelete") }}', selectedIds);
            } else {
                alert('No projects selected.');
            }
        }

        function confirmArchiveSelectedProjects() {
            const selectedIds = Array.from(document.querySelectorAll('.project-checkbox:checked'))
                .map(checkbox => checkbox.closest('tr').dataset.projectId);

            if (selectedIds.length > 0) {
                openArchiveModal(selectedIds);
            } else {
                alert('No projects selected.');
            }
        }

    function openArchiveModal(selectedIds) {
        const modal = document.getElementById('archiveConfirmationModal');
        const content = document.getElementById('archiveConfirmationContent');
        const form = document.getElementById('archiveProjectForm');
        const container = document.getElementById('archiveProjectIdsContainer');
        
        // Clear previous inputs
        container.innerHTML = '';
        
        // Create a hidden input for each ID
        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            container.appendChild(input);
        });

        // Show the modal with animation
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('opacity-0', 'scale-95');
        }, 10);
    }

    function closeArchiveModal() {
        const modal = document.getElementById('archiveConfirmationModal');
        const content = document.getElementById('archiveConfirmationContent');

        // Hide the modal with animation
        content.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
        const toolbar = document.getElementById('toolbar');
        const deleteButton = document.getElementById('deleteSelectedButton');
        const archiveButton = document.getElementById('archiveSelectedButton');
        const selectedCount = document.getElementById('selectedCount');
        const editButton = document.getElementById('editSelectedButton');
    
        // Function to toggle toolbar visibility based on selected checkboxes
        function updateToolbar() {
            const checkboxes = document.querySelectorAll('.project-checkbox');
            const selectedCheckboxes = Array.from(checkboxes).filter(checkbox => checkbox.checked);

            if (selectedCheckboxes.length > 0) {
                toolbar.classList.remove('translate-y-full'); // Slide in
                selectedCount.textContent = selectedCheckboxes.length;
                deleteButton.disabled = false;
                archiveButton.disabled = false;
                editButton.disabled = selectedCheckboxes.length !== 1; // Enable only if one project is selected
            } else {
                toolbar.classList.add('translate-y-full'); // Slide out
                selectedCount.textContent = 0;
                deleteButton.disabled = true;
                archiveButton.disabled = true;
                editButton.disabled = true;
            }
        }

        // Attach event listeners to checkboxes
        document.querySelectorAll('.project-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateToolbar);
        });

        // Attach event listener to "Select All" checkbox
        document.getElementById('selectAllCheckbox').addEventListener('change', function () {
            toggleSelectAll(this);
        });
    
        // Function to toggle all checkboxes when "Select All" is clicked
        function toggleSelectAll(selectAllCheckbox) {
            const checkboxes = document.querySelectorAll('.project-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateToolbar();
        }
    
        // Attach event listeners to checkboxes
        document.querySelectorAll('.project-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateToolbar);
        });
    
        // Attach event listener to "Select All" checkbox
        document.getElementById('selectAllCheckbox').addEventListener('change', function () {
            toggleSelectAll(this);
        });
        function editSelectedProjects() {
            const selectedCheckboxes = Array.from(document.querySelectorAll('.project-checkbox:checked'));
            if (selectedCheckboxes.length === 1) {
                const selectedRow = selectedCheckboxes[0].closest('tr');
                const projectId = selectedRow.dataset.projectId;
                const projectName = selectedRow.querySelector('.project-name a').textContent.trim();
                const projectLocation = selectedRow.querySelector('td:nth-child(3)').textContent.trim();
                const projectDescription = selectedRow.dataset.description; // Retrieve the description

                // Debugging: Log the selected project data
                console.log({ projectId, projectName, projectLocation, projectDescription });

                // Open the edit modal with the selected project's data
                openEditModal(projectId, projectName, projectLocation, projectDescription);
            } else if (selectedCheckboxes.length > 1) {
                alert('You can only edit one project at a time. Please select only one project.');
            } else {
                alert('No project selected. Please select a project to edit.');
            }
        }
        function exportProjectsToPDF() {
            const table = document.querySelector('table');
            if (!table) return alert('No table found.');

            html2canvas(table).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('l', 'pt', 'a4');
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                const imgWidth = pageWidth - 40;
                const imgHeight = canvas.height * imgWidth / canvas.width;
                pdf.addImage(imgData, 'PNG', 20, 20, imgWidth, imgHeight);
                pdf.save('Activities.pdf');
            });
        }

        function exportProjectsToCSV() {
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
            link.setAttribute('download', 'Activities.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    window.exportProjectsToPDF = exportProjectsToPDF;
    window.exportProjectsToCSV = exportProjectsToCSV;
    window.openModal = openModal;
    window.closeModal = closeModal;
    window.openEditModal = openEditModal;
    window.closeEditModal = closeEditModal;
    window.toggleDropdown = toggleDropdown;
    window.toggleStatusDropdown = toggleStatusDropdown;
    window.openProject = openProject;
    window.openDeleteModal = openDeleteModal;
    window.closeDeleteModal = closeDeleteModal;
    window.toggleSortDropdown = toggleSortDropdown;
    window.clearSearchBar = clearSearchBar;
    window.filterProjects = filterProjects;
    window.toggleSelectAll = toggleSelectAll;
    window.deleteSelectedProjects = deleteSelectedProjects;
    window.archiveSelectedProjects = archiveSelectedProjects;
    window.confirmDeleteSelectedProjects = confirmDeleteSelectedProjects;
    window.confirmArchiveSelectedProjects = confirmArchiveSelectedProjects;
    window.openArchiveModal = openArchiveModal;
    window.closeArchiveModal = closeArchiveModal;
    window.editSelectedProjects = editSelectedProjects;
    window.closeToolbar = closeToolbar;
});