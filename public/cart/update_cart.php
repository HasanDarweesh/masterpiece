<?php
session_start();
require_once "../../includes/database/config.php";

// Ensure that the form was submitted with quantities and total
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['quantities']) && is_array($_POST['quantities']) && isset($_POST['total'])) {
    $total = floatval($_POST['total']);

    // Start transaction
    $pdo->beginTransaction();

    foreach ($_POST['quantities'] as $order_item_id => $quantity) {
        // Cast the quantity to integer (you might add additional validation)
        $newQuantity = (int) $quantity;
        // Optionally, enforce a minimum quantity (e.g., 1)
        if ($newQuantity < 1) {
            $newQuantity = 1;
        }
        // Update the item's quantity in the database
        $sql = "UPDATE order_items SET quantity = :quantity WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
        $stmt->bindParam(':id', $order_item_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Calculate the new total price for the order
    // Get the order ID from one of the order items (assuming all items belong to the same order)
    $order_item_id = array_key_first($_POST['quantities']);
    $orderQuery = "SELECT order_id FROM order_items WHERE id = :id";
    $orderStmt = $pdo->prepare($orderQuery);
    $orderStmt->bindParam(':id', $order_item_id, PDO::PARAM_INT);
    $orderStmt->execute();
    $order_id = $orderStmt->fetchColumn();

    // Update the total price in the orders table
    $updateOrderQuery = "UPDATE orders SET total_price = :total_price WHERE id = :order_id";
    $updateOrderStmt = $pdo->prepare($updateOrderQuery);
    $updateOrderStmt->bindParam(':total_price', $total, PDO::PARAM_STR);
    $updateOrderStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $updateOrderStmt->execute();

    // Commit the transaction
    $pdo->commit();

    // After updating, redirect back to the cart page.
    header("Location: cart.php");
    exit();
} else {
    echo "No quantities or total were submitted.";
    echo $order_id;
}
?>
