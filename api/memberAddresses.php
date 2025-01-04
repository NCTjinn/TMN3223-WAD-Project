<?php
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

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $stmt = $conn->prepare("SELECT address_line_1, address_line_2, city, state, postcode, country FROM Addresses WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $address = $result->fetch_assoc();

        echo json_encode($address);
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        $stmt = $conn->prepare("UPDATE Addresses SET address_line_1 = ?, address_line_2 = ?, city = ?, state = ?, postcode = ?, country = ? WHERE user_id = ?");
        $stmt->bind_param("ssssssi", $data['address_line_1'], $data['address_line_2'], $data['city'], $data['state'], $data['postcode'], $data['country'], $user_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Address updated successfully']);
        } else {
            // Use HTTP response codes to indicate errors
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Failed to update address']);
        }
        
    }

} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['error' => true, 'message' => 'Database error: ' . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
