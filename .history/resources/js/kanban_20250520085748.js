// Kanban-related functions
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
