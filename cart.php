<?php
require 'config.php';
require 'functions.php';
// بدء الجلسة إذا لم تكن بدأت
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}




// معالجة إجراءات السلة
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_item'])) {
        $product_id = (int)$_POST['product_id'];
        removeCartItem($product_id);
    } 
    elseif (isset($_POST['update_quantity'])) {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        updateCartItem($product_id, $quantity);
    }
}

// الحصول على محتويات السلة
$cart_items = getCartContents();
$cart_total = getCartTotal();

require 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4">سلة التسوق</h1>
    
    <?php if (empty($cart_items)): ?>
        <div class="alert alert-info text-center">
            <h4>سلة التسوق فارغة</h4>
            <p>لم تقم بإضافة أي منتجات إلى سلة التسوق بعد</p>
            <a href="products.php" class="btn btn-primary">تصفح المنتجات</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>الصورة</th>
                        <th>المنتج</th>
                        <th>السعر</th>
                        <th>الكمية</th>
                        <th>المجموع</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td>
                            <img src="images/<?= $item['image'] ?>" alt="<?= $item['name'] ?>" width="80">
                        </td>
                        <td><?= $item['name'] ?></td>
                        <td><?= number_format($item['price'], 2) ?> ر.س</td>
                        <td>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                <div class="input-group" style="width: 120px;">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control">
                                    <button type="submit" name="update_quantity" class="btn btn-outline-primary">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </div>
                            </form>
                        </td>
                        <td><?= number_format($item['subtotal'], 2) ?> ر.س</td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                <button type="submit" name="remove_item" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end">المجموع الكلي:</td>
                        <td colspan="2"><?= number_format($cart_total, 2) ?> ر.س</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="d-flex justify-content-between mt-4">
            <a href="products.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> مواصلة التسوق
            </a>
            <a href="checkout.php" class="btn btn-primary">
                إتمام الشراء <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>