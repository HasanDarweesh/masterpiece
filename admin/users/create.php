<?php
session_start();
require_once "../../includes/database/config.php";

// Initialize an empty error message variable.
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {

    // Sanitize and assign input values.
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $password = $_POST['password']; // Plaintext from the form.
    $phone   = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $role    = trim($_POST['role']);

    // Hash the password securely.
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare a flag for file upload.
    $profilePictureUploaded = false;
    $fileExt = null;
    if (
        isset($_FILES['profile_picture']) && 
        $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK
    ) {
        $fileName   = $_FILES['profile_picture']['name'];
        $fileTmp    = $_FILES['profile_picture']['tmp_name'];
        $fileParts  = explode(".", $fileName);
        $fileExt    = strtolower(end($fileParts));
        $allowed    = ["jpg", "jpeg", "png", "gif"];
        if (in_array($fileExt, $allowed)) {
            $profilePictureUploaded = true;
        }
    }
    
    // Wrap the insertion in a try-catch block to handle possible errors.
    try {
        // Insert new user record with profile_picture initially as NULL.
        $query = "INSERT INTO users 
                  (name, email, password, phone, address, role, profile_picture, created_at, updated_at)
                  VALUES (:name, :email, :password, :phone, :address, :role, NULL, NOW(), NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        
        // Get the inserted user id.
        $id = $pdo->lastInsertId();
        
        // If a profile picture was uploaded, move it to the avatars folder and store the relative path.
        if ($profilePictureUploaded) {
            $profilePicturePath =  $id . "." . $fileExt;
            $destination = "../../uploads/avatars/" . $profilePicturePath;
            if (move_uploaded_file($fileTmp, $destination)) {
                // Now update the user record with the file path.
                $updateQuery = "UPDATE users SET profile_picture = :profile_picture WHERE id = :id";
                $updateStmt = $pdo->prepare($updateQuery);
                $updateStmt->bindParam(':profile_picture', $profilePicturePath);
                $updateStmt->bindParam(':id', $id);
                $updateStmt->execute();
            }
        }
        
        // Instead of a header redirect, output JavaScript to refresh the parent window.
        ?>
        <!DOCTYPE html>
        <html>
        <head>
          <title>Success</title>
        </head>
        <body>
          <script type="text/javascript">
            // This will reload the parent window (index page) and close the modal.
            window.parent.location.href = 'index.php';
          </script>
        </body>
        </html>
        <?php

    } catch (PDOException $e) {
        // Check if the error is from a duplicate unique value.
        if ($e->getCode() == 23000 && isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
            $errorMessage = "Error: Duplicate entry. A unique value (like email) already exists.";
        } else {
            $errorMessage = "Database error: " . $e->getMessage();
        }    
    }    
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="container my-4">
    
    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Name:</label>
            <input type="text" name="name" class="form-control" required value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
        </div>
        <div class="mb-3">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
        </div>
        <div class="mb-3">
            <label>Password:</label>
            <input type="password" name="password"  pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Phone:</label>
            <input type="text" name="phone" class="form-control" required value="<?= isset($phone) ? htmlspecialchars($phone) : '' ?>">
        </div>
        <div class="mb-3">
            <label>Address:</label>
            <input type="text" name="address" class="form-control" value="<?= isset($address) ? htmlspecialchars($address) : '' ?>">
        </div>
        <div class="mb-3">
            <label>Role:</label>
            <?php if ($_SESSION['role'] === 'superadmin'): ?>
                <select name="role" class="form-select">
                  <option value="user" <?= (isset($role) && $role == "user") ? "selected" : "" ?>>User</option>
                  <option value="admin" <?= (isset($role) && $role == "admin") ? "selected" : "" ?>>Admin</option>
                  <option value="superadmin" <?= (isset($role) && $role == "superadmin") ? "selected" : "" ?>>Superadmin</option>
                </select>
            <?php else: ?>
                <input type="hidden" name="role" value="user">
                <p class="form-control-plaintext">User</p>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label>Profile Picture:</label>
            <input type="file" name="profile_picture" class="form-control">
        </div>
        <!-- Add the submit button with a name attribute -->
        <button type="submit" name="submit" class="btn btn-success">Create User</button>
        <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>

    </form>
    <script>
  document.getElementById('cancelBtn').addEventListener('click', function(){
    // Get the modal instance in the parent document.
    var modalElement = window.parent.document.getElementById('createModal');
    var modalInstance = window.parent.bootstrap.Modal.getInstance(modalElement);
    if(modalInstance){
      modalInstance.hide();
    } else {
      // Alternatively, if the modal hasn't been instantiated yet, create and hide it.
      modalInstance = new window.parent.bootstrap.Modal(modalElement);
      modalInstance.hide();
    }
  });
</script>

</body>
</html>
