<?php
header('Content-Type: text/html; charset=utf-8');
require_once "../../includes/database/config.php";

$error_code = "";
$error_discount = "";
$error = "";

$code = "";
$discount = "";
$expiry = "";
$active = 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $discount = $_POST['discount_value'];
    $expiry = $_POST['expiration_date'];
    $active = isset($_POST['active']) ? 1 : 0;

    if ($discount < 0 || $discount > 100) {
        $error_discount = "❌ Discount percentage must be between 0% and 100.";
    }

    $checkStmt = $pdo->prepare("SELECT id FROM coupons WHERE code = ?");
    $checkStmt->execute([$code]);

    if ($checkStmt->rowCount() > 0) {
        $error_code = "❌ This coupon code already exists. Please use a different code.";
    }

    if (empty($error_code) && empty($error_discount)) {
        $stmt = $pdo->prepare("INSERT INTO coupons (code, discount_value, expiration_date, active) VALUES (?, ?, ?, ?)");
        $stmt->execute([$code, $discount, $expiry, $active]);
        header("Location: coupons.php?success=added");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Coupon</title>
    <link rel="stylesheet" href="coupon.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">


</head>
<body>

<div class="container-fluid">
    <div class="row">
<?php include('navbar.php'); ?>
        <!-- Sidebar -->
        <?php include('sidebar.php'); ?>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4">
            


            <form method="post">
                <label>Coupon Code:</label>
                <input type="text" name="code" value="<?= htmlspecialchars($code) ?>" required><br>

                <label>Discount Percentage:</label>
                <input type="number" name="discount_value" step="0.01" value="<?= htmlspecialchars($discount) ?>" placeholder="%" required><br>

                <label>Expiration Date:</label>
                <input type="date" name="expiration_date" value="<?= htmlspecialchars($expiry) ?>" required><br>

                <label>Active:</label>
                <input type="checkbox" name="active" value="1" <?= $active ? 'checked' : '' ?>><br>

                <button type="submit" class="btn btn-success">Add Coupon</button>
                <a href="coupons.php" class="btn btn-secondary">Back to List</a>

                <?php if (!empty($error_code) || !empty($error_discount)): ?>
                    <div class="form-error">
                        <?= !empty($error_code) ? $error_code . "<br>" : "" ?>
                        <?= !empty($error_discount) ? $error_discount : "" ?>
                    </div>
                <?php endif; ?>
            </form>
        </main>

    </div>
</div>

</body>
</html>
