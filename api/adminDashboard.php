<?php
// API Endpoint: /api/adminDashboard
// Supported Methods:
//   - GET: Get dashboard stats

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $requestUri = $_SERVER['REQUEST_URI'];
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    
    // Log the request URI and method
    error_log('Request URI: ' . $requestUri);
    error_log('Request Method: ' . $requestMethod);
    
    // Parse the API endpoint
    $parts = explode('/', trim($requestUri, '/'));
    error_log('Parsed parts: ' . print_r($parts, true)); // Log the parsed parts

    // Adjust for base directory
    $baseDir = 'TMN3223-WAD-Project';
    if ($parts[0] === $baseDir) {
        array_shift($parts);
    }
    error_log('Adjusted parts: ' . print_r($parts, true)); // Log the adjusted parts

    if (count($parts) < 2 || $parts[0] !== 'api' || $parts[1] !== 'adminDashboard') {
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
    require_once '../includes/AdminDashboard.php'; // Adjust the path as needed

    $admin = new Admin();
    
    if ($requestMethod === 'GET') {
        $response = $admin->getDashboardStats();
    } else {
        throw new Exception('Invalid request method');
    }

    // Send the response
    echo json_encode($response);

} catch (Exception $e) {
    error_log($e->getMessage()); // Log the error message
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => $e->getMessage()]);
}