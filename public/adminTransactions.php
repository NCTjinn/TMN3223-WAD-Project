<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: publicLogin.html");
    exit();
}

// Check user's role if needed
if ($_SESSION['role'] !== 'admin') {
    echo "Access denied";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="adminTransactions.css">
    <script src="adminTransactions.js"></script>
</head>
<body>
    <!-- Header Section -->
    <header class="admin-header">
        <div class="logo">
            <img src="../assets/images/logo.png" alt="PuffLab Logo">
        </div>
        <div class="utilities">
            <div class="user-dropdown">
                <i class="fas fa-user-circle" id="profile-icon"></i>
                <div class="dropdown-menu" id="dropdown-menu">
                    <a href="Logout.php">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Layout -->
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <a href="adminDashboard.php" class="sidebar-item" data-tooltip="Admin Dashboard">
                <i class="fas fa-tachometer-alt"></i>
                <span class="sidebar-label">Dashboard</span>
            </a>
            <a href="adminMembers.php" class="sidebar-item" data-tooltip="Member Management">
                <i class="fas fa-users"></i>
                <span class="sidebar-label">Members</span>
            </a>
            <a href="adminProducts.php" class="sidebar-item" data-tooltip="Product Management">
                <i class="fas fa-boxes"></i>
                <span class="sidebar-label">Products</span>
            </a>
            <a href="adminTransactions.php" class="sidebar-item active" data-tooltip="Transaction Records">
                <i class="fas fa-file-invoice-dollar"></i>
                <span class="sidebar-label">Transactions</span>
            </a>
            <a href="adminFaqs.php" class="sidebar-item" data-tooltip="FAQ Management">
                <i class="fas fa-question-circle"></i>
                <span class="sidebar-label">FAQs</span>
            </a>
        </aside>

        <!-- Main Content here -->
        <main class="admin-content">
            <!-- Transaction Filters -->
            <section class="filters-section">
                <div class="filter-item">
                    <label for="date-start">Start Date:</label>
                    <input type="date" id="date-start">
                </div>
                <div class="filter-item">
                    <label for="date-end">End Date:</label>
                    <input type="date" id="date-end">
                </div>
                <button class="filter-btn" id="apply-filters">Apply Filters</button>
                <div class="search-bar">
                    <input type="text" id="search-input" placeholder="Search by keyword...">
                    <button id="search-btn"><i class="fas fa-search"></i></button>
                </div>
            </section>
        
            <!-- Transaction Table -->
            <div id="error-message" class="alert alert-danger" style="display: none;"></div>
            <section class="transaction-table-section">
                <table class="transaction-table">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>User ID</th>
                            <th>Date & Time</th>
                            <th>Total Amount</th>
                            <th>Delivery Fee</th>
                            <th>Tax Amount</th>
                            <th>Payment Status</th>
                            <th>Shipping Method</th>
                        </tr>
                    </thead>
                    <tbody id="transaction-table-body">
                        <!-- Data will be dynamically inserted here -->
                    </tbody>
                </table>
            </section>
        
            <!-- Pagination -->
            <section class="pagination-section">
                <button class="pagination-btn" id="prev-page" disabled>&laquo; Previous</button>
                <span id="current-page">Page 1</span>
                <button class="pagination-btn" id="next-page">Next &raquo;</button>
            </section>
        </main>
        
        
    </div>

    <!-- Footer Section -->
    <footer class="admin-footer">
        Copyright &copy; 2024 PuffLab
    </footer>
</body>
</html>
