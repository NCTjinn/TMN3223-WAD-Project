<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "PuffLab";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Function to validate password requirements
function validatePassword($password) {
    // Check length
    if (strlen($password) < 6 || strlen($password) > 8) {
        return "Password must be 6-8 characters long.";
    }
    
    // Check for uppercase
    if (!preg_match('/[A-Z]/', $password)) {
        return "Password must contain at least one uppercase letter.";
    }
    
    // Check for number
    if (!preg_match('/[0-9]/', $password)) {
        return "Password must contain at least one number.";
    }
    
    // Check for special character
    if (!preg_match('/[!@#$%^&*]/', $password)) {
        return "Password must contain at least one special character (!@#$%^&*).";
    }
    
    // Check for spaces
    if (preg_match('/\s/', $password)) {
        return "Password must not contain spaces.";
    }
    
    return true;
}

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not authenticated');
    }

    $user_id = $_SESSION['user_id'];

    // Fetch user data
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $conn->prepare("SELECT first_name, last_name, email FROM Users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user_data = $result->fetch_assoc()) {
            echo json_encode([
                'success' => true,
                'data' => $user_data
            ]);
        } else {
            throw new Exception('No data found for this user.');
        }
    }
    
    // Handle POST requests for updates
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update personal details
        if (isset($_POST['update_details'])) {
            $first_name = trim($_POST['first_name']);
            $last_name = trim($_POST['last_name']);
            
            if (empty($first_name) || empty($last_name)) {
                throw new Exception('First name and last name are required.');
            }
            
            $stmt = $conn->prepare("UPDATE Users SET first_name = ?, last_name = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $first_name, $last_name, $user_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to update details: ' . $conn->error);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Details updated successfully'
            ]);
        }
        
        // Update password
        if (isset($_POST['update_password'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            // Validate passwords match
            if ($new_password !== $confirm_password) {
                throw new Exception('New passwords do not match.');
            }
            
            // Validate new password requirements
            $password_validation = validatePassword($new_password);
            if ($password_validation !== true) {
                throw new Exception($password_validation);
            }
            
            // Fetch current password hash - using same approach as login.php
            $stmt = $conn->prepare("SELECT password FROM Users WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($hashed_password);
                $stmt->fetch();

                if (!password_verify($current_password, $hashed_password)) {
                    throw new Exception('Current password is incorrect.');
                }

                // Hash new password
                $new_password_hash = password_hash($new_password, PASSWORD_ARGON2ID, [
                    'memory_cost' => 65536,
                    'time_cost' => 4,
                    'threads' => 3
                ]);
                
                // Update password
                $update_stmt = $conn->prepare("UPDATE Users SET password = ? WHERE user_id = ?");
                $update_stmt->bind_param("si", $new_password_hash, $user_id);
                
                if (!$update_stmt->execute()) {
                    throw new Exception('Failed to update password: ' . $conn->error);
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Password updated successfully'
                ]);
            } else {
                throw new Exception('User not found.');
            }
        }
    } else {
        throw new Exception('Invalid request method.');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>