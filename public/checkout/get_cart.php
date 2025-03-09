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
];

// Get user_id from session
$user_id = $_SESSION['user_id'];

// Fetch pending order for the user
$sql = "SELECT id FROM orders WHERE user_id = :user_id AND status = 'pending'";
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
            order_items.price,
            products.name,
            products.image,
            products.price,
            (total_price) AS total_amount
        FROM order_items
        JOIN products ON order_items.product_id = products.id
        JOIN orders ON order_items.order_id = orders.id
        WHERE order_items.order_id = :order_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total price and build cart items response
$totalPrice = 0;
foreach ($cart as $item) {
    $totalPrice += $item['total_amount'];

    $response['cart'][] = [
        'product_id' => $item['product_id'],
        'name' => $item['name'],
        'quantity' => $item['quantity'],
        'price' => $item['price'],
        'total' => $item['total_amount'],
        'image' => $item['image']
    ];
}

// Set total price in response
$response['total_price'] = $totalPrice;

// Return response as JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
