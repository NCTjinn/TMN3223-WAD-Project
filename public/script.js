document.querySelector('.search-btn').addEventListener('click', function(e) {
    const input = document.querySelector('.search-txt');
    if (input.classList.contains('open')) {
        // If already open and clicked, close everything
        input.classList.remove('open');
        input.style.width = '0';
        input.style.opacity = '0';
        input.style.visibility = 'hidden';
        searchResults.style.display = 'none';
    } else {
        // Open the search box
        input.classList.add('open');
        input.style.width = '200px';
        input.style.opacity = '1';
        input.style.visibility = 'visible';
        input.focus();
    }
    e.preventDefault();
});

// Add input event listener for search functionality
document.querySelector('.search-txt').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    
    if (searchTerm === '') {
        searchResults.style.display = 'none';
        return;
    }

    const matchingItems = menuItems.filter(item =>
        item.name.toLowerCase().includes(searchTerm) ||
        item.category.toLowerCase().includes(searchTerm)
    );

    if (matchingItems.length > 0) {
        searchResults.style.display = 'block';
        searchResults.innerHTML = matchingItems.map(item => `
            <div class="search-result-item" data-url="${item.url}">
                <span class="item-name">${item.name}</span>
                <span class="item-price">${item.price}</span>
            </div>
        `).join('');

        // Add click handlers to each result item
        const resultItems = searchResults.querySelectorAll('.search-result-item');
        resultItems.forEach(item => {
            item.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                window.location.href = url;
            });
        });
    } else {
        searchResults.style.display = 'block';
        searchResults.innerHTML = '<div class="no-results">No items found</div>';
    }
});

// Close search results when clicking outside
document.addEventListener('click', function(e) {
    const input = document.querySelector('.search-txt');
    const searchBtn = document.querySelector('.search-btn');
    const searchResultsBox = document.querySelector('.search-results');

    if (!input.contains(e.target) && 
        !searchBtn.contains(e.target) && 
        !searchResultsBox.contains(e.target)) {
        input.classList.remove('open');
        input.style.width = '0';
        input.style.opacity = '0';
        input.style.visibility = 'hidden';
        searchResults.style.display = 'none';
    }
});
