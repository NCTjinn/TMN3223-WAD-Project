<?php
// Redirect to publichome.html
header("Location: publichome.html");
exit();

// api/index.php - Main API endpoint handler
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config.php';
require_once '../User.php';
require_once '../Product.php';
require_once '../Order.php';
require_once '../Cart.php';
require_once '../Review.php';
require_once '../Reward.php';
require_once '../Admin.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Parse the request
$requestMethod = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$endpoint = array_shift($request);

// Initialize response array
$response = [];

// Authentication middleware
function authenticateRequest() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No authorization token provided']);
        exit();
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);
    // Implement your JWT verification here
    // Return user data if token is valid
    // For now, we'll return a dummy user
    return ['user_id' => 1, 'role' => 'admin'];
}

// Handle the request based on endpoint
try {
    switch($endpoint) {
        case 'auth':
            $user = new User();
            if ($requestMethod === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                if (isset($data['action'])) {
                    switch($data['action']) {
                        case 'login':
                            $response = $user->login($data['username'], $data['password']);
                            break;
                        case 'register':
                            $response = $user->register($data);
                            break;
                        default:
                            throw new Exception('Invalid action');
                    }
                }
            }
            break;

        case 'products':
            $product = new Product();
            switch($requestMethod) {
                case 'GET':
                    $category_id = $_GET['category_id'] ?? null;
                    $limit = $_GET['limit'] ?? 10;
                    $offset = $_GET['offset'] ?? 0;
                    $response = $product->getProducts($category_id, $limit, $offset);
                    break;
                case 'POST':
                    authenticateRequest(); // Only admin can create products
                    $data = json_decode(file_get_contents('php://input'), true);
                    $response = $product->createProduct($data);
                    break;
                case 'PUT':
                    authenticateRequest();
                    $productId = $request[0] ?? null;
                    $data = json_decode(file_get_contents('php://input'), true);
                    $response = $product->updateProduct($productId, $data);
                    break;
            }
            break;

        case 'cart':
            $auth = authenticateRequest();
            $cart = new Cart();
            switch($requestMethod) {
                case 'GET':
                    $response = $cart->getCartContents($auth['user_id']);
                    break;
                case 'POST':
                    $data = json_decode(file_get_contents('php://input'), true);
                    $response = $cart->addToCart($auth['user_id'], $data['product_id'], $data['quantity']);
                    break;
                case 'PUT':
                    $data = json_decode(file_get_contents('php://input'), true);
                    $response = $cart->updateQuantity($auth['user_id'], $data['product_id'], $data['quantity']);
                    break;
                case 'DELETE':
                    $productId = $request[0] ?? null;
                    $response = $cart->removeItem($auth['user_id'], $productId);
                    break;
            }
            break;

        case 'orders':
            $auth = authenticateRequest();
            $order = new Order();
            switch($requestMethod) {
                case 'GET':
                    $orderId = $request[0] ?? null;
                    if($orderId) {
                        $response = $order->getOrderDetails($orderId);
                    } else {
                        // Implement get user orders
                    }
                    break;
                case 'POST':
                    $data = json_decode(file_get_contents('php://input'), true);
                    $response = $order->createOrder($auth['user_id'], $data['cart_items'], $data['shipping_data'], $data['voucher_code'] ?? null);
                    break;
            }
            break;

        case 'reviews':
            $review = new Review();
            switch($requestMethod) {
                case 'GET':
                    $productId = $_GET['product_id'] ?? null;
                    $limit = $_GET['limit'] ?? 10;
                    $offset = $_GET['offset'] ?? 0;
                    $response = $review->getProductReviews($productId, $limit, $offset);
                    break;
                case 'POST':
                    $auth = authenticateRequest();
                    $data = json_decode(file_get_contents('php://input'), true);
                    $response = $review->createReview($auth['user_id'], $data['product_id'], $data['rating'], $data['comment']);
                    break;
            }
            break;

        case 'rewards':
            $auth = authenticateRequest();
            $reward = new Reward();
            switch($requestMethod) {
                case 'GET':
                    $response = $reward->getUserMissions($auth['user_id']);
                    break;
                case 'POST':
                    $data = json_decode(file_get_contents('php://input'), true);
                    if($auth['role'] === 'admin') {
                        $response = $reward->createMissionTemplate($data);
                    } else {
                        $response = $reward->completeMission($auth['user_id'], $data['reward_id']);
                    }
                    break;
            }
            break;

        case 'admin':
            $auth = authenticateRequest();
            if($auth['role'] !== 'admin') {
                throw new Exception('Unauthorized access');
            }
            $admin = new Admin();
            switch($requestMethod) {
                case 'GET':
                    $action = $request[0] ?? 'dashboard';
                    switch($action) {
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
                        case 'logs':
                            $response = $admin->getAdminLogs();
                            break;
                    }
                    break;
            }
            break;

        default:
            throw new Exception('Invalid endpoint');
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}