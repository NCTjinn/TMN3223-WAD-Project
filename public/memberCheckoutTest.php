<?php
// Database connection
$servername = "sql112.infinityfree.com";
$username = "if0_37979402";
$password = "tmn3223ncnhcds";
$dbname = "if0_37979402_pufflab";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
session_start();
$userId = $_SESSION['user_id'] ?? 0; // Assuming session holds user ID
if ($userId === 0) {
    header('Location: publicLogin.html'); // Redirect to login if not logged in
    exit;
}

// Fetch user's address
$address_query = "SELECT address_line_1, city, state, postcode FROM Addresses WHERE user_id = ? AND is_default = 1 LIMIT 1";
$address_stmt = $conn->prepare($address_query);
$address_stmt->bind_param("i", $userId);
$address_stmt->execute();
$address_result = $address_stmt->get_result();
$user_address = $address_result->fetch_assoc();

// Fetch cart items
$cart_query = "SELECT Products.name, Products.price, Cart.quantity FROM Cart JOIN Products ON Cart.product_id = Products.product_id WHERE Cart.user_id = ?";
$cart_stmt = $conn->prepare($cart_query);
$cart_stmt->bind_param("i", $userId);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();

$cart_items = [];
$total_price = 0;
while ($row = $cart_result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_price += $row['price'] * $row['quantity'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
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
    
    <div class="container" style="max-width: 1400px; padding: 2rem 4rem;">
        <h1 style="font-size: 2.5rem; margin-bottom: 2rem; color: #1F1F1F;">Checkout</h1>

        <div class="checkout-grid" style="grid-template-columns: 2fr 1fr; gap: 2rem;">
            <div class="checkout-form">
                <!-- Delivery Address Section -->
                <div class="order-details" style="margin-bottom: 2rem;">
                    <h2><i class='bx bx-map'></i> Delivery Address</h2>
                    <div class="divider"></div>
                    <div class="address-input-container">
                        <div class="autofill-section">
                            <p><?php echo htmlspecialchars($user_address['address_line_1'] . ", " . $user_address['city'] . ", " . $user_address['state'] . " - " . $user_address['postcode']); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods Section -->
                <div class="order-details">
                    <h2><i class='bx bx-credit-card'></i> Payment Method</h2>
                    <div class="divider"></div>
                    <form class="payment-options" method="post" action="memberProcessPayment.php" id="paymentForm">
                        <div class="delivery-options" style="grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                            <div class="option-card" onclick="document.getElementById('payment_card').checked = true">
                                <input type="radio" id="payment_card" name="payment_method" value="card" required>
                                <i class='bx bx-credit-card-front'></i>
                                <h3>Credit/Debit Card</h3>
                            </div>
                            <div class="option-card" onclick="document.getElementById('payment_banking').checked = true">
                                <input type="radio" id="payment_banking" name="payment_method" value="banking">
                                <i class='bx bx-bank'></i>
                                <h3>Online Banking</h3>
                            </div>
                            <div class="option-card" onclick="document.getElementById('payment_tng').checked = true">
                                <input type="radio" id="payment_tng" name="payment_method" value="touchngo">
                                <i class='bx bx-wallet'></i>
                                <h3>Touch N' Go</h3>
                            </div>
                            <div class="option-card" onclick="document.getElementById('payment_grabpay').checked = true">
                                <input type="radio" id="payment_grabpay" name="payment_method" value="grabpay">
                                <i class='bx bx-money'></i>
                                <h3>GrabPay</h3>
                            </div>
                        </div>
                        <button type="submit" class="continue-button">Proceed to Payment</button>
                    </form>
                </div>
            </div>

            <!-- Order Summary Section -->
            <div class="order-details" style="height: auto;">
                <h2><i class='bx bx-cart'></i> Order Summary</h2>
                <div class="divider"></div>
                <div class="summary-items" id="orderItems">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="order-item">
                            <span><?php echo htmlspecialchars($item['name']); ?> x<?php echo htmlspecialchars($item['quantity']); ?></span>
                            <span>RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="divider"></div>
                <div class="summary-total" style="font-size: 1.2rem;">
                    <span>Total Amount:</span>
                    <span>RM <?php echo number_format($total_price, 2); ?></span>
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
    <script src="memberCheckout.js"></script>
</body>
</html>