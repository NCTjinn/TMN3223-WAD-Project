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
?>

<!-- home.html (Registered User Home Page) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PuffLab Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="memberHome.css">
    <script src="script.js"></script>
    <script src="memberHome.js"></script>
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
                        <li><a href="memberMenu.php">Puffs</a></li>
                        <li><a href="memberMenu.php">Cakes</a></li>
                        <li><a href="memberMenu.php">Beverages</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="memberAboutUs.html">About Us <i class='bx bxs-chevron-down'></i></a>
                    <ul class="dropdown-content">
                        <li><a href="memberAboutUs.html">Our Team</a></li>
                        <li><a href="memberAboutUs.html">Our History</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="memberAcc.html">My Account <i class='bx bxs-chevron-down'></i></a>
                    <ul class="dropdown-content">
                        <li><a href="memberAcc.html">Dashboard</a></li> <!-- Link to My Account -->
                        <li><a href="memberOrders.html">Orders</a></li> <!-- Link to My Orders -->
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

    <div class="welcome-header">
        <h1>WELCOME BACK, (USER'S NAME)!</h1>
    </div>

    <div class="hero">
        <h1>ONE BITE IS NEVER ENOUGH</h1>
    </div>

    <div class="buy-once-more">
        <h2>BUY ONCE MORE</h2>
        <div class="previous-purchases">
            <div class="product-card"></div>
            <div class="product-card"></div>
        </div>
    </div>

    <div class="products-section">
        <div class="tab-buttons">
            <button class="tab-button active" id="bestsellers-btn">Monthly Bestsellers</button>
            <button class="tab-button" id="arrivals-btn">New Arrivals</button>
        </div>

        <div class="products-grid" id="products-grid">
            <div class="product-card"></div>
            <div class="product-card"></div>
            <div class="product-card"></div>
        </div>
    </div>

    <section class="reviews">
        <h2>Reviews</h2>
        <div class="review-container">
            <button class="nav-btn prev-btn">&lt;</button>
            <div class="review-cards">
                <div class="review-card"></div>
                <div class="review-card"></div>
                <div class="review-card"></div>
            </div>
            <button class="nav-btn next-btn">&gt;</button>
        </div>
    </section>

    <section class="community-section">
        <h2>Join our community!</h2>
        <div class="divider"></div>
    </section>

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
                        <li><a href="memberTNC.html">Terms & Conditions</a></li>
                        <li><a href="memberPrivacyPolicy.html">Privacy Policy</a></li>
                        <li><a href="memberFaqs.html">FAQs</a></li>
                    </ul>
                </div>
                <div class="footer-col3">
                    <h4>STORE INFORMATION</h4>
                    <ul>
                        <li><a href="memberAboutUs.html">About Us</a></li>
                        <li><a href="memberContactUs.html">Contact Us</a></li>
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


</body>
</html>
