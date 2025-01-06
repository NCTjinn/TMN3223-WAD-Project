// Menu Page functionality
function createMenuItemElement(item) {
    return `
        <a href="publicProduct.php?id=${item.product_id}" class="menu-item" data-category="${item.category_id}" data-product-id="${item.product_id}">
            <div class="menu-item-image">
                <img src="${item.image_url}" alt="${item.name}">
            </div>
            <div class="menu-item-details">
                <h3 class="menu-item-title">${item.name}</h3>
                <p class="menu-item-price">${item.price}</p>
            </div>
        </a>
    `;
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

let menuItems = [];  // Store menu items fetched from server

document.addEventListener('DOMContentLoaded', () => {
    // Fetch products and update the global menuItems variable
    fetch('get_products.php')
        .then(response => response.json())
        .then(products => {
            menuItems = products;  // Assign the fetched products to the variable
            displayMenuItems();
            initializeProductPage();
        });

    
    document.querySelector('.filter-container').addEventListener('click', async (e) => {
        if (e.target.classList.contains('filter-btn')) {
            const buttons = document.querySelectorAll('.filter-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            e.target.classList.add('active');
            
            const categoryId = e.target.dataset.category;
            const response = await fetch(`get_products.php?category=${categoryId}`);
        const products = await response.json();

        const menuGrid = document.getElementById('menuGrid');
        menuGrid.innerHTML = products.map(product => `
            <a href="publicProduct.php?id=${product.product_id}" class="menu-item" data-category="${product.category_id}">
                <div class="menu-item-image">
                    <img src="${product.image_url}" alt="${product.name}">
                </div>
                <div class="menu-item-details">
                    <h3 class="menu-item-title">${product.name}</h3>
                    <p class="menu-item-price">RM ${parseFloat(product.price).toFixed(2)}</p>
                </div>
            </a>
        `).join('');
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
