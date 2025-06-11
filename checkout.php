<?php
require_once 'config.php';

// بدء الجلسة
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// التحقق من وجود منتجات في السلة
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = 'checkout.php';
    header('Location: login.php');
    exit;
}

// معالجة عملية الشراء
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // هنا سيتم معالجة الدفع وإنشاء الطلب
    // ...
    
    // بعد اكتمال الشراء
    unset($_SESSION['cart']);
    $_SESSION['order_success'] = true;
    header('Location: order_success.php');
    exit;
}

// عرض الصفحة
require 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4">إتمام الشراء</h1>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">معلومات الشحن</div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">الاسم الكامل</label>
                            <input type="text" name="fullname" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">العنوان</label>
                            <textarea name="address" class="form-control" rows="3" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">المدينة</label>
                                <input type="text" name="city" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الرمز البريدي</label>
                                <input type="text" name="postal_code" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="tel" name="phone" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">ملخص الطلب</div>
                <div class="card-body">
                    <?php
                    $subtotal = 0;
                    foreach ($cart_items as $item) {
                        $subtotal += $item['subtotal'];
                    }
                    $shipping = 15.00; // تكلفة الشحن
                    $total = $subtotal + $shipping;
                    ?>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>المجموع الفرعي:</span>
                        <span><?= number_format($subtotal, 2) ?> ر.س</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>تكلفة الشحن:</span>
                        <span><?= number_format($shipping, 2) ?> ر.س</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between fw-bold">
                        <span>المجموع الكلي:</span>
                        <span><?= number_format($total, 2) ?> ر.س</span>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mt-4">
                        تأكيد الطلب والدفع
                    </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>