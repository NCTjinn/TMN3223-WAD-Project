// Tab switching functionality
document.querySelectorAll('.tab-btn').forEach(button => {
    button.addEventListener('click', () => {
        // Remove active class from all buttons and hide all content
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.style.display = 'none');
        
        // Add active class to clicked button and show corresponding content
        button.classList.add('active');
        const tabId = button.getAttribute('data-tab');
        document.getElementById(tabId).style.display = 'block';
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

// Handle thumbnail image switching
document.addEventListener('DOMContentLoaded', function() {
    // Add thumbnail click handlers if needed
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            const mainImage = document.querySelector('.main-image img');
            mainImage.src = this.src;  // Change main image to the clicked thumbnail
        });
    });
});
