<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "sql112.infinityfree.com";
$username = "if0_37979402";
$password = "tmn3223ncnhcds";
$dbname = "if0_37979402_pufflab";

// Directory to store uploaded images
$uploadDir = '../assets/images/';

try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $action = $_GET['action'] ?? '';
    $data = json_decode(file_get_contents("php://input"), true);

    switch ($action) {
        case 'fetch':
            $sql = "SELECT * FROM Products ORDER BY created_at DESC";
            $result = $conn->query($sql);
            
            if (!$result) {
                throw new Exception("Error fetching products: " . $conn->error);
            }
            
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            echo json_encode($products);
            break;

        case 'add':
            if (!isset($data['name'], $data['price'], $data['stock_quantity'], $data['description'])) {
                throw new Exception("Missing required fields");
            }

            $name = $conn->real_escape_string($data['name']);
            $category_id = $conn->real_escape_string($data['category_id']);
            $description = $conn->real_escape_string($data['description']);
            $price = $conn->real_escape_string($data['price']);
            $stock_quantity = $conn->real_escape_string($data['stock_quantity']);
            $image_url = $conn->real_escape_string($data['image_url']);
            
            $sql = "INSERT INTO Products (name, category_id, description, price, stock_quantity, image_url) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("sisdis", $name, $category_id, $description, $price, $stock_quantity, $image_url);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            echo json_encode(["success" => true, "message" => "Product added successfully"]);
            break;

        case 'update':
            if (!isset($data['product_id'])) {
                throw new Exception("Product ID is required");
            }

            $id = $conn->real_escape_string($data['product_id']);
            $name = $conn->real_escape_string($data['name']);
            $category_id = $conn->real_escape_string($data['category_id']);
            $description = $conn->real_escape_string($data['description']);
            $price = $conn->real_escape_string($data['price']);
            $stock_quantity = $conn->real_escape_string($data['stock_quantity']);
            $image_url = $conn->real_escape_string($data['image_url']);
            
            $sql = "UPDATE Products SET name = ?, category_id = ?, description = ?,
                    price = ?, stock_quantity = ?, image_url = ?
                    WHERE product_id = ?";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("sisdisi", $name, $category_id, $description, $price, $stock_quantity, $image_url, $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            echo json_encode(["success" => true, "message" => "Product updated successfully"]);
            break;

        case 'delete':
            if (!isset($data['product_id'])) {
                throw new Exception("Product ID is required");
            }
            
            $id = $conn->real_escape_string($data['product_id']);
            
            // Delete reviews first
            $sql = "DELETE FROM reviews WHERE product_id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            // Delete related user_favorites entries first
            $sql = "DELETE FROM user_favorites WHERE product_id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            // Delete related transaction details first
            $sql = "DELETE FROM transaction_details WHERE product_id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            // Now delete the product
            $sql = "DELETE FROM Products WHERE product_id = ?";
            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            echo json_encode(["success" => true, "message" => "Product and related reviews deleted successfully"]);
            break;

        case 'uploadImage':
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileSize = $_FILES['image']['size'];
                $fileType = $_FILES['image']['type'];
                
                // Validate image file type (optional)
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($fileType, $allowedTypes)) {
                    throw new Exception('Invalid image file type.');
                }
                
                // Generate a unique name for the image to prevent conflicts
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = uniqid('img_', true) . '.' . $fileExtension;
                $destination = $uploadDir . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $destination)) {
                    // Return the image URL
                    $imageUrl = '/assets/images/' . $newFileName;

                    // Encode the image as a base64 string to save it in the database (if required)
                    $imageData = file_get_contents($destination);
                    $base64Image = 'data:' . $fileType . ';base64,' . base64_encode($imageData);
                    
                    // Respond with the image URL for front-end use
                    echo json_encode(['success' => true, 'imageUrl' => $imageUrl, 'base64Image' => $base64Image]);
                } else {
                    throw new Exception('Error moving the uploaded file.');
                }
            } else {
                throw new Exception('No image uploaded or there was an error with the upload.');
            }
            break;

        default:
            throw new Exception("Invalid action");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}