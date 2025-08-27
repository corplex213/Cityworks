function saveTask(tableWrapper) {
    const table = tableWrapper.querySelector('table');
    const rows = table.querySelectorAll('tbody tr:not(.subtask-row)');
    const projectId = window.PROJECT_ID;
    const assignedTo = tableWrapper.getAttribute('data-user-id');
    let savePromises = [];
    let hasChanges = false;
    let hasValidationError = false;
    let validationMessages = [];

    rows.forEach(row => {
        const taskId = row.getAttribute('data-task-id');
        const taskNameInput = row.querySelector('td:first-child input');
        const startDateInput = row.querySelector('td:nth-child(2) input');
        const dueDateInput = row.querySelector('td:nth-child(3) input');
        const priorityElement = row.querySelector('.priority-value');
        const statusElement = row.querySelector('.status-value');
        const budgetInput = row.querySelector('td:nth-child(6) input');

        let sourceOfFunding = null;
        let otherFundingSource = null;
        if (projectType === 'POW') {
            const sourceSelect = row.querySelector('td:nth-child(7) select[name="source_of_funding"]');
            sourceOfFunding = sourceSelect ? sourceSelect.value : null;
            const otherInput = row.querySelector('td:nth-child(7) input[name="other_funding_source"]');
            otherFundingSource = otherInput ? otherInput.value : null;
        }

        if (!taskNameInput || !startDateInput || !dueDateInput || !priorityElement || !statusElement || !budgetInput) {
            console.error('Missing required elements in row:', row);
            return;
        }

        // --- Date validation ---
        const startDate = startDateInput.value;
        const dueDate = dueDateInput.value;
        if (startDate && dueDate && new Date(dueDate) < new Date(startDate)) {
            hasValidationError = true;
            validationMessages.push(`Task "${taskNameInput.value}" has a due date earlier than its start date.`);
            return;
        }

        const budgetValue = parseFloat(budgetInput.value.replace(/[^0-9.]/g, '')) || 0;

        // Detect changes for main task
        let isRowChanged = false;
        if (
            taskNameInput.value !== (taskNameInput.getAttribute('data-old-value') || '') ||
            startDateInput.value !== (startDateInput.getAttribute('data-old-value') || '') ||
            dueDateInput.value !== (dueDateInput.getAttribute('data-old-value') || '') ||
            priorityElement.textContent !== (priorityElement.getAttribute('data-old-value') || '') ||
            statusElement.textContent !== (statusElement.getAttribute('data-old-value') || '') ||
            budgetValue !== Number(budgetInput.getAttribute('data-old-value') || 0)
        ) {
            isRowChanged = true;
        }

        // POW fields
        if (projectType === 'POW') {
            if (
                sourceOfFunding !== (row.getAttribute('data-old-source_of_funding') || '') ||
                otherFundingSource !== (row.getAttribute('data-old-other_funding_source') || '')
            ) {
                isRowChanged = true;
            }
        }

        // Check for subtask changes
        const subtasks = [];
        let nextRow = row.nextElementSibling;
        while (nextRow && nextRow.classList.contains('subtask-row')) {
            let subtaskSourceOfFunding = null;
            let subtaskOtherFundingSource = null;
            if (projectType === 'POW') {
                const subtaskSourceSelect = nextRow.querySelector('td:nth-child(7) select[name="source_of_funding"]');
                subtaskSourceOfFunding = subtaskSourceSelect ? subtaskSourceSelect.value : null;
                const subtaskOtherInput = nextRow.querySelector('td:nth-child(7) input[name="other_funding_source"]');
                subtaskOtherFundingSource = subtaskOtherInput ? subtaskOtherInput.value : null;
            }
            const subtaskId = nextRow.getAttribute('data-task-id');
            const subtaskNameInput = nextRow.querySelector('td:first-child input');
            const subtaskStartDateInput = nextRow.querySelector('td:nth-child(2) input');
            const subtaskDueDateInput = nextRow.querySelector('td:nth-child(3) input');
            const subtaskPriorityElement = nextRow.querySelector('.priority-value');
            const subtaskStatusElement = nextRow.querySelector('.status-value');
            const subtaskBudgetInput = nextRow.querySelector('td:nth-child(6) input');

            if (!subtaskNameInput || !subtaskStartDateInput || !subtaskDueDateInput || !subtaskPriorityElement || !subtaskStatusElement || !subtaskBudgetInput) {
                console.error('Missing required elements in subtask row:', nextRow);
                nextRow = nextRow.nextElementSibling;
                continue;
            }

            // --- Date validation for subtasks ---
            const subtaskStartDate = subtaskStartDateInput.value;
            const subtaskDueDate = subtaskDueDateInput.value;
            if (subtaskStartDate && subtaskDueDate && new Date(subtaskDueDate) < new Date(subtaskStartDate)) {
                hasValidationError = true;
                validationMessages.push(`Subtask "${subtaskNameInput.value}" has a due date earlier than its start date.`);
                nextRow = nextRow.nextElementSibling;
                continue;
            }

            const subtaskBudgetValue = parseFloat(subtaskBudgetInput.value.replace(/[^0-9.]/g, '')) || 0;

            // Detect changes for subtask
            let isSubtaskChanged = false;
            if (
                subtaskNameInput.value !== (subtaskNameInput.getAttribute('data-old-value') || '') ||
                subtaskStartDateInput.value !== (subtaskStartDateInput.getAttribute('data-old-value') || '') ||
                subtaskDueDateInput.value !== (subtaskDueDateInput.getAttribute('data-old-value') || '') ||
                subtaskPriorityElement.textContent !== (subtaskPriorityElement.getAttribute('data-old-value') || '') ||
                subtaskStatusElement.textContent !== (subtaskStatusElement.getAttribute('data-old-value') || '') ||
                subtaskBudgetValue !== Number(subtaskBudgetInput.getAttribute('data-old-value') || 0)
            ) {
                isSubtaskChanged = true;
            }
            if (projectType === 'POW') {
                if (
                    subtaskSourceOfFunding !== (nextRow.getAttribute('data-old-source_of_funding') || '') ||
                    subtaskOtherFundingSource !== (nextRow.getAttribute('data-old-other_funding_source') || '')
                ) {
                    isSubtaskChanged = true;
                }
            }

            if (isSubtaskChanged) hasChanges = true;

            // Log activity for subtask changes
            if (isSubtaskChanged && subtaskId) {
                // Create a changes object to track what changed
                const changes = {
                    parent_task_name: taskNameInput.value // Include parent task name for context
                };
                
                let hasChanges = false;

                // Track which fields changed
                if (subtaskNameInput.value !== (subtaskNameInput.getAttribute('data-old-value') || '') && 
                    subtaskNameInput.getAttribute('data-old-value') !== null) {
                    changes.task_name = {
                        old: subtaskNameInput.getAttribute('data-old-value') || '',
                        new: subtaskNameInput.value
                    };
                    hasChanges = true;
                }
                
                if (subtaskStartDateInput.value !== (subtaskStartDateInput.getAttribute('data-old-value') || '') && 
                    subtaskStartDateInput.getAttribute('data-old-value') !== null) {
                    changes.start_date = {
                        old: subtaskStartDateInput.getAttribute('data-old-value') || '',
                        new: subtaskStartDateInput.value
                    };
                    hasChanges = true;
                }
                
                if (subtaskDueDateInput.value !== (subtaskDueDateInput.getAttribute('data-old-value') || '') && 
                    subtaskDueDateInput.getAttribute('data-old-value') !== null) {
                    changes.due_date = {
                        old: subtaskDueDateInput.getAttribute('data-old-value') || '',
                        new: subtaskDueDateInput.value
                    };
                    hasChanges = true;
                }
                
                if (subtaskPriorityElement.textContent !== (subtaskPriorityElement.getAttribute('data-old-value') || '') && 
                    subtaskPriorityElement.getAttribute('data-old-value') !== null) {
                    changes.priority = {
                        old: subtaskPriorityElement.getAttribute('data-old-value') || '',
                        new: subtaskPriorityElement.textContent
                    };
                    hasChanges = true;
                }
                
                if (subtaskStatusElement.textContent !== (subtaskStatusElement.getAttribute('data-old-value') || '') && 
                    subtaskStatusElement.getAttribute('data-old-value') !== null) {
                    changes.status = {
                        old: subtaskStatusElement.getAttribute('data-old-value') || '',
                        new: subtaskStatusElement.textContent
                    };
                    hasChanges = true;
                }
                
                if (subtaskBudgetValue !== Number(subtaskBudgetInput.getAttribute('data-old-value') || 0) && 
                    subtaskBudgetInput.getAttribute('data-old-value') !== null) {
                    changes.budget = {
                        old: subtaskBudgetInput.getAttribute('data-old-value') || '0',
                        new: subtaskBudgetValue.toString()
                    };
                    hasChanges = true;
                }
                
                // POW specific fields
                if (projectType === 'POW') {
                    if (subtaskSourceOfFunding !== (nextRow.getAttribute('data-old-source_of_funding') || '') && 
                        nextRow.getAttribute('data-old-source_of_funding') !== null) {
                        changes.source_of_funding = {
                            old: nextRow.getAttribute('data-old-source_of_funding') || '',
                            new: subtaskSourceOfFunding || ''
                        };
                        hasChanges = true;
                    }
                    
                    if (subtaskOtherFundingSource !== (nextRow.getAttribute('data-old-other_funding_source') || '') && 
                        nextRow.getAttribute('data-old-other_funding_source') !== null) {
                        changes.other_funding_source = {
                            old: nextRow.getAttribute('data-old-other_funding_source') || '',
                            new: subtaskOtherFundingSource || ''
                        };
                        hasChanges = true;
                    }
                }

                // Create activity log
                fetch(`/projects/${projectId}/activities`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        type: 'subtask_updated',
                        description: `Updated subtask ${subtaskNameInput.value}`,
                        task_id: subtaskId,
                        changes: changes
                    })
                })
                .then(response => response.json())
                .catch(error => console.error('Error logging subtask activity:', error));
            }

            // Store current values as old values for next update
            subtaskNameInput.setAttribute('data-old-value', subtaskNameInput.value);
            subtaskStartDateInput.setAttribute('data-old-value', subtaskStartDateInput.value);
            subtaskDueDateInput.setAttribute('data-old-value', subtaskDueDateInput.value);
            subtaskPriorityElement.setAttribute('data-old-value', subtaskPriorityElement.textContent);
            subtaskStatusElement.setAttribute('data-old-value', subtaskStatusElement.textContent);
            subtaskBudgetInput.setAttribute('data-old-value', subtaskBudgetValue);
            if (projectType === 'POW') {
                nextRow.setAttribute('data-old-source_of_funding', subtaskSourceOfFunding || '');
                nextRow.setAttribute('data-old-other_funding_source', subtaskOtherFundingSource || '');
            }

            subtasks.push({
                id: subtaskId,
                task_name: subtaskNameInput.value,
                start_date: subtaskStartDateInput.value,
                due_date: subtaskDueDateInput.value,
                priority: subtaskPriorityElement.textContent,
                status: subtaskStatusElement.textContent,
                budget: subtaskBudgetValue,
                project_id: projectId,
                assigned_to: assignedTo,
                source_of_funding: subtaskSourceOfFunding,
                other_funding_source: subtaskOtherFundingSource,
                isSubtaskChanged: isSubtaskChanged
            });

            nextRow = nextRow.nextElementSibling;
        }

        if (isRowChanged) hasChanges = true;

        // Store current values as old values for next update
        taskNameInput.setAttribute('data-old-value', taskNameInput.value);
        startDateInput.setAttribute('data-old-value', startDateInput.value);
        dueDateInput.setAttribute('data-old-value', dueDateInput.value);
        priorityElement.setAttribute('data-old-value', priorityElement.textContent);
        statusElement.setAttribute('data-old-value', statusElement.textContent);
        budgetInput.setAttribute('data-old-value', budgetValue);
        if (projectType === 'POW') {
            row.setAttribute('data-old-source_of_funding', sourceOfFunding || '');
            row.setAttribute('data-old-other_funding_source', otherFundingSource || '');
        }

        // Only push to savePromises if there are changes
        if (isRowChanged || subtasks.some(st => st.isSubtaskChanged)) {
            const taskData = {
                project_id: projectId,
                task_name: taskNameInput.value,
                start_date: startDateInput.value,
                due_date: dueDateInput.value,
                priority: priorityElement.textContent,
                status: statusElement.textContent,
                budget: budgetValue,
                assigned_to: assignedTo,
                subtasks: subtasks
            };
            if (projectType === 'POW') {
                taskData.source_of_funding = sourceOfFunding;
                taskData.other_funding_source = otherFundingSource;
            }

            const url = taskId ? `/tasks/${taskId}` : '/tasks';
            const method = taskId ? 'PUT' : 'POST';

            savePromises.push(
                fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(taskData)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    // Update row and subtask IDs if needed
                    if (!taskId && data.task) {
                        row.setAttribute('data-task-id', data.task.id);
                        const deleteBtn = tableWrapper.querySelector(`#deleteTableBtn-${assignedTo}`);
                        if (deleteBtn) deleteBtn.style.display = '';
                        if (data.subtasks && data.subtasks.length > 0) {
                            let subtaskRow = row.nextElementSibling;
                            data.subtasks.forEach((subtask, index) => {
                                if (subtaskRow && subtaskRow.classList.contains('subtask-row')) {
                                    subtaskRow.setAttribute('data-task-id', subtask.id);
                                    subtaskRow = subtaskRow.nextElementSibling;
                                }
                            });
                        }
                    }
                })
                .catch(error => {
                    hasValidationError = true;
                    validationMessages.push(
                        error.errors
                            ? Object.values(error.errors).flat().join('\n')
                            : error.message || 'An error occurred while saving the task.'
                    );
                })
            );
        }
    });

    // --- Handle validation and notifications ---
    if (hasValidationError) {
        alert(validationMessages.join('\n'));
        return;
    }

    if (!hasChanges) {
        alert('No changes happen.');
        return;
    }

    // Wait for all save operations to complete
    Promise.all(savePromises)
        .then(() => {
            alert('All tasks in this table have been saved successfully!');
        })
        .catch(() => {
            // This should not be reached because errors are handled above,
            // but just in case, do not show the success message.
        });
}

        function deleteTask(row) {
            if (!confirm('Are you sure you want to delete this task?')) {
                return;
            }

            const taskId = row.getAttribute('data-task-id');
            if (!taskId) {
                row.remove();
                return;
            }

            fetch(`/tasks/${taskId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                row.remove();

                // Remove from Kanban
                const kanbanCard = document.querySelector(`[data-task-id="${taskId}"]`);
                if (kanbanCard) {
                    kanbanCard.remove();
                } else if (data.parent_task_id) {
                    // If it's a subtask, update the parent Kanban card's subtasks list
                    const parentKanbanCard = document.querySelector(`.task-card[data-task-id="${data.parent_task_id}"]`);
                    if (parentKanbanCard && typeof window.loadExistingTasks === 'function') {
                        window.loadExistingTasks();
                    }
                }

                if (typeof updateTaskCounts === 'function') updateTaskCounts();
                alert('Task deleted successfully!');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the task.');
            });
        }

        function deleteTaskFromDrawer() {
            const drawer = document.getElementById('taskDetailsDrawer');
            const taskId = drawer.getAttribute('data-current-row');
            if (!confirm('Are you sure you want to delete this task?')) {
                return;
            }

            // Find the row in the table (main task or subtask)
            const row = document.querySelector(`tr[data-task-id="${taskId}"]`);

            // If it's a main task, check if it's the last row in the table
            let isMainTask = row && !row.classList.contains('subtask-row');
            if (isMainTask) {
                const tbody = row.closest('tbody');
                if (tbody && Array.from(tbody.children).filter(r => !r.classList.contains('subtask-row')).length <= 1) {
                    alert('Cannot delete the last row. At least one task must remain.');
                    return;
                }
            }

            if (!taskId) {
                if (row) row.remove();
                const kanbanCard = document.querySelector(`[data-task-id="${taskId}"]`);
                if (kanbanCard) kanbanCard.remove();
                closeTaskDetailsDrawer();
                return;
            }

            fetch(`/tasks/${taskId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (row) row.remove();

                // Remove from Kanban
                const kanbanCard = document.querySelector(`[data-task-id="${taskId}"]`);
                if (kanbanCard) {
                    kanbanCard.remove();
                } else if (data.parent_task_id) {
                    // If it's a subtask, update the parent Kanban card's subtasks list
                    const parentKanbanCard = document.querySelector(`.task-card[data-task-id="${data.parent_task_id}"]`);
                    if (parentKanbanCard && typeof window.loadExistingTasks === 'function') {
                        window.loadExistingTasks();
                    }
                }

                if (typeof updateTaskCounts === 'function') updateTaskCounts();
                closeTaskDetailsDrawer();
                alert('Task deleted successfully!');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the task.');
            });
        }
    
    // Function to delete a subtask
    function deleteSubtask(row) {
        if (!confirm('Are you sure you want to delete this subtask?')) {
            return;
        }

        const taskId = row.getAttribute('data-task-id');
        if (!taskId) {
            row.remove();
            return;
        }

        fetch(`/tasks/${taskId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            row.remove();

            // --- Remove subtask from Kanban view ---
            if (data.parent_task_id && typeof window.loadExistingTasks === 'function') {
                window.loadExistingTasks();
            }

            alert('Subtask deleted successfully!');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the subtask.');
        });
    }

    // Load existing tasks when the page loads
    document.addEventListener('DOMContentLoaded', () => {
        loadExistingTasks();
        updateUserButtonStates();
    });

    // Task drawer handler functions
    const taskDrawerHandler = {
        open: (row) => {
            const taskId = row.getAttribute('data-task-id');
            const taskName = row.querySelector('td:first-child input').value;
            const startDate = row.querySelector('td:nth-child(2) input').value;
            const dueDate = row.querySelector('td:nth-child(3) input').value;
            const priority = row.querySelector('.priority-value').textContent;
            const status = row.querySelector('.status-value').textContent;

            const drawer = document.getElementById('taskDetailsDrawer');
            drawer.setAttribute('data-current-row', taskId || '');

            // Update drawer content
            document.getElementById('taskTitle').textContent = taskName;
            document.getElementById('startDate').textContent = startDate;
            document.getElementById('dueDate').textContent = dueDate;
            document.getElementById('priority').textContent = priority;
            document.getElementById('status').textContent = status;

            // Handle completion timestamp
            const completionTimestampElement = document.getElementById('completionTimestamp');
            if (completionTimestampElement) {
                if (status === 'Completed' && taskId) {
                    // Fetch completion timestamp from server
                    fetch(`/tasks/${taskId}/completion-time`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.completion_time) {
                                completionTimestampElement.querySelector('span').textContent = 
                                    new Date(data.completion_time).toLocaleString('en-US', {
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    });
                                completionTimestampElement.classList.remove('hidden');
                            } else {
                                completionTimestampElement.classList.add('hidden');
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching completion time:', error);
                            completionTimestampElement.classList.add('hidden');
                        });
                } else {
                    completionTimestampElement.classList.add('hidden');
                }
            }
                
            // Load additional content
            loadComments();
            loadFiles();

            // Show drawer
            drawer.classList.remove('translate-x-full');
        },
        close: () => {
            const drawer = document.getElementById('taskDetailsDrawer');
            drawer.classList.add('translate-x-full');
        }
    };


    function deleteComment(commentId) {
        if (!confirm('Are you sure you want to delete this comment?')) return;

        fetch(`/comments/${commentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
            if (commentElement) commentElement.remove();
        })
        .catch(error => console.error('Error deleting comment:', error));
    }

    function deleteFile(fileId) {
        if (!confirm('Are you sure you want to delete this file?')) return;
        fetch(`/files/${fileId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
        if (response.status === 404) {
            return { message: 'File not found (already deleted)' };
        }
        return response.json().catch(() => ({ message: 'Deleted' }));
    })
        .then(data => {
            const fileElement = document.querySelector(`[data-file-id="${fileId}"]`);
            if (fileElement) fileElement.remove();
        })
        .catch(error => console.error('Error deleting file:', error));
    }
    window.deleteFile = deleteFile;

    function saveEdit(commentId) {
        const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
        if (!commentElement) return;

        const textarea = commentElement.querySelector('textarea');
        const newContent = textarea.value.trim();

        if (!newContent) return;

        fetch(`/comments/${commentId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                content: newContent
            })
        })
        .then(response => response.json())
        .then(data => {
            // Find the content element dynamically
            const contentElement = commentElement.querySelector('.text-gray-700');
            if (!contentElement) {
                console.error('Content element not found for comment:', commentId);
                return;
            }

            // Update the comment content
            contentElement.textContent = newContent;

            // Replace the edit form with the updated content
            const editForm = commentElement.querySelector('textarea').parentElement;
            editForm.replaceWith(contentElement);
        })
        .catch(error => console.error('Error updating comment:', error));
    }

    // Expose main functions to window for global/in-template usage
    window.saveTask = saveTask;
    window.deleteTask = deleteTask;
    window.deleteTaskFromDrawer = deleteTaskFromDrawer;
    window.deleteSubtask = deleteSubtask;
    window.deleteComment = deleteComment;
    window.deleteFile = deleteFile;
    window.saveEdit = saveEdit;

    // Event delegation for task table actions (delete/save)
    document.addEventListener('DOMContentLoaded', () => {
        // Existing page load logic
        loadExistingTasks();
        updateUserButtonStates();

        // DRY: Event delegation for task table buttons
        document.body.addEventListener('click', function (e) {
            const btn = e.target.closest('button');
            if (!btn) return;

            // Delete main task
            if (btn.classList.contains('delete-task-btn')) {
                const row = btn.closest('tr');
                if (row) window.deleteTask(row);
            }
            // Delete subtask
            if (btn.classList.contains('delete-subtask-btn')) {
                const row = btn.closest('tr');
                if (row) window.deleteSubtask(row);
            }
            // Save all tasks in a table
            if (btn.classList.contains('save-table-btn')) {
                const tableWrapper = btn.closest('.table-wrapper');
                if (tableWrapper) window.saveTask(tableWrapper);
            }
            // Delete table from drawer
            if (btn.classList.contains('delete-table-drawer-btn')) {
                window.deleteTaskFromDrawer();
            }
        });
    });