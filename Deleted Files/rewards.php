<?php
// API Endpoint: /api/rewards
// Supported Methods:
//   - GET: Get user missions
//   - POST: Create mission template (admin) or complete mission (user)

try {
    if ($endpoint === 'rewards') {
        $auth = authenticateRequest(); // Authenticate user
        $reward = new Reward();

        switch ($requestMethod) {
            case 'GET':
                // Retrieve missions for the authenticated user
                $response = $reward->getUserMissions($auth['user_id']);
                http_response_code(200); // OK
                echo json_encode(['status' => 'success', 'data' => $response]);
                break;

            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);

                if ($auth['role'] === 'admin') {
                    // Admin creating a mission template
                    if (empty($data['title']) || empty($data['description']) || 
                        !isset($data['points']) || !is_numeric($data['points'])) {
                        http_response_code(400); // Bad Request
                        echo json_encode(['error' => 'Missing or invalid mission template data']);
                        exit;
                    }

                    $response = $reward->createMissionTemplate($data);
                    http_response_code(201); // Created
                } else {
                    // User completing a mission
                    if (empty($data['reward_id']) || !is_numeric($data['reward_id'])) {
                        http_response_code(400); // Bad Request
                        echo json_encode(['error' => 'Invalid reward_id']);
                        exit;
                    }

                    $response = $reward->completeMission($auth['user_id'], (int)$data['reward_id']);
                    http_response_code(200); // OK
                }

                echo json_encode(['status' => 'success', 'data' => $response]);
                break;

            default:
                // Method Not Allowed
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
                exit;
        }
    } else {
        // Endpoint Not Found
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        exit;
    }
} catch (Exception $e) {
    // Handle unexpected errors
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}