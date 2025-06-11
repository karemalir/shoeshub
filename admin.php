<?php
require_once 'db_connection.php';
// بدء الجلسة إذا لم تكن بدأت
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// التحقق من تسجيل الدخول
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// إضافة منتج
if (isset($_POST['add'])) {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['admin_msg'] = "❌ طلب غير صالح";
    } else {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $image = $_POST['image'];
        $description = $_POST['description'];
        $category = $_POST['category'];
        $stock = $_POST['stock'];
        $rating = $_POST['rating'];
        
        $stmt = $pdo->prepare("INSERT INTO products (name, price, image, description, category, stock, rating) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $price, $image, $description, $category, $stock, $rating])) {
            $_SESSION['admin_msg'] = "✅ تم إضافة المنتج بنجاح!";
        } else {
            $_SESSION['admin_msg'] = "❌ حدث خطأ أثناء إضافة المنتج";
        }
    }
    header("Location: admin.php");
    exit();
}

// حذف منتج
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['admin_msg'] = "🗑️ تم حذف المنتج.";
    } else {
        $_SESSION['admin_msg'] = "❌ حدث خطأ أثناء حذف المنتج";
    }
    header("Location: admin.php");
    exit();
}

// توليد CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// جلب المنتجات
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - Shoeshub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f5f7fb;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            right: 0;
            background: linear-gradient(180deg, #4e54c8, #8f94fb);
            color: white;
            padding-top: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            z-index: 100;
        }
        
        .main-content {
            margin-right: var(--sidebar-width);
            padding: 20px;
        }
        
        .admin-logo {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-nav .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 25px;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left-color: white;
        }
        
        .sidebar-nav .nav-link i {
            width: 25px;
            text-align: center;
            margin-left: 10px;
        }
        
        .stat-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2rem;
            opacity: 0.3;
            position: absolute;
            top: 20px;
            left: 20px;
        }
    </style>
</head>
<body>
    <!-- الشريط الجانبي -->
    <div class="sidebar">
        <div class="admin-logo">
            <h4><i class="fas fa-cog me-2"></i>لوحة التحكم</h4>
        </div>
        
        <ul class="nav flex-column sidebar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="admin.php">
                    <i class="fas fa-box"></i> المنتجات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-shopping-cart"></i> الطلبات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-users"></i> العملاء
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-chart-bar"></i> التقارير
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-tags"></i> الخصومات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-comments"></i> التقييمات
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link" href="admin_logout.php">
                    <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                </a>
            </li>
        </ul>
    </div>
    
    <!-- المحتوى الرئيسي -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">إدارة المنتجات</h1>
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus me-1"></i> إضافة منتج
                </button>
            </div>
        </div>
        
        <?php if(isset($_SESSION['admin_msg'])): ?>
            <div class="alert alert-info alert-dismissible fade show">
                <?= $_SESSION['admin_msg'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['admin_msg']); ?>
        <?php endif; ?>
        
        <!-- إحصائيات سريعة -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stat-card border-0 bg-primary text-white">
                    <div class="card-body position-relative">
                        <i class="fas fa-box stat-icon"></i>
                        <h5 class="card-title">المنتجات</h5>
                        <h2 class="card-text"><?= count($products) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card border-0 bg-success text-white">
                    <div class="card-body position-relative">
                        <i class="fas fa-shopping-cart stat-icon"></i>
                        <h5 class="card-title">الطلبات</h5>
                        <h2 class="card-text">48</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card border-0 bg-info text-white">
                    <div class="card-body position-relative">
                        <i class="fas fa-users stat-icon"></i>
                        <h5 class="card-title">العملاء</h5>
                        <h2 class="card-text">124</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card border-0 bg-warning text-dark">
                    <div class="card-body position-relative">
                        <i class="fas fa-dollar-sign stat-icon"></i>
                        <h5 class="card-title">الإيرادات</h5>
                        <h2 class="card-text">24,560 ر.س</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- جدول المنتجات -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>الصورة</th>
                                <th>الاسم</th>
                                <th>السعر</th>
                                <th>التصنيف</th>
                                <th>المخزون</th>
                                <th>التقييم</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <img src="<?= htmlspecialchars($product['image']) ?>" width="50" class="rounded">
                                    </td>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td><?= $product['price'] ?> ر.س</td>
                                    <td><?= $product['category'] ?></td>
                                    <td><?= $product['stock'] ?></td>
                                    <td>
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?= $i <= $product['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                        <?php endfor; ?>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="admin.php?delete=<?= $product['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal إضافة منتج -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة منتج جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">اسم المنتج</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">السعر (ر.س)</label>
                                <input type="number" step="0.01" class="form-control" name="price" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">رابط الصورة</label>
                            <input type="text" class="form-control" name="image" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">التصنيف</label>
                                <select class="form-select" name="category" required>
                                    <option value="">اختر التصنيف</option>
                                    <option value="رجالي">رجالي</option>
                                    <option value="نسائي">نسائي</option>
                                    <option value="أطفال">أطفال</option>
                                    <option value="رياضي">رياضي</option>
                                    <option value="رسمي">رسمي</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">المخزون</label>
                                <input type="number" class="form-control" name="stock" min="0" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">التقييم (1-5)</label>
                                <input type="number" class="form-control" name="rating" min="1" max="5" step="0.1">
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" name="add" class="btn btn-primary">حفظ المنتج</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>