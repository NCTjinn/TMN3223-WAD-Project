<?php
// get_user_details.php
session_start();
require_once 'public_db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Not authenticated']));
}

try {
    $sql = "SELECT u.first_name, u.last_name, u.phone, 
            a.address_line_1, a.city, a.state, a.postcode 
            FROM Users u 
            LEFT JOIN Addresses a ON u.user_id = a.user_id AND a.is_default = 1 
            WHERE u.user_id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userDetails = $result->fetch_assoc();
    
    echo json_encode($userDetails);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
