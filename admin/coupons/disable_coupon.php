<?php
require_once "../../includes/database/config.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    header("Location: coupons.php");
    exit;
}

$checkStmt = $pdo->prepare("SELECT id FROM coupons WHERE id = ?");
$checkStmt->execute([$id]);

if ($checkStmt->rowCount() == 0) {
    header("Location: coupons.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_disable'])) {
    $stmt = $pdo->prepare("UPDATE coupons SET active = 0 WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: coupons.php?success=disabled");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Disable Coupon</title>
   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="coupon.css">
</head>
<body>

<div class="container-fluid">
    <div class="row">
 <?php include('navbar.php'); ?>
        <!-- Sidebar -->
        <?php include('sidebar.php'); ?>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4">
           

            <div class="modal mt-5">
                <div class="modal-content">
                    <h3>Are you sure you want to disable this coupon?</h3>
                    <form method="post">
                        <input type="hidden" name="confirm_disable" value="1">
                        <button type="submit" class="btn btn-success">Yes, disable it!</button>
                        <a href="coupons.php" class="btn btn-danger">No, keep it</a>
                    </form>
                </div>
            </div>
        </main>

    </div>
</div>

<script>
function cancelDisable() {
    window.location.href = 'coupons.php';
}
</script>

</body>
</html>
