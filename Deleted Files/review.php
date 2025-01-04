<?php
// API Endpoint: /api/reviews
// Supported Methods:
//   - GET: Get product reviews
//   - POST: Create review

try {
    if ($endpoint === 'reviews') {
        $review = new Review();

        switch ($requestMethod) {
            case 'GET':
                // Get reviews for a specific product
                $productId = $_GET['product_id'] ?? null;
                $limit = $_GET['limit'] ?? 10;
                $offset = $_GET['offset'] ?? 0;

                if (empty($productId) || !is_numeric($productId)) {
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'Invalid product_id']);
                    exit;
                }

                $response = $review->getProductReviews((int)$productId, (int)$limit, (int)$offset);
                http_response_code(200); // OK
                echo json_encode(['status' => 'success', 'data' => $response]);
                break;

            case 'POST':
                // Create a new review
                $auth = authenticateRequest(); // Authenticate user
                $data = json_decode(file_get_contents('php://input'), true);

                if (empty($data['product_id']) || !is_numeric($data['product_id']) ||
                    !isset($data['rating']) || !is_numeric($data['rating']) ||
                    empty($data['comment'])) {
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'Invalid product_id, rating, or comment']);
                    exit;
                }

                $response = $review->createReview(
                    $auth['user_id'],
                    (int)$data['product_id'],
                    (int)$data['rating'],
                    $data['comment']
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