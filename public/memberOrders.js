// memberOrders.js
document.addEventListener('DOMContentLoaded', function() {
    fetchOrders();
});

async function fetchOrders() {
    try {
        // Add error handling for the response
        const response = await fetch('../api/member_Orders.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        
        if (data.error) {
            throw new Error(data.message || 'An error occurred while fetching orders');
        }
        
        renderOrders('current-orders-list', data.current_orders || []);
        renderOrders('past-orders-list', data.past_orders || []);
    } catch (error) {
        console.error('Error fetching orders:', error);
        showError('Failed to load orders. Please try again later.');
    }
}

function renderOrders(containerId, orders) {
    const container = document.getElementById(containerId);
    if (!container) {
        console.error(`Container ${containerId} not found`);
        return;
    }

    if (!orders || !orders.length) {
        container.innerHTML = '<p class="no-orders">No orders found.</p>';
        return;
    }

    container.innerHTML = orders.map(order => `
        <div class="order-item">
            <div class="order-header">
                <div>
                    <h3>Order #${order.order_id}</h3>
                    <p>Placed on: ${formatDate(order.transaction_date)}</p>
                    <p>Status: <span class="status status-${order.status.toLowerCase()}">${order.status}</span></p>
                    ${order.tracking_number ? `<p>Tracking Number: ${order.tracking_number}</p>` : ''}
                    ${order.estimated_delivery ? `<p>Estimated Delivery: ${formatDate(order.estimated_delivery)}</p>` : ''}
                </div>
            </div>
            <div class="order-details">
                <div class="order-summary">
                    <h4>Items Ordered:</h4>
                    <ul class="order-items-list">
                        ${order.order_items.map(item => `<li>${item}</li>`).join('')}
                    </ul>
                    <div class="order-totals">
                        <p><strong>Subtotal:</strong> $${(parseFloat(order.total_amount) - parseFloat(order.delivery_fee) - parseFloat(order.tax_amount)).toFixed(2)}</p>
                        <p><strong>Delivery Fee:</strong> $${parseFloat(order.delivery_fee).toFixed(2)}</p>
                        <p><strong>Tax:</strong> $${parseFloat(order.tax_amount).toFixed(2)}</p>
                        <p class="total"><strong>Total:</strong> $${parseFloat(order.total_amount).toFixed(2)}</p>
                    </div>
                </div>
                <div class="order-buttons">
                    <button class="shop-btn" onclick="window.location.href='memberMenu.php'">Shop Again</button>
                    <button class="support-btn" onclick="window.location.href='https://wa.me/60133119957'">Contact Support</button>
                </div>
            </div>
        </div>
    `).join('');
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    
    try {
        const options = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return new Date(dateString).toLocaleDateString('en-US', options);
    } catch (error) {
        console.error('Error formatting date:', error);
        return dateString;
    }
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    
    const container = document.querySelector('.orders-container');
    if (container) {
        // Remove any existing error messages
        const existingError = container.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        container.prepend(errorDiv);
    }
}