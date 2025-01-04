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
    <title>PuffLab Menu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="memberTNC.css">
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

    <h1>Terms and Conditions</h1>
    <div class="container">
        

        <p>Welcome to Puff Lab Samarahan! These terms and conditions outline the rules and regulations for the use of our web application. By accessing or using this application, you agree to comply with these terms. If you do not agree with any part of these terms, please do not use the application.</p>

        <h2>1. User Accounts</h2>
        <p>To access certain features, you may need to create an account. You are responsible for maintaining the confidentiality of your account information and for all activities under your account.</p>

        <h2>2. Services</h2>
        <p>Our web app provides users with the ability to browse products, place orders, and manage purchases. Availability of services may vary based on your location.</p>

        <h2>3. Payments</h2>
        <p>All payments made through the app are secure. By making a purchase, you agree to provide accurate payment information. Refunds, if applicable, are subject to our refund policy.</p>

        <h2>4. User Responsibilities</h2>
        <ul>
            <li>You agree not to misuse the application or disrupt its functionality.</li>
            <li>You agree to provide accurate and up-to-date information when using the application.</li>
            <li>Unauthorized use of the app may result in termination of your access.</li>
        </ul>

        <h2>5. Intellectual Property</h2>
        <p>All content, including text, images, and logos, are the property of Puff Lab Samarahan unless otherwise stated. You may not use this content without prior permission.</p>

        <h2>6. Limitation of Liability</h2>
        <p>Puff Lab Samarahan will not be held liable for any damages resulting from the use of the application, including but not limited to errors, downtime, or unauthorized access.</p>

        <h2>7. Privacy</h2>
        <p>Your privacy is important to us. Please refer to our Privacy Policy for details on how we collect, use, and protect your information.</p>

        <h2>8. Changes to Terms</h2>
        <p>We reserve the right to update these terms and conditions at any time. Changes will be effective immediately upon posting. Continued use of the app constitutes your acceptance of the updated terms.</p>

        <h2>9. Contact Us</h2>
        <p>If you have any questions about these terms, please contact us at <strong>013-3119957</strong>.</p>

        <p><strong>Last Updated:</strong> January 1, 2025</p>
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
</body>
</html>
