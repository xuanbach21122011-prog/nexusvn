<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Lấy đơn hàng mới nhất của user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: index.php');
    exit;
}

// Lấy chi tiết sản phẩm trong đơn hàng
$stmt = $pdo->prepare("SELECT p.name, p.image, o.quantity, o.total FROM orders o JOIN products p ON o.product_id = p.id WHERE o.user_id = ? AND o.id = ?");
$stmt->execute([$_SESSION['user_id'], $order['id']]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán thành công - NEXUS VN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .success-container { max-width: 600px; margin: 40px auto; background: #fff; padding: 40px; border-radius: 28px; border: 1px solid #e9edf2; text-align: center; }
        .success-icon { font-size: 4rem; color: #22c55e; margin-bottom: 16px; }
        .success-container h2 { color: #065f46; }
        .order-info { background: #f1f5f9; padding: 16px; border-radius: 12px; margin: 20px 0; text-align: left; }
        .order-info p { margin: 4px 0; }
        .order-items { margin: 16px 0; text-align: left; }
        .order-items .item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e9edf2; }
        .btn-continue { background: #2563eb; color: #fff; border: none; padding: 12px 40px; border-radius: 40px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-continue:hover { background: #1d4ed8; }
    </style>
</head>
<body>
<header>
    <div class="container">
        <div class="logo"><i class="fas fa-bolt"></i> NEXUS VN</div>
        <nav>
            <a href="index.php">Trang chủ</a>
            <a href="cart.php">🛒 Giỏ hàng</a>
            <a href="leaderboard.php">🏆 Bảng xếp hạng</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="user-info">👤 <?= htmlspecialchars($_SESSION['username']) ?> (<?= number_format($_SESSION['balance'] ?? 0, 0) ?>đ)</span>
                <a href="logout.php" class="btn-logout">Đăng xuất</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<div class="success-container">
    <div class="success-icon"><i class="fas fa-check-circle"></i></div>
    <h2>✅ Thanh toán thành công!</h2>
    <p>Cảm ơn bạn đã mua hàng tại <strong>NEXUS VN</strong>.</p>

    <div class="order-info">
        <p><strong>📋 Mã đơn hàng:</strong> #<?= htmlspecialchars($order['order_code'] ?? 'N/A') ?></p>
        <p><strong>📅 Ngày mua:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        <p><strong>💰 Tổng tiền:</strong> <?= number_format($order['total'], 0) ?>đ</p>
        <p><strong>📦 Trạng thái:</strong> 
            <span style="color: #22c55e; font-weight: 700;">
                <?php if ($order['status'] == 'paid') echo 'Đã thanh toán'; ?>
            </span>
        </p>
    </div>

    <div class="order-items">
        <h4>Chi tiết đơn hàng</h4>
        <?php foreach ($items as $item): ?>
            <div class="item">
                <span><?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?></span>
                <span><?= number_format($item['total'], 0) ?>đ</span>
            </div>
        <?php endforeach; ?>
    </div>

    <a href="history.php" class="btn-continue" style="background: #6b7280;">📜 Xem lịch sử mua hàng</a>
    <a href="index.php" class="btn-continue">🛒 Tiếp tục mua sắm</a>
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