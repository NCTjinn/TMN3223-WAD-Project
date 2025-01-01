// Cart and Checkout functionality
const cart = {
    items: [
        {
            id: 1,
            name: "Vanilla Puff",
            price: 2.00,
            quantity: 2,
            image: "placeholder.jpg"
        }
    ],
    
    calculateTotal() {
        return this.items.reduce((total, item) => 
            total + (item.price * item.quantity), 0);
    },

    updateCartDisplay() {
        const cartItems = document.getElementById('cartItems');
        if(!cartItems) return;

        cartItems.innerHTML = this.items.map(item => `
            <div class="cart-item">
                <div class="product-info">
                    <div class="cart-item-image"></div>
                    <span>${item.name}</span>
                </div>
                <span>RM ${item.price.toFixed(2)}</span>
                <input type="number" value="${item.quantity}" min="1" 
                    onchange="cart.updateQuantity(${item.id}, this.value)">
                <span>RM ${(item.price * item.quantity).toFixed(2)}</span>
            </div>
        `).join('');

        const total = this.calculateTotal();
        const cartTotal = document.getElementById('cartTotal');
        const checkoutTotal = document.getElementById('checkoutTotal');
        if (cartTotal) cartTotal.textContent = `RM ${total.toFixed(2)}`;
        if (checkoutTotal) checkoutTotal.textContent = `RM ${total.toFixed(2)}`;
    },

    updateQuantity(itemId, newQuantity) {
        const item = this.items.find(i => i.id === itemId);
        if(item) {
            item.quantity = parseInt(newQuantity);
            this.updateCartDisplay();
        }
    }
};

// Add event listener for DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart display
    cart.updateCartDisplay();

    // Add click event listener to the continue button
    const continueButton = document.querySelector('.continue-button');
    if (continueButton) {
        continueButton.addEventListener('click', validateCheckout);
    }

    // Initialize tracking page elements if they exist
    initializeTrackingPage();
});

function validateCheckout() {
    // Get all required form fields
    const requiredFields = {
        firstName: document.querySelector('input[placeholder="First Name*"]'),
        lastName: document.querySelector('input[placeholder="Last Name*"]'),
        address: document.querySelector('input[placeholder="Address Line 1*"]'),
        city: document.querySelector('input[placeholder="City*"]'),
        postcode: document.querySelector('input[placeholder="Postcode*"]'),
        state: document.querySelector('input[placeholder="State*"]'),
        phone: document.querySelector('input[placeholder="Phone Number*"]')
    };

    // Check if any required field is empty
    let isValid = true;
    let emptyFields = [];

    for (const [fieldName, field] of Object.entries(requiredFields)) {
        if (!field || !field.value.trim()) {
            isValid = false;
            emptyFields.push(fieldName.replace(/([A-Z])/g, ' $1').toLowerCase());
        }
    }

    if (!isValid) {
        alert(`Please fill in all required fields: ${emptyFields.join(', ')}`);
        return;
    }

    // Check if a payment method is selected
    const paymentSelected = document.querySelector('input[name="payment"]:checked');
    if (!paymentSelected) {
        alert('Please select a payment method');
        return;
    }

    // If validation passes, redirect to tracking page
    window.location.href = 'track-order.html';
}

function initializeTrackingPage() {
    // Set up tracking page functionality if on tracking page
    const trackingPage = document.querySelector('.tracking-page');
    if (!trackingPage) return;

    // Initialize steps
    let currentStep = 0;
    const steps = document.querySelectorAll('.step');
    
    function updateProgress() {
        steps.forEach((step, index) => {
            if (index <= currentStep) {
                step.classList.add('active');
            }
        });
    }

    // Update progress every 5 seconds
    if (steps.length > 0) {
        setInterval(() => {
            if (currentStep < steps.length - 1) {
                currentStep++;
                updateProgress();
            }
        }, 5000);
    }

    // Set estimated time
    const estimatedTimeElement = document.getElementById('estimatedTime');
    if (estimatedTimeElement) {
        const now = new Date();
        now.setMinutes(now.getMinutes() + 30);
        estimatedTimeElement.textContent = now.toLocaleTimeString([], { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    }

    // Display order items
    const orderItemsElement = document.getElementById('orderItems');
    if (orderItemsElement) {
        orderItemsElement.innerHTML = cart.items.map(item => `
            <div class="order-item">
                <span>${item.name} x ${item.quantity}</span>
                <span>RM ${(item.price * item.quantity).toFixed(2)}</span>
            </div>
        `).join('');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart display
    cart.updateCartDisplay();

    // Handle delivery fee estimation
    const addressInput = document.getElementById('addressInput');
    const deliveryFeeEstimate = document.getElementById('deliveryFeeEstimate');
    
    addressInput.addEventListener('input', function() {
        const fee = calculateDeliveryFee(this.value);
        if (fee > 0) {
            deliveryFeeEstimate.textContent = `Estimated delivery fee: RM ${fee.toFixed(2)}`;
        } else {
            deliveryFeeEstimate.textContent = '';
        }
    });

    // Handle delivery options
    const optionCards = document.querySelectorAll('.option-card');
    let selectedOption = null;

    optionCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selection from other cards
            optionCards.forEach(c => {
                c.classList.remove('selected');
                c.querySelector('.time-inputs').classList.add('hidden');
            });

            // Select this card
            this.classList.add('selected');
            this.querySelector('.time-inputs').classList.remove('hidden');
            selectedOption = this.dataset.option;
        });
    });

    // Handle checkout
    const checkoutButton = document.getElementById('checkoutButton');
    checkoutButton.addEventListener('click', function() {
        if (!selectedOption) {
            alert('Please select a delivery option');
            return;
        }

        const selectedCard = document.querySelector(`.option-card[data-option="${selectedOption}"]`);
        const dateInput = selectedCard.querySelector('.date-input').value;
        const timeInput = selectedCard.querySelector('.time-input').value;

        if (!dateInput || !timeInput) {
            alert('Please select date and time');
            return;
        }

        // Check if user is logged in (implement your own logic)
        const isLoggedIn = false; // Replace with actual check
        window.location.href = isLoggedIn ? 'checkout.html' : 'login.html';
    });
});

function calculateDeliveryFee(address) {
    // Simple distance-based calculation (replace with actual logic)
    return address.length > 0 ? 5.00 : 0;
}
