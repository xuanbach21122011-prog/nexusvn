<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    header('Location: /cart.php');
    exit;
}

$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$products = $stmt->fetchAll();

$total = 0;
foreach ($products as $p) {
    $total += $p['price'] * $cart[$p['id']];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user['balance'] < $total) {
        $error = "Số dư không đủ! Vui lòng nạp thêm.";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$total, $user_id]);

        $stmt = $pdo->prepare("UPDATE users SET total_recharge = total_recharge + ? WHERE id = ?");
        $stmt->execute([$total, $user_id]);

        $order_code = 'NX' . date('Ymd') . strtoupper(substr(uniqid(), -6));

        $stmt = $pdo->prepare("INSERT INTO orders (user_id, product_id, quantity, total, order_code, status) VALUES (?, ?, ?, ?, ?, 'paid')");
        foreach ($products as $p) {
            $stmt->execute([$user_id, $p['id'], $cart[$p['id']], $p['price'] * $cart[$p['id']], $order_code]);
        }

        unset($_SESSION['cart']);
        $_SESSION['balance'] = $user['balance'] - $total;

        header('Location: /order_success.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - NEXUS VN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .checkout-container { max-width: 600px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 28px; border: 1px solid #e9edf2; }
        .checkout-container h2 { margin-bottom: 20px; }
        .checkout-item { display: flex; justify-content: space-between; border-bottom: 1px solid #e9edf2; padding: 10px 0; }
        .total { font-size: 1.5rem; font-weight: 700; text-align: right; margin: 20px 0; }
        .btn-pay { background: #2563eb; color: #fff; border: none; padding: 12px 40px; border-radius: 40px; font-weight: 600; cursor: pointer; width: 100%; }
        .btn-pay:hover { background: #1d4ed8; }
        .error { color: #ef4444; text-align: center; margin: 10px 0; }
        .balance-info { background: #f1f5f9; padding: 12px; border-radius: 12px; margin-bottom: 20px; text-align: center; }
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
                <span class="user-info">👤 <?= htmlspecialchars($_SESSION['username']) ?> (<?= number_format($_SESSION['balance'] ?? 0, 0) ?>đ)</span>
                <a href="/logout.php" class="btn-logout">Đăng xuất</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<div class="checkout-container">
    <h2>📋 Xác nhận thanh toán</h2>
    <div class="balance-info">
        💰 Số dư hiện tại: <strong><?= number_format($_SESSION['balance'] ?? 0, 0) ?>đ</strong>
    </div>
    <?php foreach ($products as $p): ?>
        <div class="checkout-item">
            <span><?= htmlspecialchars($p['name']) ?> x <?= $cart[$p['id']] ?></span>
            <span><?= number_format($p['price'] * $cart[$p['id']], 0) ?>đ</span>
        </div>
    <?php endforeach; ?>
    <div class="total">Tổng: <?= number_format($total, 0) ?>đ</div>
    <?php if (isset($error)): ?>
        <div class="error">⚠️ <?= $error ?></div>
        <a href="/payment.php" class="btn-pay" style="text-align:center; display:block; text-decoration:none; margin-top:10px;">💳 Nạp tiền ngay</a>
    <?php else: ?>
        <form method="POST">
            <button type="submit" class="btn-pay">✅ Xác nhận thanh toán</button>
        </form>
    <?php endif; ?>
    <a href="/cart.php" style="display:block; text-align:center; margin-top:16px; color:#3b82f6;">← Quay lại giỏ hàng</a>
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
</body>
</html>