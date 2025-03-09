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
$sql = "SELECT id FROM orders WHERE user_id = :user_id AND status = 'pending'";
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

// Calculate the total price
$totalPrice = 0;
$response = [
    'success' => true,
    'cart' => [],
    'total_price' => 0,
];

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

// Get phone number and address from the POST request
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';

// Update the total price, status, phone number, and address in the orders table
$sql = "UPDATE orders 
        SET total_price = :total_price, 
            status = 'processing', 
            shipping_phone = :phone, 
            shipping_address = :address 
        WHERE id = :order_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':total_price', $totalPrice, PDO::PARAM_STR);
$stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
$stmt->bindValue(':address', $address, PDO::PARAM_STR);
$stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();

// Update the response with the new total price
$response['total_price'] = $totalPrice;
$response['message'] = "Order placed successfully and is now processing.";

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;

?>
