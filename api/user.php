<?php
// API Endpoints: /api/user
// Supported Methods:
//   - GET: Fetch user details
//   - PUT: Update user details
//   - DELETE: Delete user account

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

try {
    if ($requestUri === '/api/user') {
        session_start();
        $user = new User();
        
        switch ($requestMethod) {
            case 'GET':
                if (!isset($_SESSION['user_id'])) {
                    http_response_code(401);
                    echo json_encode(['error' => 'Unauthorized']);
                    exit;
                }
                $userData = $user->getUserById($_SESSION['user_id']);
                echo json_encode(['status' => 'success', 'data' => $userData]);
                break;

            case 'PUT':
                if (!isset($_SESSION['user_id'])) {
                    http_response_code(401);
                    echo json_encode(['error' => 'Unauthorized']);
                    exit;
                }
                $data = json_decode(file_get_contents('php://input'), true);
                $response = $user->updateUser($_SESSION['user_id'], $data);
                echo json_encode($response);
                break;

            case 'DELETE':
                if (!isset($_SESSION['user_id'])) {
                    http_response_code(401);
                    echo json_encode(['error' => 'Unauthorized']);
                    exit;
                }
                $response = $user->logout();
                echo json_encode($response);
                break;

            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Invalid endpoint']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}