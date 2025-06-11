<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $site_name ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="navbar">
        <div class="container navbar-container">
            <a href="index.php" class="logo">ShoeSHub</a>
            
            <ul class="nav-links">
                <li><a href="index.php">الرئيسية</a></li>
                <li><a href="products.php">المنتجات</a></li>
                <li><a href="about.php">عن المتجر</a></li>
                <li><a href="contact.php">اتصل بنا</a></li>
            </ul>
            
            <div class="nav-actions">
            <div class="cart-icon">
                <a href="cart.php">
                    <i class="fas fa-shopping-cart"></i>
                    <?php
                    $cart_count = 0;
                    if (!empty($_SESSION['cart'])) {
                        $cart_count = array_sum($_SESSION['cart']);
                    }
                    ?>
                    <span class="cart-count" style="<?= $cart_count > 0 ? '' : 'display:none;' ?>">
                        <?= $cart_count ?>
                    </span>
                </a>
            </div>
                
                <div class="user-icon">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="profile.php">
                            <i class="fas fa-user"></i>
                        </a>
                    <?php else: ?>
                        <a href="login.php">
                            <i class="fas fa-sign-in-alt"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    
    <main>