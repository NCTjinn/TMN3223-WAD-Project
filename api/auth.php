<?php
// API Endpoints: /api/auth
// Supported Methods:
//   - POST: Login, Register

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

try {
    switch ($requestUri) {
        case '/api/auth':
            if ($requestMethod !== 'POST') {
                http_response_code(405); // Method Not Allowed
                echo json_encode(['error' => 'Only POST requests are allowed']);
                exit;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['action'])) {
                $user = new User();
                switch ($data['action']) {
                    case 'login':
                        $response = $user->login($data['username'], $data['password']);
                        echo json_encode(['status' => 'success', 'data' => $response]);
                        break;
                    case 'register':
                        $response = $user->register($data);
                        echo json_encode(['status' => 'success', 'data' => $response]);
                        break;
                    default:
                        http_response_code(400); // Bad Request
                        echo json_encode(['error' => 'Invalid action']);
                }
            } else {
                http_response_code(400); // Bad Request
                echo json_encode(['error' => 'Action not specified']);
            }
            break;
        default:
            http_response_code(404); // Not Found
            echo json_encode(['error' => 'Invalid endpoint']);
    }
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => $e->getMessage()]);
}
