// product-js.js
// Menu Page functionality
const menuItems = [
    { id: 1, name: 'Classic Cream Puff', price: 'RM 4.99', category: 'CREAM PUFF' },
    { id: 2, name: 'Iced Coffee', price: 'RM 3.99', category: 'DRINK' },
    { id: 3, name: 'Gift Box', price: 'RM 24.99', category: 'GIFTING' },
    { id: 4, name: 'Party Platter', price: 'RM 49.99', category: 'CATERING' }
];

const relatedProducts = [
    { id: 1, name: 'Chocolate Puff', price: 'RM 2.50' },
    { id: 2, name: 'Matcha Puff', price: 'RM 2.50' },
    { id: 3, name: 'Coffee Puff', price: 'RM 2.50' },
    { id: 4, name: 'Strawberry Puff', price: 'RM 2.50' }
];

function createMenuItemElement(item) {
    return `
        <a href="products/${item.id}.html" class="menu-item" data-category="${item.category}">
            <div class="menu-item-image">
                <img src="${item.image}" alt="${item.name}">
            </div>
            <div class="menu-item-details">
                <h3 class="menu-item-title">${item.name}</h3>
                <p class="menu-item-price">${item.price}</p>
            </div>
        </a>
    `;
}

// Add all the new functions here
let currentProduct = null;

function showMenuPage() {
    document.getElementById('menuPage').style.display = 'block';
    document.getElementById('productPage').style.display = 'none';
}

function showProductPage() {
    document.getElementById('menuPage').style.display = 'none';
    document.getElementById('productPage').style.display = 'block';
}

// Function to update product page content
function updateProductPage(product) {
    currentProduct = product;
    const productPage = document.getElementById('productPage');
    
    // Update product details
    productPage.querySelector('h1').textContent = product.name;
    productPage.querySelector('.price').textContent = product.price;
    
    // Reset quantity
    productPage.querySelector('.quantity-control input').value = 1;
    
    // Show the product page
    showProductPage();
}

function displayMenuItems(category = 'ALL') {
    const menuGrid = document.getElementById('menuGrid');
    menuGrid.innerHTML = menuItems
        .filter(item => category === 'ALL' || item.category === category)
        .map(createMenuItemElement)
        .join('');
}

// Product Detail Page functionality
function initializeProductPage() {
    // Tab switching
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });

    // Quantity control
    const quantityInput = document.querySelector('.quantity-control input');
    quantityInput.addEventListener('change', (e) => {
        if (e.target.value < 1) e.target.value = 1;
    });

    // Display related products
    const relatedGrid = document.querySelector('.related-grid');
    relatedGrid.innerHTML = relatedProducts.map(product => `
        <div class="related-item">
            <div class="menu-item-image"></div>
            <div class="menu-item-details">
                <h3>${product.name}</h3>
                <p>${product.price}</p>
            </div>
        </div>
    `).join('');
}

// Modify your existing DOMContentLoaded event listener to include the new functionality
document.addEventListener('DOMContentLoaded', () => {
    displayMenuItems();
    initializeProductPage();
    
    // Your existing filter functionality
    document.querySelector('.filter-container').addEventListener('click', (e) => {
        if (e.target.classList.contains('filter-btn')) {
            document.querySelectorAll('.filter-btn').forEach(btn => 
                btn.classList.remove('active'));
            e.target.classList.add('active');
            displayMenuItems(e.target.dataset.category);
        }
    });

    // Add the new click event listener for menu items
    document.getElementById('menuGrid').addEventListener('click', (e) => {
        const menuItem = e.target.closest('.menu-item');
        if (menuItem) {
            const productId = parseInt(menuItem.dataset.productId);
            const product = menuItems.find(item => item.id === productId);
            if (product) {
                updateProductPage(product);
            }
        }
    });
    
    // Add back button
    const backBtn = document.createElement('button');
    backBtn.id = 'backToMenu';
    backBtn.className = 'back-btn';
    backBtn.innerHTML = '&larr; Back to Menu';
    document.querySelector('.product-container').prepend(backBtn);
    
    document.getElementById('backToMenu').addEventListener('click', showMenuPage);
    
    // Initially show menu page
    showMenuPage();
});


    const images = [
        'menu1.png', // Add your image paths here
        'menu2.png',
        'menu3.png'
    ];

    let currentImageIndex = 0;
    const sliderImage = document.getElementById('sliderImage');
    
    // Function to shuffle and display the next image
    function showNextImage() {
        // Fade out the current image
        sliderImage.style.transition = "opacity 1s ease"; // Smooth fade transition
        sliderImage.style.opacity = 0; // Fade out the current image
    
        setTimeout(() => {
            // Update the image source after the fade-out transition is complete
            currentImageIndex = (currentImageIndex + 1) % images.length;
            sliderImage.src = images[currentImageIndex]; // Change image source
    
            // Fade in the new image
            sliderImage.style.transition = "opacity 1s ease"; // Reapply fade transition
            sliderImage.style.opacity = 1; // Fade in the new image
        }, 1000); // Wait for fade-out transition to complete before changing the image
    }
    
    // Change image every 3 seconds
    setInterval(showNextImage, 3000);

