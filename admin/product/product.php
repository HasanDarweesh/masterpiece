<?php
require_once "../../includes/database/config.php";

// Fetch products from database
try {
    $stmt = $pdo->query("SELECT products.id AS product_id, products.category_id, products.name AS product_name, products.description, products.stock, products.price, products.image, products.is_active, products.created_at, products.updated_at, categories.name AS category_name 
    FROM products 
    INNER JOIN categories ON products.category_id = categories.id");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching products: " . $e->getMessage();
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
        <h2>Product List</h2>
        <a href="add_product.php" class="btn btn-primary mb-3">Add New Product</a>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr><td colspan="10">No products found.</td></tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                            <td>
                                <?php if($product['image']): ?>
                                    <img src="uploads/product_images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" width="50">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($product['description']); ?></td>
                            <td><?php echo htmlspecialchars($product['price']); ?></td>
                            <td><?php echo htmlspecialchars($product['stock']); ?></td>
                           
                            <td >
                            <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-primary"> <i class="bi bi-pencil"></i></a> 
                            <a href="#" class="btn btn-sm btn-danger delete-product-btn" data-product-id="<?php echo $product['product_id']; ?>"> <i class="bi bi-trash-fill"></i></a>
                            <?php if ($product['is_active'] == 1): ?>
                           <a href="deactivate_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-warning"> <i class="bi bi-x-circle-fill"></i></a>
                           <?php else: ?>
                           <a href="activate_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-success"> <i class="bi bi-check-circle"></i></a>
                          <?php endif; ?>

                            </td> 
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    </div>
  </main>
  </div>
</div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Delete Product Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteProductModalLabel">Confirm Delete</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this product?
        <form action="delete_product.php" method="post" id="deleteProductForm">
          <input type="hidden" name="product_id" id="product_id_input" value="">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-danger" onclick="document.getElementById('deleteProductForm').submit();">Yes</button>
      </div>
    </div>
  </div>
</div>

    <script>
        $(document).ready(function() {
    $('.delete-product-btn').click(function(e) {
        e.preventDefault();
        
        var productId = $(this).data('product-id'); 
        console.log("Product ID: ", productId);
        
        if (productId) {
            $('#product_id_input').val(productId);
            $('#deleteProductModal').modal('show');
        } else {
            console.error("Product ID not found!");
        }
    });
});

    </script>
   
</body>
</html>
