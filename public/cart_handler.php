<?php
// cart_handler.php
require_once 'menu_data.php';
header('Content-Type: application/json');

function getCartProductDetails($productIds) {
    global $conn;
    $ids = array_map('intval', $productIds);
    $idList = implode(',', $ids);
    
    $sql = "SELECT p.product_id, p.name, p.price, p.image_url 
            FROM Products p 
            WHERE p.product_id IN ($idList)";
            
    $result = $conn->query($sql);
    $products = array();
    
    while($row = $result->fetch_assoc()) {
        $products[$row['product_id']] = $row;
    }
    
    return $products;
}

// Get product details for cart items
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action']) && $data['action'] === 'getCartDetails') {
        if (isset($data['productIds']) && is_array($data['productIds'])) {
            $productDetails = getCartProductDetails($data['productIds']);
            echo json_encode(['success' => true, 'products' => $productDetails]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid product IDs']);
        }
    }
}
?>
