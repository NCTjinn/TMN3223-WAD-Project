<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "PuffLab";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents("php://input"), true);

switch ($action) {
    case 'getMaxId':
        $sql = "SELECT MAX(faq_id) as maxId FROM FAQs";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $maxId = $row['maxId'] ?? 0;
        echo json_encode(["maxId" => $maxId + 1]);
        break;

    case 'fetch':
        $sql = "SELECT * FROM FAQs ORDER BY created_at DESC";
        $result = $conn->query($sql);
        
        $faqs = [];
        while ($row = $result->fetch_assoc()) {
            $faqs[] = $row;
        }
        echo json_encode($faqs);
        break;

    case 'add':
        $question = $conn->real_escape_string($data['question']);
        $answer = $conn->real_escape_string($data['answer']);
        $faqId = $conn->real_escape_string($data['faq_id']);
        
        $sql = "INSERT INTO FAQs (faq_id, question, answer) VALUES ($faqId, '$question', '$answer')";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "FAQ added successfully"]);
        } else {
            echo json_encode(["error" => $conn->error]);
        }
        break;

    case 'update':
        $id = $conn->real_escape_string($data['faq_id']);
        $question = $conn->real_escape_string($data['question']);
        $answer = $conn->real_escape_string($data['answer']);
        
        $sql = "UPDATE FAQs SET question = '$question', answer = '$answer' WHERE faq_id = '$id'";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "FAQ updated successfully"]);
        } else {
            echo json_encode(["error" => $conn->error]);
        }
        break;

    case 'delete':
        $id = $conn->real_escape_string($data['faq_id']);
        
        $sql = "DELETE FROM FAQs WHERE faq_id = '$id'";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "FAQ deleted successfully"]);
        } else {
            echo json_encode(["error" => $conn->error]);
        }
        break;

    default:
        echo json_encode(["error" => "Invalid action"]);
}

$conn->close();
?>