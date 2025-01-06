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
    
    $sql = "SELECT cart_id, quantity FROM Cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $new_quantity = $row['quantity'] + $quantity;
        $sql = "UPDATE Cart SET quantity = ? WHERE cart_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $new_quantity, $row['cart_id']);
    } else {
        $sql = "INSERT INTO Cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    }
    
    return $stmt->execute();
}

// Update cart item
function updateCartItem($user_id, $cart_id, $quantity) {
    global $conn;
    
    if ($quantity <= 0) {
        $sql = "DELETE FROM Cart WHERE cart_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $cart_id, $user_id);
    } else {
        $sql = "UPDATE Cart SET quantity = ? WHERE cart_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
    }
    
    return $stmt->execute();
}

// Remove cart item
function removeCartItem($user_id, $product_id) {
    global $conn;
    $sql = "DELETE FROM Cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    return $stmt->execute();
}


// Clear all items from the cart for the user
function clearCart($user_id) {
    global $conn;
    $sql = "DELETE FROM Cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    return $stmt->execute();
}

// Handle requests
$user_id = checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(getCartItems($user_id));
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            if (addToCart($user_id, $_POST['product_id'], $_POST['quantity'])) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to add item to cart']);
            }
            break;
            
        case 'update':
            if (updateCartItem($user_id, $_POST['cart_id'], $_POST['quantity'])) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update cart']);
            }
            break;
        
        case 'remove':
            if (removeCartItem($user_id, $_POST['product_id'])) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to remove item from cart']);
            }
            break;

        case 'clear':
            if (clearCart($user_id)) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to clear cart']);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
}
?>