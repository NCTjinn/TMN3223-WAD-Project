<?php
// Start the session and ensure the user is logged in
session_start();
header('Content-Type: application/json');

// Include PHPMailer files
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

// Use PHPMailer namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

if (!isset($_SESSION['user_id'])) {
    header('Location: publicLogin.html');
    exit;
}

// Database connection
$servername = "sql112.infinityfree.com";
$username = "if0_37979402";
$password = "tmn3223ncnhcds";
$dbname = "if0_37979402_pufflab";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['user_id'];

// Ensure there is a payment method posted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['payment_method'])) {
    $paymentMethod = $_POST['payment_method'];
    
    // Fetch user's default address
    $address_query = "SELECT address_line_1, city, state, postcode FROM Addresses WHERE user_id = ? AND is_default = TRUE";
    $address_stmt = $conn->prepare($address_query);
    $address_stmt->bind_param("i", $userId);
    $address_stmt->execute();
    $address_result = $address_stmt->get_result();
    $address = $address_result->fetch_assoc();
    $delivery_address = implode(", ", $address);

    // Calculate total amount from Cart
    $cart_query = "SELECT Cart.product_id, Cart.quantity, (Products.price * Cart.quantity) AS total 
    FROM Cart 
    JOIN Products ON Cart.product_id = Products.product_id 
    WHERE Cart.user_id = ?";
    $cart_stmt = $conn->prepare($cart_query);
    $cart_stmt->bind_param("i", $userId);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();

    $total_amount = 0;
    $items = [];
    while ($row = $cart_result->fetch_assoc()) {
    $total_amount += $row['total'];
    $items[] = $row;
    }

    // Insert transaction
    $transaction_sql = "INSERT INTO Transactions (user_id, total_amount, payment_status, delivery_address) VALUES (?, ?, 'successful', ?)";
    $transaction_stmt = $conn->prepare($transaction_sql);
    $transaction_stmt->bind_param("ids", $userId, $total_amount, $delivery_address);
    $transaction_stmt->execute();
    $transaction_id = $transaction_stmt->insert_id;

    // Insert transaction details
    foreach ($items as $item) {
        $detail_sql = "INSERT INTO Transaction_Details (transaction_id, product_id, quantity, price_per_item, subtotal) VALUES (?, ?, ?, (SELECT price FROM Products WHERE product_id = ?), ?)";
        $detail_stmt = $conn->prepare($detail_sql);
        $detail_stmt->bind_param("iiidi", $transaction_id, $item['product_id'], $item['quantity'], $item['product_id'], $item['total']);
        $detail_stmt->execute();
    }

    // Insert into Orders table
    $tracking_number = uniqid('TRACK_', true);
    $status = 'processing';
    $estimated_delivery = date('Y-m-d', strtotime('+7 days')); // Example: estimated delivery in 7 days
    
    $orders_sql = "INSERT INTO Orders (transaction_id, tracking_number, status, estimated_delivery) VALUES (?, ?, ?, ?)";
    $orders_stmt = $conn->prepare($orders_sql);
    $orders_stmt->bind_param("isss", $transaction_id, $tracking_number, $status, $estimated_delivery);
    $orders_stmt->execute();

    // Update Sales Summary
    $sales_summary_sql = "INSERT INTO Sales_Summary (date, total_orders, gross_sales) VALUES (CURDATE(), 1, ?) ON DUPLICATE KEY UPDATE total_orders = total_orders + 1, gross_sales = gross_sales + VALUES(gross_sales)";
    $sales_summary_stmt = $conn->prepare($sales_summary_sql);
    $sales_summary_stmt->bind_param("d", $total_amount);
    $sales_summary_stmt->execute();

    // Clear user's cart
    $clear_cart_sql = "DELETE FROM Cart WHERE user_id = ?";
    $clear_cart_stmt = $conn->prepare($clear_cart_sql);
    $clear_cart_stmt->bind_param("i", $userId);
    $clear_cart_stmt->execute();

    // Fetch user email
    $user_query = "SELECT email FROM Users WHERE user_id = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $userId);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();

    if ($user_row = $user_result->fetch_assoc()) {
        $email = $user_row['email'];
    } else {
        throw new Exception("User not found");
    }

    // Email order confirmation to user
    try {
        // Fetch transaction details
        $details_sql = "
            SELECT
                td.product_id,
                p.name,
                td.quantity,
                td.price_per_item,
                td.subtotal
            FROM Transaction_Details td
            JOIN Products p ON td.product_id = p.product_id
            WHERE td.transaction_id = ?";
        $details_stmt = $conn->prepare($details_sql);
        $details_stmt->bind_param("i", $transaction_id);
        $details_stmt->execute();
        $details_result = $details_stmt->get_result();
   
        // Build email content
        $email_body = "<h1>Order Confirmation</h1>";
        $email_body .= "<p>Thank you for your purchase!</p>";
        $email_body .= "<p>Here are your order details:</p>";
   
        $email_body .= "<table border='1' cellpadding='5' cellspacing='0'>";
        $email_body .= "<tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price Per Item</th>
                            <th>Subtotal</th>
                        </tr>";
   
        while ($row = $details_result->fetch_assoc()) {
            $email_body .= "<tr>
                                <td>" . htmlspecialchars($row['name']) . "</td>
                                <td>" . $row['quantity'] . "</td>
                                <td>RM " . number_format($row['price_per_item'], 2) . "</td>
                                <td>RM " . number_format($row['subtotal'], 2) . "</td>
                            </tr>";
        }
   
        $email_body .= "</table>";
        $email_body .= "<p><strong>Total Amount:</strong> RM " . number_format($total_amount, 2) . "</p>";
        $email_body .= "<p><strong>Tracking Number:</strong> " . htmlspecialchars($tracking_number) . "</p>";
        $email_body .= "<p><strong>Estimated Delivery:</strong> " . htmlspecialchars($estimated_delivery) . "</p>";
        $email_body .= "<p>We will notify you once your order is shipped. Thank you for shopping with us!</p>";
   
        // Server settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sxdturn@gmail.com';
        $mail->Password   = 'sfnbthazaqaurxjo';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom('ncnhcdspufflab@outlook.com', 'PuffLab Team');
        $mail->addAddress($email);
   
        // Content
        $mail->isHTML(true);
        $mail->Subject = "Your Order Confirmation - PuffLab";
        $mail->Body    = $email_body;
   
        $mail->send();
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }

    // Redirect to a confirmation page
    header("Location: memberTrack.php?transaction_id=$transaction_id");
    exit;
}

$conn->close();
?>
