<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Coupons List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="coupon.css">
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- Navbar -->
        <?php include('../../includes/admin/navbar/navbar.php'); ?>

        <!-- Sidebar -->
        <?php include('../../includes/admin/sidebar/sidebar.php'); ?>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4">
          

            <?php
            session_start();
            require_once "../../includes/database/config.php";

            if ($_SESSION['role']!== 'admin' && $_SESSION['role'] !== 'superadmin') {
                header("Location: ../../public/login/index.php");
                exit();
              } 

            if (isset($_GET['success'])) {
                $message = "";
                if ($_GET['success'] == 'added') {
                    $message = "✅ Coupon added successfully!";
                } elseif ($_GET['success'] == 'updated') {
                    $message = "✅ Coupon updated successfully!";
                } elseif ($_GET['success'] == 'restored') {
                    $message = "✅ Coupon restored successfully!";
                } elseif ($_GET['success'] == 'disabled') {
                    $message = "✅ Coupon disabled successfully!";
                }

                if ($message) {
                    echo '<div class="alert alert-success text-center">' . $message . '</div>';
                    echo '<script>setTimeout(function() { document.querySelector(".alert").style.display = "none"; }, 3000);</script>';
                }
            }

            $stmt = $pdo->prepare("SELECT * FROM coupons ORDER BY active DESC, updated_at DESC");
            $stmt->execute();
            $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <div class="text-end my-3">
                <a href="add_coupon.php" class="btn btn-success">Add Coupon</a>
            </div>

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Code</th>
                        <th>Discount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($coupons) == 0): ?>
                        <tr><td colspan="4" class="text-center">No coupons available.</td></tr>
                    <?php else: ?>
                        <?php foreach ($coupons as $coupon): ?>
                            <tr>
                                <td><?= htmlspecialchars($coupon['code']) ?></td>
                                <td><?= htmlspecialchars($coupon['discount_value']) ?>%</td>
                                <td><?= $coupon['active'] ? 'Active' : 'Inactive' ?></td>
                                <td>
                                    <a href="edit_coupon.php?id=<?= $coupon['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <?php if ($coupon['active']): ?>
                                        <a href="disable_coupon.php?id=<?= $coupon['id'] ?>" class="btn btn-danger btn-sm">Disable</a>
                                    <?php else: ?>
                                        <a href="restore_coupon.php?id=<?= $coupon['id'] ?>" class="btn btn-warning btn-sm">Restore</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

        </main>
    </div>
</div>

</body>
</html>
