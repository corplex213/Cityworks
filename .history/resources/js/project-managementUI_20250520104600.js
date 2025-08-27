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
