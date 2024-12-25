<?php
// Admin.php - Admin dashboard and management functionality
require_once 'config.php';

class Admin {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getDashboardStats() {
        try {
            $stats = [
                'total_users' => $this->getTotalUsers(),
                'total_orders' => $this->getTotalOrders(),
                'revenue_stats' => $this->getRevenueStats(),
                'top_products' => $this->getTopProducts(),
                'recent_orders' => $this->getRecentOrders(),
                'inventory_alerts' => $this->getLowStockProducts()
            ];

            return ['status' => 'success', 'data' => $stats];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function logAdminAction($adminId, $action) {
        try {
            $query = "INSERT INTO Admin_Actions_Log (admin_id, action) 
                     VALUES (:admin_id, :action)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":admin_id", $adminId);
            $stmt->bindParam(":action", $action);

            return $stmt->execute();
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getAdminLogs($startDate = null, $endDate = null, $limit = 100) {
        try {
            $query = "SELECT l.*, u.username 
                     FROM Admin_Actions_Log l 
                     JOIN Users u ON l.admin_id = u.user_id 
                     WHERE 1=1";

            if($startDate) {
                $query .= " AND l.timestamp >= :start_date";
            }
            if($endDate) {
                $query .= " AND l.timestamp <= :end_date";
            }

            $query .= " ORDER BY l.timestamp DESC LIMIT :limit";

            $stmt = $this->conn->prepare($query);
            
            if($startDate) $stmt->bindParam(":start_date", $startDate);
            if($endDate) $stmt->bindParam(":end_date", $endDate);
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function updateSalesSummary() {
        try {
            $today = date('Y-m-d');
            
            $query = "INSERT INTO Sales_Summary 
                    (date, total_orders, gross_sales, returns, net_sales, delivery_fee, tax) 
                    SELECT 
                        DATE(t.transaction_date) as date,
                        COUNT(*) as total_orders,
                        SUM(t.total_amount) as gross_sales,
                        0 as returns, -- Implement returns logic if needed
                        SUM(t.total_amount) as net_sales,
                        SUM(t.delivery_fee) as delivery_fee,
                        SUM(t.tax_amount) as tax
                    FROM Transactions t 
                    WHERE DATE(t.transaction_date) = :today 
                    GROUP BY DATE(t.transaction_date)
                    ON DUPLICATE KEY UPDATE 
                        total_orders = VALUES(total_orders),
                        gross_sales = VALUES(gross_sales),
                        net_sales = VALUES(net_sales),
                        delivery_fee = VALUES(delivery_fee),
                        tax = VALUES(tax)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":today", $today);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function getTotalUsers() {
        $query = "SELECT COUNT(*) as total FROM Users";
        $stmt = $this->conn->query($query);
        return $stmt->fetch()['total'];
    }

    private function getTotalOrders() {
        $query = "SELECT COUNT(*) as total FROM Orders";
        $stmt = $this->conn->query($query);
        return $stmt->fetch()['total'];
    }

    private function getRevenueStats() {
        $query = "SELECT 
                    SUM(total_amount) as total_revenue,
                    AVG(total_amount) as average_order_value,
                    COUNT(*) as order_count,
                    SUM(CASE 
                        WHEN transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
                        THEN total_amount ELSE 0 
                    END) as weekly_revenue,
                    SUM(CASE 
                        WHEN transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
                        THEN total_amount ELSE 0 
                    END) as monthly_revenue
                 FROM Transactions 
                 WHERE payment_status = 'successful'";
        
        $stmt = $this->conn->query($query);
        return $stmt->fetch();
    }

    private function getTopProducts($limit = 5) {
        $query = "SELECT 
                    p.product_id,
                    p.name,
                    p.price,
                    p.stock_quantity,
                    COUNT(td.product_id) as total_sales,
                    SUM(td.quantity) as units_sold,
                    SUM(td.subtotal) as revenue
                 FROM Products p
                 LEFT JOIN Transaction_Details td ON p.product_id = td.product_id
                 LEFT JOIN Transactions t ON td.transaction_id = t.transaction_id
                 WHERE t.transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
                 GROUP BY p.product_id
                 ORDER BY units_sold DESC
                 LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getRecentOrders($limit = 10) {
        $query = "SELECT 
                    o.order_id,
                    o.tracking_number,
                    o.status,
                    t.transaction_date,
                    t.total_amount,
                    u.username,
                    u.email
                 FROM Orders o
                 JOIN Transactions t ON o.transaction_id = t.transaction_id
                 JOIN Users u ON t.user_id = u.user_id
                 ORDER BY t.transaction_date DESC
                 LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getLowStockProducts($threshold = 10) {
        $query = "SELECT 
                    product_id,
                    name,
                    stock_quantity,
                    price
                 FROM Products
                 WHERE stock_quantity <= :threshold
                 ORDER BY stock_quantity ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getInventoryReport() {
        try {
            $query = "SELECT 
                        p.*,
                        pc.name as category_name,
                        COUNT(td.product_id) as total_sales,
                        COALESCE(SUM(td.quantity), 0) as units_sold
                     FROM Products p
                     LEFT JOIN Product_Categories pc ON p.category_id = pc.category_id
                     LEFT JOIN Transaction_Details td ON p.product_id = td.product_id
                     GROUP BY p.product_id
                     ORDER BY units_sold DESC";

            $stmt = $this->conn->query($query);
            return [
                'status' => 'success',
                'data' => $stmt->fetchAll()
            ];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getUserAnalytics() {
        try {
            $query = "SELECT 
                        COUNT(*) as total_users,
                        SUM(CASE WHEN created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
                            THEN 1 ELSE 0 END) as new_users_month,
                        AVG(points) as average_points,
                        COUNT(CASE WHEN role = 'member' THEN 1 END) as total_members,
                        COUNT(CASE WHEN role = 'admin' THEN 1 END) as total_admins
                     FROM Users";

            $stmt = $this->conn->query($query);
            return [
                'status' => 'success',
                'data' => $stmt->fetch()
            ];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getCustomerEngagement($days = 30) {
        try {
            $query = "SELECT 
                        COUNT(DISTINCT t.user_id) as purchasing_users,
                        COUNT(DISTINCT r.user_id) as reviewing_users,
                        COUNT(DISTINCT uf.user_id) as users_with_favorites,
                        AVG(r.rating) as average_rating
                     FROM Users u
                     LEFT JOIN Transactions t ON u.user_id = t.user_id 
                        AND t.transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL :days DAY)
                     LEFT JOIN Reviews r ON u.user_id = r.user_id
                     LEFT JOIN User_Favorites uf ON u.user_id = uf.user_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':days', $days, PDO::PARAM_INT);
            $stmt->execute();
            return [
                'status' => 'success',
                'data' => $stmt->fetch()
            ];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
?>
