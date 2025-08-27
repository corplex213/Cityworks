function switchView(view) {
        const mainTableBtn = document.getElementById('mainTableBtn');
        const kanbanBtn = document.getElementById('kanbanBtn');
        const mainTableSection = document.getElementById('mainTableSection');
        const kanbanSection = document.getElementById('kanbanSection');
        const sortBtnContainer = document.getElementById('sortBtnContainer');

        // Reset all buttons and sections
        [mainTableBtn, kanbanBtn].forEach(btn => {
            if (btn) {
                btn.classList.remove('active', 'text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400');
                btn.classList.add('text-gray-500', 'dark:text-gray-400', 'border-transparent');
            }
        });

        // Hide all sections
        [mainTableSection, kanbanSection].forEach(section => {
            if (section) {
                section.classList.add('hidden'); // Ensure all sections are hidden
            }
        });

        // Show selected section and activate button
        switch (view) {
            case 'mainTable':
                if (mainTableSection && mainTableBtn) {
                    mainTableSection.classList.remove('hidden'); // Show the Main Table section
                    mainTableBtn.classList.remove('text-gray-500', 'dark:text-gray-400', 'border-transparent');
                    mainTableBtn.classList.add('active', 'text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400');
                }
                if (sortBtnContainer) sortBtnContainer.style.display = '';
                break;
            case 'kanban':
                if (kanbanSection && kanbanBtn) {
                    kanbanSection.classList.remove('hidden'); // Show the Kanban section
                    kanbanBtn.classList.remove('text-gray-500', 'dark:text-gray-400', 'border-transparent');
                    kanbanBtn.classList.add('active', 'text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400');
                }
                if (sortBtnContainer) sortBtnContainer.style.display = 'none';
                break;
        }
    }
function switchView(view) {
            const mainTableBtn = document.getElementById('mainTableBtn');
            const kanbanBtn = document.getElementById('kanbanBtn');
            const mainTableSection = document.getElementById('mainTableSection');
            const kanbanSection = document.getElementById('kanbanSection');
            const sortBtnContainer = document.getElementById('sortBtnContainer');

            // Reset all buttons and sections
            [mainTableBtn, kanbanBtn].forEach(btn => {
                if (btn) {
                    btn.classList.remove('active', 'text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400');
                    btn.classList.add('text-gray-500', 'dark:text-gray-400', 'border-transparent');
                }
            });

            // Hide all sections
            [mainTableSection, kanbanSection].forEach(section => {
                if (section) {
                    section.classList.add('hidden');
                }
            });

            // Show selected section and activate button
            switch (view) {
                case 'mainTable':
                    if (mainTableSection && mainTableBtn) {
                        mainTableSection.classList.remove('hidden');
                        mainTableBtn.classList.remove('text-gray-500', 'dark:text-gray-400', 'border-transparent');
                        mainTableBtn.classList.add('active', 'text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400');
                    }
                    if (sortBtnContainer) sortBtnContainer.style.display = '';
                    break;
                case 'kanban':
                    if (kanbanSection && kanbanBtn) {
                        kanbanSection.classList.remove('hidden');
                        kanbanBtn.classList.remove('text-gray-500', 'dark:text-gray-400', 'border-transparent');
                        kanbanBtn.classList.add('active', 'text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400');
                    }
                    if (sortBtnContainer) sortBtnContainer.style.display = 'none';
                    break;
            }
}

// Add these new functions for tab switching
function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.add('hidden');
        });
        
        // Remove active class from all tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active', 'text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400');
            button.classList.add('text-gray-500', 'dark:text-gray-400', 'border-transparent');
        });
        
        // Show selected tab content and activate its button
        document.getElementById(tabName + 'TabContent').classList.remove('hidden');
        document.getElementById(tabName + 'Tab').classList.add('active', 'text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400');
        document.getElementById(tabName + 'Tab').classList.remove('text-gray-500', 'dark:text-gray-400', 'border-transparent');
}

function toggleUserKanban(userId) {
    const kanbanDiv = document.getElementById('user-kanban-' + userId);
    const icon = document.getElementById('toggle-icon-' + userId);
    if (!kanbanDiv) return;
    kanbanDiv.classList.toggle('hidden');
    // Rotate the arrow icon
    if (kanbanDiv.classList.contains('hidden')) {
        icon.classList.add('rotate-180');
    } else {
        icon.classList.remove('rotate-180');
    }
}

function openUserModal() {
            modalHandler.open('userSelectionModal', updateUserButtonStates);
        }

function closeUserModal() {
        modalHandler.close('userSelectionModal');
    }

function updateUserButtonStates() {
            document.querySelectorAll('#userList button').forEach(button => {
                const userId = button.getAttribute('data-user-id');
                if (selectedUsers.has(userId)) {
                    button.disabled = true;
                    button.classList.add('opacity-50', 'cursor-not-allowed');
                    button.classList.remove('hover:bg-gray-200', 'dark:hover:bg-gray-600');
                } else {
                    button.disabled = false;
                    button.classList.remove('opacity-50', 'cursor-not-allowed');
                    button.classList.add('hover:bg-gray-200', 'dark:hover:bg-gray-600');
                }
            });
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

function clearSearchBar() {
        const searchInput = document.getElementById('taskSearchInput');
        searchInput.value = ''; // Clear the input field
        filterTasks(); // Reset the table to show all rows
    }

function filterTasks() {
        const searchInput = document.getElementById('taskSearchInput').value.toLowerCase();
        const tables = document.querySelectorAll('#dynamicTablesContainer > div');
        let anyVisible = false;

            tables.forEach((tableWrapper) => {
            const tableHeader = tableWrapper.querySelector('h3');
            const tableRows = Array.from(tableWrapper.querySelectorAll('table tbody tr'));
            let tableVisible = false;

            if (!searchInput.trim()) {
                tableWrapper.style.display = '';
                tableRows.forEach((row) => row.style.display = '');
                tableVisible = true;
            } else {
                if (tableHeader && tableHeader.textContent.toLowerCase().includes(searchInput)) {
                    tableWrapper.style.display = '';
                    tableVisible = true;
                } else {
                    tableRows.forEach((row) => {
                        const taskCell = row.querySelector('td:first-child input');
                        if (taskCell && taskCell.value.toLowerCase().includes(searchInput)) {
                            row.style.display = '';
                            tableVisible = true;
                        } else {
                            row.style.display = 'none';
                        }
                    });
                    tableWrapper.style.display = tableVisible ? '' : 'none';
                }
            }
            if (tableVisible) anyVisible = true;
        });
        // Show/hide the "no tasks" placeholder
        const placeholder = document.getElementById('noTasksPlaceholder');
        if (placeholder) {
            placeholder.classList.toggle('hidden', anyVisible);
        }
}

function closeUserModal() {
            const modal = document.getElementById('userSelectionModal');
            // Animate out
            modal.classList.add('opacity-0', 'scale-95');
            // Hide after animation completes
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
}

 // Open the user selection modal
        document.getElementById('addGroupBtn').addEventListener('click', function () {
            openUserModal();
        });

        // Generic modal handler
        const modalHandler = {
            open: (modalId, callback) => {
                const modal = document.getElementById(modalId);
                modal.classList.remove('hidden');
                setTimeout(() => modal.classList.remove('opacity-0', 'scale-95'), 10);
                if (callback) callback();
            },
            close: (modalId, callback) => {
                const modal = document.getElementById(modalId);
                modal.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    if (callback) callback();
                }, 300);
            }
        };