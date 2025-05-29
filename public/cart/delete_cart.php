<?php
session_start();
require_once "../../includes/database/config.php";

// التحقق من نوع المستخدم
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    // للمستخدمين المسجلين - الحذف من قاعدة البيانات
    
    // التحقق من إرسال البيانات المطلوبة
    if (empty($_POST['order_item_id']) || empty($_POST['order_id'])) {
        die("Order item or order ID not specified.");
    }

    $order_item_id = (int) $_POST['order_item_id'];
    $order_id = (int) $_POST['order_id'];

    // بدء المعاملة
    $pdo->beginTransaction();

    // حذف عنصر الطلب من الجدول
    $query = "DELETE FROM order_items WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $order_item_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // فحص ما إذا كانت هناك عناصر متبقية في الطلب
        $checkQuery = "SELECT COUNT(*) FROM order_items WHERE order_id = :order_id";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->bindParam(":order_id", $order_id, PDO::PARAM_INT);
        $checkStmt->execute();
        $itemCount = $checkStmt->fetchColumn();

        if ($itemCount == 0) {
            // إذا لم تبق عناصر، احذف الطلب
            $deleteOrderQuery = "DELETE FROM orders WHERE id = :order_id";
            $deleteOrderStmt = $pdo->prepare($deleteOrderQuery);
            $deleteOrderStmt->bindParam(":order_id", $order_id, PDO::PARAM_INT);
            $deleteOrderStmt->execute();
        } else {
            // إعادة حساب السعر الإجمالي للطلب
            $totalPriceQuery = "
                SELECT SUM(oi.quantity * p.price) AS total_price
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :order_id
            ";
            $totalPriceStmt = $pdo->prepare($totalPriceQuery);
            $totalPriceStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $totalPriceStmt->execute();
            $newTotalPrice = $totalPriceStmt->fetchColumn();
            
            // تحديث السعر الإجمالي في جدول الطلبات
            $updateOrderQuery = "UPDATE orders SET total_price = :total_price WHERE id = :order_id";
            $updateOrderStmt = $pdo->prepare($updateOrderQuery);
            $updateOrderStmt->bindParam(':total_price', $newTotalPrice, PDO::PARAM_STR);
            $updateOrderStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $updateOrderStmt->execute();
        }

        // تأكيد المعاملة
        $pdo->commit();
    } else {
        // التراجع عن المعاملة في حالة الخطأ
        $pdo->rollBack();
        echo "Error deleting order item.";
        exit();
    }

} else {
    // للمستخدمين الضيوف - الحذف من الجلسة
    
    // التحقق من إرسال معرف المنتج
    if (empty($_POST['product_id'])) {
        die("Product ID not specified.");
    }

    $product_id = $_POST['product_id'];

    // إزالة المنتج من سلة الجلسة
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

// إعادة التوجه إلى صفحة السلة
header("Location: cart.php");
exit();
?>