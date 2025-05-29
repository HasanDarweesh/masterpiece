<?php

session_start();
require_once '../../includes/database/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Initialize response array
$response = [
    'success' => true,
    'cart' => [],
    'total_price' => 0,
    'original_total' => 0,
    'discount_value' => 0,
    'coupon_applied' => false
];

// Get user_id from session
$user_id = $_SESSION['user_id'];

// Fetch pending order for the user - Include coupon information
$sql = "SELECT id, total_price, coupon_id FROM orders WHERE user_id = :user_id AND status = 'pending'";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// If there's no pending order, return empty response
if (!$order) {
    $response['success'] = false;
    $response['message'] = 'Your cart is empty.';
    echo json_encode($response);
    exit;
}

// Fetch cart items from order_items
$order_id = $order['id'];
$sql = "SELECT 
            order_items.product_id,
            order_items.quantity,
            order_items.price as item_price,
            products.name,
            products.image,
            products.price as current_price,
            (order_items.quantity * order_items.price) AS total_amount
        FROM order_items
        JOIN products ON order_items.product_id = products.id
        WHERE order_items.order_id = :order_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate original total price (before discount)
$originalTotalPrice = 0;
foreach ($cart as $item) {
    $originalTotalPrice += $item['total_amount'];

    $response['cart'][] = [
        'product_id' => $item['product_id'],
        'name' => $item['name'],
        'quantity' => $item['quantity'],
        'price' => $item['item_price'], // Use the price from order_items
        'total' => $item['total_amount'],
        'image' => $item['image']
    ];
}

// Set original total
$response['original_total'] = $originalTotalPrice;

// Check if a coupon is applied
if ($order['coupon_id'] !== null) {
    // Coupon is applied - use the discounted total from orders table
    $response['total_price'] = $order['total_price'];
    $response['discount_value'] = $originalTotalPrice - $order['total_price'];
    $response['coupon_applied'] = true;
    
    // Get coupon details for additional information if needed
    $stmt = $pdo->prepare("SELECT code, discount_value FROM coupons WHERE id = ?");
    $stmt->execute([$order['coupon_id']]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($coupon) {
        $response['coupon_code'] = $coupon['code'];
        $response['coupon_percentage'] = $coupon['discount_value'];
    }
} else {
    // No coupon applied - use original total
    $response['total_price'] = $originalTotalPrice;
    $response['discount_value'] = 0;
    $response['coupon_applied'] = false;
}

// Return response as JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>