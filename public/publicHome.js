document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const bestsellersBtn = document.getElementById('bestsellers-btn');
    const arrivalsBtn = document.getElementById('arrivals-btn');
    const productsGrid = document.getElementById('products-grid');
    
    // Load bestsellers by default
    loadProducts('bestsellers');
    
    bestsellersBtn.addEventListener('click', function() {
        bestsellersBtn.classList.add('active');
        arrivalsBtn.classList.remove('active');
        loadProducts('bestsellers');
    });
    
    arrivalsBtn.addEventListener('click', function() {
        arrivalsBtn.classList.add('active');
        bestsellersBtn.classList.remove('active');
        loadProducts('arrivals');
    });
});

function loadProducts(type) {
    const productsGrid = document.getElementById('products-grid');
    productsGrid.innerHTML = ''; // Clear existing products
    
    // Show loading state
    productsGrid.innerHTML = '<div class="loading">Loading...</div>';
    
    fetch(`get_featured_products.php?type=${type}`)
        .then(response => response.json())
        .then(products => {
            productsGrid.innerHTML = ''; // Clear loading state
            
            products.forEach(product => {
                const productCard = document.createElement('div');
                productCard.classList.add('product-card');
                
                productCard.innerHTML = `
                    <a href="publicProduct.php?id=${product.product_id}" class="product-link">
                        <div class="product-image">
                            ${product.image_url ? 
                                `<img src="${product.image_url}" alt="${product.name}">` :
                                `<div class="placeholder-image">${product.name[0]}</div>`
                            }
                        </div>
                        <div class="product-info">
                            <h3>${product.name}</h3>
                            <p class="price">RM ${parseFloat(product.price).toFixed(2)}</p>
                            <p class="category">${product.category_name}</p>
                        </div>
                    </a>
                `;
                
                productsGrid.appendChild(productCard);
            });
        })
        .catch(error => {
            productsGrid.innerHTML = '<div class="error">Error loading products</div>';
            console.error('Error:', error);
        });
}