<?php
require_once "../../includes/database/config.php";

// Handle Add Product Form Submission
if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_stock = $_POST['product_stock'];
    $product_category = $_POST['product_category'];
    $product_price = $_POST['product_price'];
    $product_image = $_FILES['product_image']['name'];
    $product_image_tmp = $_FILES['product_image']['tmp_name'];
    $product_image_path = 'uploads/product_images/' . $product_image;

    // Ensure the image upload directory exists
    if (!file_exists('uploads/product_images/')) {
        mkdir('uploads/product_images/', 0755, true); 
    }

    if (empty($product_name) || empty($product_category)) {
        $error_message = "Product name and category are required.";
    } else {
        try {
            if (!empty($product_image)) {
                // Extract image info (e.g., type and dimensions)
                $image_info = getimagesize($product_image_tmp);
                
                if ($image_info !== false) {
                    // Get file extension and size
                    $file_ext = pathinfo($product_image, PATHINFO_EXTENSION);
                    $file_size = filesize($product_image_tmp);
                    
                    // Allowed file types and maximum size
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                    $max_file_size = 2 * 1024 * 1024; // 2MB
        
                    // Validate file type
                    if (!in_array(strtolower($file_ext), $allowed_extensions)) {
                        $error_message = "Invalid file type. Allowed types: jpg, jpeg, png, gif.";
                    }
                    // Validate file size
                    elseif ($file_size > $max_file_size) {
                        $error_message = "File size is too large. Maximum allowed size is 2MB.";
                    } else {
                        // If validation passes, move the file to the destination path
                        if (move_uploaded_file($product_image_tmp, $product_image_path)) {
                            // Prepare SQL query to insert product details into the database
                            $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, stock, price, image, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
                            $stmt->execute([$product_category, $product_name, $product_description, $product_stock, $product_price, $product_image]);
                            header('Location: product.php');
                            exit();
                        } else {
                            $error_message = "Error uploading file.";
                        }
                    }
                } else {
                    $error_message = "The uploaded file is not an image.";
                }
            } else {
                $error_message = "You must upload an image!";
            }
        } catch (PDOException $e) {
            $error_message = "Error adding product: " . $e->getMessage();
        }
    }        
}

// Fetch categories for dropdown
try {
    $stmt = $pdo->query("SELECT * FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching categories: " . $e->getMessage();
    $categories = [];
}



// Fetch categories for dropdown
try {
    $stmt = $pdo->query("SELECT * FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching categories: " . $e->getMessage();
    $categories = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="style.css">

</head>
<?php include('../../includes/admin/navbar/navbar.php') ?>
<div class="container-fluid mt-3">

  <div class="row">
  <?php include('../../includes/admin/sidebar/sidebar.php') ?>

  <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
  <div class="container mt-5">
    <div class="container">
<body>
    <div class="container">
        <h2>Add New Product</h2>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="add_product.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name:</label>
                <input type="text" class="form-control" name="product_name" required>
            </div>
            <div class="form-group">
                <label>Description:</label>
                <textarea class="form-control" name="product_description"></textarea>
            </div>
            <div class="form-group">
                <label>Stock:</label>
                <input type="number" class="form-control" name="product_stock" required>
            </div>
            <div class="form-group">
                <label>Price:</label>
                <input type="number" class="form-control" name="product_price" required>
            </div>
            <div class="form-group">
                <label>Category:</label>
                <select class="form-control" name="product_category" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Image:</label>
                <input type="file" class="form-control-file" name="product_image">
            </div>
            <button type="submit" class="btn btn-primary" name="add_product">Add Product</button>
        </form>
    </div>
</body>
</html>