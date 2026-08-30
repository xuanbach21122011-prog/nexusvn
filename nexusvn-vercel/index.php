<?php
include 'config.php';

// Xử lý thông báo thanh toán thành công
if (isset($_GET['msg']) && $_GET['msg'] == 'order_success') {
    echo "<script>alert('✅ Thanh toán thành công! Cảm ơn bạn đã mua hàng.'); window.location.href='order_success.php';</script>";
    exit;
}

// Lấy danh sách sản phẩm
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEXUS VN - Trang chủ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php
include 'config.php';

$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEXUS VN - Trang chủ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header>
    <div class="container">
        <div class="logo"><i class="fas fa-bolt"></i> NEXUS VN</div>
        <nav>
            <a href="/">Trang chủ</a>
            <a href="/cart.php">🛒 Giỏ hàng</a>
            <a href="/leaderboard.php">🏆 Bảng xếp hạng</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="user-info">👤 <?= htmlspecialchars($_SESSION['username']) ?> (<?= number_format($_SESSION['balance'] ?? 0, 0) ?>đ)</span>
                <a href="/logout.php">Đăng xuất</a>
            <?php else: ?>
                <a href="/login.php">Đăng nhập</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<section class="hero">
    <div class="container">
        <h1>Chào mừng đến <span>NEXUS VN</span></h1>
        <p>Nơi cung cấp các sản phẩm số chất lượng cao</p>
    </div>
</section>
<section class="products">
    <div class="container">
        <h2>📦 Sản phẩm nổi bật</h2>
        <div class="product-grid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <img src="<?= $product['image'] ? '/assets/uploads/' . $product['image'] : '/assets/uploads/default.jpg' ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="price"><?= number_format($product['price'], 0) ?>đ</p>
                        <p class="desc"><?= htmlspecialchars($product['description']) ?></p>
                        <form action="/cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <button type="submit" name="add_to_cart" class="btn-add">➕ Thêm vào giỏ</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-msg">Chưa có sản phẩm nào. <a href="/admin.php" style="color:#2563eb;">Thêm sản phẩm</a></p>
            <?php endif; ?>
        </div>
    </div>
</section>
<footer>
    <div class="container">
        <span>&copy; 2026 NEXUS VN. All rights reserved.</span>
        <div class="social">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-telegram"></i></a>
            <a href="#"><i class="fab fa-github"></i></a>
        </div>
    </div>
</footer>
</body>
</html>