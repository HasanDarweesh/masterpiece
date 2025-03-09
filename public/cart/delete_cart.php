<?php
require_once "../../includes/database/config.php";

// Check if the order_item_id and order_id are provided and not empty
if (empty($_POST['order_item_id']) || empty($_POST['order_id'])) {
    die("Order item or order ID not specified.");
}

// Cast the order_item_id and order_id to integers for safety
$order_item_id = (int) $_POST['order_item_id'];
$order_id = (int) $_POST['order_id'];

// Start transaction to ensure both deletions are done atomically
$pdo->beginTransaction();

// Delete the order item from the table using an integer binding
$query = "DELETE FROM order_items WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":id", $order_item_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    // Check if there are any remaining items in the order
    $checkQuery = "SELECT COUNT(*) FROM order_items WHERE order_id = :order_id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->bindParam(":order_id", $order_id, PDO::PARAM_INT);
    $checkStmt->execute();
    $itemCount = $checkStmt->fetchColumn();

    if ($itemCount == 0) {
        // If no items remain, delete the order
        $deleteOrderQuery = "DELETE FROM orders WHERE id = :order_id";
        $deleteOrderStmt = $pdo->prepare($deleteOrderQuery);
        $deleteOrderStmt->bindParam(":order_id", $order_id, PDO::PARAM_INT);
        $deleteOrderStmt->execute();
    } else {
        // Recalculate the total price for the order
        $totalPriceQuery = "
            SELECT SUM(oi.quantity * p.price) AS total_price
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ";
        $totalPriceStmt = $pdo->prepare($totalPriceQuery);
        $totalPriceStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $totalPriceStmt->execute();
        $newTotalPrice = $totalPriceStmt->fetchColumn();
        
        // Update the total price in the orders table
        $updateOrderQuery = "UPDATE orders SET total_price = :total_price WHERE id = :order_id";
        $updateOrderStmt = $pdo->prepare($updateOrderQuery);
        $updateOrderStmt->bindParam(':total_price', $newTotalPrice, PDO::PARAM_STR);
        $updateOrderStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $updateOrderStmt->execute();
    }

    // Commit the transaction
    $pdo->commit();
    header("Location: cart.php");
    exit();
} else {
    // Rollback transaction if there's an error
    $pdo->rollBack();
    echo "Error deleting order item.";
}
?>
