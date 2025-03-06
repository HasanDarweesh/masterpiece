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