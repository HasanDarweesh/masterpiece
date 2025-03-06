<?php
require_once "../../includes/database/config.php";

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];

    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);

    if ($stmt->rowCount() == 0) {
        $error = "Category not found.";
    } else {
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
        $stmt_check->execute([$category_id]);
        $product_count = $stmt_check->fetchColumn();

        if ($product_count > 0) {
           
            try {
                $stmt_update = $pdo->prepare("UPDATE products SET category_id = NULL WHERE category_id = ?");
                $stmt_update->execute([$category_id]);
               
                if ($stmt_update->rowCount() > 0) {
                    echo "Products updated successfully.";
                }
            } catch (PDOException $e) {
                $error = "Error updating products: " . $e->getMessage();
            }
        }

       
        try {
            $stmt_delete = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt_delete->execute([$category_id]);
           
            if ($stmt_delete->rowCount() > 0) {
                $success = true;
            } else {
                $error = "Category could not be deleted.";
            }
        } catch (PDOException $e) {
            $error = "Error deleting category: " . $e->getMessage();
        }
    }
} else {
    $error = "No category ID provided.";
}

?>

<div class="container">
    <h2>Delete Category</h2>
    <?php if ($success): ?>
        <div class="alert alert-success">Category deleted successfully!</div>
        <a href="category.php?page=category" class="btn btn-primary">Back to Category Management</a>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <a href="category.php?page=category" class="btn btn-secondary">Cancel</a>
    <?php endif; ?>
</div>
