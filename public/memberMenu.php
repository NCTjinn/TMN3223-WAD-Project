<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: publicLogin.html");
    exit();
}

// Check user's role if needed
if ($_SESSION['role'] !== 'member') {
    header("Location: publicLogin.html");
    exit();
}
require_once 'menu_data.php';
$categories = getCategories();
$products = getProducts();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>PuffLab Menu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="publicMenuProductCSS.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <nav>
        <div class="navbar">
            <div class="logo">
                <a href="memberHome.php">
                    <img src="../assets/images/logo.png" alt="PuffLab Logo" style="height: 50px;">
                </a>
            </div>
            <ul class="links">
                <li><a href="memberHome.php">Home</a></li>
                <li class="dropdown">
                    <a href="memberMenu.php">Menu <i class='bx bxs-chevron-down'></i></a>
                    <ul class="dropdown-content">
                        <li><a href="memberMenu.php">Cream Puff</a></li>
                        <li><a href="memberMenu.php">Petit Gateux</a></li>
                        <li><a href="memberMenu.php">Shortcakes</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="memberAboutUs.php">About Us <i class='bx bxs-chevron-down'></i></a>
                    <ul class="dropdown-content">
                        <li><a href="memberAboutUs.php">Our Team</a></li>
                        <li><a href="memberAboutUs.php">Our History</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="memberAcc.php">My Account <i class='bx bxs-chevron-down'></i></a>
                    <ul class="dropdown-content">
                        <li><a href="memberAcc.php">Dashboard</a></li> <!-- Link to My Account -->
                        <li><a href="memberOrders.php">Orders</a></li> <!-- Link to My Orders -->
                        <li><a href="Logout.php">Logout</a></li> <!-- Link to LogOut -->
                    </ul>
                </li>
            </ul>
            <div class="icons">
                <div class="search-box">
                    <input type="text" class="search-txt" placeholder="Type to search...">
                    <a href="javascript:void(0);" class="search-btn">
                        <i class='bx bx-search'></i>
                    </a>
                </div>
                <a href="memberCart.php"><i class='bx bx-cart'></i></a>
            </div>
        </div>
    </nav>

    <!-- put content here-->
    <!-- Menu Page -->
    <!-- Text overlay on the image 
    <div class="header">
        <div class="image-slider">
            <img id="sliderImage" src="../assets/images/menu1.png" alt="Menu Item" class="slider-image">
            <h1 class="overlay-text">Menu</h1> \
        </div>
    </div> -->
    
    <div class="menu-container" id="menuPage">
        <div class="product-description">
            <p>Discover our handcrafted delights, perfect for every occasion!</p>
        </div>
        <!-- Replace your static filter buttons with dynamic categories -->
        <div class="filter-container">
            <button class="filter-btn active" data-category="ALL">ALL</button>
            <?php foreach($categories as $category): ?>
            <button class="filter-btn" data-category="<?php echo htmlspecialchars($category['category_id']); ?>">
            <?php echo htmlspecialchars($category['name']); ?>
            </button>
            <?php endforeach; ?>
        </div>
        <!-- Replace your static menu grid with dynamic products -->
        <div class="menu-grid" id="menuGrid">
        <?php foreach($products as $product): ?>
        <a href="memberProduct.php?id=<?php echo htmlspecialchars($product['product_id']); ?>" class="menu-item" data-category="<?php echo htmlspecialchars($product['category_id']); ?>">
        <div class="menu-item-image">
        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="menu-item-details">
        <h3 class="menu-item-title"><?php echo htmlspecialchars($product['name']); ?></h3>
        <p class="menu-item-price">RM <?php echo number_format($product['price'], 2); ?></p>
        </div>
        </a>
        <?php endforeach; ?>
    </div>
    </div>

    
    
    <footer class="footer">
        <div class="footer-left">
            <img src="../assets/images/logo.png" alt="PuffLab Logo" style="max-height: 100px; display: block; margin: auto;">
            <p>
                Made with love in Kuching, Sarawak, Puff Lab brings you premium Japanese cream puffs with a local twist! From our pop-up freezers to our cozy spot at Lakeview UNIMAS, we’re here to make every bite special. Whether you’re treating yourself or planning an event, our fresh, flavorful, and affordable pastries are crafted to delight. Join the Puff Lab family today—where gourmet desserts meet everyday joy!
            </p>
        </div>
        <div class="footer-right">
            <div class="footer-top">
                <div class="footer-col2">
                    <h4>CUSTOMER CARE</h4>
                    <ul>
                        <li><a href="memberTNC.php">Terms & Conditions</a></li>
                        <li><a href="memberPrivacyPolicy.php">Privacy Policy</a></li>
                        <li><a href="memberFaqs.php">FAQs</a></li>
                    </ul>
                </div>
                <div class="footer-col3">
                    <h4>STORE INFORMATION</h4>
                    <ul>
                        <li><a href="memberAboutUs.php">About Us</a></li>
                        <li><a href="memberContactUs.php">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="footer-col4">
                    <h4>FOLLOW US ON</h4>
                    <div class="social-icons">
                        <a href="https://www.facebook.com/PuffLabPLT/" target="_blank"><i class="fab fa-facebook"></i></a>
                        <a href="https://www.instagram.com/puff.lab/?hl=en" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.tiktok.com/@pufflabplt" target="_blank"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright">
            Copyright &copy; 2024 PuffLab
        </div>
    </footer>
    <script src="memberMenu.js"></script>
    <script src="script.js"></script>
    
</body>
</html>