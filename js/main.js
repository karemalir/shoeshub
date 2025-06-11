// تحديث عداد السلة
function updateCartCount() {
    const cartCountElements = document.querySelectorAll('.cart-count');
    
    // إرسال طلب للحصول على عدد العناصر
    fetch('get_cart_count.php')
    .then(response => response.json())
    .then(data => {
        cartCountElements.forEach(element => {
            element.textContent = data.count;
            element.style.display = data.count > 0 ? 'inline' : 'none';
        });
    });
}

// إضافة منتج إلى السلة
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.id;
        
        // إرسال طلب إلى الخادم
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + encodeURIComponent(productId)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount();
                showAlert('تمت إضافة المنتج إلى السلة بنجاح', 'success');
            } else {
                showAlert('حدث خطأ أثناء إضافة المنتج', 'danger');
            }
        });
    });
});

// عرض التنبيهات
function showAlert(message, type) {
    const alertContainer = document.createElement('div');
    alertContainer.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    alertContainer.style.zIndex = '9999';
    alertContainer.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertContainer);
    
    setTimeout(() => {
        alertContainer.remove();
    }, 3000);
}

// تحديث العداد عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});