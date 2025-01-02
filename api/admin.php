<?php
// API Endpoints: /api/admin/*
// Supported Methods:
//   - GET: /dashboard - Get dashboard stats
//   - GET: /users - Get user analytics
//   - GET: /products - Get products
//   - GET: /transactions - Get transactions

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $requestUri = $_SERVER['REQUEST_URI'];
    $parsedUrl = parse_url($requestUri);
    $path = $parsedUrl['path'];
    
    // Add request method capture here
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    error_log('Request Method: ' . $requestMethod);
    
    // Parse the API endpoint
    $parts = explode('/', trim($path, '/'));
    error_log('Request URI: ' . $requestUri);
    error_log('Path: ' . $path);
    error_log('Parsed parts: ' . print_r($parts, true));

    // Adjust for base directory
    $baseDir = 'TMN3223-WAD-Project';
    if ($parts[0] === $baseDir) {
        array_shift($parts);
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
    // Clean endpoint name (remove .php if present)
    $endpoint = isset($parts[2]) ? str_replace('.php', '', $parts[2]) : '';
    error_log('Endpoint: ' . $endpoint);

    if (count($parts) < 3 || $parts[0] !== 'api' || $parts[1] !== 'admin') {
        throw new Exception('Invalid API endpoint');
    }

    require_once '../includes/Admin.php';
    $admin = new Admin();
    
    switch ($requestMethod) {
        case 'GET':
            switch ($endpoint) {
                case 'dashboard':
                    $data = $admin->getDashboardStats();
                    $response = ['status' => 'success', 'data' => $data];
                    break;
                    
                case 'users':
                    $data = $admin->getUserAnalytics();
                    $response = ['status' => 'success', 'data' => $data];
                    break;
                    
                case 'products':
                    $data = isset($parts[3]) && $parts[3] === 'history'
                        ? $admin->getProductHistory()
                        : $admin->getProducts();
                    $response = ['status' => 'success', 'data' => $data];
                    break;

                case 'transactions':
                    // Validate date inputs
                    $startDate = isset($_GET['start_date']) ? filter_var($_GET['start_date'], FILTER_SANITIZE_STRING) : null;
                    $endDate = isset($_GET['end_date']) ? filter_var($_GET['end_date'], FILTER_SANITIZE_STRING) : null;
                    $limit = isset($_GET['limit']) ? filter_var($_GET['limit'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 1000]]) : 100;
                        
                    if ($startDate && !strtotime($startDate)) {
                        throw new Exception('Invalid start date format');
                    }
                    if ($endDate && !strtotime($endDate)) {
                        throw new Exception('Invalid end date format');
                    }
                    
                    $data = $admin->getTransactions($startDate, $endDate, $limit);
                    $response = ['status' => 'success', 'data' => $data];
                    break;
                    
                default:
                    throw new Exception('Invalid endpoint');
            }
            break;
        
        case 'OPTIONS':
            // Handle preflight requests
            http_response_code(200);
            exit();
            
        default:
            throw new Exception('Invalid request method');
    }

    echo json_encode($response);

} catch (Exception $e) {
    error_log('API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'error' => $e->getMessage(),
        'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)
    ]);
}