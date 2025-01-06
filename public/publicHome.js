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
            
            if (products.length === 0) {
                productsGrid.innerHTML = '<div class="error">No products found</div>';
                return;
            }
            
            products.forEach(product => {
                const productCard = document.createElement('a');
                productCard.href = `publicProduct.php?id=${product.product_id}`;
                productCard.classList.add('product-card');
                
                productCard.innerHTML = `
                    <div class="product-image">
                        <img src="${product.image_url || '../assets/images/placeholder.png'}" 
                             alt="${product.name}" 
                             onerror="this.src='../assets/images/placeholder.png'">
                    </div>
                    <div class="product-info">
                        <h3>${product.name}</h3>
                        <p class="price">RM ${parseFloat(product.price).toFixed(2)}</p>
                        <p class="category">${product.category_name}</p>
                    </div>
                `;
                
                productsGrid.appendChild(productCard);
            });
        })
        .catch(error => {
            productsGrid.innerHTML = '<div class="error">Error loading products</div>';
            console.error('Error:', error);
        });
}