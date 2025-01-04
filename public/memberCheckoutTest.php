<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "PuffLab";

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
    
    <div class="container">
        <h1>Checkout</h1>
        <div class="checkout-grid">
            <div class="checkout-form">
                <div class="delivery-address">
                    <h2>Delivery Address</h2>
                    <div class="autofill-section">
                        <p><?php echo htmlspecialchars($user_address['address_line_1'] . ", " . $user_address['city'] . ", " . $user_address['state'] . " - " . $user_address['postcode']); ?></p>
                    </div>
                    <!-- Add additional delivery info modification if necessary -->
                </div>
                <div class="payment-methods">
                <h2>Payment Method</h2>
                <form class="payment-options" method="post" action="memberProcessPayment.php"> <!-- Adjust the action attribute as necessary -->
                    <select name="payment_method" id="payment_method" class="payment-selector">
                        <option value="card">Credit/Debit Card</option>
                        <option value="banking">Online Banking</option>
                        <option value="touchngo">Touch N' Go</option>
                        <option value="grabpay">GrabPay</option>
                        <option value="cash">Cash</option>
                    </select>
                    <button class="continue-button" type="submit">Continue</button>
                </form>
            </div>

            <div class="order-summary">
                <h2>Order Summary</h2>
                <div class="summary-items" id="orderItems">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="item">
                            <span><?php echo htmlspecialchars($item['name']); ?> x<?php echo htmlspecialchars($item['quantity']); ?></span>
                            <span>RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="summary-details">
                    <p>Total: RM <?php echo number_format($total_price, 2); ?></p>
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