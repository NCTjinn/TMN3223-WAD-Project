// Constants and Configuration
const baseUrl = window.location.hostname === 'localhost' 
    ? 'http://localhost/TMN3223-WAD-Project' 
    : 'https://your-production-domain.com';

const API_CONFIG = {
    // Base URL - change this based on your environment
    baseUrl: baseUrl,
    
    // API endpoints
    endpoints: {
        notifications: `${baseUrl}/api/admin/notifications`,
        dashboardStats: `${baseUrl}/api/admin/dashboard`,
        inventory: `${baseUrl}/api/admin/inventory`,
        users: `${baseUrl}/api/admin/users`,
        engagement: `${baseUrl}/api/admin/engagement`,
        unreadNotifications: `${baseUrl}/api/admin/notifications/unread`,
        products: `${baseUrl}/api/admin/products`,
        transactions: `${baseUrl}/api/admin/transactions`
    },
    
    // Request timeout in milliseconds
    timeout: 5000
};

// Global state
const dashboardState = {
    notifications: []
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
            const errorText = await response.text();
            console.error(`Error response text: ${errorText}`); // Log the error response text
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
async function fetchNotifications() {
    try {
        const data = await fetchWithAuth(API_CONFIG.endpoints.notifications);
        console.log('Notifications data:', data); // Debug log
        if (data.status === 'success') {
            dashboardState.notifications = data.notifications;
            updateNotificationBadge(data.unreadCount);
            updateNotificationPanel();
        } else {
            throw new Error(data.error || 'Failed to load notifications');
        }
    } catch (error) {
        console.error('Error fetching notifications:', error);
        showErrorMessage(error.message);
    }
}

let currentPage = 1;
const itemsPerPage = 10;
let filteredTransactions = [];

async function fetchTransactions() {
    try {
        const startDate = document.getElementById('date-start').value;
        const endDate = document.getElementById('date-end').value;
        const searchTerm = document.getElementById('search-input').value.toLowerCase();
        const url = new URL(API_CONFIG.endpoints.transactions, window.location.origin);
        
        if (startDate) url.searchParams.append('start_date', startDate);
        if (endDate) url.searchParams.append('end_date', endDate);
        url.searchParams.append('limit', '1000');

        const data = await fetchWithAuth(url.toString());
        if (data.status === 'success') {
            // Filter transactions based on search term
            filteredTransactions = data.data.filter(transaction => 
                Object.values(transaction).some(value => 
                    value.toString().toLowerCase().includes(searchTerm)
                )
            );
            currentPage = 1;
            renderTable();
        }
    } catch (error) {
        console.error('Error fetching transactions:', error);
    }
}

function renderTable() {
    const tableBody = document.getElementById('transaction-table-body');
    tableBody.innerHTML = '';

    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const paginatedTransactions = filteredTransactions.slice(start, end);

    paginatedTransactions.forEach(transaction => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${transaction.transactionId}</td>
            <td>${transaction.userId}</td>
            <td>${transaction.dateTime}</td>
            <td>${formatCurrency(transaction.totalAmount)}</td>
            <td>${formatCurrency(transaction.deliveryFee)}</td>
            <td>${formatCurrency(transaction.taxAmount)}</td>
            <td>${transaction.paymentStatus}</td>
            <td>${transaction.shippingMethod}</td>
        `;
        tableBody.appendChild(row);
    });

    document.getElementById('current-page').textContent = `Page ${currentPage}`;
    document.getElementById('prev-page').disabled = currentPage === 1;
    document.getElementById('next-page').disabled = end >= filteredTransactions.length;
}

// Notification Management
function updateNotificationBadge(count) {
    console.log(`Updating notification badge with count: ${count}`); // Debug log
    const badge = document.getElementById('notification-badge');
    if (badge) {
        badge.textContent = count;
        badge.style.visibility = count > 0 ? 'visible' : 'hidden';
    } else {
        console.warn('Notification badge element not found'); // Debug log
    }
}

function updateNotificationPanel() {
    const container = document.getElementById('notifications-list');
    if (!container) return;

    container.innerHTML = dashboardState.notifications
        .map(notification => `
            <div class="notification-item ${notification.status}" data-id="${notification.id}">
                <div class="notification-content">
                    <span class="notification-title">${notification.title}</span>
                    <span class="notification-message">${notification.message}</span>
                    <span class="notification-time">${formatTimeAgo(notification.timestamp)}</span>
                </div>
                <button class="mark-read-btn" data-id="${notification.id}">
                    <i class="fas fa-check"></i>
                </button>
            </div>
        `)
        .join('');

    attachNotificationHandlers();
}

// Event Handlers
function attachEventListeners() {
    // Notification Panel Toggle
    const notificationIcon = document.querySelector('.notification-icon');
    const notificationPanel = document.getElementById('notification-panel');
    
    if (notificationIcon && notificationPanel) {
        notificationIcon.addEventListener('click', () => {
            notificationPanel.classList.toggle('active');
        });
    }

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

    // Clear All Notifications
    const clearBtn = document.getElementById('clear-notifications');
    if (clearBtn) {
        clearBtn.addEventListener('click', async () => {
            try {
                await fetchWithAuth(`${API_CONFIG.endpoints.notifications}/clear`, { method: 'POST' });
                dashboardState.notifications = [];
                updateNotificationPanel();
                updateNotificationBadge(0);
            } catch (error) {
                console.error('Error clearing notifications:', error);
            }
        });
    }

    // Apply Filters
    document.getElementById('apply-filters').addEventListener('click', fetchTransactions);

    // Search
    document.getElementById('search-btn').addEventListener('click', fetchTransactions);

    // Pagination
    document.getElementById('prev-page').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            renderTable();
        }
    });

    document.getElementById('next-page').addEventListener('click', () => {
        if ((currentPage * itemsPerPage) < filteredTransactions.length) {
            currentPage++;
            renderTable();
        }
    });
}

function attachNotificationHandlers() {
    document.querySelectorAll('.mark-read-btn').forEach(button => {
        button.addEventListener('click', async (e) => {
            e.stopPropagation();
            const notificationId = button.dataset.id;
            try {
                await fetchWithAuth(`${API_CONFIG.endpoints.notifications}/${notificationId}/read`, { method: 'POST' });
                const notification = dashboardState.notifications.find(n => n.id === notificationId);
                if (notification) {
                    notification.status = 'read';
                    updateNotificationPanel();
                    updateNotificationBadge(dashboardState.notifications.filter(n => n.status === 'unread').length);
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        });
    });
}

// Utility Functions
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

function showErrorMessage(message) {
    // Implement a function to show error messages to the user
    alert(message);
}

// Initialize Dashboard
document.addEventListener('DOMContentLoaded', () => {
    attachEventListeners();
    fetchTransactions();
    fetchNotifications();
    
    // Set up polling intervals
    const intervals = [
        { fn: fetchTransactions, ms: 30000 },  // 30 seconds
        { fn: fetchNotifications, ms: 60000 }    // 1 minute
    ];
    
    intervals.forEach(({fn, ms}) => setInterval(fn, ms));
});