// memberCart.js
const cart = {
    items: [],
    
    loadCartItems() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'memberCart_api.php', true);
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        this.items = JSON.parse(xhr.responseText);
                        this.updateCartDisplay();
                    } catch (error) {
                        console.error('Error parsing cart data:', error);
                        alert('Failed to load cart items. Please try again.');
                    }
                } else {
                    console.error('Failed to load cart');
                    alert('Failed to load cart items. Please try again.');
                }
            }
        };
        xhr.send();
    },
    
    addItem(productId, quantity) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'memberCart_api.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    this.loadCartItems();
                } else {
                    console.error('Failed to add item');
                    alert('Failed to add item to cart. Please try again.');
                }
            }
        };
        
        xhr.send(`action=add&product_id=${productId}&quantity=${quantity}`);
    },
    
    updateQuantity(cartId, newQuantity) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'memberCart_api.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    this.loadCartItems();
                } else {
                    console.error('Failed to update quantity');
                    alert('Failed to update cart. Please try again.');
                }
            }
        };
        
        xhr.send(`action=update&cart_id=${cartId}&quantity=${newQuantity}`);
    },
    
    calculateTotal() {
        return this.items.reduce((total, item) => 
            total + (parseFloat(item.price) * item.quantity), 0);
    },
    
    updateCartDisplay() {
        const cartItems = document.getElementById('cartItems');
        if (!cartItems) return;
        
        cartItems.innerHTML = this.items.map(item => `
            <div class="cart-item">
                <div class="product-info">
                    <img src="${item.image_url}" alt="${item.name}" class="cart-item-image">
                    <span>${item.name}</span>
                </div>
                <span>RM ${parseFloat(item.price).toFixed(2)}</span>
                <div class="quantity-control">
                    <button onclick="cart.updateQuantity(${item.cart_id}, ${item.quantity - 1})">-</button>
                    <input type="number" value="${item.quantity}" min="1" 
                           onchange="cart.updateQuantity(${item.cart_id}, parseInt(this.value))">
                    <button onclick="cart.updateQuantity(${item.cart_id}, ${item.quantity + 1})">+</button>
                </div>
                <span>RM ${(item.price * item.quantity).toFixed(2)}</span>
            </div>
        `).join('');
        
        const total = this.calculateTotal();
        const cartTotal = document.getElementById('cartTotal');
        if (cartTotal) cartTotal.textContent = `RM ${total.toFixed(2)}`;
    }
};

// Initialize cart when page loads
document.addEventListener('DOMContentLoaded', () => {
    cart.loadCartItems();
    
    // Handle delivery option selection
    const optionCards = document.querySelectorAll('.option-card');
    optionCards.forEach(card => {
        card.addEventListener('click', () => {
            optionCards.forEach(c => {
                c.classList.remove('selected');
                c.querySelector('.time-inputs').classList.add('hidden');
            });
            card.classList.add('selected');
            card.querySelector('.time-inputs').classList.remove('hidden');
        });
    });
    
    // Handle checkout button
    const checkoutButton = document.getElementById('checkoutButton');
    if (checkoutButton) {
        checkoutButton.addEventListener('click', () => {
            const selectedOption = document.querySelector('.option-card.selected');
            if (!selectedOption) {
                alert('Please select a delivery option');
                return;
            }
            
            const dateInput = selectedOption.querySelector('.date-input');
            const timeInput = selectedOption.querySelector('.time-input');
            
            if (!dateInput.value || !timeInput.value) {
                alert('Please select date and time');
                return;
            }
            
            // Proceed to checkout
            window.location.href = 'memberCheckoutTest.php';
        });
    }
});
