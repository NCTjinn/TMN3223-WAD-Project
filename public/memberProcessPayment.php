<?php
// Start the session and ensure the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: publicLogin.html');
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "PuffLab";

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

    // Redirect to a confirmation page
    header("Location: memberOrders.php?transaction_id=$transaction_id");
    exit;
}

$conn->close();
?>
