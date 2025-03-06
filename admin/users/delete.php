<?php
require_once "../../includes/database/config.php";

// Expecting the ID via POST
if (!isset($_POST['user_id'])) {
    die("User not specified.");
}
$id = $_POST['user_id'];

// Update the deleted_at column (soft delete)
$query = "UPDATE users SET deleted_at = NOW() WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":id", $id);
if ($stmt->execute()) {
    header("Location: index.php");
    exit();
} else {
    echo "Error deleting user.";
}
?>
