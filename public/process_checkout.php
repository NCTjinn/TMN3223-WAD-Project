<?php
// process_checkout.php
session_start();
require_once 'public_db.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Not authenticated']));
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $conn->begin_transaction();

    // Generate unique transaction ID
    $transactionId = 'TXN' . time() . rand(1000, 9999);
    
    // Insert into transactions table
    $sql = "INSERT INTO Transactions (transaction_id, user_id, total_amount, delivery_fee, 
            payment_status, delivery_address, shipping_method, payment_method) 
            VALUES (?, ?, ?, ?, 'successful', ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $address = implode(", ", [
        $data['address']['addressLine1'],
        $data['address']['city'],
        $data['address']['state'],
        $data['address']['postcode']
    ]);
    
    $stmt->bind_param("siddsss", 
        $transactionId,
        $_SESSION['user_id'],
        $data['total'],
        $data['deliveryFee'],
        $address,
        $data['deliveryOption'],
        $data['paymentMethod']
    );
    $stmt->execute();

    // Insert transaction details
    $sql = "INSERT INTO Transaction_Details (transaction_id, product_id, quantity, price_per_item, subtotal) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    foreach ($data['items'] as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $stmt->bind_param("siids",
            $transactionId,
            $item['productID'],
            $item['quantity'],
            $item['price'],
            $subtotal
        );
        $stmt->execute();
    }

    // Clear the cart
    $sql = "DELETE FROM Cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();

    // Get user email
    $sql = "SELECT email FROM Users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userEmail = $result->fetch_assoc()['email'];

    // Send confirmation email
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com';
        $mail->Password = 'your-password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('your-email@gmail.com', 'PuffLab');
        $mail->addAddress($userEmail);
        
        $mail->isHTML(true);
        $mail->Subject = "Order Confirmation - " . $transactionId;
        
        // Generate email content
        $emailContent = "<h1>Order Confirmation</h1>";
        $emailContent .= "<p>Thank you for your order!</p>";
        $emailContent .= "<p>Transaction ID: " . $transactionId . "</p>";
        $emailContent .= "<h2>Order Details:</h2>";
        
        foreach ($data['items'] as $item) {
            $emailContent .= "<p>{$item['name']} x {$item['quantity']} - RM" . 
                number_format($item['price'] * $item['quantity'], 2) . "</p>";
        }
        
        $emailContent .= "<p>Subtotal: RM" . number_format($data['total'] - $data['deliveryFee'], 2) . "</p>";
        if ($data['deliveryFee'] > 0) {
            $emailContent .= "<p>Delivery Fee: RM" . number_format($data['deliveryFee'], 2) . "</p>";
        }
        $emailContent .= "<p>Total: RM" . number_format($data['total'], 2) . "</p>";
        
        $mail->Body = $emailContent;
        $mail->send();
    } catch (Exception $e) {
        // Log email error but continue with transaction
        error_log("Email sending failed: {$mail->ErrorInfo}");
    }

    $conn->commit();
    echo json_encode(['success' => true, 'transaction_id' => $transactionId]);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
