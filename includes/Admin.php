<?php
// Admin.php - Admin dashboard and management functionality
require_once 'config.php';

class Admin {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getDashboardStats() {
        try {
            $orderStats = $this->getOrderStats();
            $categoryRevenue = $this->getCategoryRevenue();
            $salesTrendDaily = $this->getSalesTrend('daily');
            $salesTrendWeekly = $this->getSalesTrend('weekly');
            $salesTrendMonthly = $this->getSalesTrend('monthly');
            $topProducts = $this->getTopProducts(5);
            $shippingMethodStats = $this->getShippingMethodStats();
            
            $data = [
                'revenue_stats' => $this->getRevenueStats(),
                'orderStats' => $orderStats,
                'categoryRevenue' => $categoryRevenue,
                'salesTrend' => [
                    'daily' => $salesTrendDaily,
                    'weekly' => $salesTrendWeekly,
                    'monthly' => $salesTrendMonthly
                ],
                'topProducts' => $topProducts,
                'total_customers' => $this->getTotalUsers(),
                'topCategory' => $this->getTopCategory($categoryRevenue),
                'shippingMethodStats' => $shippingMethodStats,
                'average_order_value' => $this->getAverageOrderValue(),
                'periodRevenue' => [
                    'daily' => $this->getPeriodRevenue('daily'),
                    'weekly' => $this->getPeriodRevenue('weekly'),
                    'monthly' => $this->getPeriodRevenue('monthly')
                ]
            ];
    
            // Debugging log
            error_log('Dashboard stats data: ' . print_r($data, true));
    
            return [
                'status' => 'success',
                'data' => $data
            ];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function getShippingMethodStats() {
        $query = "SELECT 
            shipping_method,
            COUNT(*) as count,
            SUM(total_amount) as total_revenue
            FROM Transactions
            WHERE transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
            GROUP BY shipping_method";
        
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getTopCategory($categoryRevenue) {
        $topCategory = array_keys($categoryRevenue, max($categoryRevenue));
        return [
            'name' => ucfirst($topCategory[0]),
            'revenue' => $categoryRevenue[$topCategory[0]]
        ];
    }
    
    private function getOrderStats() {
        $query = "SELECT 
                    SUM(CASE WHEN shipping_method = 'dine_in' THEN 1 ELSE 0 END) as dineIn,
                    SUM(CASE WHEN shipping_method = 'takeaway' THEN 1 ELSE 0 END) as takeaway,
                    SUM(CASE WHEN shipping_method = 'delivery' THEN 1 ELSE 0 END) as delivery
                  FROM Transactions";
        
        $stmt = $this->conn->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'dineIn' => $result['dineIn'] ?? 0,
            'takeaway' => $result['takeaway'] ?? 0,
            'delivery' => $result['delivery'] ?? 0
        ];
    }
    
    private function getCategoryRevenue() {
        $query = "SELECT 
            pc.name,
            SUM(td.subtotal) as revenue
            FROM Transaction_Details td
            JOIN Products p ON td.product_id = p.product_id
            JOIN Product_Categories pc ON p.category_id = pc.category_id
            GROUP BY pc.category_id";
        
        $stmt = $this->conn->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $revenue = [];
        foreach ($results as $row) {
            $revenue[strtolower($row['name'])] = floatval($row['revenue']);
        }
        
        return $revenue;
    }
    private function getSalesTrend($period = 'daily') {
        // Log current date for debugging
        error_log('Current date: ' . date('Y-m-d H:i:s'));
        
        switch ($period) {
            case 'weekly':
                $query = "SELECT 
                    DATE_FORMAT(d.date, '%Y-%m-%d') as date,
                    COALESCE(SUM(t.total_amount), 0) as amount
                    FROM (
                        SELECT DATE_SUB(CURRENT_DATE, INTERVAL n DAY) as date
                        FROM (
                            SELECT 0 as n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 
                            UNION SELECT 4 UNION SELECT 5 UNION SELECT 6
                        ) days
                    ) d
                    LEFT JOIN Transactions t ON DATE(t.transaction_date) = d.date
                    GROUP BY d.date
                    ORDER BY d.date";
                break;
            case 'monthly':
                $query = "SELECT 
                    DATE_FORMAT(d.date, '%Y-%m-%d') as date,
                    COALESCE(SUM(t.total_amount), 0) as amount
                    FROM (
                        SELECT DATE_SUB(CURRENT_DATE, INTERVAL n DAY) as date
                        FROM (
                            WITH RECURSIVE numbers AS (
                                SELECT 0 as n 
                                UNION ALL 
                                SELECT n + 1 FROM numbers WHERE n < 29
                            )
                            SELECT n FROM numbers
                        ) days
                    ) d
                    LEFT JOIN Transactions t ON DATE(t.transaction_date) = d.date
                    GROUP BY d.date
                    ORDER BY d.date";
                break;
            case 'daily':
            default:
                $query = "SELECT 
                    DATE_FORMAT(h.hour, '%H:00') as hour,
                    COALESCE(SUM(t.total_amount), 0) as amount
                    FROM (
                        SELECT DATE_ADD(
                            DATE_FORMAT(CURRENT_DATE, '%Y-%m-%d 00:00:00'),
                            INTERVAL n HOUR
                        ) as hour
                        FROM (
                            WITH RECURSIVE hours AS (
                                SELECT 0 as n 
                                UNION ALL 
                                SELECT n + 1 FROM hours WHERE n < 23
                            )
                            SELECT n FROM hours
                        ) hours
                    ) h
                    LEFT JOIN Transactions t ON DATE(t.transaction_date) = CURRENT_DATE 
                        AND HOUR(t.transaction_date) = HOUR(h.hour)
                    GROUP BY h.hour
                    ORDER BY h.hour";
                break;
        }
    
        error_log('Executing query: ' . $query);
        $stmt = $this->conn->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log('Query results: ' . print_r($results, true));
    
        if (empty($results)) {
            return ['labels' => [], 'values' => []];
        }
    
        return [
            'labels' => array_column($results, $period === 'daily' ? 'hour' : 'date'),
            'values' => array_column($results, 'amount')
        ];
    }

    private function getPeriodRevenue($period = 'daily') {
        switch ($period) {
            case 'weekly':
                $query = "SELECT SUM(total_amount) as revenue
                          FROM Transactions
                          WHERE transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 DAY) AND transaction_date <= CURRENT_DATE";
                break;
            case 'monthly':
                $query = "SELECT SUM(total_amount) as revenue
                          FROM Transactions
                          WHERE transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 29 DAY) AND transaction_date <= CURRENT_DATE";
                break;
            case 'daily':
            default:
                $query = "SELECT SUM(total_amount) as revenue
                          FROM Transactions
                          WHERE DATE(transaction_date) = CURRENT_DATE";
                break;
        }
    
        // Log the query for debugging
        error_log('Executing query: ' . $query);
    
        $stmt = $this->conn->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Log the result for debugging
        error_log('Query result: ' . print_r($result, true));
    
        return $result['revenue'] ?? 0;
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
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    private function getAverageOrderValue() {
        $query = "SELECT AVG(total_amount) as average_order_value FROM Transactions WHERE payment_status = 'successful'";
        $stmt = $this->conn->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC)['average_order_value'];
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
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
                 GROUP BY p.product_id
                 ORDER BY units_sold DESC
                 LIMIT :limit";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserAnalytics() {
        try {
            // Query to fetch user analytics
            $query = "
                SELECT 
                    COUNT(*) AS total_users,
                    SUM(CASE WHEN created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS new_users_month,
                    AVG(points) AS average_points,
                    COUNT(CASE WHEN role = 'public' THEN 1 END) AS public_users,
                    COUNT(CASE WHEN role = 'member' THEN 1 END) AS members,
                    COUNT(CASE WHEN role = 'admin' THEN 1 END) AS admins,
                    MAX(points) AS highest_points,
                    MIN(points) AS lowest_points
                FROM Users
            ";
    
            $stmt = $this->conn->query($query);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return [
                'status' => 'success',
                'data' => $data
            ];
        } catch (PDOException $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function getProducts() {
        $query = "SELECT * FROM Products";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductHistory() {
        $query = "SELECT * FROM Product_History ORDER BY deleted_at DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addProduct($data) {
        $query = "INSERT INTO Products (name, price, stock, description, image) VALUES (:name, :price, :stock, :description, :image)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':stock', $data['stock']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':image', $data['image']);
        $stmt->execute();
        return ['status' => 'success'];
    }

    public function updateProduct($productId, $data) {
        $query = "UPDATE Products SET name = :name, price = :price, stock = :stock, description = :description, image = :image WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':stock', $data['stock']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':image', $data['image']);
        $stmt->bindParam(':id', $productId);
        $stmt->execute();
        return ['status' => 'success'];
    }

    public function deleteProduct($productId) {
        $query = "DELETE FROM Products WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $productId);
        $stmt->execute();
        return ['status' => 'success'];
    }

    public function restoreProduct($productId) {
        $query = "INSERT INTO Products SELECT * FROM Product_History WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $productId);
        $stmt->execute();
        return ['status' => 'success'];
    }

    public function getMembers() {
        $query = "SELECT * FROM Members";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMember($data) {
        $query = "INSERT INTO Members (username, points, totalSpent) VALUES (:username, :points, :totalSpent)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':points', $data['points']);
        $stmt->bindParam(':totalSpent', $data['totalSpent']);
        $stmt->execute();
        return ['status' => 'success'];
    }

    public function updateMember($memberId, $data) {
        $query = "UPDATE Members SET username = :username, points = :points, totalSpent = :totalSpent WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':points', $data['points']);
        $stmt->bindParam(':totalSpent', $data['totalSpent']);
        $stmt->bindParam(':id', $memberId);
        $stmt->execute();
        return ['status' => 'success'];
    }

    public function deleteMember($memberId) {
        $query = "DELETE FROM Members WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $memberId);
        $stmt->execute();
        return ['status' => 'success'];
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
