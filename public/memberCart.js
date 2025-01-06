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
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'memberCart_api.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        // Successfully added item, reload cart items
                        this.loadCartItems();
                        resolve(true); // Resolve the promise when successful
                    } else {
                        console.error('Failed to add item');
                        alert('Failed to add item to cart. Please try again.');
                        reject(new Error('Failed to add item')); // Reject the promise on failure
                    }
                }
            };
            
            xhr.send(`action=add&product_id=${productId}&quantity=${quantity}`);
        });
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

    removeFromCart(productId) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'memberCart_api.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    this.loadCartItems();
                    alert('Item removed from cart');
                } else {
                    console.error('Failed to remove item');
                    alert('Failed to remove item from cart. Please try again.');
                }
            }
        };

        xhr.send(`action=remove&product_id=${productId}`);
    },

    clearCart() {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'memberCart_api.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    this.loadCartItems(); // Refresh the cart display after clearing
                    this.showNotification('Cart cleared successfully');
                } else {
                    console.error('Failed to clear cart');
                    alert('Failed to clear the cart. Please try again.');
                }
            }
        };
    
        xhr.send('action=clear');
    },   
    
    calculateTotal() {
        return this.items.reduce((total, item) => 
            total + (parseFloat(item.price) * item.quantity), 0);
    },
    
    updateCartDisplay() {
        const cartItems = document.getElementById('cartItems');
        if (!cartItems) return;

        if (cart.items.length === 0) {
            cartItems.innerHTML = '<div class="empty-cart">Your cart is empty</div>';
            return;
        }
        
        cartItems.innerHTML = this.items.map(item => `
            <div class="cart-item" data-item-id="${item.cart_id}">
                <div class="product-info">
                    <div class="cart-item-image">
                        <img src="${item.image_url}" alt="${item.name}">
                    </div>
                    <div class="item-details">
                        <span class="item-name">${item.name}</span>
                        ${item.note ? `<small class="item-note">${item.note}</small>` : ''}
                    </div>
                </div>
                <div class="item-price">RM ${parseFloat(item.price).toFixed(2)}</div>
                <div class="quantity-control">
                    <button class="qty-btn" onclick="cart.updateQuantity(${item.cart_id}, ${item.quantity - 1})" 
                            ${item.quantity <= 1 ? 'disabled' : ''}>-</button>
                    <input type="number" value="${item.quantity}" min="1" 
                           data-cart-item-id="${item.cart_id}"
                           onchange="cart.updateQuantity(${item.cart_id}, parseInt(this.value))">
                    <button class="qty-btn" onclick="cart.updateQuantity(${item.cart_id}, ${item.quantity + 1})">+</button>
                </div>
                <div class="item-subtotal">
                    RM ${(item.price * item.quantity).toFixed(2)}
                </div>
                <button class="remove-item" onclick="cart.removeFromCart(${item.product_id})">
                    <i class='bx bx-trash'></i>
                </button>
            </div>
        `).join('');
        
        const total = this.calculateTotal();
        const cartTotal = document.getElementById('cartTotal');
        if (cartTotal) cartTotal.textContent = `RM ${total.toFixed(2)}`;
    },
    // Show notification
    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 2000);
        }, 100);
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

    const clearCartButton = document.querySelector('.clear-cart-button');
    if (clearCartButton) {
        clearCartButton.addEventListener('click', () => {
        cart.clearCart(); // Calls the clearCart method to clear the cart
        });
    }

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