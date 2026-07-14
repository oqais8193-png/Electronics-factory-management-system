<?php
require_once "config.php";

// مسح جميع بيانات الجلسة
$_SESSION = array();

// إلغاء الجلسة
session_destroy();

// التوجيه إلى صفحة تسجيل الدخول
header("location: login.php");
exit;
?>