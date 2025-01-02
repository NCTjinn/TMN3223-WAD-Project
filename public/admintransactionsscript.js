// Constants and Configuration
const baseUrl = window.location.hostname === 'localhost' 
    ? 'http://localhost/TMN3223-WAD-Project' 
    : 'https://your-production-domain.com';

const API_CONFIG = {
    // Base URL - change this based on your environment
    baseUrl: baseUrl,
    debug: true, // Enable debug mode
    
    // API endpoints
    endpoints: {
        dashboardStats: `${baseUrl}/api/admin/dashboard`,
        users: `${baseUrl}/api/admin/users`,
        products: `${baseUrl}/api/admin/products`,
        transactions: `${baseUrl}/api/admin/transactions`
    },
    
    // Request timeout in milliseconds
    timeout: 5000
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
        if (API_CONFIG.debug) console.log(`Fetching from: ${url}`);

        const response = await fetch(url, { ...defaultOptions, ...options });
        const responseText = await response.text();

        if (!response.ok) {
            if (API_CONFIG.debug) console.log('Error response text:', responseText);
            
            // Try to parse error response
            let errorMessage;
            try {
                const errorData = JSON.parse(responseText);
                errorMessage = errorData.error || errorData.message || 'Unknown error';
            } catch (e) {
                errorMessage = responseText || 'Server error';
            }

            throw new Error(errorMessage);
        }

        // Parse successful response
        const data = JSON.parse(responseText);
        if (API_CONFIG.debug) console.log('Fetched data:', data);
        return data;
    } catch (error) {
        console.error('Error fetching data:', error);
        throw error;
    } finally {
        clearTimeout(timeoutId);
    }
}

// API Handlers
let currentPage = 1;
const itemsPerPage = 10;
let filteredTransactions = [];

async function fetchTransactions() {
    showLoadingState();
    try {
        const startDate = document.getElementById('date-start').value;
        const endDate = document.getElementById('date-end').value;
        const searchTerm = document.getElementById('search-input').value.toLowerCase();
        
        const params = new URLSearchParams();
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        params.append('limit', '1000');

        const url = `${API_CONFIG.endpoints.transactions}?${params.toString()}`;
        
        if (API_CONFIG.debug) console.log('Fetching transactions from:', url);

        const data = await fetchWithAuth(url);
        if (data.status === 'success' && Array.isArray(data.data)) {
            filteredTransactions = data.data;
            if (searchTerm) {
                filteredTransactions = filteredTransactions.filter(transaction => 
                    Object.values(transaction).some(value => 
                        value?.toString().toLowerCase().includes(searchTerm)
                    )
                );
            }
            currentPage = 1;
            renderTable();
            hideErrorMessage();
        } else {
            throw new Error(data.error || 'Invalid response format');
        }
    } catch (error) {
        console.error('Error fetching transactions:', error);
        showErrorMessage(`Failed to load transactions: ${error.message}`);
        filteredTransactions = [];
        renderTable();
    } finally {
        hideLoadingState();
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
            <td>${transaction.transaction_id}</td>
            <td>${transaction.user_id}</td>
            <td>${formatDateTime(transaction.transaction_date)}</td>
            <td>${formatCurrency(transaction.total_amount)}</td>
            <td>${formatCurrency(transaction.delivery_fee)}</td>
            <td>${formatCurrency(transaction.tax_amount)}</td>
            <td>${transaction.payment_status}</td>
            <td>${transaction.shipping_method}</td>
        `;
        tableBody.appendChild(row);
    });

    document.getElementById('current-page').textContent = `Page ${currentPage}`;
    document.getElementById('prev-page').disabled = currentPage === 1;
    document.getElementById('next-page').disabled = end >= filteredTransactions.length;
}

// Event Handlers
function attachEventListeners() {
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

// Utility Functions
function formatDateTime(dateString) {
    return new Date(dateString).toLocaleString();
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

// Add loading state functions
function showLoadingState() {
    const tableBody = document.getElementById('transaction-table-body');
    tableBody.innerHTML = '<tr><td colspan="8" class="text-center">Loading...</td></tr>';
}

function hideLoadingState() {
    // Will be cleared by renderTable()
}

function showErrorMessage(message) {
    const errorDiv = document.getElementById('error-message');
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    } else {
        console.error('Error:', message);
    }
}

function hideErrorMessage() {
    const errorDiv = document.getElementById('error-message');
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
}

// Initialize Dashboard
document.addEventListener('DOMContentLoaded', () => {
    attachEventListeners();
    fetchTransactions();
});