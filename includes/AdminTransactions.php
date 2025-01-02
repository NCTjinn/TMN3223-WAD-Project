<?php
// Admin.php - Admin dashboard and management functionality
require_once 'config.php';

class Admin {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getTransactions($startDate = null, $endDate = null, $limit = 100) {
        try {
            $query = "SELECT 
                        transaction_id, 
                        user_id, 
                        transaction_date, 
                        total_amount, 
                        delivery_fee, 
                        tax_amount, 
                        payment_status, 
                        shipping_method
                      FROM Transactions
                      WHERE 1=1";
            
            if ($startDate) {
                $query .= " AND transaction_date >= :start_date";
            }
            if ($endDate) {
                $query .= " AND transaction_date <= :end_date";
            }
    
            $query .= " ORDER BY transaction_date DESC LIMIT :limit";
    
            $stmt = $this->conn->prepare($query);
    
            if ($startDate) $stmt->bindParam(":start_date", $startDate);
            if ($endDate) $stmt->bindParam(":end_date", $endDate);
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
    
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
?>
