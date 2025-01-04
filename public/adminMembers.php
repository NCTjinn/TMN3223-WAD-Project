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

// Your protected content here
echo "Welcome to the admin section!";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="adminMembers.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js" integrity="sha512-ElRFoEQdI5Ht6kZvyzXhYG9NqjtkmlkfYk0wr6wHxU9JEHakS7UJZNeml5ALk+8IKlU6jDgMabC3vkumRokgJA==" crossorigin="anonymous" referrer-policy="no-referrer"></script>
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
            <a href="adminMembers.php" class="sidebar-item active" data-tooltip="Member Management">
                <i class="fas fa-users"></i>
                <span class="sidebar-label">Members</span>
            </a>
            <a href="adminProducts.html" class="sidebar-item" data-tooltip="Product Management">
                <i class="fas fa-boxes"></i>
                <span class="sidebar-label">Products</span>
            </a>
            <a href="adminTransactions.php" class="sidebar-item" data-tooltip="Transaction Records">
                <i class="fas fa-file-invoice-dollar"></i>
                <span class="sidebar-label">Transactions</span>
            </a>
            <a href="adminFaqs.php" class="sidebar-item" data-tooltip="FAQ Management">
                <i class="fas fa-question-circle"></i>
                <span class="sidebar-label">FAQs</span>
            </a>
        </aside>

        <!-- Main Content -->
        <main class="admin-content">
            <h1>Member Management</h1>
            <!-- Filters -->
             <!-- Add this somewhere in your main content -->
            <div id="error-message" class="error-message"></div>
            <div class="member-management-actions">
                <input type="text" id="searchBar" placeholder="Search by Username or ID" class="search-bar" />
                <div class="filter-actions">
                    <button class="dropdown-btn">Filter</button>
                    <div id="filterDropdown" class="dropdown-menu">
                        <button class="filter-option" data-filter="top-spenders">Top Spenders (High to Low)</button>
                        <button class="filter-option" data-filter="recently-active">Recently Active (Last 30 Days)</button>
                        <button class="filter-option" data-filter="inactive">Inactive Members (6+ Months)</button>
                        <button class="filter-option" data-filter="reset">Reset Filters</button>
                    </div>
                    
                </div>
            </div>
            
            
            
            <table id="memberTable">
                <thead>
                    <tr>
                        <th>Member ID</th>
                        <th>Username</th>
                        <th>Account Creation Date</th>
                        <th>Total Spent</th>
                        <th>Last Transaction</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Populated via JavaScript -->
                </tbody>
            </table>
            
            <div class="pagination">
                <button id="prevPageBtn" class="pagination-btn">Previous</button>
                <span id="pageNumbers">Page 1</span>
                <button id="nextPageBtn" class="pagination-btn">Next</button>
            </div>
            
        </main>
    </div>

    <!-- Footer Section -->
    <footer class="admin-footer">
        Copyright &copy; 2024 PuffLab
    </footer>

    <script src="adminMembers.js"></script>
</body>
</html>
