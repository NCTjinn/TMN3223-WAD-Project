// memberOrders.js
document.addEventListener('DOMContentLoaded', function() {
    fetchOrders();
});

async function fetchOrders() {
    try {
        const response = await fetch('../api/memberOrders.php');
        const data = await response.json();
        
        renderOrders('current-orders-list', data.current_orders);
        renderOrders('past-orders-list', data.past_orders);
    } catch (error) {
        console.error('Error fetching orders:', error);
        showError('Failed to load orders. Please try again later.');
    }
}

function renderOrders(containerId, orders) {
    const container = document.getElementById(containerId);
    if (!orders.length) {
        container.innerHTML = '<p>No orders found.</p>';
        return;
    }

    container.innerHTML = orders.map(order => `
        <div class="order-item">
            <div class="order-header">
                <div>
                    <h3>Order #${order.order_id}</h3>
                    <p>Placed on: ${formatDate(order.transaction_date)}</p>
                    <p>Status: <span class="status status-${order.status.toLowerCase()}">${order.status}</span></p>
                </div>
            </div>
            <div class="order-details">
                <div class="order-summary">
                    <h4>Items Ordered:</h4>
                    <ul>
                        ${order.order_items.map(item => `<li>${item}</li>`).join('')}
                    </ul>
                    <p><strong>Subtotal: $${(order.total_amount - order.delivery_fee - order.tax_amount).toFixed(2)}</strong></p>
                    <p>Delivery Fee: $${order.delivery_fee}</p>
                    <p>Tax: $${order.tax_amount}</p>
                    <p><strong>Total: $${order.total_amount}</strong></p>
                </div>
                <div class="order-buttons">
                    <button class="shop-btn" onclick="window.location.href='publicMenu.html'">Go Shop</button>
                    <button class="support-btn" onclick="window.location.href='publicContactUs.html'">Contact Support</button>
                </div>
            </div>
        </div>
    `).join('');
}

function formatDate(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    document.querySelector('.orders-container').prepend(errorDiv);
}