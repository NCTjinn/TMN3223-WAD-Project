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
                $query .= " AND transaction_date >= ?";
            }
            if ($endDate) {
                $query .= " AND transaction_date <= ?";
            }
    
            $query .= " ORDER BY transaction_date DESC LIMIT ?";
    
            $stmt = $this->db->prepare($query);
    
            // Bind parameters
            if ($startDate && $endDate) {
                $stmt->bind_param("ssi", $startDate, $endDate, $limit); // "ssi" means 2 strings and an integer
            } elseif ($startDate) {
                $stmt->bind_param("si", $startDate, $limit); // "si" means 1 string and an integer
            } elseif ($endDate) {
                $stmt->bind_param("si", $endDate, $limit); // "si" means 1 string and an integer
            } else {
                $stmt->bind_param("i", $limit); // Only limit is passed
            }
    
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public static function handleRequest($db) {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                http_response_code(405); // Method Not Allowed
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
                return;
            }

            $admin = new Admin($db);
            
            // Fetch transaction data with possible start and end dates
            $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
            $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 100;

            $transactions = $admin->getTransactions($startDate, $endDate, $limit);
            if (is_array($transactions)) {
                echo json_encode(['status' => 'success', 'data' => $transactions]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No transactions found']);
            }
        } catch (Exception $e) {
            error_log('Request Handling Error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'An internal error occurred']);
        }
    }
}

// Handle the request
Admin::handleRequest($mysqli);
?>
