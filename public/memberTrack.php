<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: publicLogin.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Track Order - PuffLab</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="checkout-css.css">
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
                    <a href="aboutus.html">About Us <i class='bx bxs-chevron-down'></i></a>
                    <ul class="dropdown-content">
                        <li><a href="ourteam.html">Our Team</a></li>
                        <li><a href="ourhistory.html">Our History</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="myacc.html">My Account <i class='bx bxs-chevron-down'></i></a>
                    <ul class="dropdown-content">
                        <li><a href="myacc.html">Dashboard</a></li> <!-- Link to My Account -->
                        <li><a href="myorders.html">Orders</a></li> <!-- Link to My Orders -->
                        <li><a href="publichome.html">Log Out</a></li> <!-- Link to LogOut -->
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
                <a href="favorites.html"><i class='bx bx-heart'></i></a>
                <a href="memberCart.php"><i class='bx bx-cart'></i></a>
            </div>
        </div>
    </nav>

<div class="container">
    <div class="tracking-page">
        <div class="tracking-header">
            <h1>Order Number #<span id="orderNumber">N/A</span></h1>
            <div class="estimated-time">
                Estimated delivery: <span id="estimatedTime">--:--</span>
            </div>
        </div>

        <div class="tracking-progress">
            <div class="progress-line"></div>
            <div class="progress-steps">
                <div class="step" data-status="confirmed">
                    <div class="step-icon"></div>
                    <span>Order Confirmed</span>
                </div>
                <div class="step" data-status="preparing">
                    <div class="step-icon"></div>
                    <span>Preparing</span>
                </div>
                <div class="step" data-status="ready">
                    <div class="step-icon"></div>
                    <span>Ready</span>
                </div>
            </div>
        </div>

        <div class="order-details">
            <h2>Order Details</h2>
            <hr class="divider">
            <div id="orderItems"></div>
            <div class="order-total">
                <span>Total:</span>
                <span id="orderTotal">RM 0.00</span>
            </div>
        </div>
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
                            <li><a href="tnc.html">Terms & Conditions</a></li>
                            <li><a href="privacy.html">Privacy Policy</a></li>
                            <li><a href="faq.html">FAQs</a></li>
                        </ul>
                    </div>
                    <div class="footer-col3">
                        <h4>STORE INFORMATION</h4>
                        <ul>
                            <li><a href="aboutus.html">About Us</a></li>
                            <li><a href="contactus.html">Contact Us</a></li>
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

<script src="memberTrack.js"></script>
</body>
</html>
