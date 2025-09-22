function clearSearchBar() {
    const searchInput = document.getElementById('searchInput');
    searchInput.value = '';
    // Remove search param from URL without reloading
    const url = new URL(window.location.href);
    url.searchParams.delete('search');
    window.history.replaceState({}, '', url);
    filterPersonnel();
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
    const personnelCards = document.querySelectorAll('.rounded-lg.p-6');
    let visibleCount = 0;

    personnelCards.forEach((card) => {
        // Get the user name from .ml-4 > .font-semibold
        let name = '';
        const ml4 = card.querySelector('.ml-4');
        if (ml4) {
            const nameEl = ml4.querySelector('.font-semibold');
            if (nameEl) {
                name = nameEl.textContent.toLowerCase();
            }
        }

        // Get the email (first .text-sm inside .ml-4)
        let email = '';
        if (ml4) {
            const emailEl = ml4.querySelector('.text-sm');
            if (emailEl) {
                email = emailEl.textContent.toLowerCase();
            }
        }

        let match = name.includes(searchInput) || email.includes(searchInput);

        if (match) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    // Show/hide position sections
    document.querySelectorAll('.mb-8').forEach(section => {
        const visibleCards = Array.from(section.querySelectorAll('.rounded-lg.p-6')).filter(card => card.style.display !== 'none');
        section.style.display = visibleCards.length > 0 ? '' : 'none';

        // Update count badge
        const countBadge = section.querySelector('.text-xs');
        if (countBadge) {
            countBadge.textContent = `${visibleCards.length} personnel`;
        }
    });

    // Show "No results found" message if no cards are visible
    let noResultsMessage = document.getElementById('noResultsMessage');
    if (!noResultsMessage) {
        const container = document.getElementById('personnelListContainer');
        if (container) {
            const messageDiv = document.createElement('div');
            messageDiv.id = 'noResultsMessage';
            messageDiv.className = 'text-center text-gray-400 py-8 hidden';
            messageDiv.innerHTML = '<i class="fas fa-search mb-2 text-2xl"></i><p>No personnel found matching your search.</p>';
            container.appendChild(messageDiv);
            noResultsMessage = messageDiv;
        }
    }
    noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';

    const totalCount = document.querySelector('.text-2xl.font-bold');
    if (totalCount) {
        totalCount.textContent = visibleCount.toString();
    }
}

// Expose clearSearchBar globally for inline onclick
window.clearSearchBar = clearSearchBar;