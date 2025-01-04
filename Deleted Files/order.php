<?php
// API Endpoint: /api/orders
// Supported Methods:
//   - GET: Retrieve order details or a list of user orders
//   - POST: Create a new order

try {
    if ($endpoint === 'orders') {
        $auth = authenticateRequest(); // Authenticate the user
        $order = new Order();

        switch ($requestMethod) {
            case 'GET':
                $orderId = $request[0] ?? null;

                if ($orderId) {
                    // Validate the order ID
                    if (!is_numeric($orderId)) {
                        http_response_code(400); // Bad Request
                        echo json_encode(['error' => 'Invalid order ID']);
                        exit;
                    }

                    // Retrieve order details
                    $response = $order->getOrderDetails((int)$orderId);
                    http_response_code(200); // OK
                    echo json_encode(['status' => 'success', 'data' => $response]);
                } else {
                    // Retrieve all user orders
                    $response = $order->getUserOrders($auth['user_id']);
                    http_response_code(200); // OK
                    echo json_encode(['status' => 'success', 'data' => $response]);
                }
                break;

            case 'POST':
                // Decode and validate the input data
                $data = json_decode(file_get_contents('php://input'), true);
                if (empty($data['cart_items']) || !is_array($data['cart_items'])) {
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'Invalid cart_items']);
                    exit;
                }

                if (empty($data['shipping_data']) || !is_array($data['shipping_data'])) {
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'Invalid shipping_data']);
                    exit;
                }

                // Optional: Validate the voucher code if provided
                $voucherCode = $data['voucher_code'] ?? null;

                // Create the order
                $response = $order->createOrder(
                    $auth['user_id'],
                    $data['cart_items'],
                    $data['shipping_data'],
                    $voucherCode
                );

                http_response_code(201); // Created
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
