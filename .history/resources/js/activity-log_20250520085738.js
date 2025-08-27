// Activity log functions
function loadActivityLog() {
    const projectId = window.PROJECT_ID;
    const container = document.getElementById('activityLogContent');
    container.innerHTML = `
        <div class="flex justify-center items-center py-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
        </div>
    `;
    fetch(`/projects/${projectId}/activities`)
        .then(response => response.json())
        .then(data => {
            if (!Array.isArray(data)) {
                console.error('Expected array of activities but got:', data);
                throw new Error('Invalid activity data format');
            }
            const activities = data.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            if (activities.length === 0) {
                container.innerHTML = `<div class="text-center text-gray-500 dark:text-gray-400 py-4">No activities found</div>`;
                return;
            }
            container.innerHTML = activities.map(activity => createActivityElement(activity)).join('');
        })
        .catch(error => {
            console.error('Error loading activities:', error);
            container.innerHTML = `<div class="text-center text-red-500 py-4">Error loading activities. Please try again.</div>`;
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
    return `
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4 mb-4">
            <div class="flex items-start">
                <div class="flex-grow">
                    <div class="text-gray-700 dark:text-gray-200">${activity.getFormattedMessage}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        ${new Date(activity.created_at).toLocaleDateString('en-US', {
                            year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
                        })}
                    </div>
                </div>
                ${getActivityTypeIcon(activity.type)}
            </div>
        </div>
    `;
}
function getActivityTypeIcon(type) {
    const icons = {
        created: '<svg class="h-5 w-5 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>',
        updated: '<svg class="h-5 w-5 text-blue-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>',
        deleted: '<svg class="h-5 w-5 text-red-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>',
        'added_engineer': '<svg class="h-5 w-5 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>',
        'removed_engineer': '<svg class="h-5 w-5 text-red-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/></svg>'
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
