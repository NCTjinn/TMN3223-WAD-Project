<?php
session_start();
require_once 'public_db.php';
header('Content-Type: application/json');

// Check if user is logged in
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'User not authenticated']);
        exit;
    }
    return $_SESSION['user_id'];
}

// Get cart items for current user
function getCartItems($user_id) {
    global $conn;
    $sql = "SELECT c.cart_id, c.quantity, p.* 
            FROM Cart c 
            JOIN Products p ON c.product_id = p.product_id 
            WHERE c.user_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    return $items;
}

// Add item to cart
function addToCart($user_id, $product_id, $quantity) {
    global $conn;
    
    // Check if item already exists in cart
    $sql = "SELECT cart_id, quantity FROM Cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Update existing cart item
        $new_quantity = $row['quantity'] + $quantity;
        $sql = "UPDATE Cart SET quantity = ? WHERE cart_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $new_quantity, $row['cart_id']);
    } else {
        // Insert new cart item
        $sql = "INSERT INTO Cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    }
    
    return $stmt->execute();
}

// Update cart item quantity
function updateCartItem($user_id, $cart_id, $quantity) {
    global $conn;
    
    if ($quantity <= 0) {
        // Delete item if quantity is 0 or negative
        $sql = "DELETE FROM Cart WHERE cart_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $cart_id, $user_id);
    } else {
        // Update quantity
        $sql = "UPDATE Cart SET quantity = ? WHERE cart_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
    }
    
    return $stmt->execute();
}

// Handle requests
$user_id = checkAuth();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Get cart items
        echo json_encode(getCartItems($user_id));
        break;
        
    case 'POST':
        // Add item to cart
        $data = json_decode(file_get_contents('php://input'), true);
        if (addToCart($user_id, $data['product_id'], $data['quantity'])) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add item to cart']);
        }
        break;
        
    case 'PUT':
        // Update cart item
        $data = json_decode(file_get_contents('php://input'), true);
        if (updateCartItem($user_id, $data['cart_id'], $data['quantity'])) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update cart']);
        }
        break;
}
?>
