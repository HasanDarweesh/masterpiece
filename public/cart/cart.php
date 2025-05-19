<?php
require "../../includes/database/config.php";
include("../../includes/navbar/index.php");
$sql = "SELECT
        order_items.id,
        order_items.order_id,
        order_items.product_id,
        order_items.quantity,
        order_items.price,
        order_items.print_text,
        orders.total_price,
        products.name,
        products.image,
        products.price,
          (order_items.quantity * products.price) AS total_amount
        FROM
          order_items
          JOIN products ON order_items.product_id = products.id
          JOIN orders ON order_items.order_id = orders.id
        WHERE
          orders.user_id = :user_id  AND orders.status = 'pending'";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="Untree.co">
  <link rel="shortcut icon" href="store.png">

  <meta name="description" content="" />
  <meta name="keywords" content="bootstrap, bootstrap4" />

		<!-- Bootstrap CSS -->
		<link href="../../includes/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
		<link href="../../includes/css/tiny-slider.css" rel="stylesheet">
		<link href="../../includes/css/style.css" rel="stylesheet">
		<link href="../../includes/css/cart.css" rel="stylesheet">
		<title>Craftify: Cart </title>
	</head>

	<body>
		<!-- Start Hero Section -->
	  <div class="container d-flex justify-content-start align-items:center mt-5">
						<div class="intro-excerpt">
							<h2 class="cart">Cart</h2>
						</div>
			</div>
	
	<!-- End Hero Section -->
  <div class="untree_co-section before-footer-section">
    <div class="container">
      <div class="row">
        <?php if (empty($order_items)): ?>
          <div class="text-center w-100"><h4>Your cart is empty.</h4></div>
        <?php else: ?>
          <!-- Set the form action to update_cart.php -->
          <form class="<?php echo empty($order_items) ? 'col-md-12' : 'col-md-8'; ?>" method="post" action="update_cart.php">
            <div class="site-blocks-table table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th class="product-thumbnail">Image</th>
                    <th class="product-name">Product</th>
                    <th class="product-text">Custom Text</th>
                    <th class="product-price">Price</th>
                    <th class="product-quantity">Quantity</th>
                    <th class="product-total">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $total = 0.00;
                  foreach ($order_items as $item): 
                    $total += floatval($item['total_amount']);
                  ?>
                  <tr>
                    <td class="product-thumbnail" data-label="Image">
                      <img src="<?php echo "../../admin/product/uploads/product_images/" . htmlspecialchars($item['image']); ?>" alt="Image" class="img-fluid" width="100px">
                    </td>
                    <td class="product-name" data-label="Product">
                      <h2 class="h5 text-black"><?php echo htmlspecialchars($item['name']); ?></h2>
                    </td>
                    <td data-label="Custom Text"><?php echo htmlspecialchars($item['print_text']); ?></td>
                    <td data-label="Price">JOD <?php echo htmlspecialchars($item['price']); ?></td>
                    <td data-label="Quantity">
                      <div class="input-group mb-3 d-flex align-items-center quantity-container" style="max-width: 120px;">
                        <div class="input-group-prepend">
                          <button class="btn btn-outline-black decrease" type="button">&minus;</button>
                        </div>
                        <!-- Set the input's name to include the order_item id -->
                        <input type="text" class="form-control text-center quantity-amount" name="quantities[<?php echo $item['id']; ?>]" value="<?php echo htmlspecialchars($item['quantity']); ?>" aria-label="Quantity">
                        <div class="input-group-append">
                          <button class="btn btn-outline-black increase" type="button">&plus;</button>
                        </div>
                      </div>
                    </td>
                    <td data-label="Total">JOD <?php echo htmlspecialchars($item['total_amount']); ?></td>
                    <td data-label="Remove">
                      <!-- Trash button triggers the modal -->
                      <button type="button" class="btn btn-danger btn-md delete-btn" 
                              data-order-item-id="<?php echo htmlspecialchars($item['id']); ?>"
                              data-order-id="<?php echo htmlspecialchars($item['order_id']); ?>"
                              data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i style="color: #D11E1E"  class="bi bi-trash"></i>
                      </button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <div class="row mb-5">
              <div class="col-md-auto mb-3 mb-md-0">
                <!-- Hidden input field for total price -->
                <input type="hidden" name="total" value="<?php echo $total; ?>">
                <!-- Submit this form to update the quantities -->
                <button class="btn btn-black btn-sm btn-block" type="submit">Update Cart</button>
              </div>
              <div class="col-md-auto">
                <a href="../products/shop.php">
                  <button type="button" class="btn btn-outline btn-sm btn-block">Continue Shopping</button>
                </a>
              </div>
            </div>
          </form>
          <div class="col-md-4">
            <div class="row justify-content-end">
              <div class="col-md-12 text-right border-bottom mb-5">
                <h3 class="text-black h4 text-uppercase">Cart Totals</h3>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <span class="text-black">Subtotal</span>
              </div>
              <div class="col-md-6 text-right">
                <strong class="text-black">JOD <?php echo number_format($total, 2); ?></strong>
              </div>
            </div>
            <div class="row mb-5">
              <div class="col-md-6">
                <span class="text-black">Shipping & Handling</span>
              </div>
              <div class="col-md-6 text-right">
                <strong class="text-black">Free</strong>
              </div>
            </div>
            <div class="row mb-5">
              <div class="col-md-6">
                <span class="text-black">Total</span>
              </div>
              <div class="col-md-6 text-right">
                <strong class="text-black">JOD <?php echo number_format($total, 2); ?></strong>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <a href="../checkout/index.php"><button class="btn btn-black btn-lg btn-block cart-btn" onclick="window.location='checkout.html'">Proceed To Checkout</button></a>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Delete Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="deleteForm" action="delete_cart.php" method="POST">
        <input type="hidden" name="order_item_id" value="">
        <input type="hidden" name="order_id" value="">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Remove item</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to remove this item from your cart?
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-danger remove-btn">Remove</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  
  
  <!-- Set the order_item_id in the modal when a delete button is clicked -->
  <script>
  document.addEventListener("DOMContentLoaded", function() {
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');

    deleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget; // Button that triggered the modal
        const orderItemId = button.getAttribute('data-order-item-id');
        const orderId = button.getAttribute('data-order-id');

        // Update the modal's hidden input fields
        deleteForm.querySelector('input[name="order_item_id"]').value = orderItemId;
        deleteForm.querySelector('input[name="order_id"]').value = orderId;
    });
});
</script>

  <script>
document.addEventListener("DOMContentLoaded", function() {
    // Function to calculate and update the total
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.quantity-amount').forEach(function(input) {
            const quantity = parseInt(input.value);
            const price = parseFloat(input.closest('tr').querySelector('[data-label="Price"]').textContent.replace('JOD ', ''));
            total += quantity * price;
        });

        // Update the hidden input field for total
        document.querySelector('input[name="total"]').value = total.toFixed(2);
        // Update the display of the total price
        document.querySelector('.cart-totals strong.text-black').textContent = 'JOD ' + total.toFixed(2);
    }

    // Update total on quantity change
    document.querySelectorAll('.quantity-amount').forEach(function(input) {
        input.addEventListener('input', updateTotal);
    });

    // Ensure the total is updated before form submission
    const form = document.querySelector('form[action="update_cart.php"]');
    form.addEventListener('submit', function(event) {
        updateTotal(); // Ensure total is updated before submitting the form
    });

    // Update total initially in case quantities were pre-filled
    updateTotal();
});
  </script>


		<!-- Start Footer Section -->
      <?php include("../../includes/footer/index.php") ?>
		<!-- End Footer Section -->	


		<script src="../../includes/js/bootstrap.bundle.min.js"></script>
		<script src="../../includes/js/tiny-slider.js"></script>
		<script src="../../includes/js/custom.js"></script>
	</body>

</html>
