<?php
require 'config.php';
require 'functions.php';
// بدء الجلسة إذا لم تكن بدأت
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}




$response = ['success' => false, 'cart_count' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    
    // إضافة المنتج إلى السلة
    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = 1;
    } else {
        $_SESSION['cart'][$product_id] += 1;
    }
    
    $response['success'] = true;
    $response['cart_count'] = getCartCount();
}

header('Content-Type: application/json');
echo json_encode($response);
?>