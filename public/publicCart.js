class GuestCartManager {
    constructor() {
        this.STORAGE_KEY = 'publicCart';
        this.initializeCart();
        this.setupEventListeners();
    }

    async initializeCart() {
        // Check and initialize the cart if necessary
        if (!localStorage.getItem(this.STORAGE_KEY)) {
            this.saveCart({
                items: [],
                lastUpdated: new Date().toISOString()
            });
        }
    
        // Update cart display and count
        this.updateCartCount();
        await this.updateCartDisplay(); // Make this async only if fetching external data
    }
    // Set up all event listeners
    setupEventListeners() {
        // Product page add to cart button
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-cart-btn')) {
                this.handleAddToCart(e.target);
            }
        });

        // Cart page quantity buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('qty-btn')) {
                const input = e.target.closest('.quantity-control').querySelector('input');
                const change = e.target.textContent === '+' ? 1 : -1;
                this.updateQuantity(input, change);
            }
        });

        // Listen for user login
        window.addEventListener('userLoggedIn', (e) => {
            this.transferCartToUser(e.detail.userId);
        });
    }

    // Add removeFromCart method
    removeFromCart(productId) {
        const cart = this.getCart();
        cart.items = cart.items.filter(item => item.id !== productId);
        this.saveCart(cart);
        this.updateCartDisplay();
        this.showNotification('Item removed from cart');
    }

    // Update clearCart method
    clearCart() {
        const emptyCart = {
            items: [],
            lastUpdated: new Date().toISOString()
        };
        this.saveCart(emptyCart);
        this.showNotification('Cart cleared successfully');
    }

    // Handle adding item to cart
    handleAddToCart(button) {
        console.log('Add to cart clicked', button.dataset); // Add this for debugging
        const productData = {
            id: parseInt(button.dataset.productId),
            name: button.dataset.name,
            price: parseFloat(button.dataset.price),
            quantity: parseInt(document.getElementById('quantity')?.value || 1),
            note: document.querySelector('.note-box textarea')?.value || ''
        };
        console.log('Product data:', productData); // Add this for debugging

    
        const cart = this.getCart();
        const existingItemIndex = cart.items.findIndex(item => item.id === productData.id);
    
        if (existingItemIndex > -1) {
            cart.items[existingItemIndex].quantity += productData.quantity;
        } else {
            cart.items.push(productData);
        }
    
        this.saveCart(cart);
        this.updateCartDisplay();
        this.updateCartCount();
        this.showNotification('Item added to cart successfully!');
    }

    // Get cart data from localStorage
    getCart() {
        const cartData = localStorage.getItem(this.STORAGE_KEY);
        return cartData ? JSON.parse(cartData) : { items: [], lastUpdated: new Date().toISOString() };
    }

    // Save cart data to localStorage
    saveCart(cartData) {
        cartData.lastUpdated = new Date().toISOString();
        localStorage.setItem(this.STORAGE_KEY, JSON.stringify(cartData));
        this.updateCartDisplay();
        this.updateCartCount();
    }

    // Update quantity of cart item
    updateQuantity(input, change) {
        let newValue = parseInt(input.value) + change;
        newValue = Math.max(1, newValue);
        input.value = newValue;

        if (input.hasAttribute('data-cart-item-id')) {
            const itemId = parseInt(input.getAttribute('data-cart-item-id'));
            const cart = this.getCart();
            const itemIndex = cart.items.findIndex(item => item.id === itemId);
            
            if (itemIndex > -1) {
                cart.items[itemIndex].quantity = newValue;
                this.saveCart(cart);
            }
        }
    }

    // Transfer cart to user account after login
    async transferCartToUser(userId) {
        const cart = this.getCart();
        if (cart.items.length === 0) return;

        try {
            const response = await fetch('/api/transfer-cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    userId: userId,
                    items: cart.items
                })
            });

            if (response.ok) {
                // Clear guest cart after successful transfer
                this.clearCart();
                return true;
            }
            return false;
        } catch (error) {
            console.error('Error transferring cart:', error);
            return false;
        }
    }

    // Clear cart
    clearCart() {
        localStorage.removeItem(this.STORAGE_KEY);
        this.initializeCart();
    }
    // Get product details from server
    async getProductDetails(productIds) {
        try {
            const response = await fetch('cart_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'getCartDetails',
                    productIds: productIds
                })
            });

            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();
            return data.success ? data.products : {};
        } catch (error) {
            console.error('Error fetching product details:', error);
            return {};
        }
    }

    async updateCartDisplay() {
        const cartItems = document.getElementById('cartItems');
        if (!cartItems) return;
    
        const cart = this.getCart();
        
        if (cart.items.length === 0) {
            cartItems.innerHTML = '<div class="empty-cart">Your cart is empty</div>';
            this.updateCartTotal();
            return;
        }
    
        const productIds = cart.items.map(item => item.id);
        const productDetails = await this.getProductDetails(productIds);
    
        cartItems.innerHTML = cart.items.map(item => {
            const product = productDetails[item.id] || {};
            const itemPrice = parseFloat(product.price || item.price);
            const subtotal = itemPrice * item.quantity;
            const imageUrl = product.image_url || 'placeholder.jpg';
    
            return `
                <div class="cart-item" data-item-id="${item.id}">
                    <div class="product-info">
                        <div class="cart-item-image">
                            <img src="${imageUrl}" alt="${product.name || item.name}"
                                 onerror="this.src='placeholder.jpg'">
                        </div>
                        <div class="item-details">
                            <span class="item-name">${product.name || item.name}</span>
                            ${item.note ? `<small class="item-note">${item.note}</small>` : ''}
                        </div>
                    </div>
                    <div class="item-price">RM ${itemPrice.toFixed(2)}</div>
                    <div class="quantity-control">
                        <button class="qty-btn" ${item.quantity <= 1 ? 'disabled' : ''}>-</button>
                        <input type="number" value="${item.quantity}" min="1" 
                               data-cart-item-id="${item.id}" readonly>
                        <button class="qty-btn">+</button>
                    </div>
                    <div class="item-subtotal">
                        RM ${subtotal.toFixed(2)}
                    </div>
                    <button class="remove-item" onclick="cartManager.removeFromCart(${item.id})">
                        <i class='bx bx-trash'></i>
                    </button>
                </div>
            `;
        }).join('');
    
        this.updateCartTotal();
    }

    // Update cart total
    // Update updateCartTotal method
    updateCartTotal() {
        const cart = this.getCart();
        const total = cart.items.reduce((sum, item) => {
            const itemPrice = parseFloat(item.price);
            return sum + (itemPrice * item.quantity);
        }, 0);
        
        const cartTotal = document.getElementById('cartTotal');
        const checkoutTotal = document.getElementById('checkoutTotal');
        
        if (cartTotal) cartTotal.textContent = `RM ${total.toFixed(2)}`;
        if (checkoutTotal) checkoutTotal.textContent = `RM ${total.toFixed(2)}`;
    }

    // Update cart count badge
    updateCartCount() {
        const cart = this.getCart();
        const totalItems = cart.items.reduce((sum, item) => sum + item.quantity, 0);
    }

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
}

// Initialize cart manager
const cartManager = new GuestCartManager();