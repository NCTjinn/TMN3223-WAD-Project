// Constants and Configuration
const baseUrl = window.location.hostname === 'localhost' 
    ? 'http://localhost/TMN3223-WAD-Project' 
    : 'https://your-production-domain.com';

const API_CONFIG = {
    // Base URL - change this based on your environment
    baseUrl: baseUrl,
    
    // API endpoints
    endpoints: {
        dashboardStats: `${baseUrl}/api/adminDashboard`,
        transactions: `${baseUrl}/api/adminTransactions`
    },
    
    // Request timeout in milliseconds
    timeout: 5000
};

const CHART_COLORS = {
    primary: '#6c7a5d',
    secondary: '#C2C9AD',
    accent: '#ff5722',
    background: '#F2EDD3',
    text: '#1F1F1F'
};

const CHART_OPTIONS = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom'
        }
    }
};

// State Management
let dashboardState = {
    currentPeriod: 'daily',
    lastUpdate: new Date(),
    chartInstances: {}
};

// Enhanced fetch function with timeout and better error handling
async function fetchWithAuth(endpoint, options = {}) {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), API_CONFIG.timeout);

    try {
        const defaultOptions = {
            method: 'GET',
            signal: controller.signal,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        };

        const url = endpoint.startsWith('http') ? endpoint : `${API_CONFIG.baseUrl}${endpoint}`;
        console.log(`Fetching from: ${url}`); // Debug log

        const response = await fetch(url, { ...defaultOptions, ...options });

        if (!response.ok) {
            // Handle different HTTP error codes
            switch (response.status) {
                case 404:
                    throw new Error(`API endpoint not found: ${endpoint}`);
                case 401:
                    throw new Error('Authentication required');
                case 403:
                    throw new Error('Access forbidden');
                case 500:
                    throw new Error('Internal server error');
                default:
                    throw new Error(`HTTP error! status: ${response.status}`);
            }
        }

        const data = await response.json();
        console.log('Fetched data:', data); // Debug log
        return data;
    } catch (error) {
        console.error('Error fetching data:', error);
        throw error;
    } finally {
        clearTimeout(timeoutId);
    }
}

// API Handlers
async function fetchDashboardData() {
    console.log('Fetching dashboard data...'); // Debug log
    showLoadingState();
    try {
        const data = await fetchWithAuth(API_CONFIG.endpoints.dashboardStats);
        console.log('Fetched dashboard data:', data); // Debug log
        if (data.status === 'success') {
            console.log('Fetched dashboard data:', data.data); // Debug log
            updateDashboard(data.data);
            updateStats(data.data); // Ensure this function is called
            updateLastUpdated();
            hideErrorMessage();
        } else {
            throw new Error(data.error || 'Failed to load dashboard data');
        }
    } catch (error) {
        console.error('Error fetching dashboard data:', error);
        showErrorMessage(error.message);
    } finally {
        hideLoadingState();
    }
}

// Chart Initialization and Updates
function initializeCharts() {
    const charts = {
        pie: initializePieChart(),
        bar: initializeBarChart(),
        product: initializeProductChart(),
        line: initializeLineChart()
    };
    
    dashboardState.chartInstances = charts;
}

function initializePieChart() {
    const ctx = document.getElementById('pieChart').getContext('2d');
    return new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Dine-In', 'Takeaway', 'Delivery'],
            datasets: [{
                data: [0, 0, 0],
                backgroundColor: Object.values(CHART_COLORS).slice(0, 3)
            }]
        },
        options: {
            ...CHART_OPTIONS,
            cutout: '50%'
        }
    });
}

function initializeBarChart() {
    const ctx = document.getElementById('barChart').getContext('2d');
    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Puffs', 'Cakes', 'Beverages'],
            datasets: [{
                label: 'Revenue',
                data: [0, 0, 0],
                backgroundColor: CHART_COLORS.primary
            }]
        },
        options: CHART_OPTIONS
    });
}

function initializeProductChart() {
    const ctx = document.getElementById('productChart').getContext('2d');
    return new Chart(ctx, {
        type: 'bar', // Vertical bar chart
        data: {
            labels: [], // Labels will be empty initially
            datasets: [{
                label: 'Units Sold',
                data: [], // Data will be empty initially
                backgroundColor: CHART_COLORS.secondary
            }]
        },
        options: {
            ...CHART_OPTIONS,
            plugins: {
                legend: {
                    display: false // Hide the legend
                },
                tooltip: {
                    callbacks: {
                        title: (tooltipItems) => {
                            return tooltipItems[0].label; // Show the product name in the tooltip
                        },
                        label: (tooltipItem) => {
                            return `Units Sold: ${tooltipItem.raw}`; // Show the units sold in the tooltip
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: false // Show the x-axis labels
                },
                y: {
                    display: false, // Hide the y-axis labels
                    beginAtZero: true // Ensure the y-axis starts at zero
                }
            }
        }
    });
}

function initializeLineChart() {
    const ctx = document.getElementById('lineChart').getContext('2d');
    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Sales',
                data: [],
                borderColor: CHART_COLORS.primary,
                tension: 0.4
            }]
        },
        options: {
            ...CHART_OPTIONS,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// UI Updates
function updateDashboard(data) {
    console.log('Updating dashboard with data:', data); // Debugging log

    // Ensure that orderStats contains the expected values
    const dineInOrders = parseInt(data.orderStats.dineIn) || 0;
    const takeawayOrders = parseInt(data.orderStats.takeaway) || 0;
    const deliveryOrders = parseInt(data.orderStats.delivery) || 0;

    const totalOrders = dineInOrders + takeawayOrders + deliveryOrders;

    // Ensure totalOrders is not zero to avoid division by zero
    const dineInPercentage = totalOrders ? ((dineInOrders / totalOrders) * 100).toFixed(2) : 0;
    const takeawayPercentage = totalOrders ? ((takeawayOrders / totalOrders) * 100).toFixed(2) : 0;
    const deliveryPercentage = totalOrders ? ((deliveryOrders / totalOrders) * 100).toFixed(2) : 0;

    const statsMap = {
        'dineInPercentage': `${dineInPercentage}%`,
        'takeawayPercentage': `${takeawayPercentage}%`,
        'deliveryPercentage': `${deliveryPercentage}%`,
        'totalRevenue': formatCurrency(data.revenue_stats.total_revenue),
        'weeklyRevenue': formatCurrency(data.revenue_stats.weekly_revenue),
        'monthlyRevenue': formatCurrency(data.revenue_stats.monthly_revenue),
        'topCategory': data.topCategory.name,
        'topCategoryRevenue': formatCurrency(data.topCategory.revenue),
        'topProduct': data.topProducts[0]?.name || 'N/A',
        'topProductUnits': formatNumber(data.topProducts[0]?.units_sold || 0),
        'topProductRevenue': formatCurrency(data.topProducts[0]?.revenue || 0),
        'totalCustomers': formatNumber(data.total_customers),
        'averageOrderValue': formatCurrency(data.average_order_value),
        'periodRevenue': formatCurrency(data.period_stats?.revenue || 0),
    };

    Object.entries(statsMap).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
            // Add fade-in animation for updated values
            element.classList.add('value-updated');
            setTimeout(() => element.classList.remove('value-updated'), 500);
        } else {
            console.warn(`Element with id: ${id} not found`); // Debug log
        }
    });

    updateCharts(data);
    updateLastUpdated();
}

function updateCharts(data) {
    const { chartInstances } = dashboardState;

    // Check if data structure is correct
    if (!data.orderStats || !data.categoryRevenue || !data.topProducts || !data.salesTrend) {
        console.error('Data structure is incorrect:', data);
        return;
    }

    // Update Pie Chart
    if (chartInstances.pie) {
        console.log('Updating Pie Chart with data:', data.orderStats); // Debug log
        chartInstances.pie.data.datasets[0].data = [
            data.orderStats.dineIn || 0,
            data.orderStats.takeaway || 0,
            data.orderStats.delivery || 0
        ];
        chartInstances.pie.update();
    }

    // Update Bar Chart
    if (chartInstances.bar) {
        console.log('Updating Bar Chart with data:', data.categoryRevenue); // Debug log
        chartInstances.bar.data.datasets[0].data = [
            data.categoryRevenue.puffs || 0,
            data.categoryRevenue.cakes || 0,
            data.categoryRevenue.beverages || 0
        ];
        chartInstances.bar.update();
    }

    // Update Product Chart
    if (chartInstances.product) {
        console.log('Updating Product Chart with data:', data.topProducts); // Debug log
        const topProducts = data.topProducts.slice(0, 5); // Get only the top 5 products
        chartInstances.product.data.labels = topProducts.map(p => p.name);
        chartInstances.product.data.datasets[0].data = topProducts.map(p => p.units_sold || 0);
        chartInstances.product.update();
    }

    // Update Line Chart based on selected period
    if (chartInstances.line) {
        console.log('Current period:', dashboardState.currentPeriod);
        console.log('Sales trend data:', data.salesTrend);
        
        const trendData = data.salesTrend[dashboardState.currentPeriod];
        if (trendData && trendData.labels && trendData.values) {
            let labels = [];
            let values = [];

            try {
                // Get current date in YYYY-MM-DD format
                const currentDate = new Date().toISOString().split('T')[0];
                console.log('Current date:', currentDate);

                // Generate complete date range
                if (dashboardState.currentPeriod === 'weekly') {
                    const startDate = new Date();
                    startDate.setDate(startDate.getDate() - 6);
                    labels = getDatesInRange(startDate, new Date());
                } else if (dashboardState.currentPeriod === 'monthly') {
                    const startDate = new Date();
                    startDate.setDate(startDate.getDate() - 29);
                    labels = getDatesInRange(startDate, new Date());
                } else {
                    labels = trendData.labels;
                }

                // Map values to dates, using 0 for missing dates
                values = labels.map(date => {
                    const index = trendData.labels.indexOf(date);
                    return index !== -1 ? trendData.values[index] : 0;
                });

                console.log('Generated labels:', labels);
                console.log('Generated values:', values);

                chartInstances.line.data.labels = labels;
                chartInstances.line.data.datasets[0].data = values;
                chartInstances.line.update();
            } catch (error) {
                console.error('Error updating chart:', error);
                chartInstances.line.data.labels = trendData.labels;
                chartInstances.line.data.datasets[0].data = trendData.values;
                chartInstances.line.update();
            }
        } else {
            console.error('Sales trend data is missing or incomplete');
            chartInstances.line.data.labels = [];
            chartInstances.line.data.datasets[0].data = [];
            chartInstances.line.update();
        }
    }
}

// Function to update stats
function updateStats(data) {
    console.log('Updating stats with data:', data); // Debugging log

    // Ensure that orderStats contains the expected values
    const dineInOrders = parseInt(data.orderStats.dineIn) || 0;
    const takeawayOrders = parseInt(data.orderStats.takeaway) || 0;
    const deliveryOrders = parseInt(data.orderStats.delivery) || 0;

    const totalOrders = dineInOrders + takeawayOrders + deliveryOrders;

    // Ensure totalOrders is not zero to avoid division by zero
    const dineInPercentage = totalOrders ? ((dineInOrders / totalOrders) * 100).toFixed(2) : 0;
    const takeawayPercentage = totalOrders ? ((takeawayOrders / totalOrders) * 100).toFixed(2) : 0;
    const deliveryPercentage = totalOrders ? ((deliveryOrders / totalOrders) * 100).toFixed(2) : 0;

    const periodRevenue = data.periodRevenue[dashboardState.currentPeriod] || 0;
    console.log('Current period:', dashboardState.currentPeriod); // Debug log
    console.log('Period revenue:', periodRevenue); // Debug log

    const statsMap = {
        'dineInPercentage': `${dineInPercentage}%`,
        'takeawayPercentage': `${takeawayPercentage}%`,
        'deliveryPercentage': `${deliveryPercentage}%`,
        'totalRevenue': formatCurrency(data.revenue_stats.total_revenue),
        'weeklyRevenue': formatCurrency(data.revenue_stats.weekly_revenue),
        'monthlyRevenue': formatCurrency(data.revenue_stats.monthly_revenue),
        'topCategory': data.topCategory.name,
        'topCategoryRevenue': formatCurrency(data.topCategory.revenue),
        'topProduct': data.topProducts[0]?.name || 'N/A',
        'topProductUnits': formatNumber(data.topProducts[0]?.units_sold || 0),
        'topProductRevenue': formatCurrency(data.topProducts[0]?.revenue || 0),
        'totalCustomers': formatNumber(data.total_customers),
        'averageOrderValue': formatCurrency(data.average_order_value),
        'periodRevenue': formatCurrency(periodRevenue)
    };

    Object.entries(statsMap).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
            // Add fade-in animation for updated values
            element.classList.add('value-updated');
            setTimeout(() => element.classList.remove('value-updated'), 500);
        } else {
            console.warn(`Element with id: ${id} not found`); // Debug log
        }
    });
}

// Event Handlers
function attachEventListeners() {
    // Period Selection Buttons
    document.querySelectorAll('.sales-btn').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelectorAll('.sales-btn').forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            dashboardState.currentPeriod = button.dataset.period;
            fetchDashboardData();
        });
    });

    // User Dropdown Toggle
    const profileIcon = document.getElementById('profile-icon');
    const dropdownMenu = document.getElementById('dropdown-menu');
    
    if (profileIcon && dropdownMenu) {
        profileIcon.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownMenu.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', () => {
            dropdownMenu.classList.remove('active');
        });
    }
}

// Utility Functions
function getDatesInRange(startDate, endDate) {
    const dates = [];
    let currentDate = new Date(startDate);
    while (currentDate <= endDate) {
        dates.push(currentDate.toISOString().split('T')[0]);
        currentDate.setDate(currentDate.getDate() + 1);
    }
    return dates;
}

function formatCurrency(value) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(value);
}

function formatNumber(value) {
    return new Intl.NumberFormat('en-US').format(value);
}

function formatPercentage(value) {
    return `${value >= 0 ? '+' : ''}${value.toFixed(1)}%`;
}

function formatTimeAgo(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    const intervals = {
        year: 31536000,
        month: 2592000,
        week: 604800,
        day: 86400,
        hour: 3600,
        minute: 60
    };

    for (const [unit, secondsInUnit] of Object.entries(intervals)) {
        const interval = Math.floor(seconds / secondsInUnit);
        if (interval >= 1) {
            return `${interval} ${unit}${interval === 1 ? '' : 's'} ago`;
        }
    }
    return 'Just now';
}

function updateLastUpdated() {
    const element = document.getElementById('lastUpdated');
    if (element) {
        dashboardState.lastUpdate = new Date();
        element.textContent = dashboardState.lastUpdate.toLocaleTimeString();
    }
}

// Loading State Management
function showLoadingState() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.classList.add('active');
    }
}

function hideLoadingState() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.classList.remove('active');
    }
}

// Error Handling
function showErrorMessage(message) {
    const errorElement = document.getElementById('error-message');
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.add('active');
    }
}

function hideErrorMessage() {
    const errorElement = document.getElementById('error-message');
    if (errorElement) {
        errorElement.classList.remove('active');
    }
}

// Initialize Dashboard
document.addEventListener('DOMContentLoaded', () => {
    initializeCharts();
    attachEventListeners();
    fetchDashboardData();
    
    // Set up polling intervals
    const intervals = [
        { fn: fetchDashboardData, ms: 30000 },  // 30 seconds
    ];
    
    intervals.forEach(({fn, ms}) => setInterval(fn, ms));
});