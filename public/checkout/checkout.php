<?php

session_start();
require_once '../../includes/database/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch the pending order for the user, if it exists
$sql = "SELECT id, total_price, coupon_id FROM orders WHERE user_id = :user_id AND status = 'pending'";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Your cart is empty.']);
    exit;
}

$order_id = $order['id'];

// Fetch the products in the order
$sql = "SELECT 
            order_items.product_id,
            order_items.quantity,
            order_items.price,
            products.name,
            products.image,
            products.price,
            (order_items.quantity * products.price) AS total_amount
        FROM order_items
        JOIN products ON order_items.product_id = products.id
        WHERE order_items.order_id = :order_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the original total price (before any discount)
$originalTotalPrice = 0;
$response = [
    'success' => true,
    'cart' => [],
    'total_price' => 0,
];

foreach ($cart as $item) {
    $originalTotalPrice += $item['total_amount'];

    $response['cart'][] = [
        'product_id' => $item['product_id'],
        'name' => $item['name'],
        'quantity' => $item['quantity'],
        'price' => $item['price'],
        'total' => $item['total_amount'],
        'image' => $item['image']
    ];
}

// Get phone number and address from the POST request
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';

// Check if a coupon was applied to this order
$finalTotalPrice = $originalTotalPrice; // Default to original price

if ($order['coupon_id'] !== null) {
    // If coupon was applied, use the already calculated total_price from the order
    $finalTotalPrice = $order['total_price'];
} else {
    // If no coupon applied, update the total price to the calculated original price
    // This ensures the total_price in orders table is updated with the current cart total
    $sql = "UPDATE orders SET total_price = :total_price WHERE id = :order_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':total_price', $originalTotalPrice, PDO::PARAM_STR);
    $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $finalTotalPrice = $originalTotalPrice;
}

// Update the status, phone number, and address in the orders table
// DO NOT update total_price here if coupon was applied
$sql = "UPDATE orders 
        SET status = 'processing', 
            shipping_phone = :phone, 
            shipping_address = :address 
        WHERE id = :order_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
$stmt->bindValue(':address', $address, PDO::PARAM_STR);
$stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();

// Update the response with the final total price (with discount if applied)
$response['total_price'] = $finalTotalPrice;
$response['message'] = "Order placed successfully and is now processing.";

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;

?>