<?php
require_once 'menu_data.php';
header('Content-Type: application/json');

// Sanitize input
$category_id = isset($_GET['category']) && $_GET['category'] !== 'ALL' 
    ? intval($_GET['category']) 
    : null;

$products = getProducts($category_id);

// Return JSON response
echo json_encode($products);
?>
