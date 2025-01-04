<?php
// memberOrders.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
header('Content-Type: application/json');

// Database configuration
$config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'pufflab'
];

try {
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => true, 'message' => 'User not authenticated']);
        exit;
    }

$user_id = $_SESSION['user_id'];
// Function to get orders with details
function getOrders($user_id, $status_array) {
    global $conn;
    
    $sql = "SELECT 
                o.order_id,
                o.tracking_number,
                o.status,
                o.estimated_delivery,
                t.transaction_id,
                t.transaction_date,
                t.total_amount,
                t.delivery_fee,
                t.tax_amount,
                GROUP_CONCAT(
                    CONCAT(p.name, ' x', td.quantity, ' - $', td.price_per_item) 
                    SEPARATOR '||'
                ) as order_items
            FROM Orders o
            JOIN Transactions t ON o.transaction_id = t.transaction_id
            JOIN Transaction_Details td ON t.transaction_id = td.transaction_id
            JOIN Products p ON td.product_id = p.product_id
            WHERE t.user_id = ? 
            AND o.status IN ('".implode("','", $status_array)."')
            GROUP BY o.order_id
            ORDER BY t.transaction_date DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $row['order_items'] = explode('||', $row['order_items']);
        $orders[] = $row;
    }
    
    return $orders;
}

// Get current orders (processing, shipped)
$current_orders = getOrders($user_id, ['processing', 'shipped']);

// Get past orders (delivered, cancelled)
$past_orders = getOrders($user_id, ['delivered', 'cancelled']);

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'current_orders' => $current_orders,
    'past_orders' => $past_orders
]);