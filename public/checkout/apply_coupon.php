<?php
session_start();
require_once '../../includes/database/config.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Prepare response array
$response = [
    'success' => false,
    'message' => '',
    'original_total' => 0,
    'discount_value' => 0,
    'new_total' => 0,
];

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }

    $user_id = $_SESSION['user_id'];

    // Fetch the latest pending order for the user
    $stmt = $pdo->prepare("SELECT id, total_price, coupon_id FROM orders WHERE user_id = :user_id AND status = 'pending' ORDER BY id DESC LIMIT 1");
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception('No pending order found.');
    }

    $order_id = $order['id'];
    $original_total = floatval($order['total_price']);
    $response['original_total'] = $original_total;

    $coupon_code = $_POST['coupon_code'] ?? '';

    // Check if coupon exists (case-insensitive)
    $stmt = $pdo->prepare("SELECT id, discount_value, active, expiration_date FROM coupons WHERE UPPER(code) = UPPER(?)");
    $stmt->execute([$coupon_code]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$coupon) {
        throw new Exception('This coupon does not exist.');
    } elseif ($coupon['active'] != 1) {
        throw new Exception('This coupon is not active.');
    } elseif ($coupon['expiration_date'] < date('Y-m-d')) {
        throw new Exception('This coupon has expired.');
    }

    // Apply discount
    $discount_percentage = floatval($coupon['discount_value']);
    $discount_amount = $original_total * ($discount_percentage / 100);

    // Ensure discount does not exceed original total
    $discount_amount = min($discount_amount, $original_total);

    // Calculate new total
    $new_total = max(0, $original_total - $discount_amount); // Ensure non-negative

    // Log for debugging
    error_log("Applying coupon: original_total=$original_total, discount_percentage=$discount_percentage, discount_amount=$discount_amount, new_total=$new_total");

    // Update orders table
    $stmt = $pdo->prepare("UPDATE orders SET coupon_id = :coupon_id, total_price = :new_total WHERE id = :order_id");
    $stmt->bindValue(':coupon_id', $coupon['id'], PDO::PARAM_INT);
    $stmt->bindValue(':new_total', $new_total, PDO::PARAM_STR); // Using PARAM_STR for consistency
    $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->execute();

    // Verify the update
    $stmt = $pdo->prepare("SELECT total_price FROM orders WHERE id = :order_id");
    $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->execute();
    $updated_total = floatval($stmt->fetchColumn());

    if ($updated_total != $new_total) {
        throw new Exception("Failed to update total_price. Expected $new_total, got $updated_total");
    }

    $response['success'] = true;
    $response['message'] = 'Coupon applied successfully.';
    $response['discount_value'] = $discount_amount;
    $response['new_total'] = $new_total;

} catch (Exception $e) {
    error_log("Error in apply_coupon.php: " . $e->getMessage());
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>