document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => {
                content.classList.remove('active');
                content.style.display = 'none';
            });
            
            // Add active class to clicked button
            button.classList.add('active');
            
            // Show corresponding content
            const tabId = button.getAttribute('data-tab');
            const tabContent = document.getElementById(tabId);
            tabContent.classList.add('active');
            tabContent.style.display = 'block';
        });
    });

    // Initialize thumbnails if they exist
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            const mainImage = document.querySelector('.main-image img');
            mainImage.src = this.src;
        });
    });
});

// Quantity control functionality
function updateQuantity(change) {
    const input = document.getElementById('quantity');
    const newValue = parseInt(input.value) + change;
    if (newValue >= 1) {
        input.value = newValue;
    }
}

function addToCart(productId) {
    const quantity = parseInt(document.getElementById('quantity').value);
    if (typeof cart !== 'undefined') {
        // Now addItem returns a Promise, so you can use .then() and .catch()
        cart.addItem(productId, quantity)
            .then(success => {
                if (success) {
                    alert('Item added to cart!');
                }
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                alert('Failed to add item to cart. Please try again.');
            });
    } else {
        console.error('Cart functionality not loaded');
        alert('Cart functionality is not available. Please refresh the page.');
    }
}