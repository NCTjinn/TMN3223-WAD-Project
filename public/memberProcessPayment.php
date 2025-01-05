<?php
// Start the session and ensure the user is logged in
session_start();

// Add this after session_start()
if (!isset($_SESSION['user_id']) || !isset($_POST['payment_method'])) {
    logError("Missing required session data or payment method");
    header("Location: memberCart.php?error=" . urlencode("Missing required data"));
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: publicLogin.html');
    exit;
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Enhanced error logging function
function logError($message, $sql = '', $error = '') {
    $errorLog = date('Y-m-d H:i:s') . " - Error: " . $message . "\nSQL: " . $sql . "\nError: " . $error . "\n";
    error_log($errorLog, 3, "checkout_errors.log");
    
    // Store error in session for displaying to user
    $_SESSION['checkout_error'] = $message;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['payment_method'])) {
    try {
        // Start transaction
        $conn->begin_transaction();
        
        $paymentMethod = $_POST['payment_method'];
        $payment_status = "processing";
        // Debug log
        error_log("Starting checkout process for user: " . $userId);
        error_log("Payment method: " . $paymentMethod);
        
        // Fetch user's default address
        $address_query = "SELECT address_line_1, city, state, postcode FROM Addresses WHERE user_id = ? AND is_default = 1 LIMIT 1";
        $address_stmt = $conn->prepare($address_query);
        if (!$address_stmt) {
            throw new Exception("Address prepare failed: " . $conn->error);
        }
        
        $address_stmt->bind_param("i", $userId);
        if (!$address_stmt->execute()) {
            throw new Exception("Address execute failed: " . $address_stmt->error);
        }
        
        $address_result = $address_stmt->get_result();
        $address = $address_result->fetch_assoc();
        
        // Debug log address
        error_log("Address query result: " . print_r($address, true));
        
        if (!$address) {
            throw new Exception("No default address found for user");
        }
        
        $delivery_address = $address['address_line_1'] . ", " . $address['city'] . ", " . $address['state'] . " " . $address['postcode'];

        // Calculate total amount from Cart
        $cart_query = "SELECT Cart.product_id, Cart.quantity, Products.price, (Products.price * Cart.quantity) as total 
                      FROM Cart 
                      JOIN Products ON Cart.product_id = Products.product_id 
                      WHERE Cart.user_id = ?";
        $cart_stmt = $conn->prepare($cart_query);
        if (!$cart_stmt) {
            throw new Exception("Cart prepare failed: " . $conn->error);
        }
        
        $cart_stmt->bind_param("i", $userId);
        if (!$cart_stmt->execute()) {
            throw new Exception("Cart execute failed: " . $cart_stmt->error);
        }
        
        $cart_result = $cart_stmt->get_result();
        
        $total_amount = 0;
        $items = [];
        while ($row = $cart_result->fetch_assoc()) {
            $total_amount += $row['total'];
            $items[] = $row;
        }
        
        // Debug log cart items
        error_log("Cart items: " . print_r($items, true));
        error_log("Total amount: " . $total_amount);
        
        if (empty($items)) {
            throw new Exception("Cart is empty");
        }

        $delivery_address = "{$address['address_line_1']}, {$address['city']}, {$address['state']}, {$address['postcode']}";

        // Insert transaction
        $transaction_sql = "INSERT INTO Transactions (user_id, total_amount, delivery_address, payment_status, shipping_method) VALUES (?, ?, ?, 'successful', 'Delivery')";
        $transaction_stmt = $conn->prepare($transaction_sql);
        $transaction_stmt->bind_param('ids', $userId, $total_amount, $delivery_address);
        $transaction_stmt->execute();
        $transaction_id = $transaction_stmt->insert_id;

        // Insert transaction details
        $detail_sql = "INSERT INTO Transaction_Details (transaction_id, product_id, quantity, price_per_item, subtotal) VALUES (?, ?, ?, ?, ?)";
        $detail_stmt = $conn->prepare($detail_sql);
        foreach ($items as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $detail_stmt->bind_param('iiidd', $transaction_id, $item['product_id'], $item['quantity'], $item['price'], $subtotal);
        $detail_stmt->execute();
    }

        // Insert into Orders table
        $tracking_number = 'TRACK_' . uniqid();
        $status = 'processing';
        $estimated_delivery = date('Y-m-d', strtotime('+7 days'));
        
        $orders_sql = "INSERT INTO Orders (transaction_id, tracking_number, status, estimated_delivery) 
                      VALUES (?, ?, ?, ?)";
        $orders_stmt = $conn->prepare($orders_sql);
        if (!$orders_stmt) {
            throw new Exception("Orders prepare failed: " . $conn->error);
        }
        
        $orders_stmt->bind_param("isss", $transaction_id, $tracking_number, $status, $estimated_delivery);
        if (!$orders_stmt->execute()) {
            throw new Exception("Orders execute failed: " . $orders_stmt->error);
        }

        error_log("Order created with tracking number: " . $tracking_number);

        // Update Sales Summary
        $current_date = date('Y-m-d');
        $sales_summary_sql = "INSERT INTO Sales_Summary (date, total_orders, gross_sales) 
                            VALUES (?, 1, ?) 
                            ON DUPLICATE KEY UPDATE 
                            total_orders = total_orders + 1, 
                            gross_sales = gross_sales + VALUES(gross_sales)";
        $sales_summary_stmt = $conn->prepare($sales_summary_sql);
        if (!$sales_summary_stmt) {
            throw new Exception("Sales summary prepare failed: " . $conn->error);
        }
        
        $sales_summary_stmt->bind_param("sd", $current_date, $total_amount);
        if (!$sales_summary_stmt->execute()) {
            throw new Exception("Sales summary execute failed: " . $sales_summary_stmt->error);
        }

        // Clear user's cart
        $clear_cart_sql = "DELETE FROM Cart WHERE user_id = ?";
        $clear_cart_stmt = $conn->prepare($clear_cart_sql);
        if (!$clear_cart_stmt) {
            throw new Exception("Clear cart prepare failed: " . $conn->error);
        }
        
        $clear_cart_stmt->bind_param("i", $userId);
        if (!$clear_cart_stmt->execute()) {
            throw new Exception("Clear cart execute failed: " . $clear_cart_stmt->error);
        }

        error_log("Cart cleared for user: " . $userId);

        // Commit transaction
        $conn->commit();
        error_log("Transaction committed successfully");

        // Redirect to confirmation page
        header("Location: memberOrders.php?transaction_id=$transaction_id");
        exit;

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error_message = $e->getMessage();
        logError("Checkout failed", "", $error_message);
        header("Location: memberCart.php?error=" . urlencode($error_message));
        exit;
    }
}

$conn->close();
?>