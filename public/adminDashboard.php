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
    <link rel="stylesheet" href="adminDashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="adminDashboard.js" defer></script>
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
            <a href="adminDashboard.php" class="sidebar-item active" data-tooltip="Admin Dashboard">
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
            <div class="dashboard-header">
                <h1>Admin Dashboard</h1>
                <p class="last-updated">Last updated: <span id="lastUpdated"></span></p>
                <div id="error-message" class="error-message"></div>
            </div>
            
            <div class="dashboard-grid">
                <!-- Card 1: Order Statistics -->
                <div class="dashboard-item pie-chart">
                    <h3>Order Distribution</h3>
                    <canvas id="pieChart"></canvas>
                    <div class="pie-chart-summary">
                        <div class="stat-row">
                            <span class="stat-label">Dine-In:</span>
                            <span id="dineInPercentage" class="stat-value"></span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Takeaway:</span>
                            <span id="takeawayPercentage" class="stat-value"></span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Delivery:</span>
                            <span id="deliveryPercentage" class="stat-value"></span>
                        </div>
                    </div>
                </div>
        
                <!-- Card 2: Revenue Statistics -->
                <div class="dashboard-item bar-chart">
                    <h3>Category Performance</h3>
                    <canvas id="barChart"></canvas>
                    <div class="bar-chart-summary">
                        <div class="revenue-stats">
                            <div class="stat-row">
                                <span class="stat-label">Total Revenue:</span>
                                <span id="totalRevenue" class="stat-value"></span>
                            </div>
                            <div class="stat-row">
                                <span class="stat-label">Weekly Revenue:</span>
                                <span id="weeklyRevenue" class="stat-value"></span>
                            </div>
                            <div class="stat-row">
                                <span class="stat-label">Monthly Revenue:</span>
                                <span id="monthlyRevenue" class="stat-value"></span>
                            </div>
                        </div>
                        <div class="category-stats">
                            <div class="stat-row">
                                <i class="fas fa-crown"></i>
                                <span class="stat-label">Top Category:</span>
                                <span id="topCategory" class="stat-value"></span>
                            </div>
                            <div class="stat-row">
                                <span class="stat-label">Category Revenue:</span>
                                <span id="topCategoryRevenue" class="stat-value"></span>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Card 3: Product Analytics -->
                <div class="dashboard-item product-performance">
                    <h3>Product Analytics</h3>
                    <canvas id="productChart"></canvas>
                    <div class="product-summary">
                        <div class="top-products">
                            <div class="stat-row">
                                <i class="fas fa-star"></i>
                                <span class="stat-label">Best Seller:</span>
                                <span id="topProduct" class="stat-value"></span>
                            </div>
                            <div class="stat-row">
                                <span class="stat-label">Units Sold:</span>
                                <span id="topProductUnits" class="stat-value"></span>
                            </div>
                            <div class="stat-row">
                                <span class="stat-label">Revenue:</span>
                                <span id="topProductRevenue" class="stat-value"></span>
                            </div>
                        </div>
                        <div class="customer-stats">
                            <div class="stat-row">
                                <i class="fas fa-users"></i>
                                <span class="stat-label">Total Customers:</span>
                                <span id="totalCustomers" class="stat-value"></span>
                            </div>
                            <div class="stat-row">
                                <span class="stat-label">Average Order Value:</span>
                                <span id="averageOrderValue" class="stat-value"></span>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Card 4: Sales Trends -->
                <div class="dashboard-item line-chart">
                    <h3>Sales Trends</h3>
                    <div class="sales-options">
                        <button class="sales-btn active" data-period="daily">Daily</button>
                        <button class="sales-btn" data-period="weekly">Weekly</button>
                        <button class="sales-btn" data-period="monthly">Monthly</button>
                    </div>
                    <canvas id="lineChart"></canvas>
                    <div class="chart-summary">
                        <div class="trend-stats">
                            <div class="stat-row">
                                <span class="stat-label">Period Revenue:</span>
                                <span id="periodRevenue" class="stat-value"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Footer Section -->
    <footer class="admin-footer">
        <p>Copyright &copy; 2024 PuffLab</p>
    </footer>
</body>
</html>