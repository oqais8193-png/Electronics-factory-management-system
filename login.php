<?php
require_once "config.php";
requireLogout();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // في نظام حقيقي، نتحقق من اسم المستخدم وكلمة المرور في قاعدة البيانات
    // هنا نستخدم بيانات افتراضية للتوضيح
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header("location: index.php");
        exit;
    } else {
        $login_err = "اسم المستخدم أو كلمة المرور غير صحيحة.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام إدارة المصنع</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #2c3e50, #4a6572);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        
        .gear-container {
            perspective: 1000px;
            width: 400px;
            height: 500px;
            position: relative;
        }
        
        .gear {
            width: 300px;
            height: 300px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: rotate 20s linear infinite;
        }
        
        .gear-inner {
            position: absolute;
            width: 200px;
            height: 200px;
            background: linear-gradient(45deg, #3498db, #2980b9);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-weight: bold;
            font-size: 20px;
        }
        
        .gear-tooth {
            position: absolute;
            width: 40px;
            height: 120px;
            background: linear-gradient(45deg, #3498db, #2980b9);
            border-radius: 10px;
            top: 50%;
            left: 50%;
            transform-origin: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }
        
        .gear-tooth:nth-child(1) { transform: translate(-50%, -50%) rotate(0deg) translateY(-130px); }
        .gear-tooth:nth-child(2) { transform: translate(-50%, -50%) rotate(45deg) translateY(-130px); }
        .gear-tooth:nth-child(3) { transform: translate(-50%, -50%) rotate(90deg) translateY(-130px); }
        .gear-tooth:nth-child(4) { transform: translate(-50%, -50%) rotate(135deg) translateY(-130px); }
        .gear-tooth:nth-child(5) { transform: translate(-50%, -50%) rotate(180deg) translateY(-130px); }
        .gear-tooth:nth-child(6) { transform: translate(-50%, -50%) rotate(225deg) translateY(-130px); }
        .gear-tooth:nth-child(7) { transform: translate(-50%, -50%) rotate(270deg) translateY(-130px); }
        .gear-tooth:nth-child(8) { transform: translate(-50%, -50%) rotate(315deg) translateY(-130px); }
        
        .login-form {
            position: absolute;
            width: 320px;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            backdrop-filter: blur(10px);
            animation: fadeIn 1s ease-out;
            z-index: 10;
        }
        
        .login-form h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #34495e;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .form-group input:focus {
            border-color: #3498db;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.5);
            outline: none;
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background: linear-gradient(135deg, #2980b9, #3498db);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .error-msg {
            color: #e74c3c;
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        
        @keyframes rotate {
            from { transform: translate(-50%, -50%) rotate(0deg); }
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, -60%); }
            to { opacity: 1; transform: translate(-50%, -50%); }
        }
        
        .industrial-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: -1;
        }
        
        .small-gear {
            position: absolute;
            width: 150px;
            height: 150px;
            animation: rotateReverse 15s linear infinite;
        }
        
        .small-gear .gear-inner {
            width: 100px;
            height: 100px;
            background: linear-gradient(45deg, #e74c3c, #c0392b);
        }
        
        .small-gear .gear-tooth {
            width: 20px;
            height: 60px;
            background: linear-gradient(45deg, #e74c3c, #c0392b);
        }
        
        .small-gear:nth-child(1) {
            top: 20%;
            left: 20%;
        }
        
        .small-gear:nth-child(2) {
            bottom: 20%;
            right: 20%;
        }
        
        @keyframes rotateReverse {
            from { transform: rotate(0deg); }
            to { transform: rotate(-360deg); }
        }
    </style>
</head>
<body>
    <div class="industrial-bg"></div>
    
    <div class="small-gear">
        <div class="gear-tooth"></div>
        <div class="gear-tooth"></div>
        <div class="gear-tooth"></div>
        <div class="gear-tooth"></div>
        <div class="gear-tooth"></div>
        <div class="gear-tooth"></div>
        <div class="gear-tooth"></div>
        <div class="gear-tooth"></div>
        <div class="gear-inner"></div>
    </div>
    
    <div class="small-gear">
        <div class="gear-tooth"></div>
        <div class="gear-tooth"></div>
        <div class="gear-tooth"></div>
        <div class="gear-tooth"></div>
        <div class="gear-tooth"></div>
        <div class="gear-tooth"></div>
        <div class="gear-tooth"></div>
        <div class="gear-tooth"></div>
        <div class="gear-inner"></div>
    </div>
    
    <div class="gear-container">
        <div class="gear">
            <div class="gear-tooth"></div>
            <div class="gear-tooth"></div>
            <div class="gear-tooth"></div>
            <div class="gear-tooth"></div>
            <div class="gear-tooth"></div>
            <div class="gear-tooth"></div>
            <div class="gear-tooth"></div>
            <div class="gear-tooth"></div>
            <div class="gear-inner">نظام الإدارة</div>
        </div>
        
        <div class="login-form">
            <h2>تسجيل الدخول إلى النظام</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="username">اسم المستخدم</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-login">دخول</button>
                <?php if (!empty($login_err)) echo '<div class="error-msg">' . $login_err . '</div>'; ?>
            </form>
            <div style="text-align: center; margin-top: 20px; color: #7f8c8d; font-size: 14px;">
                بيانات الدخول: admin / admin123
            </div>
        </div>
    </div>
</body>
</html>