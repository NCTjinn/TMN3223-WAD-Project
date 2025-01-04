document.addEventListener('DOMContentLoaded', function() {
    initializeTrackingPage();
});

function initializeTrackingPage() {
    const steps = document.querySelectorAll('.step');
    const orderNumber = document.getElementById('orderNumber');
    const estimatedTime = document.getElementById('estimatedTime');
    const orderItems = document.getElementById('orderItems');
    const orderTotal = document.getElementById('orderTotal');

    function updateProgress(currentStatus) {
        const statusOrder = ['confirmed', 'preparing', 'ready'];
        const currentIndex = statusOrder.indexOf(currentStatus);

        steps.forEach((step, index) => {
            step.classList.toggle('active', index <= currentIndex);
        });
    }

    function updateOrderDetails(data) {
        orderNumber.textContent = data.transaction_id || 'N/A';
        estimatedTime.textContent = data.estimated_delivery || '--:--';
        orderTotal.textContent = `RM ${parseFloat(data.total_amount || 0).toFixed(2)}`;

        orderItems.innerHTML = '';
        (data.details || []).forEach(item => {
            const itemDiv = document.createElement('div');
            itemDiv.classList.add('order-item');
            itemDiv.innerHTML = `
                <span>${item.name} x ${item.quantity}</span>
                <span>RM ${parseFloat(item.subtotal).toFixed(2)}</span>
            `;
            orderItems.appendChild(itemDiv);
        });
    }

    function fetchOrderDetails() {
        fetch('get_Order.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                } else {
                    updateProgress(data.status);
                    updateOrderDetails(data);
                }
            })
            .catch(error => console.error('Error fetching order details:', error));
    }

    // Initial fetch
    fetchOrderDetails();

    // Poll for updates every 30 seconds
    setInterval(fetchOrderDetails, 30000);
}
