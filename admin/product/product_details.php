<?php
require_once "../../includes/database/config.php";
include("../../includes/navbar/index.php");

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; 
}

function getProductDetails($product_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    return $stmt->fetch();
}

// فحص ما إذا كان المستخدم مسجل دخول
function isUserLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
            $product_id = $product['id'];
            $quantity = 1;
            $print_text = isset($_POST['print_text']) ? $_POST['print_text'] : '';

            // التحقق من تسجيل الدخول
            if (isUserLoggedIn()) {
                // للمستخدمين المسجلين - حفظ في قاعدة البيانات
                $user_id = $_SESSION['user_id'];
                
                // البحث عن طلب معلق للمستخدم
                $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? AND status = 'pending' LIMIT 1");
                $stmt->execute([$user_id]);
                $order = $stmt->fetch();

                if ($order) {
                    $order_id = $order['id'];
                } else {
                    // إنشاء طلب جديد
                    $stmt = $pdo->prepare("INSERT INTO orders (user_id, status, created_at) VALUES (?, 'pending', NOW())");
                    $stmt->execute([$user_id]);

                    if ($stmt->rowCount() > 0) {
                        $order_id = $pdo->lastInsertId();
                    } else {
                        echo "Error creating order.";
                        exit();
                    }
                }

                // إضافة المنتج لعناصر الطلب
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, print_text) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$order_id, $product_id, $quantity, $product['price'], $print_text]);

                if ($stmt->rowCount() > 0) {
                    $success_message = "Product added to order successfully.";
                } else {
                    $error_message = "Error adding product to order.";
                }
            }

            // إضافة المنتج للسلة (للمستخدمين المسجلين والضيوف)
            if (!isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] = [
                    'quantity' => $quantity,
                    'product_details' => $product,
                    'print_text' => $print_text
                ];
            } else {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                // تحديث النص المخصص إذا تم إدخاله
                if (!empty($print_text)) {
                    $_SESSION['cart'][$product_id]['print_text'] = $print_text;
                }
            }

            // إعادة توجيه للسلة
            header('Location: ../../public/cart/cart.php');
            exit();
        }
    } else {
        echo "Product not found.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="Untree.co">
  <link rel="shortcut icon" href="favicon.png">

  <meta name="description" content="" />
  <meta name="keywords" content="bootstrap, bootstrap4" />

  <!-- Bootstrap CSS -->
  <link href="../../includes/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../includes/css/style.css" rel="stylesheet">
  <link href="../../includes/css/tiny-slider.css" rel="stylesheet">
  <link href="../../includes/css/customlmg.css" rel="stylesheet">

  <title>View Details</title>
</head>

<body>
<!-- Start Blog Section -->
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <div class="card shadow-lg border-0">
                <div class="row g-0">
                    <div class="col-md-6">
                        <div class="product-image-container" style="position: relative;">
                            <img src="../../admin/product/uploads/product_images/<?php echo htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                 class="img-fluid rounded-start w-100">
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="card-body text-center">
                            <h2 class="card-title text-primary"><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <h3 class="text-danger"><?php echo 'JOD ' . number_format($product['price'], 2); ?></h3>

                            <?php if (!isUserLoggedIn()): ?>
                                <div class="alert alert-info mt-3">
                                    <small>You're shopping as a guest. <a href="../../public/login/index.php">Login</a> to save your order history.</small>
                                </div>
                            <?php endif; ?>

                            <!-- Add to cart -->
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                                <!-- Text input for print text -->
                                <div class="form-group">
                                    <label for="print_text">more details</label>
                                    <textarea class="form-control" name="print_text" id="print_text" rows="3"></textarea>
                                </div>

                                <button type="submit" name="add_to_cart" class="btn btn-outline-secondary btn-lg mt-4">Add to Cart</button>
                            </form>

                            <a href="../../public/products/shop.php" class="btn btn-outline-secondary btn-lg mt-4">Back to Store</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Product Details Section -->

<!-- Start Footer Section -->
<?php include '../../includes/footer/index.php'; ?>
<!-- End Footer Section -->

<script src="../../includes/js/bootstrap.bundle.min.js"></script>
<script src="../../includes/js/tiny-slider.js"></script>
<script src="../../includes/js/custom.js"></script>
</body>
</html>