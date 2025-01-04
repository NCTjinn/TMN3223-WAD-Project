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
    <!-- Checkout Page -->
    <div class="container">
        <h1>Checkout</h1>
        
        <div class="checkout-grid">
            <div class="checkout-form">
            <div class="delivery-address">
            <h2>Delivery Address</h2>
            <div class="autofill-section">
            <label>
            <input type="checkbox" id="autofill-address" onchange="checkout.loadUserDetails()">
                Use my registered address
            </label>
            </div>
            <form class="delivery-form">
            <div class="form-row">
            <input type="text" name="firstName" class="form-input" placeholder="First Name*" required>
            <input type="text" name="lastName" class="form-input" placeholder="Last Name*" required>
            </div>
            <input type="text" name="addressLine1" class="form-input" placeholder="Address Line 1*" required>
            <div class="form-row">
            <input type="text" name="city" class="form-input" placeholder="City*" required>
            <input type="text" name="postcode" class="form-input" placeholder="Postcode*" required>
            <input type="text" name="state" class="form-input" placeholder="State*" required>
            </div>
            <input type="tel" name="phone" class="form-input" placeholder="Phone Number*" required>
            </form>
        </div>
    
                <div class="payment-methods">
                    <h2>Payment Method</h2>
                    <div class="payment-options">
                        <label class="payment-option">
                            <input type="radio" name="payment" value="card">
                            <span>Credit/Debit Card</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment" value="banking">
                            <span>Online Banking</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment" value="touchngo">
                            <span>Touch N' Go</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment" value="grabpay">
                            <span>GrabPay</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment" value="cash">
                            <span>Cash</span>
                        </label>
                    </div>
                    <!-- Replace the existing continue-button -->
                    <button class="continue-button" onclick="validateCheckout()">Continue</button>
                </div>
            </div>
    
            <div class="order-summary">
            <h2>Order Summary</h2>
                <div class="summary-items" id="orderItems">
                <!-- Items will be populated by JavaScript -->
                </div>
                <div class="summary-details">
                <!-- Totals will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function validateCheckout(event) {
            event.preventDefault(); // Prevent default form submission

            // Retrieve form values
            const firstName = document.getElementById("firstName").value.trim();
            const lastName = document.getElementById("lastName").value.trim();
            const addressLine1 = document.getElementById("addressLine1").value.trim();
            const city = document.getElementById("city").value.trim();
            const postcode = document.getElementById("postcode").value.trim();
            const state = document.getElementById("state").value.trim();
            const phone = document.getElementById("phone").value.trim();

            // Check if a payment method is selected
            const paymentMethods = document.getElementsByName("payment");
            let paymentSelected = false;
            for (const method of paymentMethods) {
                if (method.checked) {
                    paymentSelected = true;
                    break;
                }
            }

            // Check if all required fields are filled
            //if (!firstName || !lastName || !addressLine1 || !city || !postcode || !state || !phone || !paymentSelected) {
            //    alert("Please fill in all required fields and select a payment method.");
            //    return;
            //}

            // Additional checks for Credit/Debit Card inputs if selected
            const cardInputs = document.querySelector('input[name="payment"][value="card"]:checked');
            if (cardInputs) {
                const cardNumber = document.getElementById("cardNumber").value.trim();
                const cardExpiry = document.getElementById("cardExpiry").value.trim();
                const cardCVC = document.getElementById("cardCVC").value.trim();

                if (!cardNumber || !cardExpiry || !cardCVC) {
                    alert("Please fill in all required Credit/Debit Card details.");
                    return;
                }
            }

            // If validation passes, proceed to the next page
            alert("Order placed successfully!");
            window.location.href = "track-order.html";
        }
    </script>
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
        <script src="memberCart.js"></script>
        <script src="memberCheckout.js"></script>
        
</body>
</html>
