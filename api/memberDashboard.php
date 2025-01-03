<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
header('Content-Type: application/json');

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
    $dashboard = [];

    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM Transactions WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $dashboard['total_orders'] = $result->fetch_assoc()['total'];

    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM Addresses WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $dashboard['saved_addresses'] = $result->fetch_assoc()['total'];

    $stmt = $conn->prepare("SELECT created_at FROM Users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    $dashboard['account_created'] = date('M d, Y', strtotime($user_data['created_at']));

    echo json_encode($dashboard);

} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode([
        'error' => true,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
