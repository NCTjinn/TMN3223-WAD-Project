function fetchDashboardData() {
    showLoadingState();

    fetch('../api/memberDashboard.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.message);
            }
            updateDashboard(data);
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorState();
        });
}

function showLoadingState() {
    const overviewItems = document.querySelectorAll('.overview-item p');
    overviewItems.forEach(item => item.innerHTML = '<i class="fa fa-spinner fa-spin"></i>');
}

function showErrorState() {
    const overviewItems = document.querySelectorAll('.overview-item p');
    overviewItems.forEach(item => item.textContent = 'Error loading data');
}

function updateDashboard(data) {
    document.getElementById('orders-overview').querySelector('p').textContent = 
        `${data.total_orders} Orders`;
    document.getElementById('addresses-overview').querySelector('p').textContent = 
        `${data.saved_addresses} Addresses`;
    document.getElementById('status-overview').querySelector('p').textContent = 
        `Member since ${data.account_created}`;
}

document.addEventListener('DOMContentLoaded', fetchDashboardData);
