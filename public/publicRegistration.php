<?php
// publicRegistration.php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "PuffLab";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents("php://input"), true);

// Start session
session_start();

// Generate and verify CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
        die(json_encode(['success' => false, 'message' => 'Invalid request']));
    }

    try {
        // Sanitize and validate inputs
        $username = filter_var($data['username'], FILTER_SANITIZE_STRING);
        $firstName = filter_var($data['first_name'], FILTER_SANITIZE_STRING);
        $lastName = filter_var($data['last_name'], FILTER_SANITIZE_STRING);
        $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        $password = $data['password'] ?? '';
        $role = 'member';

        // Additional validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }

        if (strlen($password) < 8) {
            throw new Exception('Password must be at least 8 characters long');
        }

        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT username, email FROM Users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($existing_username, $existing_email);
            $stmt->fetch();

            if ($existing_username === $username) {
                throw new Exception('Username already exists');
            }
            if ($existing_email === $email) {
                throw new Exception('Email already registered');
            }
        }

        $stmt->close();

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);

        // Insert new user
        $stmt = $conn->prepare("
            INSERT INTO Users (username, first_name, last_name, email, password, role)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssss", $username, $firstName, $lastName, $email, $hashedPassword, $role);
        $stmt->execute();

        // Success response
        echo json_encode(['success' => true, 'message' => 'Registration successful']);

    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    die(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

$conn->close();
?>
