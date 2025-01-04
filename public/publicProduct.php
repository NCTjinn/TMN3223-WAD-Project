<?php
require_once 'menu_data.php';

// Get product ID from URL and validate it
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product details
$product = getProductById($product_id);

// Redirect to menu page if product not found
if (!$product) {
    header('Location: publicMenu.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?> | PuffLab</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="publicMenuProductCSS.css">
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
                    <a href="publicAboutUs.html">About Us <i class='bx bxs-chevron-down'></i></a>
                    <ul class="dropdown-content">
                        <li><a href="publicAboutUs.html">Our Team</a></li>
                        <li><a href="publicAboutUs.html">Our History</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="publicLogin.html">My Account <i class='bx bxs-chevron-down'></i></a>
                    <ul class="dropdown-content">
                        <li><a href="publicLogin.html">Log In</a></li> <!-- Link to Log In -->
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
                <a href="publicCart.php"><i class='bx bx-cart'></i></a>
            </div>
        </div>
    </nav>

    <div class="product-container">
        <button class="back-btn" onclick="window.location.href='publicMenu.php'">&larr; Back to Menu</button>
        
        <div class="product-main">
            <div class="product-gallery">
                <div class="main-image">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="product-img">
                </div>
                <?php if (isset($product['additional_images'])): ?>
                <div class="thumbnail-container">
                    <?php foreach($product['additional_images'] as $image): ?>
                    <img src="<?php echo htmlspecialchars($image); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?> thumbnail" 
                         class="thumbnail">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="price">RM <?php echo number_format($product['price'], 2); ?></p>
                
                <div class="product-tabs">
                    <button class="tab-btn active" data-tab="ingredients">Ingredients</button>
                    <button class="tab-btn" data-tab="serving">Serving Tips</button>
                </div>
                
                <div class="tab-content" id="ingredients">
                    <p><?php echo nl2br(htmlspecialchars($product['ingredients'] ?? 'Ingredients information coming soon.')); ?></p>
                </div>
                
                <div class="tab-content" id="serving" style="display: none;">
                    <p><?php echo nl2br(htmlspecialchars($product['serving_tips'] ?? 'Serving tips coming soon.')); ?></p>
                </div>
                
                <div class="additional-note">
                    <h3>Additional Note</h3>
                    <div class="note-box">
                        <textarea placeholder="Add any special requests..."></textarea>
                    </div>
                </div>
                
                <div class="add-to-cart">
                    <div class="quantity-control">
                        <button class="qty-btn" onclick="updateQuantity(-1)">-</button>
                        <input type="number" value="1" min="1" id="quantity">
                        <button class="qty-btn" onclick="updateQuantity(1)">+</button>
                    </div>
                    <button class="add-cart-btn" onclick="addToCart()">Add To Cart</button>
                </div>
            </div>
        </div>

        <?php if (isset($product['faqs']) && !empty($product['faqs'])): ?>
        <div class="faq-section">
            <h2>FAQs</h2>
            <div class="faq-content">
                <?php foreach($product['faqs'] as $faq): ?>
                <div class="faq-item">
                    <h3><?php echo htmlspecialchars($faq['question']); ?></h3>
                    <p><?php echo htmlspecialchars($faq['answer']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
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
    <script src="publicMenuProductJS.js"></script>
    <script src="publicMenuProductPage.js"></script>
</body>
</html>
