<?php
require_once "../../includes/database/config.php";

if (!isset($_GET['id'])) {
    die("User not specified.");
}

$id = $_GET['id'];

try {
    // Set deleted_at to NULL to reactivate the user.
    $query = "UPDATE users SET deleted_at = NULL WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $id);
    
    if ($stmt->execute()) {
        // Redirect back to index after successful reactivation.
        header("Location: index.php");
        exit();
    } else {
        echo "Error reactivating user.";
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
