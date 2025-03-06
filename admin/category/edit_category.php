<?php
require_once "../../includes/database/config.php";

$error = '';
$success = false;
$category = null;
$upload_dir = 'uploads/category_images/'; 

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true); 
}

if (isset($_GET['id'])) {
    $category_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$category) {
        $error = "Category not found.";
    }
} else {
    $error = "No category ID provided.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $category) {
    $category_name = $_POST['category_name'];
    $current_category_image = $category['category_image']; 
    $category_image = $current_category_image; 

    if (empty($category_name)) {
        $error = "Category name is required.";
    } else {
        if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] == 0) {
            // Handle image upload
            $file_name = $_FILES['category_image']['name'];
            $file_tmp_name = $_FILES['category_image']['tmp_name'];
            $file_size = $_FILES['category_image']['size'];
            $file_type = $_FILES['category_image']['type'];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

            // Validate file type and size 
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $max_file_size = 2 * 1024 * 1024; // 2MB

            if (!in_array(strtolower($file_ext), $allowed_extensions)) {
                $error .= "Invalid file type. Allowed types: jpg, jpeg, png, gif. ";
            }

            if ($file_size > $max_file_size) {
                $error .= "File size too large. Max size: 2MB. ";
            }

            if (empty($error)) {
                $unique_filename = uniqid() . '_' . $file_name;
                $destination = $upload_dir . $unique_filename;

                if (move_uploaded_file($file_tmp_name, $destination)) {
                    // Delete old image if it's not the default and exists
                    if (!empty($current_category_image) && file_exists($current_category_image) && !strpos($current_category_image, 'default_category.png')) {
                        unlink($current_category_image);
                    }
                    // Use new uploaded image path
                    $category_image = $destination; 
                } else {
                    $error .= "Error uploading file. ";
                }
            }
        }

        if (empty($error)) {
            try {
                
                $stmt = $pdo->prepare("UPDATE categories SET name = ?, category_image = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$category_name, $category_image, $category_id]);

                $success = true;
            } catch (PDOException $e) {
                $error = "Error updating category: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

      <!-- jQuery -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


</head>
<body>
<?php include('../../includes/admin/navbar/navbar.php') ?>
<div class="container-fluid mt-3">

  <div class="row">
  <?php include('../../includes/admin/sidebar/sidebar.php') ?>

  <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
  <div class="container mt-5">
    
  <div class="container">
    <h2>Edit Category</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success">Category updated successfully!</div>
    <?php endif; ?>

    <?php if ($category): ?>
        <form method="POST" action="edit_category.php?page=edit_category&id=<?php echo $category['id']; ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="category_name">Category Name</label>
                <input type="text" class="form-control" id="category_name" name="category_name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="category_image"></label><br>
                <input type="file" class="form-control-file" id="category_image" name="category_image">
                <br>
                <small class="form-text text-muted">Upload a new category image (jpg, jpeg, png, gif, max 2MB)..</small>
                <?php if (!empty($category['category_image'])): ?>
                <?php endif; ?>
            </div>
            <br>
            <button type="submit" class="btn btn-primary">Update Category</button>
            <a href="category.php?page=category" class="btn btn-secondary">Cancel</a>
        </form>
    <?php endif; ?>
</div>

    </div>
  </main>
  </div>
  </div>
  </main>
  </div>
</div>





