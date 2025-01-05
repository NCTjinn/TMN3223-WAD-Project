// Check if cart object already exists and create/update accordingly
window.cartManager = window.cartManager || {
    items: [],
    
    async loadCartItems() {
        try {
            const response = await fetch('memberCart_api.php');
            if (!response.ok) throw new Error('Failed to load cart');
            this.items = await response.json();
            this.updateCartDisplay();
        } catch (error) {
            console.error('Error loading cart:', error);
            alert('Failed to load cart items. Please try again.');
        }
    },
    
    async addItem(productId, quantity) {
        try {
            const response = await fetch('memberCart_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            });
            
            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Failed to add item');
            }
            
            await this.loadCartItems(); // Reload cart after successful addition
            return true;
        } catch (error) {
            console.error('Error adding item:', error);
            alert('Failed to add item to cart. Please try again.');
            return false;
        }
    },
    
    async updateQuantity(cartId, newQuantity) {
        try {
            const response = await fetch('memberCart_api.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ cart_id: cartId, quantity: newQuantity })
            });
            
            if (!response.ok) throw new Error('Failed to update quantity');
            await this.loadCartItems();
        } catch (error) {
            console.error('Error updating quantity:', error);
            alert('Failed to update cart. Please try again.');
        }
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
                    <button onclick="cartManager.updateQuantity(${item.cart_id}, ${item.quantity - 1})">-</button>
                    <input type="number" value="${item.quantity}" min="1" 
                        onchange="cartManager.updateQuantity(${item.cart_id}, this.value)">
                    <button onclick="cartManager.updateQuantity(${item.cart_id}, ${item.quantity + 1})">+</button>
                </div>
                <span>RM ${(item.price * item.quantity).toFixed(2)}</span>
            </div>
        `).join('');
        
        const total = this.calculateTotal();
        const cartTotal = document.getElementById('cartTotal');
        if (cartTotal) cartTotal.textContent = `RM ${total.toFixed(2)}`;
    }
};

// Initialize cart when page loads using an IIFE to avoid global scope pollution
(function() {
    document.addEventListener('DOMContentLoaded', () => {
        // Load cart items
        cartManager.loadCartItems();
        
        // Handle delivery option selection
        const optionCards = document.querySelectorAll('.option-card');
        optionCards.forEach(card => {
            card.addEventListener('click', () => {
                optionCards.forEach(c => {
                    c.classList.remove('selected');
                    const timeInputs = c.querySelector('.time-inputs');
                    if (timeInputs) {
                        timeInputs.classList.add('hidden');
                    }
                });
                card.classList.add('selected');
                const timeInputs = card.querySelector('.time-inputs');
                if (timeInputs) {
                    timeInputs.classList.remove('hidden');
                }
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
                
                if (!dateInput?.value || !timeInput?.value) {
                    alert('Please select date and time');
                    return;
                }
                
                // Proceed to checkout
                window.location.href = 'memberCheckoutTest.php';
            });
        }
    });
})();