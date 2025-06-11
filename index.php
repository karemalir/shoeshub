<?php
require 'config.php';
require 'functions.php'; // تأكد من وجود هذا السطر

// بدء الجلسة إذا لم تكن بدأت
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// لا يجب أن يكون هناك استدعاء لدوال غير موجودة مثل pathrobust() أو getminded()
?>

<?php require 'includes/header.php'; ?>

<div class="hero">
    <div class="container">
        <h1>مرحباً بكم في متجر ShoeSHub</h1>
        <p>اكتشف أحدث تشكيلة من الأحذية بجودة عالية وأسعار منافسة</p>
        <a href="products.php" class="btn btn-primary">تصفح المنتجات</a>
    </div>
</div>

<div class="container my-5">
    <h2 class="text-center mb-4">المنتجات الأكثر مبيعاً</h2>
    
    <div class="row">
        <?php
        $products = getProducts(); // استدعاء الدالة الصحيحة
        foreach (array_slice($products, 0, 4) as $product) {
            echo '<div class="col-md-3 mb-4">';
            echo '<div class="card product-card">';
            echo '<img src="images/' . $product['image'] . '" class="card-img-top" alt="' . $product['name'] . '">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . $product['name'] . '</h5>';
            echo '<p class="card-text">' . number_format($product['price'], 2) . ' ر.س</p>';
            echo '<a href="product.php?id=' . $product['id'] . '" class="btn btn-primary">تفاصيل</a>';
            echo '<button class="btn btn-outline-secondary add-to-cart" data-id="' . $product['id'] . '">';
            echo '<i class="fas fa-shopping-cart"></i>';
            echo '</button>';
            echo '</div></div></div>';
        }
        ?>
    </div>
</div>

<?php require 'includes/footer.php'; ?>