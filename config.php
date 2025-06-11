<?php
// عرض جميع الأخطاء (للتطوير فقط)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


// اتصال قاعدة البيانات
$host = 'localhost';
$dbname = 'shoeshub_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// بدء الجلسة
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// تهيئة السلة إذا لم تكن موجودة
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// إعدادات الموقع
$site_name = "ShoeSHub";
$base_url = "http://localhost/shoeshub/";
?>


