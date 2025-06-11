<?php
require 'config.php';

// بدء الجلسة إذا لم تكن بدأت
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// تحديث كمية منتج في السلة
function updateCartItem($product_id, $quantity) {
    if (isset($_SESSION['cart'][$product_id])) {
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
}

// إزالة منتج من السلة
function removeCartItem($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

// الحصول على محتويات السلة
function getCartContents() {
    global $pdo;
    
    if (empty($_SESSION['cart'])) {
        return [];
    }
    
    // استخدم استعلام أكثر أماناً
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = rtrim(str_repeat('?,', count($product_ids)), ',');
    
    $sql = "SELECT * FROM products WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($product_ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $cart_items = [];
    foreach ($products as $product) {
        $product_id = $product['id'];
        $quantity = $_SESSION['cart'][$product_id];
        
        // تأكد أن السعر رقم
        $price = is_numeric($product['price']) ? (float)$product['price'] : 0;
        
        // تأكد أن الكمية رقم
        $quantity = is_numeric($quantity) ? (int)$quantity : 0;
        
        // حساب المجموع الفرعي
        $subtotal = $price * $quantity;
        
        $cart_items[] = [
            'id' => $product_id,
            'name' => $product['name'],
            'price' => $price,
            'image' => $product['image'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
    
    return $cart_items;
}

// حساب المجموع الكلي للسلة
function getCartTotal() {
    $cart_items = getCartContents();
    $total = 0;
    
    foreach ($cart_items as $item) {
        $total += $item['subtotal'];
    }
    
    return $total;
}

// حساب عدد العناصر في السلة
function getCartCount() {
    if (empty($_SESSION['cart'])) {
        return 0;
    }
    
    $count = 0;
    foreach ($_SESSION['cart'] as $quantity) {
        if (is_numeric($quantity)) {
            $count += (int)$quantity;
        }
    }
    
    return $count;
}

// تسجيل مستخدم جديد
function registerUser($name, $email, $password) {
    global $pdo;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    return $stmt->execute([$name, $email, $hashed_password]);
}

// تسجيل الدخول
function loginUser($email, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        return true;
    }
    return false;
}

// الحصول على جميع المنتجات
function getProducts($category = null) {
    global $pdo;
    $sql = "SELECT * FROM products";
    
    if ($category) {
        $sql .= " WHERE category = :category";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['category' => $category]);
    } else {
        $stmt = $pdo->query($sql);
    }
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// الحصول على منتج بواسطة ID
function getProductById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// إضافة منتج للسلة
function addToCart($product_id, $quantity = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}
?>