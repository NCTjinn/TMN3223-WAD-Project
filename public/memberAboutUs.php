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
    <title>About Us - PuffLab</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css">
    <link rel="stylesheet" href="memberAboutUs.css">
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

    <div class="about-us">
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1>Welcome to PuffLab</h1>
                <p>Where Passion Meets Pastry</p>
            </div>
            <div class="hero-image">
                <img src="../assets/images/puff lab title.jpg" alt="Delicious PuffLab Pastries">
            </div>
        </section>
    
        <!-- Separator Line -->
        <div class="separator"></div>
    
        <!-- Our Team Section -->
        <section class="team">
            <div class="section-header">
                <h1>Our Team</h1>
                <p>Meet the Culinary Experts</p>
            </div>
            <div class="team-content">
                <div class="team-text">
                    <p>
                        Our team is the heartbeat of PuffLab. Led by founders Muhammad Yazid Bin Mohd. Rizman Clement, Azizah Sayan Binti Lukman Hakim, and Chong Siaw Ting, 
                        we are a group of culinary artisans and service professionals united by a passion for exceptional pastry experiences. Our team's diverse backgrounds 
                        contribute to our unique creations, blending traditional techniques with innovative flavors. Each member brings their unique flair to our bakery, 
                        ensuring that every visit to PuffLab is both memorable and delightful.
                    </p>
                </div>
                <div class="team-image">
                    <img src="../assets/images/pufflab staff.jpeg" alt="PuffLab Team Photo">
                </div>
            </div>
        </section>
    
        <!-- Separator Line -->
        <div class="separator"></div>
    
        <!-- Our History Section -->
        <section class="history">
            <div class="section-header">
                <h1>Our History</h1>
                <p>From Humble Beginnings to Culinary Excellence</p>
            </div>
            <div class="history-content">
                <div class="history-video">
                    <video muted playsinline loop>
                        <source src="../assets/videos/puff lab video.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <div class="video-controls">
                        <i class="control-icon play-pause-icon fas fa-play"></i>
                        <i class="control-icon mute-icon fas fa-volume-mute"></i>
                    </div>
                </div>
                
                <div class="history-text">
                    <p>
                        PuffLab's journey began at UNIMAS in September 2019 as a modest project named PuffyPuff. Officially established as PuffLab in 2020, 
                        our bakery has grown from a university project into a community favorite. Each step of our growth has been guided by innovation and the 
                        support of our patrons. As a registered business since November 2020, we continue to push the boundaries of pastry art, making every effort 
                        to enrich Kuching's culinary scene with our delightful creations.
                    </p>
                </div>
            </div>
        </section>
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

    <script src="memberAboutUs.js"></script>
    
</body>
</html>
