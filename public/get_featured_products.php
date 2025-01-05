<?php
require_once 'public_db.php';

function getBestSellers() {
    global $conn;
    
    $sql = "SELECT p.product_id, p.name, p.price, p.image_url, pc.name as category_name, 
            SUM(td.quantity) as total_sold 
            FROM Products p 
            JOIN Product_Categories pc ON p.category_id = pc.category_id
            JOIN Transaction_Details td ON p.product_id = td.product_id
            JOIN Transactions t ON td.transaction_id = t.transaction_id
            WHERE t.transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)
            GROUP BY p.product_id
            ORDER BY total_sold DESC
            LIMIT 3";
            
    $result = $conn->query($sql);
    $bestsellers = array();
    
    while($row = $result->fetch_assoc()) {
        $bestsellers[] = $row;
    }
    
    return $bestsellers;
}

function getNewArrivals() {
    global $conn;
    
    // Fetch the latest products by product_id in descending order
    $sql = "SELECT p.product_id, p.name, p.price, p.image_url, pc.name as category_name 
            FROM Products p 
            JOIN Product_Categories pc ON p.category_id = pc.category_id
            ORDER BY p.product_id DESC 
            LIMIT 3";
            
    $result = $conn->query($sql);
    $newArrivals = array();
    
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $newArrivals[] = $row;
        }
    } else {
        // Log the SQL error if there's an issue
        error_log('SQL Error: ' . $conn->error);
    }
    
    return $newArrivals;
}

// Handle the AJAX request
if(isset($_GET['type'])) {
    header('Content-Type: application/json');
    
    if($_GET['type'] === 'bestsellers') {
        echo json_encode(getBestSellers());
    } else if($_GET['type'] === 'arrivals') {
        echo json_encode(getNewArrivals());
    }
}
?>
