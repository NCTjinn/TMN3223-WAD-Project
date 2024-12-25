<?php
// Product.php - Product management and inventory
require_once 'config.php';

class Product {
    private $conn;
    private $table = 'Products';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createProduct($data) {
        try {
            $query = "INSERT INTO " . $this->table . " 
                    (name, category_id, description, price, stock_quantity, image_url) 
                    VALUES (:name, :category_id, :description, :price, :stock_quantity, :image_url)";

            $stmt = $this->conn->prepare($query);
            
            // Bind values
            $stmt->bindParam(":name", $data['name']);
            $stmt->bindParam(":category_id", $data['category_id']);
            $stmt->bindParam(":description", $data['description']);
            $stmt->bindParam(":price", $data['price']);
            $stmt->bindParam(":stock_quantity", $data['stock_quantity']);
            $stmt->bindParam(":image_url", $data['image_url']);

            if($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Product created successfully',
                    'product_id' => $this->conn->lastInsertId()
                ];
            }
            return ['status' => 'error', 'message' => 'Failed to create product'];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getProducts($category_id = null, $limit = 10, $offset = 0) {
        try {
            $query = "SELECT p.*, pc.name as category_name, 
                     (SELECT AVG(rating) FROM Reviews r WHERE r.product_id = p.product_id) as average_rating,
                     (SELECT COUNT(*) FROM Reviews r WHERE r.product_id = p.product_id) as review_count
                     FROM " . $this->table . " p 
                     LEFT JOIN Product_Categories pc ON p.category_id = pc.category_id";
            
            if($category_id) {
                $query .= " WHERE p.category_id = :category_id";
            }
            
            $query .= " LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            
            if($category_id) {
                $stmt->bindParam(":category_id", $category_id);
            }
            
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
            $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function updateProduct($productId, $data) {
        try {
            $updateFields = [];
            $params = [];

            foreach($data as $key => $value) {
                if($key !== 'product_id') {
                    $updateFields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }

            if(!empty($updateFields)) {
                $query = "UPDATE " . $this->table . " SET " . implode(", ", $updateFields) . 
                         " WHERE product_id = :product_id";
                
                $stmt = $this->conn->prepare($query);
                $params[":product_id"] = $productId;
                
                if($stmt->execute($params)) {
                    return ['status' => 'success', 'message' => 'Product updated successfully'];
                }
            }
            return ['status' => 'error', 'message' => 'No fields to update'];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function updateStock($productId, $quantity, $operation = 'subtract') {
        try {
            $query = "UPDATE " . $this->table . " SET stock_quantity = stock_quantity " . 
                     ($operation === 'add' ? '+' : '-') . " :quantity 
                     WHERE product_id = :product_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":quantity", $quantity);
            $stmt->bindParam(":product_id", $productId);

            if($stmt->execute()) {
                return ['status' => 'success', 'message' => 'Stock updated successfully'];
            }
            return ['status' => 'error', 'message' => 'Failed to update stock'];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getProductReviews($productId, $limit = 10, $offset = 0) {
        try {
            $query = "SELECT r.*, u.display_name 
                     FROM Reviews r 
                     JOIN Users u ON r.user_id = u.user_id 
                     WHERE r.product_id = :product_id 
                     ORDER BY r.created_at DESC 
                     LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":product_id", $productId);
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
            $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}