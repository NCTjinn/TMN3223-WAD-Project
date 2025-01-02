<?php
// API Endpoint: /api/adminTransactions
// Supported Methods:
//   - GET: Get transaction records with optional date filtering and pagination

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

    // At this point, parts should be ['api', 'adminTransactions']
    if (count($parts) < 2 || $parts[0] !== 'api' || $parts[1] !== 'adminTransactions') {
        throw new Exception('Invalid API endpoint');
    }

    require_once '../includes/AdminTransactions.php';
    $admin = new Admin();
    
    switch ($requestMethod) {
        case 'GET':
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
        'error' => $e->getMessage()
    ]);
}