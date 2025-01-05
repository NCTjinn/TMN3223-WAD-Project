// API Handlers
let currentPage = 1;
const itemsPerPage = 10;
let filteredTransactions = [];

async function fetchTransactions() {
    console.log('Fetching transactions...'); // Debug log
    showLoadingState();
    try {
        const startDate = document.getElementById('date-start').value;
        const endDate = document.getElementById('date-end').value;
        const searchTerm = document.getElementById('search-input').value.toLowerCase();
        
        const params = new URLSearchParams();
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        params.append('limit', '1000');

        const url = `fetchAdminTransactions.php?${params.toString()}`;
        
        // Debug log directly without using API_CONFIG
        console.log('Fetching transactions from:', url);

        const response = await fetch(url);
        console.log('Response status:', response.status); // Debug log for response status
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Fetched transactions data:', data); // Debug log for fetched data

        if (data.status === 'error') {
            throw new Error(data.message || 'Failed to load transactions');
        }

        if (data.status === 'success' && data.data) {
            filteredTransactions = data.data;

            // Apply search filter if there is a search term
            if (searchTerm) {
                filteredTransactions = filteredTransactions.filter(transaction => 
                    Object.values(transaction).some(value => 
                        value?.toString().toLowerCase().includes(searchTerm)
                    )
                );
            }

            currentPage = 1; // Reset to the first page
            renderTable();
            hideErrorMessage();
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
    // Profile dropdown functionality
    const profileIcon = document.getElementById('profile-icon');
    const profileDropdown = document.getElementById('dropdown-menu');

    profileIcon.addEventListener('click', (e) => {
        e.stopPropagation();
        profileDropdown.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.user-dropdown')) {
            profileDropdown.classList.remove('active');
        }
    });

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