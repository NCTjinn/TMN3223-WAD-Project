// product-page.js

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

// Add item to the cart
function addToCart(productId, productName, productPrice) {
    const quantity = document.getElementById('quantity').value;
    const note = document.querySelector('.note-box textarea').value;
    
    // Create cart item object
    const cartItem = {
        id: productId,
        name: productName,
        price: productPrice,
        quantity: parseInt(quantity),
        note: note
    };
    
    // Get the existing cart or create a new one if it doesn't exist
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    
    // Add new item to the cart
    cart.push(cartItem);
    
    // Save the updated cart back to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Show confirmation alert
    alert('Item added to cart!');
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
