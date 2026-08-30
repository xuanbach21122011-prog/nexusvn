<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$order = $stmt->fetch();
if (!$order) {
    header('Location: index.php');
    exit;
}
$stmt = $pdo->prepare("SELECT p.name, p.image, o.quantity, o.total FROM orders o JOIN products p ON o.product_id = p.id WHERE o.user_id = ? AND o.id = ?");
$stmt->execute([$_SESSION['user_id'], $order['id']]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thanh toán thành công – NEXUS VN</title>
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

<div class="success-container" style="max-width:600px; margin:40px auto; background:#12101a; padding:40px; border-radius:28px; border:1px solid #211c2e; text-align:center;">
  <div class="success-icon" style="font-size:4rem; color:#22c55e; margin-bottom:16px;"><i class="fas fa-check-circle"></i></div>
  <h2 style="color:#34d399;">✅ Thanh toán thành công!</h2>
  <p>Cảm ơn bạn đã mua hàng tại <strong>NEXUS VN</strong>.</p>
  <div style="background:#1e1730; padding:16px; border-radius:12px; margin:20px 0; text-align:left;">
    <p><strong>📋 Mã đơn hàng:</strong> #<?= htmlspecialchars($order['order_code'] ?? 'N/A') ?></p>
    <p><strong>📅 Ngày mua:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
    <p><strong>💰 Tổng tiền:</strong> <?= number_format($order['total'], 0) ?>đ</p>
    <p><strong>📦 Trạng thái:</strong> <span style="color:#34d399; font-weight:700;">Đã thanh toán</span></p>
  </div>
  <div style="margin:16px 0; text-align:left;">
    <h4>Chi tiết đơn hàng</h4>
    <?php foreach ($items as $item): ?>
      <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #1f1b2b;">
        <span><?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?></span>
        <span><?= number_format($item['total'], 0) ?>đ</span>
      </div>
    <?php endforeach; ?>
  </div>
  <a href="history.php" class="btn-checkout" style="background:#6b7280; display:inline-block; padding:12px 32px; border-radius:40px; font-weight:600; color:#fff; text-decoration:none; margin:4px;"><i class="fas fa-history"></i> Xem lịch sử</a>
  <a href="index.php" class="btn-checkout" style="display:inline-block; padding:12px 32px; border-radius:40px; font-weight:600; color:#fff; text-decoration:none; margin:4px;"><i class="fas fa-home"></i> Tiếp tục mua sắm</a>
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
