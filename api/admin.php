<?php
// API Endpoints: /api/admin/*
// Supported Methods:
//   - GET: /dashboard - Get dashboard stats
//   - GET: /inventory - Get inventory report
//   - GET: /users - Get user analytics
//   - GET: /engagement - Get customer engagement
//   - GET: /logs - Get admin logs
//   - GET: /products - Get products
//   - GET: /transactions - Get transactions

try {
    $requestUri = $_SERVER['REQUEST_URI'];
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    
    // Parse the API endpoint
    $parts = explode('/', trim($requestUri, '/'));
    error_log('Parsed parts: ' . print_r($parts, true)); // Log the parsed parts

    // Adjust for base directory
    $baseDir = 'TMN3223-WAD-Project';
    if ($parts[0] === $baseDir) {
        array_shift($parts);
    }

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

    // Include or autoload the Admin class
    require_once '../includes/Admin.php'; // Adjust the path as needed

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
                    
                case 'products':
                    $response = isset($parts[3]) && $parts[3] === 'history'
                        ? $admin->getProductHistory()
                        : $admin->getProducts();
                    break;

                case 'transactions':
                    $startDate = $_GET['start_date'] ?? null;
                    $endDate = $_GET['end_date'] ?? null;
                    $limit = $_GET['limit'] ?? 100;
                    $response = $admin->getTransactions($startDate, $endDate, $limit);
                    break;
                    
                default:
                    throw new Exception('Invalid endpoint');
            }
            break;
        
        default:
            throw new Exception('Invalid request method');
    }

    // Send the response
    echo json_encode($response);

} catch (Exception $e) {
    error_log($e->getMessage()); // Log the error message
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => $e->getMessage()]);
}