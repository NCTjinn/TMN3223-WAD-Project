<?php
session_start();

// Hardcoding user_id for testing purposes
$_SESSION['user_id'] = 4;

// Database configuration
$dbConfig = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'your_database_name'
];

$conn = new mysqli($dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['database']);
if ($conn->connect_error) {
    exit(json_encode(['error' => true, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    fetchAddress($conn, $user_id);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    updateAddress($conn, $user_id);
}

function fetchAddress($conn, $user_id) {
    $query = "SELECT * FROM Addresses WHERE user_id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => true, 'message' => 'No address found']);
    }
    $stmt->close();
}

function updateAddress($conn, $user_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $conn->prepare("UPDATE Addresses SET address_line_1=?, address_line_2=?, city=?, state=?, postcode=?, country=?, phone=? WHERE address_id=? AND user_id=?");
    $stmt->bind_param("ssssssisi", $data['address_line_1'], $data['address_line_2'], $data['city'], $data['state'], $data['postcode'], $data['country'], $data['phone'], $data['address_id'], $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => true, 'message' => 'Update failed: ' . $stmt->error]);
    }
    $stmt->close();
}

$conn->close();
?>
