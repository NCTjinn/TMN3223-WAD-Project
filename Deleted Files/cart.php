<?php
// API Endpoint: /api/cart
// Supported Methods:
//   - GET: Retrieve cart contents
//   - POST: Add an item to the cart
//   - PUT: Update item quantity
//   - DELETE: Remove an item from the cart

try {
    if ($endpoint === 'cart') {
        $auth = authenticateRequest(); // Authenticate user
        $cart = new Cart();

        switch ($requestMethod) {
            case 'GET':
                // Retrieve cart contents for the authenticated user
                $response = $cart->getCartContents($auth['user_id']);
                http_response_code(200); // OK
                echo json_encode(['status' => 'success', 'data' => $response]);
                break;

            case 'POST':
                // Add a product to the cart
                $data = json_decode(file_get_contents('php://input'), true);
                if (empty($data['product_id']) || empty($data['quantity']) || !is_numeric($data['quantity'])) {
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'Invalid product_id or quantity']);
                    exit;
                }

                $response = $cart->addToCart($auth['user_id'], $data['product_id'], (int)$data['quantity']);
                http_response_code(201); // Created
                echo json_encode(['status' => 'success', 'data' => $response]);
                break;

            case 'PUT':
                // Update product quantity in the cart
                $data = json_decode(file_get_contents('php://input'), true);
                if (empty($data['product_id']) || empty($data['quantity']) || !is_numeric($data['quantity'])) {
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'Invalid product_id or quantity']);
                    exit;
                }

                $response = $cart->updateQuantity($auth['user_id'], $data['product_id'], (int)$data['quantity']);
                http_response_code(200); // OK
                echo json_encode(['status' => 'success', 'data' => $response]);
                break;

            case 'DELETE':
                // Remove an item from the cart
                $productId = $request[0] ?? null;
                if (empty($productId) || !is_numeric($productId)) {
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'Invalid product_id']);
                    exit;
                }

                $response = $cart->removeItem($auth['user_id'], (int)$productId);
                http_response_code(200); // OK
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
