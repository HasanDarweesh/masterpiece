<?php
session_start();
require_once "../../includes/database/config.php";

if ($_SESSION['role']!== 'admin' && $_SESSION['role'] !== 'superadmin') {
  header("Location: ../../public/login/index.php");
  exit();
} 
// Total orders.
$orderQuery = "SELECT COUNT(*) AS total_orders FROM orders";
$stmt = $pdo->query($orderQuery);
$orderData = $stmt->fetch(PDO::FETCH_ASSOC);
$totalOrders = $orderData['total_orders'];

// Total products.
$productQuery = "SELECT COUNT(*) AS total_products FROM products";
$stmt = $pdo->query($productQuery);
$productData = $stmt->fetch(PDO::FETCH_ASSOC);
$totalProducts = $productData['total_products'];

// Total coupons.
$couponQuery = "SELECT COUNT(*) AS total_coupons FROM coupons";
$stmt = $pdo->query($couponQuery);
$couponData = $stmt->fetch(PDO::FETCH_ASSOC);
$totalCoupons = $couponData['total_coupons'];

// Total categories.
$categoryQuery = "SELECT COUNT(*) AS total_categories FROM categories";
$stmt = $pdo->query($categoryQuery);
$categoryData = $stmt->fetch(PDO::FETCH_ASSOC);
$totalCategories = $categoryData['total_categories'];

// User breakdown queries:
$querySuper = "SELECT COUNT(*) AS total FROM users WHERE role = 'superadmin' AND deleted_at IS NULL";
$stmt = $pdo->query($querySuper);
$dataSuper = $stmt->fetch(PDO::FETCH_ASSOC);
$totalSuper = $dataSuper['total'];

$queryAdmin = "SELECT COUNT(*) AS total FROM users WHERE role = 'admin' AND deleted_at IS NULL";
$stmt = $pdo->query($queryAdmin);
$dataAdmin = $stmt->fetch(PDO::FETCH_ASSOC);
$totalAdmin = $dataAdmin['total'];

$queryUser = "SELECT COUNT(*) AS total FROM users WHERE role = 'user' AND deleted_at IS NULL";
$stmt = $pdo->query($queryUser);
$dataUser = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRegular = $dataUser['total'];

$queryDeleted = "SELECT COUNT(*) AS total FROM users WHERE deleted_at IS NOT NULL";
$stmt = $pdo->query($queryDeleted);
$dataDeleted = $stmt->fetch(PDO::FETCH_ASSOC);
$totalDeleted = $dataDeleted['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="style.css"> 
</head>
<body>
  <!-- Navbar -->
  <?php include('../../includes/admin/navbar/navbar.php') ?>


  <!-- Page Body -->
  <div class="container-fluid mt-3">
    <div class="row">
      <!-- Sidebar -->
      <?php include('../../includes/admin/sidebar/sidebar.php') ?>
      <!-- Main Content -->
          <!-- Main Content -->
          <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
        <!-- User Breakdown Widget, full width -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card text-white bg-danger">
              <div class="card-header">User Breakdown</div>
              <div class="card-body">
                <div class="row text-center">
                  <div class="col-md-3">
                    <h5 class="card-title"><?= htmlspecialchars($totalSuper) ?></h5>
                    <p class="card-text">Superadmins</p>
                  </div>
                  <div class="col-md-3">
                    <h5 class="card-title"><?= htmlspecialchars($totalAdmin) ?></h5>
                    <p class="card-text">Admins</p>
                  </div>
                  <div class="col-md-3">
                    <h5 class="card-title"><?= htmlspecialchars($totalRegular) ?></h5>
                    <p class="card-text">Users</p>
                  </div>
                  <div class="col-md-3">
                    <h5 class="card-title"><?= htmlspecialchars($totalDeleted) ?></h5>
                    <p class="card-text">Deleted Users</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Other Widgets -->
        <div class="row">
          <!-- Total Orders Widget -->
          <div class="col-md-3 mb-4">
            <div class="card text-white bg-primary h-100">
              <div class="card-header">Total Orders</div>
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($totalOrders) ?></h5>
                <p class="card-text">Orders placed in the system.</p>
              </div>
            </div>
          </div>
          <!-- Total Products Widget -->
          <div class="col-md-3 mb-4">
            <div class="card text-white bg-success h-100">
              <div class="card-header">Total Products</div>
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($totalProducts) ?></h5>
                <p class="card-text">Products available in inventory.</p>
              </div>
            </div>
          </div>
          <!-- Total Coupons Widget -->
          <div class="col-md-3 mb-4">
            <div class="card text-white bg-secondary h-100">
              <div class="card-header">Total Coupons</div>
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($totalCoupons) ?></h5>
                <p class="card-text">Coupons active in the system.</p>
              </div>
            </div>
          </div>
          <!-- Total Categories Widget -->
          <div class="col-md-3 mb-4">
            <div class="card text-black bg-warning h-100">
              <div class="card-header">Total Categories</div>
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($totalCategories) ?></h5>
                <p class="card-text">Categories available.</p>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Bootstrap 5 JS and Popper -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
