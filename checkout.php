<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    header('Location: cart.php');
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
        header('Location: order_success.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thanh toán – NEXUS VN</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
  <div class="container">
    <div class="logo"><i class="fas fa-bolt"></i> NEXUS VN</div>
    <nav>
      <a href="index.php"><i class="fas fa-home"></i> Trang chủ</a>
      <a href="cart.php"><i class="fas fa-shopping-cart"></i> Giỏ hàng</a>
      <a href="leaderboard.php"><i class="fas fa-trophy"></i> Bảng xếp hạng</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <span class="user-info">👤 <?= htmlspecialchars($_SESSION['username']) ?> (<?= number_format($_SESSION['balance'] ?? 0, 0) ?>đ)</span>
        <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<div class="checkout-container">
  <h2><i class="fas fa-credit-card"></i> Xác nhận thanh toán</h2>
  <div class="balance-info" style="background:#1e1730; padding:12px; border-radius:12px; margin-bottom:20px; text-align:center;">
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
    <div class="error" style="color:#ef4444; text-align:center;">⚠️ <?= $error ?></div>
    <a href="payment.php" class="btn-pay" style="display:block; text-align:center; text-decoration:none; margin-top:10px;"><i class="fas fa-coins"></i> Nạp tiền ngay</a>
  <?php else: ?>
    <form method="POST">
      <button type="submit" class="btn-pay"><i class="fas fa-check-circle"></i> Xác nhận thanh toán</button>
    </form>
  <?php endif; ?>
  <a href="cart.php" style="display:block; text-align:center; margin-top:16px; color:#c084fc;">← Quay lại giỏ hàng</a>
</div>

<footer>
  <div class="container">
    <div class="footer-grid">
      <div class="footer-col"><h4>NEXUS VN</h4><ul><li>Giới thiệu</li><li>Blog</li><li>Hướng dẫn</li></ul></div>
      <div class="footer-col"><h4>Sản phẩm</h4><ul><li>Key game</li><li>Phần mềm</li><li>Tiện ích</li></ul></div>
      <div class="footer-col"><h4>Hỗ trợ</h4><ul><li>FAQ</li><li>Liên hệ</li><li>Chính sách bảo mật</li></ul></div>
      <div class="footer-col"><h4>Kết nối</h4><ul><li>Facebook</li><li>Telegram</li><li>GitHub</li></ul></div>
    </div>
    <div class="footer-bottom">
      <span>&copy; 2026 NEXUS VN. All rights reserved.</span>
      <div class="footer-social">
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="#"><i class="fab fa-telegram"></i></a>
        <a href="#"><i class="fab fa-github"></i></a>
        <a href="#"><i class="fab fa-youtube"></i></a>
      </div>
    </div>
  </div>
</footer>
</body>
</html>
