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

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Privacy Policy - PuffLab</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css">
    <link rel="stylesheet" href="memberPrivacyPolicy.css">
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

    
    <main>
        <div class="main-content">
            <div class="content-header">
                <h1>Privacy Policy</h1>
                <p>At PuffLab, your privacy matters to us. Learn how we collect, use, and safeguard your data below.</p>
            </div>
            <section class="fade-in">
                <h2>1. Information We Collect</h2>
                <ul>
                    <li><strong>Personal Data:</strong> Names, addresses, email addresses, and phone numbers provided during registration or transactions.</li>
                    <li><strong>Usage Data:</strong> Insights on site navigation, including IP addresses, browser details, and cookies.</li>
                </ul>
            </section>
            <section class="fade-in">
                <h2>2. How We Use Your Information</h2>
                <p>We use your data to:</p>
                <ul>
                    <li>Provide seamless services and improve your experience.</li>
                    <li>Send updates and promotional content.</li>
                    <li>Analyze and enhance website usability.</li>
                </ul>
            </section>
            <section class="fade-in">
                <h2>3. Cookies and Tracking</h2>
                <p>We use cookies to optimize site functionality and provide personalized experiences. Adjust your browser settings if you wish to limit cookies.</p>
            </section>
            <section class="fade-in">
                <h2>4. Your Rights</h2>
                <ul>
                    <li>Access, amend, or delete your data, as applicable.</li>
                    <li>Opt out of promotional communications at any time.</li>
                </ul>
            </section>
            <div class="contact-us slide-in">
                <h2>Contact Us</h2>
                <p>Have questions? Reach out at <a href="mailto:puff.lab2020@gmail.com">puff.lab2020@gmail.com</a>.</p>
            </div>
        </div>
    </main>


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

    <script src="memberPrivacyPolicy.js"></script>
    
</body>
</html>
