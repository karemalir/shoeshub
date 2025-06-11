<?php 
require 'config.php';
require 'functions.php';

// بدء الجلسة إذا لم تكن بدأت
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$category = isset($_GET['category']) ? $_GET['category'] : null;
$products = getProducts($category);
?>

<?php require 'includes/header.php'; ?>

<div class="container my-5">
    <h1 class="mb-4">جميع المنتجات</h1>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">التصنيفات</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a href="products.php">جميع الفئات</a></li>
                    <li class="list-group-item"><a href="products.php?category=رياضية">أحذية رياضية</a></li>
                    <li class="list-group-item"><a href="products.php?category=رسمية">أحذية رسمية</a></li>
                    <li class="list-group-item"><a href="products.php?category=صيفية">أحذية صيفية</a></li>
                </ul>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="row">
                <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card product-card">
                        <img src="images/<?= $product['image'] ?>" class="card-img-top" alt="<?= $product['name'] ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= $product['name'] ?></h5>
                            <p class="card-text"><?= number_format($product['price'], 2) ?> ر.س</p>
                            <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-primary">تفاصيل</a>
                            <button class="btn btn-outline-secondary add-to-cart" data-id="<?= $product['id'] ?>">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>