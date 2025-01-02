// product-page.js
const products = {
    'classic-cream-puff': {
        name: 'Classic Cream Puff',
        price: 'RM 4.99',
        ingredients: 'Fresh choux pastry, vanilla custard cream, powdered sugar',
        servingTips: 'Best served chilled. Consume within 24 hours for optimal freshness.',
        images: {
            main: 'products/classic-cream-puff/main.jpg',
            thumbnails: [
                'products/classic-cream-puff/thumb1.jpg',
                'products/classic-cream-puff/thumb2.jpg',
                'products/classic-cream-puff/thumb3.jpg'
            ]
        }
    },
    // Add more products...
};

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

// Quantity control
function updateQuantity(change) {
    const input = document.getElementById('quantity');
    const newValue = parseInt(input.value) + change;
    if (newValue >= 1) {
        input.value = newValue;
    }
}

// Add to cart functionality
function addToCart() {
    const quantity = document.getElementById('quantity').value;
    const note = document.querySelector('.note-box textarea').value;
    
    // Get product info from URL
    const productId = window.location.pathname.split('/').pop().replace('.html', '');
    const product = products[productId];
    
    // Create cart item
    const cartItem = {
        id: productId,
        name: product.name,
        price: product.price,
        quantity: parseInt(quantity),
        note: note
    };
    
    // Get existing cart or create new one
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    
    // Add item to cart
    cart.push(cartItem);
    
    // Save cart
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Show confirmation
    alert('Item added to cart!');
}

// Load product data
function loadProductData() {
    const productId = window.location.pathname.split('/').pop().replace('.html', '');
    const product = products[productId];
    
    if (product) {
        document.querySelector('.product-info h1').textContent = product.name;
        document.querySelector('.price').textContent = product.price;
        document.querySelector('#ingredients').textContent = product.ingredients;
        document.querySelector('#serving').textContent = product.servingTips;
        
        // Load images
        document.querySelector('.main-image img').src = product.images.main;
        const thumbnails = document.querySelectorAll('.thumbnail');
        product.images.thumbnails.forEach((src, index) => {
            if (thumbnails[index]) {
                thumbnails[index].src = src;
            }
        });
    }
}

// Initialize page
document.addEventListener('DOMContentLoaded', loadProductData);
