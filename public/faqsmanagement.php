<?php
header('Content-Type: application/json');

// Database credentials (these should be secured in a config file or environment variables)
$servername = "sql112.infinityfree.com";
$username = "if0_37979402";
$password = "tmn3223ncnhcds";
$dbname = "if0_37979402_pufflab";

// Establishing connection with the database
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($mysqli->connect_error) {
    error_log("Database connection failed: " . $mysqli->connect_error); // Log the error
    die(json_encode(["error" => "Database connection failed"])); // Return error in JSON
}

class FAQ {
    private $db;

    // Constructor to inject the database connection
    public function __construct($db) {
        $this->db = $db;
    }

    // Method to get the maximum FAQ ID (for creating new FAQ)
    public function getMaxId() {
        try {
            $query = "SELECT MAX(faq_id) as maxId FROM FAQs";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $maxId = $row['maxId'] ?? 0;
            return $maxId + 1;
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Method to fetch all FAQs
    public function fetchFAQs() {
        try {
            $query = "SELECT * FROM FAQs ORDER BY created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Method to add a new FAQ
    public function addFAQ($faqId, $question, $answer) {
        try {
            $query = "INSERT INTO FAQs (faq_id, question, answer) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("iss", $faqId, $question, $answer);
            $stmt->execute();
            return ['status' => 'success', 'message' => 'FAQ added successfully'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Method to update an existing FAQ
    public function updateFAQ($faqId, $question, $answer) {
        try {
            $query = "UPDATE FAQs SET question = ?, answer = ? WHERE faq_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ssi", $question, $answer, $faqId);
            $stmt->execute();
            return ['status' => 'success', 'message' => 'FAQ updated successfully'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Method to delete an FAQ
    public function deleteFAQ($faqId) {
        try {
            $query = "DELETE FROM FAQs WHERE faq_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $faqId);
            $stmt->execute();
            return ['status' => 'success', 'message' => 'FAQ deleted successfully'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Static method to handle the incoming HTTP requests
    public static function handleRequest($db) {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405); // Method Not Allowed
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
                return;
            }

            $faq = new FAQ($db);

            // Handle GET request to fetch FAQ data
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $action = isset($_GET['action']) ? $_GET['action'] : '';
                switch ($action) {
                    case 'getMaxId':
                        $maxId = $faq->getMaxId();
                        echo json_encode(['status' => 'success', 'data' => ['maxId' => $maxId]]);
                        break;

                    case 'fetch':
                        $faqs = $faq->fetchFAQs();
                        echo json_encode(['status' => 'success', 'data' => $faqs]);
                        break;

                    default:
                        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
                        break;
                }
            }

            // Handle POST request to add/update/delete FAQ
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = json_decode(file_get_contents("php://input"), true);

                $action = isset($data['action']) ? $data['action'] : '';

                switch ($action) {
                    case 'add':
                        $faqId = isset($data['faq_id']) ? $data['faq_id'] : null;
                        $question = isset($data['question']) ? $data['question'] : '';
                        $answer = isset($data['answer']) ? $data['answer'] : '';
                        if ($faqId && $question && $answer) {
                            $result = $faq->addFAQ($faqId, $question, $answer);
                            echo json_encode($result);
                        } else {
                            echo json_encode(['status' => 'error', 'message' => 'Missing data']);
                        }
                        break;

                    case 'update':
                        $faqId = isset($data['faq_id']) ? $data['faq_id'] : null;
                        $question = isset($data['question']) ? $data['question'] : '';
                        $answer = isset($data['answer']) ? $data['answer'] : '';
                        if ($faqId && $question && $answer) {
                            $result = $faq->updateFAQ($faqId, $question, $answer);
                            echo json_encode($result);
                        } else {
                            echo json_encode(['status' => 'error', 'message' => 'Missing data']);
                        }
                        break;

                    case 'delete':
                        $faqId = isset($data['faq_id']) ? $data['faq_id'] : null;
                        if ($faqId) {
                            $result = $faq->deleteFAQ($faqId);
                            echo json_encode($result);
                        } else {
                            echo json_encode(['status' => 'error', 'message' => 'Missing FAQ ID']);
                        }
                        break;

                    default:
                        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
                        break;
                }
            }
        } catch (Exception $e) {
            // Catch any errors and log them
            error_log('Request Handling Error: ' . $e->getMessage());
            http_response_code(500); // Internal Server Error
            echo json_encode(['status' => 'error', 'message' => 'An internal error occurred']);
        }
    }
}

// Handle the request
FAQ::handleRequest($mysqli);
?>
