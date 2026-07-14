<?php
require_once "config.php";
requireLogin();

// التحقق من وجود معرّف المنتج
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: products.php");
    exit;
}

$id = $_GET['id'];
$product_name = "";

// جلب اسم المنتج لعرضه في رسالة التأكيد
$sql = "SELECT name FROM products WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $param_id);
    $param_id = $id;
    
    if ($stmt->execute()) {
        $stmt->bind_result($name);
        $stmt->fetch();
        $product_name = $name;
    }
    $stmt->close();
}

// معالجة طلب الحذف
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (deleteProduct($id, $conn)) {
        header("location: products.php?message=" . urlencode("تم حذف المنتج بنجاح!") . "&message_class=success");
        exit();
    } else {
        $error = "حدث خطأ أثناء حذف المنتج. يرجى المحاولة مرة أخرى.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حذف المنتج - نظام إدارة المصنع</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #2c3e50, #4a6572);
            color: #ecf0f1;
            min-height: 100vh;
        }
        
        .header {
            background: rgba(0, 0, 0, 0.7);
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
            border-bottom: 2px solid #e74c3c;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #e74c3c;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .nav {
            background: rgba(0, 0, 0, 0.6);
            padding: 15px;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            border-bottom: 1px solid #7f8c8d;
        }
        
        .nav a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 8px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            transition: all 0.3s;
            font-weight: 500;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }
        
        .nav a:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
            background: linear-gradient(135deg, #2980b9, #3498db);
        }
        
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
            color: #e74c3c;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .confirmation-box {
            background: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid #e74c3c;
            text-align: center;
        }
        
        .warning-icon {
            font-size: 4rem;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        
        .confirmation-message {
            font-size: 1.2rem;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        
        .product-name {
            font-weight: bold;
            color: #e74c3c;
            font-size: 1.3rem;
        }
        
        .btn-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #7f8c8d, #95a5a6);
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }
        
        .error-message {
            color: #e74c3c;
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            margin-top: 50px;
            background: rgba(0, 0, 0, 0.7);
            border-top: 2px solid #3498db;
            color: #bdc3c7;
        }
        
        @media (max-width: 768px) {
            .nav {
                flex-direction: column;
                align-items: center;
            }
            
            .nav a {
                width: 80%;
                text-align: center;
            }
            
            .btn-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>نظام إدارة مصنع الإلكترونيات</h1>
        <p>حذف منتج من النظام</p>
    </div>
    
    <div class="nav">
        <a href="index.php">الصفحة الرئيسية</a>
        <a href="products.php">إدارة المنتجات</a>
        <a href="add_product.php">إضافة منتج جديد</a>
        <a href="logout.php">تسجيل الخروج</a>
    </div>
    
    <div class="container">
        <h2 class="page-title">حذف المنتج</h2>
        
        <div class="confirmation-box">
            <div class="warning-icon">⚠️</div>
            
            <div class="confirmation-message">
                <p>هل أنت متأكد من أنك تريد حذف المنتج التالي؟</p>
                <p class="product-name">"<?php echo $product_name; ?>"</p>
                <p>هذا الإجراء لا يمكن التراجع عنه.</p>
            </div>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id; ?>" method="post">
                <div class="btn-group">
                    <button type="submit" class="btn btn-danger">نعم، احذف المنتج</button>
                    <a href="products.php" class="btn btn-secondary">إلغاء والعودة</a>
                </div>
            </form>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="footer">
        <p>© 2023 نظام إدارة مصنع الإلكترونيات. جميع الحقوق محفوظة.</p>
    </div>
</body>
</html>