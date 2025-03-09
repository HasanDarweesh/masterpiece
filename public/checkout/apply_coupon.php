<?php
session_start();
require_once '../../includes/database/config.php';

// Prepare response array
$response = [
    'success' => false,
    'message' => '',
    'original_total' => 0,
    'discount_value' => 0,
    'new_total' => 0,
];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'User not logged in';
    echo json_encode($response);
    exit;
}

// Get user_id from session
$user_id = $_SESSION['user_id'];

// Fetch the latest pending order for the user
$stmt = $pdo->prepare("SELECT id, total_price, coupon_id FROM orders WHERE user_id = :user_id AND status = 'pending' ORDER BY id DESC LIMIT 1");
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    $response['message'] = 'No pending order found.';
    echo json_encode($response);
    exit;
}

// Check if the coupon_id is NULL
if ($order['coupon_id'] !== null) {
    $response['message'] = 'Coupon already applied to this order.';
    $response['original_total'] = $order['total_price'];
    $response['new_total'] = $order['total_price'];
    echo json_encode($response);
    exit;
}

// Read the original total price from the orders table
$order_id = $order['id'];
$original_total = $order['total_price'];
$response['original_total'] = $original_total;

// Read coupon code from request
$coupon_code = $_POST['coupon_code'] ?? '';

// Check if coupon exists in the database
$stmt = $pdo->prepare("SELECT id, discount_value, active, expiration_date FROM coupons WHERE code = ?");
$stmt->execute([$coupon_code]);
$coupon = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$coupon) {
    $response['message'] = 'This coupon does not exist.';
    $response['new_total'] = $original_total; // Retain original total
} elseif ($coupon['active'] != 1) {
    $response['message'] = 'This coupon is not active.';
    $response['new_total'] = $original_total; // Retain original total
} elseif ($coupon['expiration_date'] < date('Y-m-d')) {
    $response['message'] = 'This coupon has expired.';
    $response['new_total'] = $original_total; // Retain original total
} else {
    // Apply discount if coupon is valid
    $discount_value = $coupon['discount_value'] ?? 0;

    // Ensure discount does not exceed original total
    if ($discount_value > $original_total) {
        $discount_value = $original_total;
    }

    // Calculate new total
    $new_total = $original_total - $discount_value; // Ensure total is not negative

    // Success response
    $response['success'] = true;
    $response['message'] = 'Coupon applied successfully.';
    $response['discount_value'] = $discount_value;
    $response['new_total'] = $new_total;

    // Save applied coupon and updated total_price in the orders table
    $stmt = $pdo->prepare("UPDATE orders SET coupon_id = :coupon_id, total_price = :new_total WHERE id = :order_id");
    $stmt->bindValue(':coupon_id', $coupon['id'], PDO::PARAM_INT);
    $stmt->bindValue(':new_total', $new_total, PDO::PARAM_STR); // Ensure correct data type
    $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->execute();
}

// Return response as JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
