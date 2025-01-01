<?php
// API Endpoints: /api/admin/*
// Supported Methods:
//   - GET: /dashboard - Get dashboard stats
//   - GET: /inventory - Get inventory report
//   - GET: /users - Get user analytics
//   - GET: /engagement - Get customer engagement
//   - GET: /logs - Get admin logs

try {
    $requestUri = $_SERVER['REQUEST_URI'];
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    
    // Parse the API endpoint
    $parts = explode('/', trim($requestUri, '/'));
    if (count($parts) < 3 || $parts[0] !== 'api' || $parts[1] !== 'admin') {
        throw new Exception('Invalid API endpoint');
    }
    
    // Temporarily bypass user authentication
    // $auth = authenticateRequest();
    // if (!$auth) {
    //     http_response_code(401); // Unauthorized
    //     echo json_encode(['error' => 'Authentication required']);
    //     exit;
    // }
    
    // Verify admin role
    // if ($auth['role'] !== 'admin') {
    //     http_response_code(403); // Forbidden
    //     echo json_encode(['error' => 'Unauthorized access. Admin privileges required.']);
    //     exit;
    // }

    $endpoint = $parts[2];
    $admin = new Admin();
    
    switch ($requestMethod) {
        case 'GET':
            switch ($endpoint) {
                case 'dashboard':
                    $response = $admin->getDashboardStats();
                    break;
                    
                case 'inventory':
                    $response = $admin->getInventoryReport();
                    break;
                    
                case 'users':
                    $response = $admin->getUserAnalytics();
                    break;
                    
                case 'engagement':
                    $response = $admin->getCustomerEngagement();
                    break;
                    
                case 'notifications':
                    $response = isset($parts[3]) && $parts[3] === 'unread' 
                        ? $admin->getUnreadNotifications()
                        : $admin->getNotifications();
                    break;
                    
                case 'products':
                    $response = isset($parts[3]) && $parts[3] === 'history'
                        ? $admin->getProductHistory()
                        : $admin->getProducts();
                    break;
                    
                default:
                    throw new Exception('Invalid endpoint');
            }
            break;
            
        // Handle other HTTP methods similarly
        default:
            throw new Exception('Method not allowed');
    }
    
    // Send response
    header('Content-Type: application/json');
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>