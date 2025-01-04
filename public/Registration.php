<?php
session_start();
header('Content-Type: application/json');

// Include PHPMailer files
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

// Use PHPMailer namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

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

// Generate and verify CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
        die(json_encode(['success' => false, 'message' => 'Invalid request']));
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Sanitize and validate inputs
        $username = filter_var($data['username'], FILTER_SANITIZE_STRING);
        $firstName = filter_var($data['first_name'], FILTER_SANITIZE_STRING);
        $lastName = filter_var($data['last_name'], FILTER_SANITIZE_STRING);
        $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        $password = $data['password'] ?? '';
        $role = 'member';

        $addressLine1 = filter_var($data['address_line_1'], FILTER_SANITIZE_STRING);
        $addressLine2 = filter_var($data['address_line_2'], FILTER_SANITIZE_STRING);
        $city = filter_var($data['city'], FILTER_SANITIZE_STRING);
        $state = filter_var($data['state'], FILTER_SANITIZE_STRING);
        $postcode = filter_var($data['postcode'], FILTER_SANITIZE_STRING);
        $country = filter_var($data['country'], FILTER_SANITIZE_STRING);
        $isDefault = filter_var($data['is_default'], FILTER_VALIDATE_BOOLEAN);

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
        
        // Get the user_id of the newly created user
        $user_id = $stmt->insert_id;

        $stmt->close();

        // Insert address for the new user
        $stmt = $conn->prepare("
            INSERT INTO Addresses (user_id, address_line_1, address_line_2, city, state, postcode, country, is_default)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("issssssi", $user_id, $addressLine1, $addressLine2, $city, $state, $postcode, $country, $isDefault);
        $stmt->execute();

        $stmt->close();

        // Commit transaction
        $conn->commit();

        try {
            // Server settings
            $mail->SMTPDebug = 0; // Disable debug output
            $mail->isSMTP(); // Send using SMTP
            $mail->Host       = 'smtp-mail.outlook.com'; // Set the SMTP server to send through
            $mail->SMTPAuth   = true; // Enable SMTP authentication
            $mail->Username   = 'ncnhcdspufflab@outlook.com'; // SMTP username
            $mail->Password   = 'TMF3113PuffLab'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
            $mail->Port       = 587; // TCP port to connect to

            // Recipients
            $mail->setFrom('ncnhcdspufflab@outlook.com', 'PuffLab Team');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = "Welcome to PuffLab, " . htmlspecialchars($firstName) . "!";
            $mail->Body    = "<h1>Welcome to PuffLab!</h1><p>Thank you for registering, " . htmlspecialchars($firstName) . " " . htmlspecialchars($lastName) . ". We are excited to have you on board.</p>";

            $mail->send();

        } catch (Exception $e) {
            // Log email sending error but continue with transaction
            error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }

        // Success response
        echo json_encode(['success' => true, 'message' => 'Registration successful']);

    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();

        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    die(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

$conn->close();
?>
