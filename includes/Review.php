<?php
// Review.php - Review and rating management
require_once 'config.php';

class Review {
    private $conn;
    private $table = 'Reviews';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createReview($userId, $productId, $rating, $comment) {
        try {
            // Check if user has already reviewed this product
            if($this->hasUserReviewed($userId, $productId)) {
                return ['status' => 'error', 'message' => 'You have already reviewed this product'];
            }

            $query = "INSERT INTO " . $this->table . " 
                    (user_id, product_id, rating, comment) 
                    VALUES (:user_id, :product_id, :rating, :comment)";

            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":product_id", $productId);
            $stmt->bindParam(":rating", $rating);
            $stmt->bindParam(":comment", $comment);

            if($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Review submitted successfully',
                    'review_id' => $this->conn->lastInsertId()
                ];
            }
            return ['status' => 'error', 'message' => 'Failed to submit review'];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getProductReviews($productId, $limit = 10, $offset = 0) {
        try {
            $query = "SELECT r.*, u.display_name 
                     FROM " . $this->table . " r 
                     JOIN Users u ON r.user_id = u.user_id 
                     WHERE r.product_id = :product_id 
                     ORDER BY r.created_at DESC 
                     LIMIT :limit OFFSET :offset";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":product_id", $productId);
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
            $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
            $stmt->execute();

            return [
                'status' => 'success',
                'reviews' => $stmt->fetchAll(),
                'total' => $this->getProductReviewCount($productId)
            ];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getProductRatingStats($productId) {
        try {
            $query = "SELECT 
                        COUNT(*) as total_reviews,
                        AVG(rating) as average_rating,
                        SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                        SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                        SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                        SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                        SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                     FROM " . $this->table . " 
                     WHERE product_id = :product_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":product_id", $productId);
            $stmt->execute();

            return $stmt->fetch();
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function hasUserReviewed($userId, $productId) {
        $query = "SELECT COUNT(*) FROM " . $this->table . " 
                 WHERE user_id = :user_id AND product_id = :product_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":product_id", $productId);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }

    private function getProductReviewCount($productId) {
        $query = "SELECT COUNT(*) FROM " . $this->table . " 
                 WHERE product_id = :product_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":product_id", $productId);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
}