
<?php
session_start();
require_once "../../includes/database/config.php";

// Only allow POST requests that include "user_id"
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['user_id'])) {
    die("Invalid request.");
}

$user_id = $_POST['user_id'];
$name    = trim($_POST['name']);
$email   = trim($_POST['email']);
$passwordInput = $_POST['password']; // Might be empty if not changing the password.
$phone   = trim($_POST['phone']);
$address = trim($_POST['address']);
$role    = trim($_POST['role']);


try {
    // First, fetch the current user record to use existing password and profile picture if needed.
    $query  = "SELECT password, profile_picture FROM users WHERE id = :id AND deleted_at IS NULL";
    $stmt   = $pdo->prepare($query);
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$currentUser) {
        die("User not found.");
    }
    
    // If the password field is empty, use the existing hashed password; otherwise, hash the new password.
    if (empty($passwordInput)) {
        $hashedPassword = $currentUser['password'];
    } else {
        $hashedPassword = password_hash($passwordInput, PASSWORD_DEFAULT);
    }
    
    // Process an optional file upload for the profile picture.
    $profilePicturePath = $currentUser['profile_picture']; // Default to current value.
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileName  = $_FILES['profile_picture']['name'];
        $fileTmp   = $_FILES['profile_picture']['tmp_name'];
        $fileParts = explode(".", $fileName);
        $fileExt   = strtolower(end($fileParts));
        $allowed   = ["jpg", "jpeg", "png", "gif"];
        if (in_array($fileExt, $allowed)) {
            $profilePicturePath = $user_id . "." . $fileExt;
            $destination = "../../uploads/avatars/" . $profilePicturePath;
            // Overwrite any existing file for this user.
            move_uploaded_file($fileTmp, $destination);
        }
    }
    
    // Prepare the update statement with the new values.
    $updateQuery = "UPDATE users SET 
                        name = :name,
                        email = :email,
                        password = :password,
                        phone = :phone,
                        address = :address,
                        role = :role,
                        profile_picture = :profile_picture,
                        updated_at = NOW() 
                    WHERE id = :id";
    
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->bindParam(':name', $name);
    $updateStmt->bindParam(':email', $email);
    $updateStmt->bindParam(':password', $hashedPassword);
    $updateStmt->bindParam(':phone', $phone);
    $updateStmt->bindParam(':address', $address);
    $updateStmt->bindParam(':role', $role);
    $updateStmt->bindParam(':profile_picture', $profilePicturePath);
    $updateStmt->bindParam(':id', $user_id);
    
    if ($updateStmt->execute()) {
        // On success, redirect to the main index or dashboard page.
        header("Location: index.php");
        exit();
    } else {
        die("Error updating user.");
    }
    
} catch (PDOException $e) {
    if ($e->getCode() == 23000 && isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
        header("Location: index.php");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }   
}
?>
