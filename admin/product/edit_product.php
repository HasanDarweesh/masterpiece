<?php
require_once "../../includes/database/config.php";
// Fetch categories for dropdown
try {
    $stmt_cat = $pdo->query("SELECT * FROM categories");
    $categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching categories: " . $e->getMessage();
    $categories = [];
}

// Fetch product details for editing
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    try {
        
        $stmt = $pdo->prepare("SELECT id AS product_id, category_id, name AS product_name, description AS product_description, stock AS product_stock, price AS product_price, image, is_active FROM products WHERE id = ?");


        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) {
            echo "Product not found.";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error fetching product details: " . $e->getMessage();
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}

// Handle Update Product Form Submission
if (isset($_POST['update_product'])) {
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_stock = $_POST['product_stock'];
    $product_price = $_POST['product_price'];


    $product_category = $_POST['product_category'];
    $product_image_old = $_POST['product_image_old'];

    $product_image_new = $_FILES['product_image_new']['name'];
    $product_image_tmp = $_FILES['product_image_new']['tmp_name'];
    $product_image_path = 'uploads/product_images/' . $product_image_new;

    // Basic validation
    if (empty($product_name)|| empty($product_category)) {
        $error_message = "Product name, and category are required.";
    } else {
        try {
            // Upload new image if provided
            if (!empty($product_image_new)) {
                move_uploaded_file($product_image_tmp, $product_image_path);
                $product_image = $product_image_new;
                // Delete old image if it exists and is not default
                if (!empty($product_image_old) && file_exists('uploads/product_images/' . $product_image_old)) {
                    unlink('uploads/product_images/' . $product_image_old);
                }
            } else {
                $product_image = $product_image_old; // Keep old image if no new image uploaded
            }


            // Update product in database
            $stmt = $pdo->prepare("UPDATE products SET category_id = ?, name = ?, description = ?, stock = ?, price = ?, image = ? WHERE id = ?");
            $stmt->execute([$product_category, $product_name, $product_description, $product_stock, $product_price, $product_image, $product_id]);
           $success_message = "Product updated successfully!";

        
            // Refresh product details after update
            $stmt = $pdo->prepare("SELECT id AS product_id, category_id, name AS product_name, description AS product_description, stock AS product_stock, image, is_active FROM products WHERE id = ?");
            

            


            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);


        } catch (PDOException $e) {
            $error_message = "Error updating product: " . $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="style.css" rel="stylesheet">

</head>
<body>
<?php include('../../includes/admin/navbar/navbar.php') ?>
<div class="container-fluid mt-3">

  <div class="row">
  <?php include('../../includes/admin/sidebar/sidebar.php') ?>

  <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
  <div class="container mt-5">
    <div class="container">
        <h2>Edit Product</h2>
    <?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
    <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
        <form action="edit_product.php?id=<?php echo $product_id; ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="product_image_old" value="<?php echo htmlspecialchars($product['image']); ?>">
            <div class="form-group">
                <label for="product_name">Product Name:</label>
                <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>">

            </div>
            <div class="form-group">
                <label for="product_description">Product Description:</label>
                <textarea class="form-control" id="product_description" name="product_description"><?php echo htmlspecialchars($product['product_description']); ?></textarea>


            </div>
            <div class="form-group">
            <label for="product_stock">Stock Quantity:</label>
            <input type="number" class="form-control" id="product_stock" name="product_stock" value="<?php echo htmlspecialchars($product['product_stock']); ?>" >
            </div>

           
            <div class="form-group">
            <label for="product_price">Product Price:</label>
            <input type="number" class="form-control" id="product_price" name="product_price" value="<?php echo isset($product['product_price']) ? number_format($product['product_price'], 2) : '0.00'; ?>" step="0.01">
            </div>


            <div class="form-group">
                <label for="product_category">Category:</label>
                <select class="form-control" id="product_category" name="product_category" >
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php if ($product['category_id'] == $category['id']) echo 'selected'; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                   

                </select>
            </div>
            <div class="form-group">
                <label for="product_image_new">Product Image:</label>
                <input type="file" class="form-control-file" id="product_image_new" name="product_image_new">
                <?php if (!empty($product['product_image'])): ?>
                    <img src="uploads/product_images/<?php echo htmlspecialchars($product['product_image']); ?>" alt="Current Product Image" width="100">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary" name="update_product">Update Product</button>
            <a href="product.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
