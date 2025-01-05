<?php
session_start();

// Check if user is logged in and redirect accordingly
if (!isset($_SESSION['user_id'])) {
    header("Location: TMN3223-WAD-Project/public/publicHome.html");
    exit();
} 

// Redirect based on user's role
if ($_SESSION['role'] === 'member') {
    header("Location: TMN3223-WAD-Project/public/memberHome.php");
    exit();
} elseif ($_SESSION['role'] === 'admin') {
    header("Location: TMN3223-WAD-Project/public/adminDashboard.php");
    exit();
} else {
    header("Location: TMN3223-WAD-Project/public/publicHome.html");
    exit();
}

// Basic API setup
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Parse the request
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$endpoint = array_shift($request);

// Handle the request based on endpoint
try {
    switch($endpoint) {
        case 'adminDashboard':
            require 'adminDashboard.php';
            break;
        case 'adminTransactions':
            require 'adminTransactions.php';
            break;
        default:
            throw new Exception('Invalid endpoint');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
