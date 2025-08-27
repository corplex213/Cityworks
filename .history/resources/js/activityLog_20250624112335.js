function loadActivityLog() {
        const projectId = window.PROJECT_ID;
        const container = document.getElementById('activityLogContent');
        
        // Show loading state
        container.innerHTML = `
            <div class="flex justify-center items-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            </div>
        `;
        
        fetch(`/projects/${projectId}/activities`)
            .then(response => response.json())
            .then(data => {
                console.log("Activity data received:", data);
                console.log('Loaded activities:', data);
                
                if (data.length === 0) {
                    container.innerHTML = `<div class="text-center text-gray-500 dark:text-gray-400 py-4">No activity yet</div>`;
                } else {
                    container.innerHTML = '';
                    data.forEach(activity => {
                        // Debug each activity
                        console.log(`Activity ID: ${activity.id}, Type: ${activity.type}`);
                        console.log(`Formatted message: ${activity.formatted_message}`); 
                        
                        container.innerHTML += createActivityElement(activity);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading activities:', error);
                container.innerHTML = `
                    <div class="text-center text-red-500 py-4">
                        Error loading activities. Please try again.
                    </div>
                `;
            });
    }

    function openActivityLogDrawer() {
        const drawer = document.getElementById('activityLogDrawer');
        if (!drawer) return;
        
        drawer.classList.remove('translate-x-full');
        loadActivityLog();
    }

    function closeActivityLogDrawer() {
        const drawer = document.getElementById('activityLogDrawer');
        if (!drawer) return;
        
        drawer.classList.add('translate-x-full');
    }

    function createActivityElement(activity) {
        const user = activity.causer ? activity.causer.name : 'System';
        const action = activity.description || 'updated';
        const subject = activity.subject && activity.subject.task_name ? activity.subject.task_name : '';
        const icon = getActivityTypeIcon(action);
        let changesHtml = '';

        // Fields to exclude from log display
        const excludedFields = ['assigned_to', 'project_id'];

        // Only show fields that were actually changed or set (not null/empty)
        if (activity.properties && activity.properties.attributes) {
            const attrs = activity.properties.attributes;
            const old = activity.properties.old || {};
            changesHtml += '<div class="mt-2 space-y-1">';
            Object.keys(attrs).forEach(key => {
                // Exclude unwanted fields
                if (excludedFields.includes(key)) return;

                // Skip if value is null, undefined, or empty string
                if (
                    attrs[key] === null ||
                    attrs[key] === undefined ||
                    (typeof attrs[key] === 'string' && attrs[key].trim() === '')
                ) {
                    return;
                }

                // Format dates as "Mon DD"
                let displayValue = attrs[key];
                let oldValue = old[key];
                if (['start_date', 'due_date'].includes(key)) {
                    if (attrs[key]) {
                        displayValue = new Date(attrs[key]).toLocaleDateString('en-US', { month: 'short', day: '2-digit' });
                    }
                    if (old[key]) {
                        oldValue = new Date(old[key]).toLocaleDateString('en-US', { month: 'short', day: '2-digit' });
                    }
                }

                // Show only changed fields for updates, or only set fields for creates
                if (old[key] !== undefined && old[key] !== attrs[key]) {
                    changesHtml += `
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <span class="font-medium capitalize mr-2">${key.replace(/_/g, ' ')}:</span>
                            <span class="line-through text-red-400 mr-1">"${oldValue}"</span>
                            <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            <span class="text-green-500 font-semibold">"${displayValue}"</span>
                        </div>
                    `;
                } else if (Object.keys(old).length === 0) {
                    // For "created" actions, show only set fields
                    changesHtml += `
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <span class="font-medium capitalize mr-2">${key.replace(/_/g, ' ')}:</span>
                            <span class="text-green-500 font-semibold">"${displayValue}"</span>
                        </div>
                    `;
                }
            });
            changesHtml += '</div>';
        }

        let message = `
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">${icon}</div>
                <div>
                    <span class="font-semibold text-blue-600 dark:text-blue-400">${user}</span>
                    <span class="text-gray-700 dark:text-gray-200">${action}</span>
                    ${subject ? ` <span class="font-semibold text-gray-900 dark:text-white">"${subject}"</span>` : ''}
                </div>
            </div>
            ${changesHtml}
            <div class="text-xs text-gray-400 mt-2">${new Date(activity.created_at).toLocaleString()}</div>
        `;

        // Timeline dot and line effect
        return `
            <div class="relative pl-8 mb-8 last:mb-0">
                <span class="absolute left-0 top-2 w-3 h-3 bg-blue-500 rounded-full border-2 border-white dark:border-gray-800 shadow"></span>
                <span class="absolute left-1.5 top-6 w-0.5 h-full bg-gray-200 dark:bg-gray-700"></span>
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                    ${message}
                </div>
            </div>
        `;
    }

    function getActivityTypeIcon(type) {
    const iconStyle = 'width: 24px; height: 24px; min-width: 24px; min-height: 24px; max-width: 24px; max-height: 24px; margin-left: 8px; flex-shrink: 0;';
        
        const icons = {
            created: `<svg style="${iconStyle}" fill="none" stroke="#10B981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>`,
            
            updated: `<svg style="${iconStyle}" fill="none" stroke="#3B82F6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>`,
            
            deleted: `<svg style="${iconStyle}" fill="none" stroke="#EF4444" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>`,
            
            'added_engineer': `<svg style="${iconStyle}" fill="none" stroke="#10B981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>`,
            
            'removed_engineer': `<svg style="${iconStyle}" fill="none" stroke="#EF4444" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/></svg>`,

            'subtask_added': `<svg style="${iconStyle}" fill="none" stroke="#8B5CF6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>`,
            
            'subtask_updated': `<svg style="${iconStyle}" fill="none" stroke="#6366F1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>`,
            
            'subtask_deleted': `<svg style="${iconStyle}" fill="none" stroke="#F87171" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 8h3m-3-4h3m-6 4h.01M9 16h.01"/></svg>`
        };
        
        return icons[type] || '';
    }

    function filterActivities() {
        const searchTerm = document.getElementById('activitySearchInput').value.toLowerCase();
        const filter = document.getElementById('activityFilter').value;
        const activities = document.querySelectorAll('#activityLogContent > div');

        activities.forEach(activity => {
            const text = activity.textContent.toLowerCase();
            const type = activity.getAttribute('data-type');
            const matchesSearch = text.includes(searchTerm);
            const matchesFilter = filter === 'all' || type === filter;

            if (matchesSearch && matchesFilter) {
                activity.classList.remove('hidden');
            } else {
                activity.classList.add('hidden');
            }
        });
    }

window.openActivityLogDrawer = openActivityLogDrawer;
window.closeActivityLogDrawer = closeActivityLogDrawer;