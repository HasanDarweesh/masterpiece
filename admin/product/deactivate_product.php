<?php
require_once "../../includes/database/config.php";

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    try {
        
        $stmt = $pdo->prepare("UPDATE products SET is_active = 0 WHERE id = ?");
        $stmt->execute([$product_id]);

        header("Location: product.php");
        exit();
    } catch (PDOException $e) {
        echo "Error deactivating product: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>

    
