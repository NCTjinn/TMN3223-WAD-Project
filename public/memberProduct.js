// Add this to your existing script section or publicMenuProductPage.js
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

// Update the addToCart function
function addToCart(productId) {
    const quantity = parseInt(document.getElementById('quantity').value);
    // Make sure cart.js is loaded
    if (typeof cart !== 'undefined') {
        cart.addItem(productId, quantity).then(success => {
            if (success) {
                alert('Item added to cart!');
            }
        });
    } else {
        console.error('Cart functionality not loaded');
    }
}
