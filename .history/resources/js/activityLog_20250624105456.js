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
        // Get user name or fallback
        const user = activity.causer ? activity.causer.name : 'System';
        // Format the changes (properties)
        let changesHtml = '';
        if (activity.properties && activity.properties.attributes) {
            const attrs = activity.properties.attributes;
            const old = activity.properties.old || {};
            changesHtml += '<ul class="ml-4">';
            Object.keys(attrs).forEach(key => {
                if (old[key] !== undefined && old[key] !== attrs[key]) {
                    changesHtml += `<li><span class="font-semibold">${key.replace(/_/g, ' ')}:</span> "${old[key]}" → "${attrs[key]}"</li>`;
                } else {
                    changesHtml += `<li><span class="font-semibold">${key.replace(/_/g, ' ')}:</span> "${attrs[key]}"</li>`;
                }
            });
            changesHtml += '</ul>';
        }

        // Compose message
        let message = `<strong>${user}</strong> ${activity.description}`;
        if (activity.subject && activity.subject.task_name) {
            message += ` task "<strong>${activity.subject.task_name}</strong>"`;
        }
        if (changesHtml) {
            message += `<br>${changesHtml}`;
        }

        return `
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4 mb-4">
                <div class="flex items-start">
                    <div class="flex-grow">
                        <div class="text-gray-700 dark:text-gray-200 break-words whitespace-normal" style="word-break: break-word; overflow-wrap: break-word;">
                            ${message}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            ${new Date(activity.created_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            })}
                        </div>
                    </div>
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