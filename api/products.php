<?php
// API Endpoint: /api/products
// Supported Methods:
//   - GET: List products
//   - POST: Create a new product (admin only)
//   - PUT: Update an existing product (admin only)

try {
    if ($endpoint === 'products') {
        $product = new Product();
        switch ($requestMethod) {
            case 'GET':
                // Retrieve query parameters with default values
                $category_id = $_GET['category_id'] ?? null;
                $limit = filter_var($_GET['limit'] ?? 10, FILTER_VALIDATE_INT);
                $offset = filter_var($_GET['offset'] ?? 0, FILTER_VALIDATE_INT);
                
                // Validate limit and offset
                if ($limit === false || $offset === false) {
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'Invalid limit or offset']);
                    exit;
                }

                $response = $product->getProducts($category_id, $limit, $offset);
                http_response_code(200); // OK
                echo json_encode(['status' => 'success', 'data' => $response]);
                break;

            case 'POST':
                // Authenticate the request (admin only)
                authenticateRequest();
                
                // Decode and validate input data
                $data = json_decode(file_get_contents('php://input'), true);
                if (empty($data)) {
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'Invalid input data']);
                    exit;
                }

                $response = $product->createProduct($data);
                http_response_code(201); // Created
                echo json_encode(['status' => 'success', 'data' => $response]);
                break;

            case 'PUT':
                // Authenticate the request (admin only)
                authenticateRequest();

                // Validate product ID from the request path
                $productId = $request[0] ?? null;
                if (empty($productId) || !is_numeric($productId)) {
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'Invalid product ID']);
                    exit;
                }

                // Decode and validate input data
                $data = json_decode(file_get_contents('php://input'), true);
                if (empty($data)) {
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'Invalid input data']);
                    exit;
                }

                $response = $product->updateProduct($productId, $data);
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
