<?php
// Cart.php - Shopping cart management
require_once 'config.php';

class Cart {
    private $conn;
    private $table = 'Cart';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function addToCart($userId, $productId, $quantity) {
        try {
            // Check if product already exists in cart
            $existingItem = $this->getCartItem($userId, $productId);
            
            if($existingItem) {
                // Update quantity
                $query = "UPDATE " . $this->table . " 
                         SET quantity = quantity + :quantity 
                         WHERE user_id = :user_id AND product_id = :product_id";
            } else {
                // Add new item
                $query = "INSERT INTO " . $this->table . " 
                         (user_id, product_id, quantity) 
                         VALUES (:user_id, :product_id, :quantity)";
            }

            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":product_id", $productId);
            $stmt->bindParam(":quantity", $quantity);

            if($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Item added to cart successfully'
                ];
            }
            return ['status' => 'error', 'message' => 'Failed to add item to cart'];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getCartContents($userId) {
        try {
            $query = "SELECT c.*, p.name, p.price, p.image_url, 
                     (p.price * c.quantity) as subtotal 
                     FROM " . $this->table . " c 
                     JOIN Products p ON c.product_id = p.product_id 
                     WHERE c.user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $userId);
            $stmt->execute();

            return [
                'status' => 'success',
                'items' => $stmt->fetchAll(),
                'total' => $this->getCartTotal($userId)
            ];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function updateQuantity($userId, $productId, $quantity) {
        try {
            $query = "UPDATE " . $this->table . " 
                     SET quantity = :quantity 
                     WHERE user_id = :user_id AND product_id = :product_id";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":quantity", $quantity);
            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":product_id", $productId);

            if($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Cart updated successfully',
                    'new_total' => $this->getCartTotal($userId)
                ];
            }
            return ['status' => 'error', 'message' => 'Failed to update cart'];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function removeItem($userId, $productId) {
        try {
            $query = "DELETE FROM " . $this->table . " 
                     WHERE user_id = :user_id AND product_id = :product_id";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":product_id", $productId);

            if($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Item removed from cart',
                    'new_total' => $this->getCartTotal($userId)
                ];
            }
            return ['status' => 'error', 'message' => 'Failed to remove item'];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function getCartItem($userId, $productId) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE user_id = :user_id AND product_id = :product_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":product_id", $productId);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    private function getCartTotal($userId) {
        $query = "SELECT SUM(p.price * c.quantity) as total 
                 FROM " . $this->table . " c 
                 JOIN Products p ON c.product_id = p.product_id 
                 WHERE c.user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}