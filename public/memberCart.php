<?php
session_start();

if (isset($_GET['error'])) {
    echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
}
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
    <link rel="stylesheet" href="checkout-css.css">
    <link rel="stylesheet" href="cart-styles.css">
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
    <!-- Cart Page -->
    <div class="container">
        <div class="cart-header">
        <h1>Cart</h1>
        <u><a href="memberMenu.php" class="continue-browsing ">Continue browsing</a></u>
    </div>

    <div class="cart-table">
        <div class="cart-table-header">
            <span>Product</span>
            <span>Price</span>
            <span>Quantity</span>
            <span>Subtotal</span>
        </div>

        <div id="cartItems">
            <!-- Cart items will be populated by JavaScript -->
        </div>

        <div class="cart-total">
            Total: <span id="cartTotal">RM 0.00</span>
        </div>
    </div>
    
    <div class="delivery-options">
    <div class="option-card" data-option="pickup">
        <i class="fas fa-store"></i>
        <h3>Pick Up</h3>
        <div class="time-inputs hidden">
            <input type="date" class="date-input">
            <select class="time-input">
                <option value="">Select time</option>
                <option value="10:00">10:00 AM</option>
                <option value="11:00">11:00 AM</option>
                <option value="12:00">12:00 PM</option>
                <option value="1:00">1:00 PM</option>
                <option value="2:00">2:00 PM</option>
                <option value="3:00">3:00 PM</option>
                <option value="4:00">4:00 PM</option>
                <option value="5:00">5:00 PM</option>
            </select>
        </div>
    </div>
    <div class="option-card" data-option="delivery">
        <i class="fas fa-truck"></i>
        <h3>Delivery</h3>
        <div class="time-inputs hidden">
            <input type="date" class="date-input">
            <select class="time-input">
                <option value="">Select time</option>
                <option value="10:00">10:00 AM</option>
                <option value="11:00">11:00 AM</option>
                <option value="12:00">12:00 PM</option>
                <option value="1:00">1:00 PM</option>
                <option value="2:00">2:00 PM</option>
                <option value="3:00">3:00 PM</option>
                <option value="4:00">4:00 PM</option>
                <option value="5:00">5:00 PM</option>
            </select>
        </div>
    </div>
</div>


        <button id="checkoutButton" class="checkout-button">Check Out</button>
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

    <script src="script.js"></script>
    <script src="memberCart.js"></script>
</body>
</html>
