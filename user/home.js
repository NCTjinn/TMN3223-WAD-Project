document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const bestsellersBtn = document.getElementById('bestsellers-btn');
    const arrivalsBtn = document.getElementById('arrivals-btn');
    
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

    // Review carousel navigation
    const reviewCards = document.querySelector('.review-cards');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    
    prevBtn.addEventListener('click', () => {
        reviewCards.scrollBy({
            left: -320,
            behavior: 'smooth'
        });
    });
    
    nextBtn.addEventListener('click', () => {
        reviewCards.scrollBy({
            left: 320,
            behavior: 'smooth'
        });
    });
});

function loadProducts(type) {
    const productsGrid = document.getElementById('products-grid');
    productsGrid.innerHTML = '';
    
    const products = type === 'bestsellers' 
        ? [
            { name: 'Best Product 1' },
            { name: 'Best Product 2' },
            { name: 'Best Product 3' }
          ]
        : [
            { name: 'New Arrival 1' },
            { name: 'New Arrival 2' },
            { name: 'New Arrival 3' }
          ];
    
    products.forEach(product => {
        const productCard = document.createElement('div');
        productCard.classList.add('product-card');
        productCard.innerHTML = `<h3>${product.name}</h3>`;
        productsGrid.appendChild(productCard);
    });
}