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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="adminProducts.css">
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
            <a href="adminproducts.php" class="sidebar-item active" data-tooltip="Product Management">
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

        <main class="admin-content">
            <div class="product-management-header">
                <h1>Product Management</h1>
                <div class="button-group">
                    <button class="add-product-btn" id="addProductBtn">Add New Product</button>
                </div>
            </div>
            
            <!--
            <div class="filter-search">
                <select id="filterDropdown" class="styled-dropdown">
                    <option value="all">All Products</option>
                    <option value="inStock">In Stock</option>
                    <option value="outOfStock">Out of Stock</option>
                </select>
                <div class="search-container">
                    <input type="text" id="searchBar" placeholder="Search products...">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </div> -->
            
        
            <div id="confirmation-message" class="hidden"></div>
        
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    <!-- Rows dynamically generated here -->
                </tbody>
            </table>
        
            <button class="edit-btn" data-id="PRODUCT_ID">Edit</button>
            <button class="delete-btn" data-id="PRODUCT_ID">Delete</button>

            <!-- Add/Edit Product Modal -->
            <div class="modal" id="productModal">
            <div class="modal-content">
            <h2 id="modalTitle">Add New Product</h2>
            <form id="productForm">
            <!-- Other form inputs -->
            <form id="productForm">
    <div class="form-group">
        <label for="productName">Product Name:</label>
        <input type="text" id="productName" name="productName" required>
    </div>
    <div class="form-group">
        <label for="productPrice">Price:</label>
        <input type="number" id="productPrice" name="productPrice" step="0.01" required>
    </div>
    <div class="form-group">
        <label for="productStock">Stock Quantity:</label>
        <input type="number" id="productStock" name="productStock" required>
    </div>
    <div class="form-group">
        <label for="productDescription">Description:</label>
        <textarea id="productDescription" name="productDescription" required></textarea>
    </div>
            <input type="hidden" id="productId"> <!-- For editing -->
            <div class="form-group">
                <label for="productImage">Product Image:</label>
                <div class="upload-area" id="uploadArea">
                    <p>Drag & drop an image here or <span>Browse Files</span></p>
                    <input type="file" id="productImage" accept="image/*" required hidden>
                </div>
                <img id="imagePreview" src="" alt="Preview" class="hidden">
            </div>

            <!-- Other form fields -->
            <div class="modal-buttons">
                <button type="button" id="cancelModal">Cancel</button>
                <button type="submit" id="saveProduct">Save Product</button>
            </div>
            </form>
            </div>
            </div>

        
        </main>
    </div>

    <!-- Footer Section -->
    <footer class="admin-footer">
        Copyright &copy; 2024 PuffLab
    </footer>

    <script src="adminProducts.js"></script>
</body>
</html>
