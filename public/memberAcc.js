// Example: Use AJAX to fetch data dynamically
function fetchDashboardData() {
    fetch('backend_endpoint.php') // Replace with your backend API endpoint
        .then(response => response.json())
        .then(data => {
            // Populate HTML with fetched data
            document.querySelector('.overview-item:nth-child(1) p').innerText = data.total_orders + ' Orders';
            document.querySelector('.overview-item:nth-child(2) p').innerText = data.saved_addresses + ' Addresses';
            document.querySelector('.overview-item:nth-child(3) p').innerText = data.account_status;
            document.querySelector('.overview-item:nth-child(4) p').innerText = data.reward_points + ' Points';
        })
        .catch(error => console.error('Error fetching data:', error));
}

// Call the function when the page loads
document.addEventListener('DOMContentLoaded', fetchDashboardData);
