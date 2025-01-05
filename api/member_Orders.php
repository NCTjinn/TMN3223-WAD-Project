<?php
// memberOrders.php
error_reporting(0); // Disable error reporting for production
ini_set('display_errors', 0); // Disable error display

session_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Database configuration
$config = [
    'host' => 'sql112.infinityfree.com:3306', // Added port number
    'username' => 'if0_37979402',
    'password' => 'tmn3223ncnhcds',
    'database' => 'if0_37979402_pufflab'
];

try {
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Set the charset to ensure proper encoding
    $conn->set_charset("utf8mb4");

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not authenticated');
    }

    $user_id = $_SESSION['user_id'];

    // Function to get orders with details
    function getOrders($conn, $user_id, $status_array) {
        $status_list = implode(',', array_map(function($status) use ($conn) {
            return "'" . $conn->real_escape_string($status) . "'";
        }, $status_array));
        
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
                        CONCAT(p.name, ' x', td.quantity, ' - $', FORMAT(td.price_per_item, 2)) 
                        SEPARATOR '||'
                    ) as order_items
                FROM Orders o
                JOIN Transactions t ON o.transaction_id = t.transaction_id
                JOIN Transaction_Details td ON t.transaction_id = td.transaction_id
                JOIN Products p ON td.product_id = p.product_id
                WHERE t.user_id = ?
                AND o.status IN ($status_list)
                GROUP BY o.order_id, t.transaction_id
                ORDER BY t.transaction_date DESC";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $user_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $orders = [];
        
        while ($row = $result->fetch_assoc()) {
            $row['order_items'] = explode('||', $row['order_items']);
            // Ensure numeric values are properly formatted
            $row['total_amount'] = number_format((float)$row['total_amount'], 2, '.', '');
            $row['delivery_fee'] = number_format((float)$row['delivery_fee'], 2, '.', '');
            $row['tax_amount'] = number_format((float)$row['tax_amount'], 2, '.', '');
            $orders[] = $row;
        }
        
        return $orders;
    }

    // Get current orders (processing, shipped)
    $current_orders = getOrders($conn, $user_id, ['processing', 'shipped']);

    // Get past orders (delivered, cancelled)
    $past_orders = getOrders($conn, $user_id, ['delivered', 'cancelled']);

    echo json_encode([
        'current_orders' => $current_orders,
        'past_orders' => $past_orders
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
    exit;
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}