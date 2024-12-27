<?php
// Order.php - Order and transaction management
require_once 'config.php';
require_once 'Product.php';

class Order {
    private $conn;
    private $productObj;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->productObj = new Product();
    }

    public function createOrder($userId, $cartItems, $shippingData, $voucherCode = null) {
        try {
            $this->conn->beginTransaction();

            // Calculate totals
            $totalAmount = 0;
            $deliveryFee = 10.00; // Base delivery fee
            $taxRate = 0.06; // 6% tax rate

            // Calculate total amount from cart items
            foreach ($cartItems as $item) {
                $totalAmount += $item['price'] * $item['quantity'];
            }

            // Process voucher if provided
            $discountAmount = 0;
            if ($voucherCode) {
                $voucher = $this->getVoucher($voucherCode);
                if ($voucher && !$voucher['redeemed_by']) {
                    $discountAmount = $totalAmount * ($voucher['discount_percentage'] / 100);
                    $this->redeemVoucher($voucherCode, $userId); // Mark voucher as redeemed
                }
            }

            // Calculate final amounts
            $taxAmount = ($totalAmount - $discountAmount) * $taxRate;
            $finalTotal = $totalAmount - $discountAmount + $taxAmount + $deliveryFee;

            // Create transaction record
            $query = "INSERT INTO Transactions 
                     (user_id, total_amount, delivery_fee, tax_amount, payment_status, 
                      delivery_address, shipping_method, voucher_code) 
                     VALUES (:user_id, :total_amount, :delivery_fee, :tax_amount, 'pending', 
                      :delivery_address, :shipping_method, :voucher_code)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":total_amount", $finalTotal);
            $stmt->bindParam(":delivery_fee", $deliveryFee);
            $stmt->bindParam(":tax_amount", $taxAmount);
            $stmt->bindParam(":delivery_address", $shippingData['address']);
            $stmt->bindParam(":shipping_method", $shippingData['method']);
            $stmt->bindParam(":voucher_code", $voucherCode);
            $stmt->execute();
            $transactionId = $this->conn->lastInsertId();

            // Create transaction details and update product stock
            foreach ($cartItems as $item) {
                $this->createTransactionDetail($transactionId, $item);
                $this->productObj->updateStock($item['product_id'], $item['quantity'], 'subtract');
            }

            // Create order record
            $trackingNumber = $this->generateTrackingNumber();
            $estimatedDelivery = date('Y-m-d', strtotime('+3 days'));

            $query = "INSERT INTO Orders 
                     (transaction_id, tracking_number, status, estimated_delivery) 
                     VALUES (:transaction_id, :tracking_number, 'processing', :estimated_delivery)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":transaction_id", $transactionId);
            $stmt->bindParam(":tracking_number", $trackingNumber);
            $stmt->bindParam(":estimated_delivery", $estimatedDelivery);
            $stmt->execute();

            // Clear cart
            $this->clearCart($userId);

            $this->conn->commit();

            return [
                'status' => 'success',
                'message' => 'Order created successfully',
                'order_id' => $this->conn->lastInsertId(),
                'tracking_number' => $trackingNumber
            ];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function createTransactionDetail($transactionId, $item) {
        $query = "INSERT INTO Transaction_Details 
                 (transaction_id, product_id, quantity, price_per_item, subtotal) 
                 VALUES (:transaction_id, :product_id, :quantity, :price_per_item, :subtotal)";
        $stmt = $this->conn->prepare($query);
        $subtotal = $item['price'] * $item['quantity'];
        $stmt->bindParam(":transaction_id", $transactionId);
        $stmt->bindParam(":product_id", $item['product_id']);
        $stmt->bindParam(":quantity", $item['quantity']);
        $stmt->bindParam(":price_per_item", $item['price']);
        $stmt->bindParam(":subtotal", $subtotal);
        return $stmt->execute();
    }

    private function generateTrackingNumber() {
        return 'PL' . date('Ymd') . substr(uniqid(), -6);
    }

    public function getOrderDetails($orderId) {
        try {
            $query = "SELECT o.*, t.*, td.*, p.name as product_name 
                      FROM Orders o 
                      JOIN Transactions t ON o.transaction_id = t.transaction_id 
                      JOIN Transaction_Details td ON t.transaction_id = td.transaction_id 
                      JOIN Products p ON td.product_id = p.product_id 
                      WHERE o.order_id = :order_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":order_id", $orderId);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function updateOrderStatus($orderId, $status) {
        try {
            $query = "UPDATE Orders SET status = :status WHERE order_id = :order_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":order_id", $orderId);
            if ($stmt->execute()) {
                return ['status' => 'success', 'message' => 'Order status updated successfully'];
            }
            return ['status' => 'error', 'message' => 'Failed to update order status'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function clearCart($userId) {
        $query = "DELETE FROM Cart WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        return $stmt->execute();
    }

    private function getVoucher($voucherCode) {
        $query = "SELECT * FROM Vouchers 
                  WHERE voucher_code = :voucher_code 
                  AND expiry_date >= CURRENT_DATE 
                  AND redeemed_by IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":voucher_code", $voucherCode);
        $stmt->execute();
        return $stmt->fetch();
    }

    private function redeemVoucher($voucherCode, $userId) {
        $query = "UPDATE Vouchers 
                  SET redeemed_by = :user_id 
                  WHERE voucher_code = :voucher_code";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":voucher_code", $voucherCode);
        return $stmt->execute();
    }

    public function getUserOrders($userId) {
        try {
            $query = "SELECT o.order_id, o.tracking_number, o.status, o.estimated_delivery, 
                             t.total_amount, t.delivery_fee, t.tax_amount, t.voucher_code, 
                             td.product_id, td.quantity, td.price_per_item, td.subtotal, 
                             p.name AS product_name
                      FROM Orders o
                      JOIN Transactions t ON o.transaction_id = t.transaction_id
                      JOIN Transaction_Details td ON t.transaction_id = td.transaction_id
                      JOIN Products p ON td.product_id = p.product_id
                      WHERE t.user_id = :user_id
                      ORDER BY o.order_id DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $userId);
            $stmt->execute();

            $orders = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $orderId = $row['order_id'];
                if (!isset($orders[$orderId])) {
                    $orders[$orderId] = [
                        'order_id' => $row['order_id'],
                        'tracking_number' => $row['tracking_number'],
                        'status' => $row['status'],
                        'estimated_delivery' => $row['estimated_delivery'],
                        'total_amount' => $row['total_amount'],
                        'delivery_fee' => $row['delivery_fee'],
                        'tax_amount' => $row['tax_amount'],
                        'voucher_code' => $row['voucher_code'],
                        'items' => []
                    ];
                }
                $orders[$orderId]['items'][] = [
                    'product_id' => $row['product_id'],
                    'product_name' => $row['product_name'],
                    'quantity' => $row['quantity'],
                    'price_per_item' => $row['price_per_item'],
                    'subtotal' => $row['subtotal']
                ];
            }
            return array_values($orders);
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
