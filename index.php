<?php
require_once "config.php";
requireLogin();

// Get statistics from database
$total_products = 0;
$in_stock_products = 0;
$low_stock_products = 0;
$out_of_stock_products = 0;

// Get total products count
$sql_total = "SELECT COUNT(*) as total FROM products WHERE deleted = 0";
$result_total = $conn->query($sql_total);
if ($result_total && $result_total->num_rows > 0) {
    $row = $result_total->fetch_assoc();
    $total_products = $row['total'];
}

// Get in stock products count
$sql_in_stock = "SELECT COUNT(*) as count FROM products WHERE status = 'in_stock' AND deleted = 0";
$result_in_stock = $conn->query($sql_in_stock);
if ($result_in_stock && $result_in_stock->num_rows > 0) {
    $row = $result_in_stock->fetch_assoc();
    $in_stock_products = $row['count'];
}

// Get low stock products count
$sql_low_stock = "SELECT COUNT(*) as count FROM products WHERE status = 'low_stock' AND deleted = 0";
$result_low_stock = $conn->query($sql_low_stock);
if ($result_low_stock && $result_low_stock->num_rows > 0) {
    $row = $result_low_stock->fetch_assoc();
    $low_stock_products = $row['count'];
}

// Get out of stock products count
$sql_out_of_stock = "SELECT COUNT(*) as count FROM products WHERE status = 'out_of_stock' AND deleted = 0";
$result_out_of_stock = $conn->query($sql_out_of_stock);
if ($result_out_of_stock && $result_out_of_stock->num_rows > 0) {
    $row = $result_out_of_stock->fetch_assoc();
    $out_of_stock_products = $row['count'];
}

// Get last update time
$sql_last_update = "SELECT MAX(updated_at) as last_update FROM products WHERE deleted = 0";
$result_last_update = $conn->query($sql_last_update);
$last_update = "غير متاح";
if ($result_last_update && $result_last_update->num_rows > 0) {
    $row = $result_last_update->fetch_assoc();
    if ($row['last_update']) {
        $last_update = date('Y-m-d H:i', strtotime($row['last_update']));
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة مصنع الإلكترونيات</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }
        
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .card {
            background: rgba(0, 0, 0, 0.7);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #34495e;
            perspective: 1000px;
        }
        
        .card:hover {
            transform: translateY(-10px) rotateX(5deg);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
        }
        
        .card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #3498db;
        }
        
        .card p {
            font-size: 2.5rem;
            font-weight: bold;
            color: #ecf0f1;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .card .icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #3498db;
        }
        
        .welcome-section {
            background: rgba(0, 0, 0, 0.7);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid #34495e;
        }
        
        .welcome-section h2 {
            font-size: 2rem;
            margin-bottom: 15px;
            color: #3498db;
        }
        
        .welcome-section p {
            font-size: 1.2rem;
            line-height: 1.6;
            color: #bdc3c7;
        }
        
        .industrial-design {
            background-image: 
                linear-gradient(rgba(52, 152, 219, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(52, 152, 219, 0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            border-radius: 15px;
            padding: 5px;
            margin-top: 5px;
        }
        
        .charts-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }
        
        .chart-container {
            background: rgba(0, 0, 0, 0.7);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid #34495e;
        }
        
        .chart-title {
            text-align: center;
            margin-bottom: 15px;
            color: #3498db;
            font-size: 1.3rem;
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
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            .charts-section {
                grid-template-columns: 1fr;
            }
            
            .nav {
                flex-direction: column;
                align-items: center;
            }
            
            .nav a {
                width: 80%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>نظام إدارة مصنع الإلكترونيات</h1>
        <p>نظام متكامل لإدارة منتجات المصنع والمخزون</p>
    </div>
    
    <div class="nav">
        <a href="index.php">الصفحة الرئيسية</a>
        <a href="products.php">إدارة المنتجات</a>
        <a href="add_product.php">إضافة منتج جديد</a>
        <a href="logout.php">تسجيل الخروج</a>
    </div>
    
    <div class="container">
        <div class="welcome-section">
            <h2>مرحباً بك في نظام الإدارة</h2>
            <p>هذا النظام يمكّنك من إدارة منتجات مصنع الإلكترونيات بكل سهولة. يمكنك عرض المنتجات، إضافة منتجات جديدة، تعديل المنتجات الحالية، وحذف المنتجات.</p>
        </div>
        
        <div class="dashboard-cards">
            <div class="card industrial-design">
                <div class="icon">📊</div>
                <h3>إجمالي المنتجات</h3>
                <p><?php echo $total_products; ?></p>
            </div>
            
            <div class="card industrial-design">
                <div class="icon">📦</div>
                <h3>المنتجات في المخزون</h3>
                <p><?php echo $in_stock_products; ?></p>
            </div>
            
            <div class="card industrial-design">
                <div class="icon">⚠️</div>
                <h3>منتجات منخفضة المخزون</h3>
                <p><?php echo $low_stock_products; ?></p>
            </div>
            
            <div class="card industrial-design">
                <div class="icon">🔄</div>
                <h3>آخر تحديث</h3>
                <p><?php echo $last_update; ?></p>
            </div>
        </div>
        
        <div class="charts-section">
            <div class="chart-container">
                <h3 class="chart-title">حالة المخزون</h3>
                <canvas id="stockStatusChart"></canvas>
            </div>
            
            <div class="chart-container">
                <h3 class="chart-title">توزيع المنتجات حسب الحالة</h3>
                <canvas id="stockPieChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p>© 2023 نظام إدارة مصنع الإلكترونيات. جميع الحقوق محفوظة.</p>
    </div>

    <script>
        // Stock Status Chart
        const stockStatusCtx = document.getElementById('stockStatusChart').getContext('2d');
        const stockStatusChart = new Chart(stockStatusCtx, {
            type: 'bar',
            data: {
                labels: ['متوفر', 'منخفض', 'غير متوفر'],
                datasets: [{
                    label: 'عدد المنتجات',
                    data: [
                        <?php echo $in_stock_products; ?>,
                        <?php echo $low_stock_products; ?>,
                        <?php echo $out_of_stock_products; ?>
                    ],
                    backgroundColor: [
                        'rgba(46, 204, 113, 0.7)',
                        'rgba(241, 196, 15, 0.7)',
                        'rgba(231, 76, 60, 0.7)'
                    ],
                    borderColor: [
                        'rgba(46, 204, 113, 1)',
                        'rgba(241, 196, 15, 1)',
                        'rgba(231, 76, 60, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#ecf0f1'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#ecf0f1'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#ecf0f1'
                        }
                    }
                }
            }
        });

        // Stock Pie Chart
        const stockPieCtx = document.getElementById('stockPieChart').getContext('2d');
        const stockPieChart = new Chart(stockPieCtx, {
            type: 'pie',
            data: {
                labels: ['متوفر', 'منخفض', 'غير متوفر'],
                datasets: [{
                    data: [
                        <?php echo $in_stock_products; ?>,
                        <?php echo $low_stock_products; ?>,
                        <?php echo $out_of_stock_products; ?>
                    ],
                    backgroundColor: [
                        'rgba(46, 204, 113, 0.7)',
                        'rgba(241, 196, 15, 0.7)',
                        'rgba(231, 76, 60, 0.7)'
                    ],
                    borderColor: [
                        'rgba(46, 204, 113, 1)',
                        'rgba(241, 196, 15, 1)',
                        'rgba(231, 76, 60, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#ecf0f1',
                            padding: 20
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>