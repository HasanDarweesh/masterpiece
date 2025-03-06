<?php
require_once "../../includes/database/config.php";

$error = '';
$success = false;
// Directory to store uploaded images
$upload_dir = 'uploads/category_images/'; 

if (!file_exists($upload_dir)) {
    // Create directory if it doesn't exist
    mkdir($upload_dir, 0755, true); 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = $_POST['category_name'];


    if (empty($category_name)) {
        $error = "Category name is required.";
    } else {
        $category_image = ''; 

        if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] == 0) {
            $file_name = $_FILES['category_image']['name'];
            $file_tmp_name = $_FILES['category_image']['tmp_name'];
            $file_size = $_FILES['category_image']['size'];
            $file_type = $_FILES['category_image']['type'];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

            // Validate file type and size (optional: add more validation)
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
                    $category_image = $destination; // Store file path in database
                } else {
                    $error .= "Error uploading file. ";
                }
            }
        } else {// Set default image if no image uploaded
            $category_image = 'uploads/category_images/default_category.png'; 
        }


        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO categories (name, category_image) VALUES (?, ?)");
                $stmt->execute([$category_name, $category_image]);
                $success = true;
            } catch (PDOException $e) {
                $error = "Error adding category: " . $e->getMessage();
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
    <title>Add New Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
 <!-- jQuery -->
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap CSS -->

    
  </head>
  <body>
  <?php include('../../includes/admin/navbar/navbar.php') ?>
  <div class="container-fluid mt-3">
  <div class="row">
  <?php include('../../includes/admin/sidebar/sidebar.php') ?>
  <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
  <div class="container mt-5">
  <div class="container">
    <h2>Add New Category</h2>
    <?php if ($success): ?>
        <div class="alert alert-success">Category added successfully!</div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" action="add_category.php?page=add_category" enctype="multipart/form-data">
        <div class="form-group">
            <label for="category_name">Category Name</label><br>
            <input type="text" class="form-control" id="name" name="category_name" required>
        </div>
        <div class="form-group">
            <label for="category_image"></label><br>
            <input type="file" class="form-control-file" id="category_image" name="category_image">
            <small class="form-text text-muted">Upload a category image (jpg, jpeg, png, gif, max 2MB)..</small>
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Add Category</button>
        <a href="category.php?page=category" class="btn btn-secondary">Cancel</a>
    </form>
</div>



    </div>
  </main>
  </div>
</div>

  </body>
  </html>

