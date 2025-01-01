<?php
require_once '../includes/config.php';

try {
    $query = "SELECT * FROM Products LIMIT 5";
    $stmt = $conn->query($query);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h1>Test Database Connection</h1>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th></tr>";
    foreach ($products as $product) {
        echo "<tr>";
        echo "<td>{$product['product_id']}</td>";
        echo "<td>{$product['name']}</td>";
        echo "<td>{$product['price']}</td>";
        echo "<td>{$product['stock_quantity']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
