<?php

session_start();
include '../../includes/database/config.php';

if ($_SESSION['role']!== 'admin' && $_SESSION['role'] !== 'superadmin') {
  header("Location: ../../public/login/index.php");
  exit();
} 

if (isset($_POST['order_id']) && isset($_POST['status'])) {
  $order_id = $_POST['order_id'];
  $new_status = $_POST['status'];

  $update_sql = "UPDATE orders SET status = :status WHERE id = :order_id";
  $stmt = $pdo->prepare($update_sql);
  $stmt->bindParam(':status', $new_status, PDO::PARAM_STR);
  $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
  $stmt->execute();

  // Prevent form resubmission issue
  header("Location: view_orders.php");
  exit;
}


$status_filter = '';
if (isset($_GET['status']) && in_array($_GET['status'], ['pending', 'processing', 'completed', 'cancelled'])) {
  $status_filter = $_GET['status'];
}

$sql = "SELECT orders.id, users.name AS user_name, orders.total_price, orders.status, 
                coupons.discount_value AS discount_value
        FROM orders
        JOIN users ON orders.user_id = users.id
        LEFT JOIN coupons ON orders.coupon_id = coupons.id";

if ($status_filter) {
  $sql .= " WHERE orders.status = :status";
}

$sql .= " ORDER BY orders.created_at DESC";


$stmt = $pdo->prepare($sql);

if ($status_filter) {
  $stmt->bindParam(':status', $status_filter, PDO::PARAM_STR);
}

$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Orders</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">

  <link rel="stylesheet" href="../home/style.css">

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
        <div class="container mt-5">
          <h1>View Orders</h1>

          <form method="GET" action="view_orders.php" class="mb-4">
            <select name="status" class="form-control w-25 d-inline-block">
              <option value="">All Statuses</option>
              <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
              <option value="processing" <?php echo (isset($_GET['status']) && $_GET['status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
              <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
              <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
          </form>

          <table class="table table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>User Name</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Discount</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $order): ?>
                <tr>
                  <td><?php echo $order['id']; ?></td>
                  <td><?php echo $order['user_name']; ?></td>
                  <td><?php echo '$' . number_format($order['total_price'], 2); ?></td>
                  <td><?php echo ucfirst($order['status']); ?></td>
                  <td><?php echo isset($order['discount_value']) ? '$' . number_format($order['discount_value'], 2) : 'No Discount'; ?></td>
                  <td>

                    <div class="btn-container">
                      <button class="btn btn-success mt-2"
                        data-toggle="modal"
                        data-target="#productModal"
                        data-orderid="<?php echo $order['id']; ?>"
                        data-status="<?php echo $order['status']; ?>">
                        Update
                      </button>
                      <a href="view_order_products.php?order_id=<?php echo $order['id']; ?>" class="btn btn-info mt-2">
                        Show Products
                      </a>
                    </div>

                  </td>

                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Update</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div id="ordersStatus">
                  <form method="POST" action="view_orders.php">
                    <input type="hidden" name="order_id" value="">
                    <select name="status" class="form-control">
                      <option value="pending">Pending</option>
                      <option value="processing">Processing</option>
                      <option value="completed">Completed</option>
                      <option value="cancelled">Cancelled</option>
                    </select>
                    <button type="submit" class="btn btn-success mt-2" name="update_status">Update</button>
                  </form>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>

      </main>
    </div>
  </div>


  <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    $(document).ready(function() {
      $('#productModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        if (button.length) { // Ensure button exists
          var orderId = button.data('orderid');
          var status = button.data('status');

          var modal = $(this);
          modal.find('input[name="order_id"]').val(orderId);
          modal.find('select[name="status"]').val(status);
        }
      });
    });
  </script>

</body>

</html>