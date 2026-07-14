<?php
session_start();

// إعدادات الاتصال بقاعدة البيانات
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'electronics_factory');

// إنشاء الاتصال
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// تعيين ترميز الأحرف
$conn->set_charset("utf8");

// التحقق من تسجيل الدخول
function isLoggedIn() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}

// إعادة التوجيه إذا لم يكن المستخدم مسجلاً
function requireLogin() {
    if (!isLoggedIn()) {
        header("location: login.php");
        exit;
    }
}

// إعادة التوجيه إذا كان المستخدم مسجلاً
function requireLogout() {
    if (isLoggedIn()) {
        header("location: index.php");
        exit;
    }
}

// دالة لتحديث حالة المنتج بناء على الكمية
function updateProductStatus($product_id, $conn) {
    // جلب كمية المنتج
    $sql = "SELECT quantity FROM products WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->bind_result($quantity);
        $stmt->fetch();
        $stmt->close();
        
        // تحديد الحالة بناء على الكمية
        $status = 'in_stock';
        if ($quantity <= 0) {
            $status = 'out_of_stock';
        } elseif ($quantity < 10) {
            $status = 'low_stock';
        }
        
        // تحديث حالة المنتج
        $update_sql = "UPDATE products SET status = ? WHERE id = ?";
        if ($update_stmt = $conn->prepare($update_sql)) {
            $update_stmt->bind_param("si", $status, $product_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
    }
}

// دالة لحذف المنتج (حذف ناعم)
function deleteProduct($product_id, $conn) {
    // التحقق مما إذا كان عمود 'deleted' موجوداً
    $column_check = $conn->query("SHOW COLUMNS FROM products LIKE 'deleted'");
    $deleted_column_exists = ($column_check->num_rows > 0);
    
    if ($deleted_column_exists) {
        $sql = "UPDATE products SET deleted = 1 WHERE id = ?";
    } else {
        $sql = "DELETE FROM products WHERE id = ?";
    }
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $param_id);
        $param_id = $product_id;
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
        
        $stmt->close();
    }
    
    return false;
}

// دالة للتحقق من وجود عمود في الجدول
function columnExists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
    return ($result->num_rows > 0);
}
?>