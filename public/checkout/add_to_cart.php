<?php
session_start();
require_once '../../includes/database/config.php';


// التحقق من إذا كان المستخدم قد سجل الدخول
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to add items to your cart.";
    exit;
}

// الحصول على product_id والكمية من الطلب
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : (isset($_POST['product_id']) ? intval($_POST['product_id']) : 0);
$quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : (isset($_POST['quantity']) ? intval($_POST['quantity']) : 1);

// التحقق من صحة المدخلات
if ($product_id <= 0 || $quantity <= 0) {
    die("Invalid product or quantity.");
}

// استرجاع المنتج من قاعدة البيانات
$stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// التحقق من وجود المنتج
if (!$product) {
    die("Product not found.");
}

// الحصول على user_id من الجلسة
$user_id = $_SESSION['user_id'];

// التحقق من وجود طلب قيد المعالجة لهذا المستخدم
$stmt = $pdo->prepare("SELECT id FROM orders WHERE user_id = :user_id AND status = 'pending' ORDER BY id DESC LIMIT 1");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);


// إذا لم يكن هناك طلب قيد المعالجة، يتم إنشاء طلب جديد
if (!$order) {
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, status, shipping_phone, shipping_address) VALUES (:user_id, 0, 'pending', '', '')");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $order_id = $pdo->lastInsertId(); // الحصول على id الطلب الجديد
} else {
    $order_id = $order['id']; // استخدام id الطلب الموجود
}

// التحقق من وجود المنتج بالفعل في order_items
$stmt = $pdo->prepare("SELECT id, quantity FROM order_items WHERE order_id = :order_id AND product_id = :product_id");
$stmt->bindParam(':order_id', $order_id);
$stmt->bindParam(':product_id', $product_id);
$stmt->execute();
$item = $stmt->fetch(PDO::FETCH_ASSOC);

// إذا كان المنتج موجودًا في order_items، يتم تحديث الكمية
if ($item) {
    $new_quantity = $item['quantity'] + $quantity;
    $stmt = $pdo->prepare("UPDATE order_items SET quantity = :quantity WHERE order_id = :order_id AND product_id = :product_id");
    $stmt->bindParam(':quantity', $new_quantity);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
} else {
    // إذا لم يكن المنتج موجودًا في order_items، يتم إضافته
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)");
    $stmt->bindParam(':order_id', $order_id);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':price', $product['price']);
    $stmt->execute();
}

// تحديث السعر الإجمالي للطلب
$stmt = $pdo->prepare("UPDATE orders SET total_price = (SELECT SUM(quantity * price) FROM order_items WHERE order_id = :order_id) WHERE id = :order_id");
$stmt->bindParam(':order_id', $order_id);
$stmt->execute();

// إعادة توجيه المستخدم إلى صفحة الدفع
header('Location: ../checkout.html');
exit;
?>
