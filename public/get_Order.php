<?php
session_start();
require_once 'public_db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

function getLatestTransaction($user_id) {
    global $conn;
    $sql = "SELECT 
                t.transaction_id,
                t.total_amount,
                t.delivery_fee,
                t.tax_amount,
                t.shipping_method,
                t.delivery_address,
                t.transaction_date,
                o.status,
                o.tracking_number,
                o.estimated_delivery,
                (SELECT SUM(td.subtotal) FROM Transaction_Details td 
                 WHERE td.transaction_id = t.transaction_id) as subtotal
            FROM Transactions t 
            LEFT JOIN Orders o ON t.transaction_id = o.transaction_id 
            WHERE t.user_id = ? 
            ORDER BY t.transaction_date DESC 
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getTransactionDetails($transaction_id) {
    global $conn;
    $sql = "SELECT 
                td.quantity,
                td.price_per_item,
                td.subtotal,
                p.name,
                p.image_url
            FROM Transaction_Details td 
            JOIN Products p ON td.product_id = p.product_id
            WHERE td.transaction_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $transaction_id);
    $stmt->execute();

    $details = [];
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $details[] = $row;
    }
    return $details;
}

$user_id = $_SESSION['user_id'];
$latest_transaction = getLatestTransaction($user_id);

if ($latest_transaction) {
    $transaction_details = getTransactionDetails($latest_transaction['transaction_id']);
    $latest_transaction['details'] = $transaction_details;
    echo json_encode($latest_transaction);
} else {
    echo json_encode(['error' => 'No transactions found']);
}
?>