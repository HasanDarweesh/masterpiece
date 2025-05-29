<?php 
session_start(); 
require_once "../../includes/database/config.php";

// التحقق من إرسال البيانات
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['quantities']) && is_array($_POST['quantities']) && isset($_POST['total'])) {
    $total = floatval($_POST['total']);
    
    // التحقق من نوع المستخدم
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        // للمستخدمين المسجلين - التحديث في قاعدة البيانات
        $pdo->beginTransaction();
        
        foreach ($_POST['quantities'] as $order_item_id => $quantity) {
            $newQuantity = (int) $quantity;
            if ($newQuantity < 1) {
                $newQuantity = 1;
            }
            
            // تحديث الكمية في قاعدة البيانات
            $sql = "UPDATE order_items SET quantity = :quantity WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
            $stmt->bindParam(':id', $order_item_id, PDO::PARAM_INT);
            $stmt->execute();
        }
        
        // حساب السعر الإجمالي الجديد للطلب
        $order_item_id = array_key_first($_POST['quantities']);
        $orderQuery = "SELECT order_id FROM order_items WHERE id = :id";
        $orderStmt = $pdo->prepare($orderQuery);
        $orderStmt->bindParam(':id', $order_item_id, PDO::PARAM_INT);
        $orderStmt->execute();
        $order_id = $orderStmt->fetchColumn();
        
        // تحديث السعر الإجمالي في جدول الطلبات
        $updateOrderQuery = "UPDATE orders SET total_price = :total_price WHERE id = :order_id";
        $updateOrderStmt = $pdo->prepare($updateOrderQuery);
        $updateOrderStmt->bindParam(':total_price', $total, PDO::PARAM_STR);
        $updateOrderStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $updateOrderStmt->execute();
        
        $pdo->commit();
        
    } else {
        // للمستخدمين الضيوف - التحديث في الجلسة
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        foreach ($_POST['quantities'] as $product_id => $quantity) {
            $newQuantity = (int) $quantity;
            if ($newQuantity < 1) {
                $newQuantity = 1;
            }
            
            // تحديث الكمية في سلة الجلسة
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] = $newQuantity;
            }
        }
    }
    
    // إعادة التوجه إلى صفحة السلة
    header("Location: cart.php");
    exit();
} else {
    echo "No quantities or total were submitted.";
}
?>