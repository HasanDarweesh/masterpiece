<?php
require_once "../../includes/database/config.php";
session_start();
if ($_SESSION['role']!== 'admin' && $_SESSION['role'] !== 'superadmin') {
    header("Location: ../../public/login/index.php");
    exit();
  } 

// Handle category activation/deactivation
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $category_id = $_GET['id'];

    if ($action === 'activate') {
        $stmt = $pdo->prepare("UPDATE categories SET is_active = TRUE WHERE id = ?");
        $stmt->execute([$category_id]);
    } elseif ($action === 'deactivate') {
        $stmt = $pdo->prepare("UPDATE categories SET is_active = FALSE WHERE id = ?");
        $stmt->execute([$category_id]);
    }
}

// Fetch all categories
$stmt = $pdo->query("SELECT id, name, category_image, is_active, created_at, updated_at FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include('../../includes/admin/navbar/navbar.php') ?>
<div class="container-fluid mt-3">

  <div class="row">
  <?php include('../../includes/admin/sidebar/sidebar.php') ?>

  <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
  <div class="container mt-5">
    <h2>Category Management</h2>
    <a href="add_category.php?page=add_category" class="btn btn-primary">Add New Category</a>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Image</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
            <tr>
                <td><?php echo $category['name']; ?></td>
                <td>
                    <?php if (!empty($category['category_image'])): ?>
                        <img src="<?php echo $category['category_image']; ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" style="max-width: 50px; max-height: 50px;">
                        
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td><?php echo array_key_exists('is_active', $category) ? ($category['is_active'] ? 'Yes' : 'No') : 'No'; ?></td>
               
                

                    <td>
                    <a href="edit_category.php?page=edit_category&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-primary"> <i class="bi bi-pencil"></i></a>
                    
                    <!-- <a href="javascript:void(0);" class="btn btn-sm btn-danger delete-category-btn" data-id="<?php echo $category['id']; ?>"> <i class="bi bi-trash"></i></a> -->
                    <?php if ($category['is_active']): ?>
                        <a href="category.php?page=category&action=deactivate&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-x-circle-fill"></i></a>
                    <?php else: ?>
                        <a href="category.php?page=category&action=activate&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-success"> <i class="bi bi-check-circle"></i></a>
                        
                    <?php endif; ?>
                   

                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>



<!-- Delete Category Modal -->
<div class="modal fade" id="deletecategoryModal" tabindex="-1" role="dialog" aria-labelledby="deletecategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deletecategoryModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this Category?
        <form action="delete_category.php" method="post" id="deleteCategoryForm">
            <input type="hidden" name="category_id" id="category_id_input" value="">
        </form>
      </div>
      <div class="modal-footer">
        <!-- Use data-bs-dismiss to close the modal -->
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
        <button type="button" class="btn btn-danger" onclick="document.getElementById('deleteCategoryForm').submit();">Yes</button>
      </div>
    </div>
  </div>
</div>
  </div>
  </main>
  </div>
</div>
                
   <!-- jQuery -->
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- JavaScript Code -->
    <script>
$(document).ready(function() {
    $('.delete-category-btn').click(function(e) {
        e.preventDefault();
        
        var categoryId = $(this).data('id'); 
        console.log("Category Id: ", categoryId);  
        
        if (categoryId) {  
            $('#category_id_input').val(categoryId);  
            var modal = new bootstrap.Modal(document.getElementById('deletecategoryModal'));
            modal.show();
        } else {
            console.error("Category ID not found!");  
        }
    });
});
</script>


</body>
</html>