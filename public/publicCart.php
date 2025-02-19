<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>PuffLab Menu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="cart-styles.css">
    <link rel="stylesheet" href="checkout-css.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <nav>
        <div class="navbar">
            <div class="logo">
                <a href="publicHome.html">
                    <img src="../assets/images/logo.png" alt="PuffLab Logo" style="height: 50px;">
                </a>
            </div>
            <ul class="links">
                <li><a href="publicHome.html">Home</a></li>
                <li class="dropdown">
                    <a href="publicMenu.php">Menu <i class='bx bxs-chevron-down'></i></a>
                    <ul class="dropdown-content">
                        <li><a href="publicMenu.php">Puffs</a></li>
                        <li><a href="publicMenu.php">Cakes</a></li>
                        <li><a href="publicMenu.php">Beverages</a></li>
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
                        <li><a href="publicLogin.html">Log In</a></li> <!-- Link to LogOut -->
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
                <a href="publicCart.php"><i class='bx bx-cart'></i></a>
            </div>
        </div>
    </nav>
    <!-- Cart Page -->
    <div class="container">
        <div class="cart-header">
        <h1>Cart</h1>
        <u><a href="publicMenu.php" class="continue-browsing ">Continue browsing</a></u>
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

        <button onclick="cartManager.clearCart()" class="clear-cart-button">Clear Cart</button>

        <div class="cart-total">
            Total: <span id="cartTotal">RM 0.00</span>
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

    <script src="script.js"></script>
    <script src="publicCart.js"></script>
    <script src="publicCheckout.js"></script>
    <script type="module">
    import cartManager from 'publicCart.js';
    window.cartManager = cartManager; // Make it globally available for onclick handlers
    </script>
    
</body>
</html>