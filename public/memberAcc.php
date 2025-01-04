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

// Your protected content here
echo "Welcome to the member section!";
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>PuffLab</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css">
    <link rel="stylesheet" href="memberAcc.css">
    <script src="script.js"></script>
    <script src="memberAcc.js"></script>
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
                    <a href="memberMenu.html">Menu <i class='bx bxs-chevron-down'></i></a>
                    <ul class="dropdown-content">
                        <li><a href="memberMenu.html">Cream Puff</a></li>
                        <li><a href="memberMenu.html">Petit Gateux</a></li>
                        <li><a href="memberMenu.html">Shortcakes</a></li>
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
                    <a href="memberAcc.php">My Account <i class='bx bxs-chevron-down'></i></a>
                    <ul class="dropdown-content">
                        <li><a href="memberAcc.php">Dashboard</a></li> <!-- Link to My Account -->
                        <li><a href="memberOrders.php">Orders</a></li> <!-- Link to My Orders -->
                        <li><a href="Logout.php">Log Out</a></li> <!-- Link to LogOut -->
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

    <!-- My Account Page -->
    <div class="container">
        <!-- Page Title -->
        <div class="page-title">
            <h1>My Account</h1>
            <p>Welcome to your account dashboard! Here you can manage your orders, saved addresses, and personal details.</p>
        </div>

        <!-- Content Section -->
        <div class="content-section">
            <!-- Options Selector (Left Box) -->
            <div class="left-box">
                <ul>
                    <li><a href="memberAcc.php" class="active">Dashboard</a></li> <!-- Link to My Account -->
                    <li><a href="memberOrders.php">Orders</a></li> <!-- Link to My Orders -->
                    <li><a href="memberAddresses.php">Addresses</a></li> <!-- Link to My Rewards -->
                    <li><a href="memberAccount.php">Account Details</a></li> <!-- Link to My Account Details -->
                    <li><a href="Logout.php">Log Out</a></li> <!-- Link to LogOut -->
                </ul>
            </div>

            <!-- Dashboard Details (Right Box) -->
            <div class="right-box">
                <h2>Dashboard</h2>
                <p>This is your account dashboard. Use the navigation on the left to manage your orders, addresses, and account settings.</p>
                
                <h3>Quick Overview:</h3>
                <div class="overview-grid">
                    <div class="overview-item" id="orders-overview">
                        <h4>Total Orders</h4>
                        <p>Loading...</p>
                    </div>
                    <div class="overview-item" id="addresses-overview">
                        <h4>Saved Addresses</h4>
                        <p>Loading...</p>
                    </div>
                    <div class="overview-item" id="status-overview">
                        <h4>Account Status</h4>
                        <p>Loading...</p>
                    </div>
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
</body>
</html>
