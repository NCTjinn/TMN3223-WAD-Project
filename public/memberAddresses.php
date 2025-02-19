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
    <title>My Addresses - PuffLab</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css">
    <link rel="stylesheet" href="memberAddresses.css">
    <script src="memberAddresses.js"></script>
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

    <!-- My Account Page -->
    <div class="container">
        <!-- Page Title -->
        <div class="page-title">
            <h1>My Account</h1>
        </div>

        <!-- Content Section -->
        <div class="content-section">
            <!-- Options Selector (Left Box) -->
            <div class="left-box">
                <ul>
                    <li><a href="memberAcc.php">Dashboard</a></li> <!-- Link to My Account -->
                    <li><a href="memberOrders.php">Orders</a></li> <!-- Link to My Orders -->
                    <li><a href="memberAddresses.php" class="active">Addresses</a></li> <!-- Link to My Rewards -->
                    <li><a href="memberAccount.php">Account Details</a></li> <!-- Link to My Account Details -->
                    <li><a href="Logout.php">Log Out</a></li> <!-- Link to LogOut -->
                </ul>
            </div>

            <div class="right-box">
                <h2>My Address</h2>
                <!-- Existing Addresses -->
                <div class="addresses-list">
                    <div class="address-card">
                        <div class="address-icon">
                            <i class="fa fa-home"></i>
                        </div>
                        <div class="address-details">
                            <!-- Dynamic content will be loaded here -->
                        </div>
                        <div class="address-actions">
                            <button class="edit-btn"><i class="fa fa-edit"></i> Edit</button>
                        </div>
                    </div>
                </div>
                
                <!-- Modal for Editing Address -->
                <div class="modal" id="address-modal">
                    <div class="modal-content">
                        <span class="close-modal">&times;</span>
                        <h3>Edit Address</h3>
                        <form action="#" method="POST" class="modal-form">
                            <div class="form-group">
                                <label for="address-line-1">Address Line 1</label>
                                <input type="text" id="address-line-1" name="address-line-1" placeholder="Enter address line 1" required>
                            </div>
                            <div class="form-group">
                                <label for="address-line-2">Address Line 2</label>
                                <input type="text" id="address-line-2" name="address-line-2" placeholder="Enter address line 2">
                            </div>
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" placeholder="Enter city" required>
                            </div>
                            <div class="form-group">
                                <label for="state">State</label>
                                <input type="text" id="state" name="state" placeholder="Enter state" required>
                            </div>
                            <div class="form-group">
                                <label for="postcode">Postcode</label>
                                <input type="text" id="postcode" name="postcode" placeholder="Enter postcode" required>
                            </div>
                            <div class="form-group">
                                <label for="country">Country</label>
                                <input type="text" id="country" name="country" placeholder="Enter country" required>
                            </div>
                            <div class="modal-actions">
                                <button type="button" class="cancel-btn">Cancel</button>
                                <button type="submit" class="save-btn">Save New Address</button>
                            </div>
                        </form>
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
