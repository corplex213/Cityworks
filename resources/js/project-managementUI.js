// Common CSS classes
    const commonClasses = {
        button: 'font-semibold py-2 px-4 rounded-lg shadow transition',
        primaryButton: 'bg-blue-500 hover:bg-blue-600 text-white',
        successButton: 'bg-green-500 hover:bg-green-600 text-white',
        dangerButton: 'bg-red-500 hover:bg-red-600 text-white',
        tableCell: 'px-6 py-4 border-r border-gray-300 dark:border-gray-600',
        input: 'w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-2'
    };

    // Color mapping for status and priority
    const colorMapping = {
        High: 'bg-red-500',
        Normal: 'bg-yellow-500',
        Low: 'bg-green-500',
        Completed: 'bg-green-500 text-white',
            'For Checking': 'bg-blue-500 text-white',
            'For Revision': 'bg-yellow-500 text-white',
            'Deferred': 'bg-red-500 text-white'
    };

function getColor(type, value) {
    return colorMapping[value] || (type === 'priority' ? 'bg-yellow-500' : 'bg-gray-500');
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


function switchTab(tabName) {
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.add('hidden');
        });
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active', 'text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400');
            button.classList.add('text-gray-500', 'dark:text-gray-400', 'border-transparent');
        });
        document.getElementById(tabName + 'TabContent').classList.remove('hidden');
        document.getElementById(tabName + 'Tab').classList.add('active', 'text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400');
        document.getElementById(tabName + 'Tab').classList.remove('text-gray-500', 'dark:text-gray-400', 'border-transparent');
}

function toggleUserKanban(userId) {
    const kanbanDiv = document.getElementById('user-kanban-' + userId);
    const icon = document.getElementById('toggle-icon-' + userId);
    if (!kanbanDiv) return;
    kanbanDiv.classList.toggle('hidden');
    if (kanbanDiv.classList.contains('hidden')) {
        icon.classList.add('rotate-180');
    } else {
        icon.classList.remove('rotate-180');
    }
}

function openUserModal() {
    modalHandler.open('userSelectionModal', () => {
        updateUserButtonStates();
        setupUserSelectionHandlers();
    });
}
window.openUserModal = openUserModal;
function closeUserModal() {
            const modal = document.getElementById('userSelectionModal');
            // Animate out
            modal.classList.add('opacity-0', 'scale-95');
            // Hide after animation completes
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
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
window.updateUserButtonStates = updateUserButtonStates;

function setupUserSelectionHandlers() {
    document.querySelectorAll('#userList button').forEach(button => {
        button.onclick = function () {
            const userId = this.getAttribute('data-user-id');
            const userName = this.textContent.trim();
            if (!selectedUsers.has(userId)) {
                selectedUsers.add(userId);
                closeUserModal();
                addUserTable(userName, userId);
                updateUserButtonStates();
            }
        };
    });
}

function clearSearchBar() {
        const searchInput = document.getElementById('taskSearchInput');
        searchInput.value = ''; 
        filterTasks();
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


 // Open the user selection modal
        const addGroupBtn = document.getElementById('addGroupBtn');
        if (addGroupBtn) {
            addGroupBtn.addEventListener('click', function () {
                openUserModal();
            });
        }

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
// Load existing tasks when the page loads
document.addEventListener('DOMContentLoaded', () => {
    if (window.showGlobalLoading) {
        window.showGlobalLoading();
    }

    Promise.resolve(window.loadExistingTasks && window.loadExistingTasks())
        .finally(() => {
            if (window.hideGlobalLoading) window.hideGlobalLoading();

            setTimeout(() => {
                updateUserButtonStates();
                setupUserSelectionHandlers();
            }, 0);
        });
});
function enableTaskDescriptionEdit() {
    const readonly = document.getElementById('taskDescriptionReadonly');
    const editor = document.getElementById('taskDescriptionEditorWrapper');

    // Animate out readonly
    readonly.classList.add('opacity-0', 'translate-y-2');
    setTimeout(() => {
        readonly.classList.add('hidden');
        editor.classList.remove('hidden');
        editor.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => {
            editor.classList.remove('opacity-0', 'translate-y-2');
        }, 10);
        if (window.quill) window.quill.enable(true);
    }, 300);
}

function showTaskDescriptionReadonly(content) {
    const readonly = document.getElementById('taskDescriptionReadonly');
    const editor = document.getElementById('taskDescriptionEditorWrapper');
    const descContent = document.getElementById('taskDescriptionContent');

    // Normalize content for empty check
    const temp = document.createElement('div');
    temp.innerHTML = content || '';
    const text = temp.textContent.trim();
    const isEmpty = !content || content.trim() === '' || content.trim() === '<p><br></p>' || text === '';

    // Only update the description, not the whole div!
    if (descContent) {
        descContent.innerHTML = isEmpty
            ? '<span class="text-gray-400">Add a brief description to your task.</span>'
            : content;
    }

    // Animate out editor, animate in readonly
    editor.classList.add('opacity-0', 'translate-y-2');
    setTimeout(() => {
        editor.classList.add('hidden');
        readonly.classList.remove('hidden');
        readonly.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => {
            readonly.classList.remove('opacity-0', 'translate-y-2');
        }, 10);
        if (window.quill) window.quill.enable(false);
    }, 300);
}

function saveTaskDescription(description) {
    const drawer = document.getElementById('taskDetailsDrawer');
    const taskId = drawer.getAttribute('data-current-row');
    const saveBtn = document.getElementById('saveTaskDescBtn');
    const saveBtnText = document.getElementById('saveBtnText');
    const saveSpinner = document.getElementById('saveSpinner');
    if (!taskId) {
        alert('No task selected.');
        return;
    }

    // Animate button
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.classList.add('opacity-60', 'cursor-not-allowed');
        if (saveBtnText) saveBtnText.textContent = 'Saving...';
        if (saveSpinner) saveSpinner.classList.remove('hidden');
    }

    fetch(`/tasks/${taskId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            task_description: description
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (window.quill && data.task_description !== undefined) {
            window.quill.root.innerHTML = data.task_description || '';
        }
        showTaskDescriptionReadonly(data.task_description);
        if (window.loadExistingTasks) window.loadExistingTasks();
        showToast('Task description saved!', 'success');
    })
    .catch(error => {
        showToast(error.error || 'Failed to save task description.', 'error');
    })
    .finally(() => {
        // Restore button state
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.classList.remove('opacity-60', 'cursor-not-allowed');
            if (saveBtnText) saveBtnText.textContent = 'Save';
            if (saveSpinner) saveSpinner.classList.add('hidden');
        }
    });
}



// Simple toast function (add this somewhere in your JS)
function showToast(message, type = 'success') {
    let toast = document.createElement('div');
    toast.textContent = message;
    toast.className = `fixed bottom-8 right-8 z-50 px-6 py-3 rounded-lg shadow-lg text-white font-semibold transition
        ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 500);
    }, 2000);
}

window.enableTaskDescriptionEdit = enableTaskDescriptionEdit;
window.showTaskDescriptionReadonly = showTaskDescriptionReadonly;
window.showToast = showToast;
window.saveTaskDescription = saveTaskDescription;
window.switchView = switchView;
window.switchTab = switchTab;
window.toggleUserKanban = toggleUserKanban;
window.openUserModal = openUserModal;
window.closeUserModal = closeUserModal;
window.clearSearchBar = clearSearchBar;
window.filterTasks = filterTasks;
window.getColor = getColor;
window.updateUserButtonStates = updateUserButtonStates;