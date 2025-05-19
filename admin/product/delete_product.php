<?php
require_once "../../includes/database/config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    try {
        // Fetch product image name to delete from folder
        $stmt_select = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $stmt_select->execute([$product_id]);
        $product = $stmt_select->fetch(PDO::FETCH_ASSOC);
        $product_image = $product['image']; 

        // Delete product from database
        $stmt_delete = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt_delete->execute([$product_id]);

        // Delete product image from folder if it exists
        if (!empty($product_image) && file_exists('uploads/product_images/' . $product_image)) {
            unlink('uploads/product_images/' . $product_image);
        }

        header("Location: product.php");
        exit();
    } catch (PDOException $e) {
        echo "Error deleting product: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
