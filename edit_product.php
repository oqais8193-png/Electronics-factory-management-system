<?php
require_once "config.php";
requireLogin();

// التحقق من وجود معرّف المنتج
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: products.php");
    exit;
}

$id = $_GET['id'];
$name = $category = $specifications = $quantity = $price = $supplier = $manufacturing_date = "";
$name_err = $category_err = $quantity_err = $price_err = "";

// جلب بيانات المنتج الحالية
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
        } else {
            header("location: products.php");
            exit;
        }
    } else {
        echo "حدث خطأ أثناء جلب بيانات المنتج.";
    }
    
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // التحقق من الاسم
    if (empty(trim($_POST["name"]))) {
        $name_err = "يرجى إدخال اسم المنتج.";
    } else {
        $name = trim($_POST["name"]);
    }
    
    // التحقق من الفئة
    if (empty(trim($_POST["category"]))) {
        $category_err = "يرجى إدخال فئة المنتج.";
    } else {
        $category = trim($_POST["category"]);
    }
    
    // المواصفات (اختياري)
    $specifications = trim($_POST["specifications"]);
    
    // التحقق من الكمية
    if (empty(trim($_POST["quantity"]))) {
        $quantity_err = "يرجى إدخال كمية المنتج.";
    } else {
        $quantity = trim($_POST["quantity"]);
    }
    
    // التحقق من السعر
    if (empty(trim($_POST["price"]))) {
        $price_err = "يرجى إدخال سعر المنتج.";
    } else {
        $price = trim($_POST["price"]);
    }
    
    // المورد (اختياري)
    $supplier = trim($_POST["supplier"]);
    
    // تاريخ التصنيع (اختياري)
    $manufacturing_date = trim($_POST["manufacturing_date"]);
    
    // إذا لم توجد أخطاء، نقوم بتحديث البيانات في قاعدة البيانات
    if (empty($name_err) && empty($category_err) && empty($quantity_err) && empty($price_err)) {
        $sql = "UPDATE products SET name=?, category=?, specifications=?, quantity=?, price=?, supplier=?, manufacturing_date=? WHERE id=?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssidssi", $param_name, $param_category, $param_specifications, $param_quantity, $param_price, $param_supplier, $param_manufacturing_date, $param_id);
            
            // تعيين المعاملات
            $param_name = $name;
            $param_category = $category;
            $param_specifications = $specifications;
            $param_quantity = $quantity;
            $param_price = $price;
            $param_supplier = $supplier;
            $param_manufacturing_date = $manufacturing_date;
            $param_id = $id;
            
            if ($stmt->execute()) {
                // تحديث حالة المنتج بناء على الكمية الجديدة
                updateProductStatus($id, $conn);
                
                header("location: products.php");
                exit();
            } else {
                echo "حدث خطأ أثناء تعديل المنتج. يرجى المحاولة مرة أخرى.";
            }
            
            $stmt->close();
        }
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل المنتج - نظام إدارة المصنع</title>
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
            max-width: 800px;
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
        
        .product-form {
            background: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid #34495e;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #3498db;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #34495e;
            border-radius: 8px;
            background: rgba(52, 73, 94, 0.6);
            color: #ecf0f1;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #3498db;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.5);
            outline: none;
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .error {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #f39c12, #e67e22);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            background: linear-gradient(135deg, #e67e22, #f39c12);
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
            .nav {
                flex-direction: column;
                align-items: center;
            }
            
            .nav a {
                width: 80%;
                text-align: center;
            }
            
            .product-form {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>نظام إدارة مصنع الإلكترونيات</h1>
        <p>تعديل بيانات المنتج</p>
    </div>
    
    <div class="nav">
        <a href="index.php">الصفحة الرئيسية</a>
        <a href="products.php">إدارة المنتجات</a>
        <a href="add_product.php">إضافة منتج جديد</a>
        <a href="logout.php">تسجيل الخروج</a>
    </div>
    
    <div class="container">
        <h2 class="page-title">تعديل المنتج</h2>
        
        <div class="product-form">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id; ?>" method="post">
                <div class="form-group">
                    <label for="name">اسم المنتج *</label>
                    <input type="text" id="name" name="name" value="<?php echo $name; ?>" required>
                    <span class="error"><?php echo $name_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="category">الفئة *</label>
                    <input type="text" id="category" name="category" value="<?php echo $category; ?>" required>
                    <span class="error"><?php echo $category_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="specifications">المواصفات</label>
                    <textarea id="specifications" name="specifications"><?php echo $specifications; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="quantity">الكمية *</label>
                    <input type="number" id="quantity" name="quantity" min="0" value="<?php echo $quantity; ?>" required>
                    <span class="error"><?php echo $quantity_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="price">السعر (ريال سعودي) *</label>
                    <input type="number" id="price" name="price" min="0" step="0.01" value="<?php echo $price; ?>" required>
                    <span class="error"><?php echo $price_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="supplier">المورد</label>
                    <input type="text" id="supplier" name="supplier" value="<?php echo $supplier; ?>">
                </div>
                
                <div class="form-group">
                    <label for="manufacturing_date">تاريخ التصنيع</label>
                    <input type="date" id="manufacturing_date" name="manufacturing_date" value="<?php echo $manufacturing_date; ?>">
                </div>
                
                <button type="submit" class="btn-submit">تحديث المنتج</button>
            </form>
        </div>
    </div>
    
    <div class="footer">
        <p>© 2023 نظام إدارة مصنع الإلكترونيات. جميع الحقوق محفوظة.</p>
    </div>
</body>
</html>