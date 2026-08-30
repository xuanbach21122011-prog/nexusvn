<?php
include 'config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['add_to_cart']) && isset($_POST['product_id'])) {
    $id = (int)$_POST['product_id'];
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] += 1;
    } else {
        $_SESSION['cart'][$id] = 1;
    }
    header('Location: /cart.php');
    exit;
}

if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header('Location: /cart.php');
    exit;
}

if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $id => $qty) {
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = (int)$qty;
        }
    }
    header('Location: /cart.php');
    exit;
}

$cartItems = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    foreach ($products as $p) {
        $qty = $_SESSION['cart'][$p['id']];
        $subtotal = $p['price'] * $qty;
        $total += $subtotal;
        $cartItems[] = ['product' => $p, 'qty' => $qty, 'subtotal' => $subtotal];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - NEXUS VN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .cart-container { max-width: 800px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 28px; border: 1px solid #e9edf2; }
        .cart-item { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e9edf2; padding: 16px 0; gap: 16px; flex-wrap: wrap; }
        .cart-item img { width: 60px; height: 60px; object-fit: cover; border-radius: 12px; background: #f1f5f9; }
        .cart-item .info { flex: 1; }
        .cart-item .info h3 { font-size: 1rem; }
        .cart-item .info .price { color: #2563eb; font-weight: 700; }
        .cart-item input[type="number"] { width: 60px; padding: 6px; border-radius: 8px; border: 1px solid #d1d5db; text-align: center; }
        .cart-item .btn-remove { background: #ef4444; color: #fff; border: none; padding: 6px 16px; border-radius: 40px; cursor: pointer; }
        .cart-total { text-align: right; font-size: 1.5rem; font-weight: 700; margin: 20px 0; }
        .btn-checkout { background: #2563eb; color: #fff; border: none; padding: 12px 40px; border-radius: 40px; font-weight: 600; cursor: pointer; }
        .btn-checkout:hover { background: #1d4ed8; }
        .empty-cart { text-align: center; padding: 40px; color: #94a3b8; }
    </style>
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
                <span class="user-info">👤 <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="/logout.php" class="btn-logout">Đăng xuất</a>
            <?php else: ?>
                <a href="/login.php">Đăng nhập</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<div class="cart-container">
    <h2>🛒 Giỏ hàng của bạn</h2>
    <?php if (empty($cartItems)): ?>
        <div class="empty-cart">Giỏ hàng trống. <a href="/" style="color:#2563eb;">Tiếp tục mua sắm</a></div>
    <?php else: ?>
        <form method="POST">
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <img src="/assets/uploads/<?= htmlspecialchars($item['product']['image'] ?? 'default.jpg') ?>" alt="">
                    <div class="info">
                        <h3><?= htmlspecialchars($item['product']['name']) ?></h3>
                        <span class="price"><?= number_format($item['product']['price'], 0) ?>đ</span>
                    </div>
                    <input type="number" name="quantity[<?= $item['product']['id'] ?>]" value="<?= $item['qty'] ?>" min="0">
                    <span><?= number_format($item['subtotal'], 0) ?>đ</span>
                    <a href="/cart.php?remove=<?= $item['product']['id'] ?>" class="btn-remove">Xoá</a>
                </div>
            <?php endforeach; ?>
            <div style="margin-top:16px;">
                <button type="submit" name="update_cart" class="btn-checkout" style="background:#6b7280;">Cập nhật giỏ</button>
            </div>
        </form>
        <div class="cart-total">Tổng: <?= number_format($total, 0) ?>đ</div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/checkout.php" class="btn-checkout">Tiến hành thanh toán</a>
        <?php else: ?>
            <p>Vui lòng <a href="/login.php" style="color:#2563eb;">đăng nhập</a> để thanh toán.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

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