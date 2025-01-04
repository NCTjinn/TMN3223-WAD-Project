<?php
require_once 'public_db.php';

// Fetch categories
function getCategories() {
    global $conn;
    $sql = "SELECT category_id, name FROM Product_Categories";
    $result = $conn->query($sql);
    $categories = array();
    
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    return $categories;
}

// Fetch products with optional category filter
function getProducts($category_id = null) {
    global $conn;
    $sql = "SELECT p.*, pc.name as category_name 
            FROM Products p 
            JOIN Product_Categories pc ON p.category_id = pc.category_id";
    
    if ($category_id) {
        $sql .= " WHERE p.category_id = ?";
    }
    
    $stmt = $conn->prepare($sql);
    if ($category_id) {
        $stmt->bind_param("i", $category_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $products = array();
    
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    return $products;
}
?>