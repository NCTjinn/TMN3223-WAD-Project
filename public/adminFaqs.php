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
    <title>Admin FAQ Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="adminFaqs.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
            <a href="adminTransactions.php" class="sidebar-item" data-tooltip="Transaction Records">
                <i class="fas fa-file-invoice-dollar"></i>
                <span class="sidebar-label">Transactions</span>
            </a>
            <a href="adminFaqs.php" class="sidebar-item active" data-tooltip="FAQ Management">
                <i class="fas fa-question-circle"></i>
                <span class="sidebar-label">FAQs</span>
            </a>
        </aside>

    <!-- Main Content -->
    <main class="admin-content">
        <h1>FAQ Management</h1>
        <div class="faq-section">
            <div class="section-header">
                <h2 class="section-title">FAQ</h2>
                <button id="addFaqButton" class="add-faq-btn">
                    <i class="fas fa-plus"></i> Add FAQ
                </button>
            </div>
            <div id="faqContainer">
                <!-- FAQ items will be displayed here -->
            </div>
        </div>
    </main>

    <!-- Modal for Editing FAQ -->
    <div class="modal" id="faqModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Add/Edit FAQ</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="faqForm">
                    <div class="form-group">
                        <label for="faqQuestion">Question</label>
                        <input type="text" id="faqQuestion" name="faqQuestion" placeholder="Enter the question" required>
                    </div>
                    <div class="form-group">
                        <label for="faqAnswer">Answer</label>
                        <textarea id="faqAnswer" name="faqAnswer" placeholder="Enter the answer" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-actions">
                <button type="button" class="cancel-btn">Cancel</button>
                <button type="submit" class="save-btn" form="faqForm">Save Changes</button> <!-- Added 'form' attribute -->
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <footer class="admin-footer">
        Copyright &copy; 2024 PuffLab
    </footer>

    <!--script-->
    <script src="adminFaqs.js"></script>

</body>
</html>
