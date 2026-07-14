<?php
require_once "config.php";
requireLogin();

// في أعلى الصفحة بعد فتح الاتصال بقاعدة البيانات
$message = "";
$message_class = "";

if (isset($_GET['message']) && isset($_GET['message_class'])) {
    $message = urldecode($_GET['message']);
    $message_class = $_GET['message_class'];
}

// جلب جميع المنتجات من قاعدة البيانات مع التصفية إذا وجدت
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// التحقق مما إذا كان العمود 'deleted' موجوداً في الجدول
$column_check = $conn->query("SHOW COLUMNS FROM products LIKE 'deleted'");
$deleted_column_exists = ($column_check->num_rows > 0);

// بناء الاستعلام الأساسي
if ($deleted_column_exists) {
    $sql = "SELECT * FROM products WHERE deleted = 0";
} else {
    $sql = "SELECT * FROM products WHERE 1=1";
}

// إضافة شرط البحث إذا كان موجوداً
if (!empty($search)) {
    $sql .= " AND name LIKE '%" . $conn->real_escape_string($search) . "%'";
}

// إضافة شرط التصفية إذا كان موجوداً
if ($filter != 'all') {
    $sql .= " AND status = '$filter'";
}

// إضافة الترتيب
$sql .= " ORDER BY created_at DESC";

$result = $conn->query($sql);

// معالجة طلب الحذف إذا كان موجوداً
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    if ($deleted_column_exists && deleteProduct($delete_id, $conn)) {
        header("location: products.php?message=" . urlencode("تم حذف المنتج بنجاح!") . "&message_class=success");
        exit();
    } else {
        // إذا لم يكن عمود deleted موجوداً، استخدم حذفاً فعلياً
        $delete_sql = "DELETE FROM products WHERE id = $delete_id";
        if ($conn->query($delete_sql)) {
            header("location: products.php?message=" . urlencode("تم حذف المنتج بنجاح!") . "&message_class=success");
            exit();
        } else {
            $error = "حدث خطأ أثناء حذف المنتج.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المنتجات - نظام إدارة المصنع</title>
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
        
        .header p {
            font-size: 1.2rem;
            color: #bdc3c7;
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
            max-width: 1400px;
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
        
        .filter-section {
            background: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #34495e;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .filter-form {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .filter-label {
            font-weight: 600;
            color: #3498db;
            white-space: nowrap;
        }
        
        .filter-select, .search-input {
            padding: 10px 15px;
            border: 1px solid #34495e;
            border-radius: 8px;
            background: rgba(52, 73, 94, 0.6);
            color: #ecf0f1;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .search-input {
            min-width: 250px;
        }
        
        .filter-select:focus, .search-input:focus {
            border-color: #3498db;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.5);
            outline: none;
        }
        
        .filter-btn {
            padding: 10px 20px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .filter-btn:hover {
            background: linear-gradient(135deg, #2980b9, #3498db);
            transform: translateY(-2px);
        }
        
        .results-count {
            color: #bdc3c7;
            font-size: 16px;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 1rem;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        
        .products-table thead tr {
            background: linear-gradient(135deg, #3498db, #2980b9);
            text-align: right;
            color: #ecf0f1;
        }
        
        .products-table th,
        .products-table td {
            padding: 15px 20px;
            text-align: right;
            border-bottom: 1px solid #34495e;
        }
        
        .products-table tbody tr {
            transition: all 0.3s;
        }
        
        .products-table tbody tr:hover {
            background: rgba(52, 152, 219, 0.1);
            transform: scale(1.01);
        }
        
        .products-table tbody tr:last-of-type {
            border-bottom: 2px solid #3498db;
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
        
        .action-btn {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            margin-left: 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        
        .view-btn {
            background: #3498db;
        }
        
        .edit-btn {
            background: #f39c12;
        }
        
        .delete-btn {
            background: #e74c3c;
        }
        
        .action-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
        }
        
        .message.success {
            background: rgba(46, 204, 113, 0.2);
            color: #27ae60;
            border: 1px solid #27ae60;
        }
        
        .message.error {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            margin-top: 50px;
            background: rgba(0, 0, 0, 0.7);
            border-top: 2px solid #3498db;
            color: #bdc3c7;
        }
        
        .no-products {
            text-align: center;
            padding: 30px;
            color: #bdc3c7;
            font-size: 18px;
        }
        
        .search-container {
            position: relative;
        }
        
        .search-btn {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #3498db;
            cursor: pointer;
        }
        
        @media (max-width: 1024px) {
            .filter-section {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-input {
                min-width: 100%;
            }
        }
        
        @media (max-width: 768px) {
            .products-table {
                display: block;
                overflow-x: auto;
            }
            
            .nav {
                flex-direction: column;
                align-items: center;
            }
            
            .nav a {
                width: 80%;
                text-align: center;
            }
            
            .action-btns {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }
            
            .action-btn {
                margin-left: 0;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>نظام إدارة مصنع الإلكترونيات</h1>
        <p>إدارة المنتجات وعرض المخزون</p>
    </div>
    
    <div class="nav">
        <a href="index.php">الصفحة الرئيسية</a>
        <a href="products.php">إدارة المنتجات</a>
        <a href="add_product.php">إضافة منتج جديد</a>
        <a href="logout.php">تسجيل الخروج</a>
    </div>
    
    <div class="container">
        <h2 class="page-title">قائمة المنتجات</h2>
        
        <!-- قسم الفلتر والبحث -->
        <div class="filter-section">
            <form method="GET" action="products.php" class="filter-form">
                <div class="filter-group">
                    <span class="filter-label">تصفية حسب الحالة:</span>
                    <select name="filter" class="filter-select">
                        <option value="all" <?php echo $filter == 'all' ? 'selected' : ''; ?>>جميع المنتجات</option>
                        <option value="in_stock" <?php echo $filter == 'in_stock' ? 'selected' : ''; ?>>متوفر</option>
                        <option value="low_stock" <?php echo $filter == 'low_stock' ? 'selected' : ''; ?>>منخفض</option>
                        <option value="out_of_stock" <?php echo $filter == 'out_of_stock' ? 'selected' : ''; ?>>غير متوفر</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <span class="filter-label">بحث بالاسم:</span>
                    <div class="search-container">
                        <input type="text" name="search" class="search-input" placeholder="ابحث باسم المنتج..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="search-btn">🔍</button>
                    </div>
                </div>
                
                <button type="submit" class="filter-btn">تصفية</button>
                
                <?php if (!empty($search) || $filter != 'all'): ?>
                <a href="products.php" class="filter-btn" style="background: #7f8c8d;">إعادة تعيين</a>
                <?php endif; ?>
            </form>
            
            <div class="results-count">
                <?php 
                $total = $result->num_rows;
                echo "عرض " . $total . " منتج"; 
                ?>
            </div>
        </div>
        
        <?php if (!empty($message)): ?>
        <div class="message <?php echo $message_class; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($result->num_rows > 0): ?>
        <table class="products-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم المنتج</th>
                    <th>الفئة</th>
                    <th>الكمية</th>
                    <th>السعر</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $counter = 1;
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $counter . "</td>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['category'] . "</td>";
                    echo "<td>" . $row['quantity'] . "</td>";
                    echo "<td>" . number_format($row['price'], 2) . " ر.س</td>";
                    
                    // عرض حالة المنتج بلون مناسب
                    $status_class = "status-" . $row['status'];
                    $status_text = "";
                    if ($row['status'] == 'in_stock') $status_text = "متوفر";
                    else if ($row['status'] == 'low_stock') $status_text = "منخفض";
                    else if ($row['status'] == 'out_of_stock') $status_text = "غير متوفر";
                    
                    echo "<td><span class='$status_class'>$status_text</span></td>";
                    echo "<td class='action-btns'>";
                    echo "<a href='view_product.php?id=" . $row['id'] . "' class='action-btn view-btn'>عرض</a>";
                    echo "<a href='edit_product.php?id=" . $row['id'] . "' class='action-btn edit-btn'>تعديل</a>";
                    echo "<a href='products.php?delete_id=" . $row['id'] . "' class='action-btn delete-btn' onclick=\"return confirm('هل أنت متأكد من أنك تريد حذف هذا المنتج؟')\">حذف</a>";
                    echo "</td>";
                    echo "</tr>";
                    $counter++;
                }
                ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="no-products">
            <p>لا توجد منتجات تطابق معايير البحث.</p>
            <?php if (!empty($search) || $filter != 'all'): ?>
            <p><a href="products.php" style="color: #3498db;">انقر هنا لعرض جميع المنتجات</a></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="footer">
        <p>© 2023 نظام إدارة مصنع الإلكترونيات. جميع الحقوق محفوظة.</p>
    </div>

    <script>
        // تأكيد الحذف
        function confirmDelete(productName) {
            return confirm("هل أنت متأكد من أنك تريد حذف المنتج '" + productName + "'؟ هذا الإجراء لا يمكن التراجع عنه.");
        }
        
        // إرسال نموذج البحث عند الضغط على Enter
        document.querySelector('.search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>