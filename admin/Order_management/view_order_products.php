<?php
require_once '../../includes/database/config.php';

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    $sql = "SELECT products.name AS product_name, order_items.quantity, order_items.price, products.image, order_items.print_text
    FROM order_items
    JOIN products ON order_items.product_id = products.id
    WHERE order_items.order_id = :order_id";


    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->execute();

    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    header("Location: view_orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Product Details</title>
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
                <!-- User Breakdown Widget, full width -->
                <div class="container mt-5">

                    <h1>Order Products</h1>
                    <a href="view_orders.php" class="btn btn-primary mb-3">Back to Orders</a>

                    <?php if (count($orderItems) > 0): ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product Image</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Custom Text</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderItems as $item): ?>
                                    <tr>
                                        <td>
                                            <?php if ($item['image']): ?>
                                                <img src="../../admin/product/uploads/product_images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="img-fluid" style="max-width: 100px;">
                                            <?php else: ?>
                                                <img src="default-image.jpg" alt="No Image" class="img-fluid" style="max-width: 100px;">
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo $item['print_text']; ?></td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No products found for this order.</p>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>
</body>

</html>