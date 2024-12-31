<?php
// API Endpoints: /api/admin/*
// Supported Methods:
//   - GET: /dashboard - Get dashboard stats
//   - GET: /inventory - Get inventory report
//   - GET: /users - Get user analytics
//   - GET: /engagement - Get customer engagement
//   - GET: /logs - Get admin logs

try {
    if ($endpoint === 'admin') {
        $auth = authenticateRequest(); // Authenticate user
        
        // Verify admin role
        if ($auth['role'] !== 'admin') {
            http_response_code(403); // Forbidden
            echo json_encode(['error' => 'Unauthorized access. Admin privileges required.']);
            exit;
        }

        $admin = new Admin();
        
        switch ($requestMethod) {
            case 'GET':
                $action = $request[0] ?? 'dashboard';
                
                switch ($action) {
                    case 'dashboard':
                        $response = $admin->getDashboardStats();
                        http_response_code(200); // OK
                        break;
                        
                    case 'inventory':
                        $response = $admin->getInventoryReport();
                        http_response_code(200); // OK
                        break;
                        
                    case 'users':
                        $response = $admin->getUserAnalytics();
                        http_response_code(200); // OK
                        break;
                        
                    case 'engagement':
                        $response = $admin->getCustomerEngagement();
                        http_response_code(200); // OK
                        break;
                        
                    case 'logs':
                        // Optional pagination parameters
                        $limit = $_GET['limit'] ?? 50;
                        $offset = $_GET['offset'] ?? 0;
                        
                        if (!is_numeric($limit) || !is_numeric($offset)) {
                            http_response_code(400); // Bad Request
                            echo json_encode(['error' => 'Invalid pagination parameters']);
                            exit;
                        }
                        
                        $response = $admin->getAdminLogs((int)$limit, (int)$offset);
                        http_response_code(200); // OK
                        break;

                    case 'notifications':
                        $response = $admin->getNotifications();
                        http_response_code(200);
                        break;
                        
                    default:
                        http_response_code(404); // Not Found
                        echo json_encode(['error' => 'Invalid admin endpoint']);
                        exit;
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