// memberCheckout.js
const checkout = {
    items: [],
    deliveryOption: null,
    deliveryFee: 1.99,

    async loadUserDetails() {
        try {
            const response = await fetch('get_userDetails.php');
            if (!response.ok) throw new Error('Failed to load user details');
            const userDetails = await response.json();
            
            // Only populate if the autofill checkbox is checked
            const autofillCheckbox = document.getElementById('autofill-address');
            if (autofillCheckbox && autofillCheckbox.checked) {
                this.populateUserDetails(userDetails);
            }
        } catch (error) {
            console.error('Error loading user details:', error);
        }
    },

    populateUserDetails(details) {
        const fields = {
            'firstName': details.first_name,
            'lastName': details.last_name,
            'addressLine1': details.address_line_1,
            'city': details.city,
            'postcode': details.postcode,
            'state': details.state,
            'phone': details.phone
        };

        for (const [id, value] of Object.entries(fields)) {
            const input = document.querySelector(`input[name="${id}"]`);
            if (input && value) {
                input.value = value;
            }
        }
    },
    
    async loadCartItems() {
        try {
            const response = await fetch('memberCart_api.php');
            if (!response.ok) throw new Error('Failed to load cart');
            this.items = await response.json();
            this.updateCheckoutDisplay();
            await this.loadUserDetails(); // Load user details on initialization
        } catch (error) {
            console.error('Error loading cart:', error);
            alert('Failed to load cart items. Please try again.');
        }
    },

    calculateSubtotal() {
        return this.items.reduce((total, item) => 
            total + (parseFloat(item.price) * item.quantity), 0);
    },

    calculateDeliveryFee(address) {
        // Basic distance-based calculation (replace with actual logic)
        if (!address) return 0;
        
        // Example distance calculation - replace with actual logic
        const baseDistance = 5; // km
        const baseFee = 1.99;
        const extraKmFee = 0.50;
        
        // Mock distance calculation based on postcode
        const postcode = address.postcode;
        const distance = postcode ? Math.abs(parseInt(postcode) - 93350) / 1000 : baseDistance;
        
        const fee = baseFee + Math.max(0, (distance - baseDistance) * extraKmFee);
        return Math.min(fee, 10); // Cap at RM10
    },

    updateCheckoutDisplay() {
        // Update order items
        const orderItems = document.getElementById('orderItems');
        if (orderItems) {
            orderItems.innerHTML = this.items.map(item => `
                <div class="order-item">
                    <div class="item-details">
                        <span class="item-name">${item.name}</span>
                        <span class="item-quantity">x${item.quantity}</span>
                    </div>
                    <span class="item-price">RM ${(parseFloat(item.price) * item.quantity).toFixed(2)}</span>
                </div>
            `).join('');
        }

        // Calculate totals
        const subtotal = this.calculateSubtotal();
        const deliveryFee = this.deliveryOption === 'delivery' ? this.deliveryFee : 0;
        const total = subtotal + deliveryFee;

        // Update summary section
        const summaryDetails = document.querySelector('.summary-details');
        if (summaryDetails) {
            summaryDetails.innerHTML = `
                <div class="summary-line">
                    <span>Subtotal</span>
                    <span>RM ${subtotal.toFixed(2)}</span>
                </div>
                ${this.deliveryOption === 'delivery' ? `
                <div class="summary-line">
                    <span>Delivery Fee</span>
                    <span>RM ${deliveryFee.toFixed(2)}</span>
                </div>
                ` : ''}
                <div class="summary-total">
                    <span>Total</span>
                    <span>RM ${total.toFixed(2)}</span>
                </div>
            `;
        }
    },

    async processCheckout() {
        try {
            const formData = new FormData(document.querySelector('.delivery-form'));
            const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
            
            const orderData = {
                items: this.items,
                deliveryOption: this.deliveryOption,
                deliveryFee: this.deliveryOption === 'delivery' ? this.deliveryFee : 0,
                total: this.calculateSubtotal() + (this.deliveryOption === 'delivery' ? this.deliveryFee : 0),
                paymentMethod: paymentMethod,
                address: {
                    firstName: formData.get('firstName'),
                    lastName: formData.get('lastName'),
                    addressLine1: formData.get('addressLine1'),
                    city: formData.get('city'),
                    postcode: formData.get('postcode'),
                    state: formData.get('state'),
                    phone: formData.get('phone')
                }
            };

            const response = await fetch('process_checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(orderData)
            });

            if (!response.ok) {
                throw new Error('Checkout failed');
            }

            window.location.href = 'paymentsuccess.html';
        } catch (error) {
            console.error('Error processing checkout:', error);
            alert('Failed to process checkout. Please try again.');
        }
    },

    validateCheckout() {
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
    
        // Proceed with checkout
        alert('Order placed successfully!');
        window.location.href = 'memberTrack.php';
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // Initialize checkout
    checkout.loadCartItems();

    // Handle delivery option selection
    const optionCards = document.querySelectorAll('.option-card');
    optionCards.forEach(card => {
        card.addEventListener('click', function() {
            optionCards.forEach(c => {
                c.classList.remove('selected');
                c.querySelector('.time-inputs').classList.add('hidden');
            });
            
            card.classList.add('selected');
            card.querySelector('.time-inputs').classList.remove('hidden');
            
            // Update delivery option and recalculate totals
            checkout.deliveryOption = card.dataset.option;
            checkout.updateCheckoutDisplay();
        });
    });

    // Handle address input for delivery fee calculation
    const addressInput = document.querySelector('input[placeholder="Address Line 1*"]');
    const postcodeInput = document.querySelector('input[placeholder="Postcode*"]');
    if (addressInput && postcodeInput) {
        const updateDeliveryFee = () => {
            if (checkout.deliveryOption === 'delivery') {
                const address = {
                    line1: addressInput.value,
                    postcode: postcodeInput.value
                };
                const fee = checkout.calculateDeliveryFee(address);
                checkout.updateCheckoutDisplay();
            }
        };

        addressInput.addEventListener('input', updateDeliveryFee);
        postcodeInput.addEventListener('input', updateDeliveryFee);
    }
});