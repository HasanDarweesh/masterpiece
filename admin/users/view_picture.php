<?php
require_once "../../includes/database/config.php";

if (!isset($_GET['student_id'])) {
    die("No user specified.");
}

$student_id = $_GET['student_id'];
$query = "SELECT profile_picture, name FROM users WHERE id = :id AND deleted_at IS NULL";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":id", $student_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

if (!empty($user['profile_picture'])) {
    $imagePath = "../../uploads/avatars/" . $user['profile_picture'];
} else {
    $imagePath = "../../uploads/default-avatar.png";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Full Picture</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container my-5 text-center">
  <h1><?php echo $user['name'];?>'s Full Profile Picture</h1>
  <img src="<?= htmlspecialchars($imagePath) ?>" alt="Profile Picture" class="img-fluid">
  <br><br>
  <!-- This exit button uses history.back() to return to the previous (listing) page -->
  <a href="index.php" class="btn btn-secondary">Exit</a>
</body>
</html>
