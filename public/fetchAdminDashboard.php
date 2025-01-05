<?php
header('Content-Type: application/json');

$servername = "sql112.infinityfree.com";
$username = "if0_37979402";
$password = "tmn3223ncnhcds";
$dbname = "if0_37979402_pufflab";

$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($mysqli->connect_error) {
    error_log("Database connection failed: " . $mysqli->connect_error);
    die(json_encode(["error" => "Database connection failed"]));
}

class Admin {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getDashboardStats() {
        return [
            'status' => 'success',
            'data' => [
                'revenue_stats' => $this->getRevenueStats(),
                'orderStats' => $this->getOrderStats(),
                'categoryRevenue' => $this->getCategoryRevenue(),
                'salesTrend' => [
                    'daily' => $this->getSalesTrend('daily'),
                    'weekly' => $this->getSalesTrend('weekly'),
                    'monthly' => $this->getSalesTrend('monthly')
                ],
                'topProducts' => $this->getTopProducts(5),
                'total_customers' => $this->getTotalUsers(),
                'topCategory' => $this->getTopCategory($this->getCategoryRevenue()),
                'average_order_value' => $this->getAverageOrderValue(),
                'periodRevenue' => [
                    'daily' => $this->getPeriodRevenue('daily'),
                    'weekly' => $this->getPeriodRevenue('weekly'),
                    'monthly' => $this->getPeriodRevenue('monthly')
                ]
            ]
        ];
    }

    public static function handleRequest($db) {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                http_response_code(405); // Method Not Allowed
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
                return;
            }

            $admin = new Admin($db);
            $response = $admin->getDashboardStats();
            echo json_encode($response);
        } catch (Exception $e) {
            error_log('Request Handling Error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'An internal error occurred']);
        }
    }

    private function executeQuery($query, $params = [], $types = "") {
        $stmt = $this->db->prepare($query);
        if ($stmt === false) {
            error_log("Query preparation failed: " . $this->db->error);
            return null;
        }
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    private function getRevenueStats() {
        $query = "SELECT 
                    SUM(total_amount) AS total_revenue,
                    AVG(total_amount) AS average_order_value,
                    COUNT(*) AS order_count,
                    SUM(CASE WHEN transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) THEN total_amount ELSE 0 END) AS weekly_revenue,
                    SUM(CASE WHEN transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) THEN total_amount ELSE 0 END) AS monthly_revenue
                  FROM Transactions 
                  WHERE payment_status = 'successful'";
        $result = $this->executeQuery($query);
        return $result[0] ?? [];
    }

    private function getOrderStats() {
        $query = "SELECT 
                    SUM(CASE WHEN shipping_method = 'dine_in' THEN 1 ELSE 0 END) AS dineIn,
                    SUM(CASE WHEN shipping_method = 'takeaway' THEN 1 ELSE 0 END) AS takeaway,
                    SUM(CASE WHEN shipping_method = 'delivery' THEN 1 ELSE 0 END) AS delivery
                  FROM Transactions";
        $result = $this->executeQuery($query);
        return $result[0] ?? [];
    }

    private function getCategoryRevenue() {
        $query = "SELECT 
                    pc.name,
                    SUM(td.subtotal) AS revenue
                  FROM Transaction_Details td
                  JOIN Products p ON td.product_id = p.product_id
                  JOIN Product_Categories pc ON p.category_id = pc.category_id
                  GROUP BY pc.category_id";
        $results = $this->executeQuery($query);
        $revenue = [];
        foreach ($results as $row) {
            $revenue[strtolower($row['name'])] = floatval($row['revenue']);
        }
        return $revenue;
    }

    private function getSalesTrend($period) {
        $query = match ($period) {
            'weekly' => "SELECT DATE_FORMAT(transaction_date, '%Y-%m-%d') AS date, SUM(total_amount) AS amount
                         FROM Transactions 
                         WHERE transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
                         GROUP BY DATE(transaction_date)",
            'monthly' => "SELECT DATE_FORMAT(transaction_date, '%Y-%m-%d') AS date, SUM(total_amount) AS amount
                          FROM Transactions 
                          WHERE transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
                          GROUP BY DATE(transaction_date)",
            default => "SELECT HOUR(transaction_date) AS hour, SUM(total_amount) AS amount
                        FROM Transactions 
                        WHERE DATE(transaction_date) = CURRENT_DATE
                        GROUP BY HOUR(transaction_date)",
        };

        $results = $this->executeQuery($query);
        return [
            'labels' => array_column($results, $period === 'daily' ? 'hour' : 'date'),
            'values' => array_column($results, 'amount')
        ];
    }

    private function getPeriodRevenue($period) {
        $query = match ($period) {
            'weekly' => "SELECT SUM(total_amount) AS revenue FROM Transactions WHERE transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 DAY)",
            'monthly' => "SELECT SUM(total_amount) AS revenue FROM Transactions WHERE transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 29 DAY)",
            default => "SELECT SUM(total_amount) AS revenue FROM Transactions WHERE DATE(transaction_date) = CURRENT_DATE",
        };
        $result = $this->executeQuery($query);
        return $result[0]['revenue'] ?? 0;
    }

    private function getTotalUsers() {
        $query = "SELECT COUNT(*) AS total FROM Users";
        $result = $this->executeQuery($query);
        return $result[0]['total'] ?? 0;
    }

    private function getTopCategory($categoryRevenue) {
        $topCategory = array_keys($categoryRevenue, max($categoryRevenue));
        return [
            'name' => ucfirst($topCategory[0]),
            'revenue' => $categoryRevenue[$topCategory[0]]
        ];
    }

    private function getAverageOrderValue() {
        $query = "SELECT AVG(total_amount) AS average_order_value FROM Transactions WHERE payment_status = 'successful'";
        $result = $this->executeQuery($query);
        return $result[0]['average_order_value'] ?? 0;
    }

    private function getTopProducts($limit) {
        $query = "SELECT 
                    p.product_id, p.name, p.price, p.stock_quantity,
                    COUNT(td.product_id) AS total_sales,
                    SUM(td.quantity) AS units_sold,
                    SUM(td.subtotal) AS revenue
                  FROM Products p
                  LEFT JOIN Transaction_Details td ON p.product_id = td.product_id
                  LEFT JOIN Transactions t ON td.transaction_id = t.transaction_id
                  GROUP BY p.product_id
                  ORDER BY units_sold DESC
                  LIMIT ?";
        $results = $this->executeQuery($query, [$limit], "i");
        return $results;
    }
}

// Handle the request
Admin::handleRequest($mysqli);
