function clearSearchBar() {
    const searchInput = document.getElementById('searchInput');
    searchInput.value = '';
    window.location.href = window.personnelRoute || '/personnel';
}

// Add debounce function to prevent too many searches
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Handle search input with URL updates
const handleSearch = debounce((searchValue) => {
    const url = new URL(window.location.href);
    if (searchValue) {
        url.searchParams.set('search', searchValue);
    } else {
        url.searchParams.delete('search');
    }
    window.history.pushState({}, '', url);
}, 500);

document.addEventListener('DOMContentLoaded', () => {
    // Add event listener to search input
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            handleSearch(e.target.value);
            filterPersonnel();
        });

        // Initialize search if there's a value in the URL
        const urlParams = new URLSearchParams(window.location.search);
        const searchValue = urlParams.get('search');
        if (searchValue) {
            searchInput.value = searchValue;
            filterPersonnel();
        }
    }

    // Attach filterPersonnel to input event
    if (searchInput) {
        searchInput.addEventListener('input', filterPersonnel);
    }
});

function filterPersonnel() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const personnelCards = document.querySelectorAll('.bg-gray-700.rounded-lg'); // Select all personnel cards
    let visibleCount = 0;

    personnelCards.forEach((card) => {
        const name = card.querySelector('.font-semibold.text-gray-200').textContent.toLowerCase();
        const email = card.querySelector('.text-sm.text-gray-400').textContent.toLowerCase();
        const position = card.closest('.mb-8').querySelector('.text-md.font-semibold.text-gray-300').textContent.toLowerCase();
        const tasks = Array.from(card.querySelectorAll('.bg-gray-800 .text-gray-400')).map(el => el.textContent.toLowerCase());

        if (
            name.includes(searchInput) ||
            email.includes(searchInput) ||
            position.includes(searchInput) ||
            tasks.some(task => task.includes(searchInput))
        ) {
            card.style.display = ''; // Show the card
            card.closest('.mb-8').style.display = ''; // Show the position section
            visibleCount++;
        } else {
            card.style.display = 'none'; // Hide the card

            // Check if all cards in this position are hidden
            const positionSection = card.closest('.mb-8');
            const visibleCardsInPosition = positionSection.querySelectorAll('.bg-gray-700.rounded-lg[style="display: "]');
            if (visibleCardsInPosition.length === 0) {
                positionSection.style.display = 'none'; // Hide the position section
            }
        }
    });

    // Show "No results found" message if no cards are visible
    let noResultsMessage = document.getElementById('noResultsMessage');
    if (!noResultsMessage) {
        const container = document.querySelector('.p-6');
        const messageDiv = document.createElement('div');
        messageDiv.id = 'noResultsMessage';
        messageDiv.className = 'text-center text-gray-400 py-8 hidden';
        messageDiv.innerHTML = '<i class="fas fa-search mb-2 text-2xl"></i><p>No personnel found matching your search.</p>';
        container.appendChild(messageDiv);
        noResultsMessage = messageDiv;
    }

    noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';

    // Update counts in position headers
    document.querySelectorAll('.mb-8').forEach(section => {
        const visibleCards = section.querySelectorAll('.bg-gray-700.rounded-lg[style="display: "]');
        const countBadge = section.querySelector('.text-xs.bg-gray-700');
        if (countBadge) {
            countBadge.textContent = `${visibleCards.length} personnel`;
        }
    });

    // Update total count in stats
    const totalCount = document.querySelector('.text-2xl.font-bold.text-gray-200');
    if (totalCount) {
        totalCount.textContent = visibleCount.toString();
    }
}

// Expose clearSearchBar globally for inline onclick
window.clearSearchBar = clearSearchBar;