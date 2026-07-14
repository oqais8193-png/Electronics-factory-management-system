<?php
require_once "config.php";
requireLogin();

// التحقق من وجود معرّف المنتج
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: products.php");
    exit;
}

$id = $_GET['id'];

// جلب بيانات المنتج
$sql = "SELECT * FROM products WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $param_id);
    $param_id = $id;
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $name = $row['name'];
            $category = $row['category'];
            $specifications = $row['specifications'];
            $quantity = $row['quantity'];
            $price = $row['price'];
            $supplier = $row['supplier'];
            $manufacturing_date = $row['manufacturing_date'];
            $status = $row['status'];
            $created_at = $row['created_at'];
            $updated_at = $row['updated_at'];
            
            // تحويل حالة المنتج إلى نص
            $status_text = "";
            if ($status == 'in_stock') $status_text = "متوفر";
            else if ($status == 'low_stock') $status_text = "منخفض";
            else if ($status == 'out_of_stock') $status_text = "غير متوفر";
            
            // تحويل التاريخ إلى صيغة مقروءة
            $manufacturing_date_formatted = $manufacturing_date ? date('Y-m-d', strtotime($manufacturing_date)) : 'غير محدد';
            $created_at_formatted = date('Y-m-d H:i', strtotime($created_at));
            $updated_at_formatted = date('Y-m-d H:i', strtotime($updated_at));
        } else {
            header("location: products.php");
            exit;
        }
    } else {
        echo "حدث خطأ أثناء جلب بيانات المنتج.";
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض المنتج - نظام إدارة المصنع</title>
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
            border-bottom: 2px solid #3498db;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #3498db;
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
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
            color: #3498db;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .product-details {
            background: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid #34495e;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #34495e;
        }
        
        .detail-label {
            flex: 1;
            font-weight: 600;
            color: #3498db;
        }
        
        .detail-value {
            flex: 2;
            color: #ecf0f1;
        }
        
        .status-in_stock {
            padding: 5px 10px;
            border-radius: 5px;
            background: #27ae60;
            color: white;
            font-weight: 500;
        }
        
        .status-low_stock {
            padding: 5px 10px;
            border-radius: 5px;
            background: #f39c12;
            color: white;
            font-weight: 500;
        }
        
        .status-out_of_stock {
            padding: 5px 10px;
            border-radius: 5px;
            background: #e74c3c;
            color: white;
            font-weight: 500;
        }
        
        .specifications {
            white-space: pre-line;
            line-height: 1.6;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }
        
        .action-btn {
            padding: 12px 25px;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .edit-btn {
            background: linear-gradient(135deg, #f39c12, #e67e22);
        }
        
        .back-btn {
            background: linear-gradient(135deg, #7f8c8d, #95a5a6);
        }
        
        .action-btn:hover {
            opacity: 0.9;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
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
            .detail-row {
                flex-direction: column;
            }
            
            .detail-label {
                margin-bottom: 5px;
            }
            
            .nav {
                flex-direction: column;
                align-items: center;
            }
            
            .nav a {
                width: 80%;
                text-align: center;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>نظام إدارة مصنع الإلكترونيات</h1>
        <p>عرض تفاصيل المنتج</p>
    </div>
    
    <div class="nav">
        <a href="index.php">الصفحة الرئيسية</a>
        <a href="products.php">إدارة المنتجات</a>
        <a href="add_product.php">إضافة منتج جديد</a>
        <a href="logout.php">تسجيل الخروج</a>
    </div>
    
    <div class="container">
        <h2 class="page-title">تفاصيل المنتج</h2>
        
        <div class="product-details">
            <div class="detail-row">
                <div class="detail-label">اسم المنتج:</div>
                <div class="detail-value"><?php echo $name; ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">الفئة:</div>
                <div class="detail-value"><?php echo $category; ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">المواصفات:</div>
                <div class="detail-value specifications"><?php echo $specifications ? $specifications : 'لا توجد مواصفات'; ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">الكمية:</div>
                <div class="detail-value"><?php echo $quantity; ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">السعر:</div>
                <div class="detail-value"><?php echo number_format($price, 2); ?> ر.س</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">المورد:</div>
                <div class="detail-value"><?php echo $supplier ? $supplier : 'غير محدد'; ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">تاريخ التصنيع:</div>
                <div class="detail-value"><?php echo $manufacturing_date_formatted; ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">الحالة:</div>
                <div class="detail-value"><span class="status-<?php echo $status; ?>"><?php echo $status_text; ?></span></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">تاريخ الإضافة:</div>
                <div class="detail-value"><?php echo $created_at_formatted; ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">آخر تحديث:</div>
                <div class="detail-value"><?php echo $updated_at_formatted; ?></div>
            </div>
            
            <div class="action-buttons">
                <a href="edit_product.php?id=<?php echo $id; ?>" class="action-btn edit-btn">تعديل المنتج</a>
                <a href="products.php" class="action-btn back-btn">العودة إلى القائمة</a>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p>© 2023 نظام إدارة مصنع الإلكترونيات. جميع الحقوق محفوظة.</p>
    </div>
</body>
</html>