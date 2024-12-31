const API_ENDPOINTS = {
    notifications: '/api/admin/notifications',
    dashboardStats: '/api/admin/dashboard',
    inventory: '/api/admin/inventory',
    users: '/api/admin/users',
    engagement: '/api/admin/engagement'
  };
  
  async function fetchDashboardData() {
    try {
      const response = await fetch(API_ENDPOINTS.dashboardStats);
      const data = await response.json();
      
      if (data.status === 'success') {
        updateCharts(data.data);
        updateStats(data.data);
      }
    } catch (error) {
      console.error('Error fetching dashboard data:', error);
    }
  }
  
  function updateCharts(data) {
    // Update Pie Chart
    pieChart.data.datasets[0].data = [
      data.orderStats.dineIn,
      data.orderStats.takeaway,
      data.orderStats.delivery
    ];
    pieChart.update();
  
    // Update Bar Chart
    barChart.data.datasets[0].data = [
      data.categoryRevenue.puffs,
      data.categoryRevenue.cakes,
      data.categoryRevenue.beverages
    ];
    barChart.update();
  
    // Update Product Chart
    productChart.data.datasets[0].data = data.topProducts.map(product => product.units_sold);
    productChart.data.labels = data.topProducts.map(product => product.name);
    productChart.update();
  
    // Update Line Chart
    lineChart.data.datasets[0].data = data.salesTrend.values;
    lineChart.data.labels = data.salesTrend.labels;
    lineChart.update();
  }
  
  function updateStats(data) {
    document.getElementById('total-orders').textContent = data.total_orders;
    document.getElementById('total-revenue').textContent = `$${data.revenue_stats.total_revenue}`;
    document.getElementById('avg-order').textContent = `$${data.revenue_stats.average_order_value}`;
  }
  
  async function fetchNotifications() {
    try {
      const response = await fetch(API_ENDPOINTS.notifications);
      const data = await response.json();
      
      if (data.status === 'success') {
        updateNotificationBadge(data.unreadCount);
        notifications = data.notifications;
        renderNotifications();
      }
    } catch (error) {
      console.error('Error fetching notifications:', error);
    }
  }

document.addEventListener('DOMContentLoaded', function () {
    const profileIcon = document.getElementById('profile-icon');
    const profileDropdown = document.getElementById('dropdown-menu');
    const notificationIcon = document.querySelector('.fa-bell');
    const notificationDropdown = document.createElement('div');

    // Dynamically create notification dropdown
    notificationDropdown.className = 'dropdown-menu';
    notificationDropdown.id = 'notification-dropdown';

    // Notifications data
    const notifications = [
        { message: "New user registered", isRead: false },
        { message: "System backup completed", isRead: true },
        { message: "New comment on blog post", isRead: false },
    ];

    // Render notifications
    function renderNotifications() {
        notificationDropdown.innerHTML = `
            <div class="dropdown-header">Notifications</div>
            <button class="mark-all-btn">Mark All as Read</button>
        `;

        notifications.forEach((notification, index) => {
            const unreadClass = notification.isRead
                ? ''
                : `<span class="unread-indicator"></span>`;
            notificationDropdown.innerHTML += `
                <div class="notification-item" data-index="${index}">
                    ${unreadClass}
                    <div class="notification-text">
                        <span class="notification-title">${notification.message}</span>
                        <span class="notification-time">5 mins ago</span>
                    </div>
                </div>
            `;
        });

        const markAllBtn = notificationDropdown.querySelector('.mark-all-btn');
        markAllBtn.addEventListener('click', () => {
            notifications.forEach((n) => (n.isRead = true));
            renderNotifications();
        });

        const notificationItems = notificationDropdown.querySelectorAll('.notification-item');
        notificationItems.forEach((item) => {
            item.addEventListener('click', (e) => {
                const index = e.currentTarget.getAttribute('data-index');
                notifications[index].isRead = true;
                renderNotifications();
            });
        });
    }

    // Append notification dropdown to the body
    document.body.appendChild(notificationDropdown);

    // Position notification dropdown
    function positionNotificationDropdown() {
        const rect = notificationIcon.getBoundingClientRect();
        let dropdownTop = rect.bottom + window.scrollY + 10; // Add spacing
        let dropdownLeft = rect.left;

        if (dropdownTop + 300 > window.innerHeight + window.scrollY) {
            dropdownTop = rect.top + window.scrollY - 300 - 10;
        }
        if (dropdownLeft + 300 > window.innerWidth) {
            dropdownLeft = window.innerWidth - 310;
        }

        notificationDropdown.style.position = 'absolute';
        notificationDropdown.style.top = `${dropdownTop}px`;
        notificationDropdown.style.left = `${dropdownLeft}px`;
    }

    // Toggle notification dropdown
    notificationIcon.addEventListener('click', function (e) {
        e.stopPropagation();
        const isActive = notificationDropdown.classList.contains('active');
        closeAllDropdowns();
        if (!isActive) {
            positionNotificationDropdown();
            notificationDropdown.classList.add('active');
        }
    });

    // Toggle profile dropdown
    profileIcon.addEventListener('click', function (e) {
        e.stopPropagation();
        const isActive = profileDropdown.classList.contains('active');
        closeAllDropdowns();
        if (!isActive) {
            profileDropdown.classList.add('active');
        }
    });

    // Close all dropdowns when clicking outside
    document.addEventListener('click', function (e) {
        if (
            !e.target.closest('.user-dropdown') &&
            !e.target.closest('.icon') &&
            !e.target.closest('#notification-dropdown')
        ) {
            closeAllDropdowns();
        }
    });

    // Helper function to close all dropdowns
    function closeAllDropdowns() {
        notificationDropdown.classList.remove('active');
        profileDropdown.classList.remove('active');
    }

    // Update notification badge
    function updateNotificationBadge(count) {
        const badge = document.getElementById('notification-badge');
        
        if (count > 0) {
            badge.textContent = count; // Update badge number
            badge.style.visibility = 'visible'; // Show the badge
        } else {
            badge.style.visibility = 'hidden'; // Hide the badge
        }
    }
    
    // Simulate fetching unread notifications
    function fetchNotifications() {
        // Replace this with your actual API call
        fetch('/api/notifications/unread')
            .then((response) => response.json())
            .then((data) => {
                const unreadCount = data.unreadCount || 0; // Default to 0 if no unreadCount is present
                updateNotificationBadge(unreadCount);
            })
            .catch(console.error);
    }
    
    // Call the function periodically or on page load
    fetchNotifications();
    setInterval(fetchNotifications, 60000); // Check every 60 seconds

    // Initial render of notifications
    renderNotifications();

    // Update "Last Updated" timestamp
    function updateLastUpdated() {
        const lastUpdatedElement = document.getElementById('lastUpdated');
        const now = new Date();
        const formattedTime = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        lastUpdatedElement.textContent = `${formattedTime}`;
    }

    updateLastUpdated(); // Call initially
    setInterval(updateLastUpdated, 300000); // Update every 5 minutes

    // Pie Chart
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['Dine-In', 'Takeaway', 'Delivery'],
            datasets: [{
                data: [50, 30, 20],
                backgroundColor: ['#6c7a5d', '#ff5722', '#c2c9ad']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });

    // Bar Chart
    const ctxBar = document.getElementById('barChart').getContext('2d');
    const barChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['Puffs', 'Cakes', 'Beverages'],
            datasets: [{
                label: 'Revenue ($)',
                data: [1200, 800, 600],
                backgroundColor: ['rgba(255,87,34,0.8)', 'rgba(194,201,173,0.8)', 'rgba(108,122,93,0.8)']
            }]
        },
        options: {
            responsive: true
        }
    });

    // Product Performance Chart
    const ctxProduct = document.getElementById('productChart').getContext('2d');
    const productChart = new Chart(ctxProduct, {
        type: 'bar',
        data: {
            labels: ['Durian Puff', 'Chocolate Cake', 'Milk Tea'],
            datasets: [{
                label: 'Units Sold',
                data: [850, 600, 400],
                backgroundColor: ['#ff5722', '#c2c9ad', '#6c7a5d']
            }]
        },
        options: {
            indexAxis: 'y', /* Horizontal bar chart */
            responsive: true
        }
    });

    // Line Chart with Filled Area
    const ctxLine = document.getElementById('lineChart').getContext('2d');
    const gradient = ctxLine.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(108, 122, 93, 0.4)');
    gradient.addColorStop(1, 'rgba(108, 122, 93, 0)');

    const lineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: ['8am', '10am', '12pm', '2pm', '4pm', '6pm', '8pm'],
            datasets: [{
                label: 'Daily Sales',
                data: [100, 215, 150, 308, 257, 420, 334],
                borderColor: '#6c7a5d',
                backgroundColor: gradient,
                fill: true,
                tension: 0.4 // Adds curvature
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Sales Buttons
    const salesButtons = document.querySelectorAll('.sales-btn');
    salesButtons.forEach(button => {
        button.addEventListener('click', () => {
            salesButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            const period = button.getAttribute('data-period');

            const data = {
                daily: {
                    labels: ['8am', '10am', '12pm', '2pm', '4pm', '6pm', '8pm'],
                    data: [100, 215, 150, 308, 257, 420, 334]
                },
                weekly: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    data: [520, 550, 710, 850, 930, 1050, 1120]
                },
                monthly: {
                    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    data: [3000, 3200, 3100, 3300]
                }
            };

            lineChart.data.labels = data[period].labels;
            lineChart.data.datasets[0].data = data[period].data;
            lineChart.update();
        });
    });

    // Replace setInterval calls with real API calls
    document.addEventListener('DOMContentLoaded', function() {
        fetchDashboardData();
        fetchNotifications();
        
        setInterval(fetchDashboardData, 30000); // Update every 30 seconds
        setInterval(fetchNotifications, 60000); // Update every minute
    });

    // Fake Real-Time Data Update (Increment Only Latest Data)
    setInterval(() => {
        // Increment Pie Chart logically
        pieChart.data.datasets[0].data = pieChart.data.datasets[0].data.map((value) => value + Math.random() * 2);
        pieChart.update();

        // Increment Bar Chart logically
        barChart.data.datasets[0].data = barChart.data.datasets[0].data.map((value) => value + Math.random() * 5);
        barChart.update();

        // Increment Product Chart logically
        productChart.data.datasets[0].data[0] += Math.random() * 2; // Only increment "Durian Puff" for realism
        productChart.update();

        // Increment only latest point in Line Chart
        const lastIndex = lineChart.data.datasets[0].data.length - 1;
        lineChart.data.datasets[0].data[lastIndex] += Math.random() * 20;
        lineChart.update();
    }, 10000); // Update every 10 seconds
});

// Add to admindashboardscript.js
function showLoadingState() {
    document.querySelector('.dashboard-content').classList.add('loading');
}

function hideLoadingState() {
    document.querySelector('.dashboard-content').classList.remove('loading');
}