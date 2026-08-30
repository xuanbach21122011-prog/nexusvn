<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT o.*, p.name as product_name, p.image FROM orders o JOIN products p ON o.product_id = p.id WHERE o.user_id = ? ORDER BY o.id DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

$groupedOrders = [];
foreach ($orders as $order) {
    $code = $order['order_code'] ?? '#' . $order['id'];
    if (!isset($groupedOrders[$code])) {
        $groupedOrders[$code] = ['id' => $order['id'], 'order_code' => $code, 'created_at' => $order['created_at'], 'total' => $order['total'], 'status' => $order['status'], 'items' => []];
    }
    $groupedOrders[$code]['items'][] = ['name' => $order['product_name'], 'image' => $order['image'], 'quantity' => $order['quantity'], 'total' => $order['total']];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lịch sử mua hàng – NEXUS VN</title>
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
        <span class="user-info">👤 <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<div class="history-container">
  <h2><i class="fas fa-history"></i> Lịch sử mua hàng</h2>
  <?php if (empty($groupedOrders)): ?>
    <p class="empty-msg">Bạn chưa có đơn hàng nào. <a href="index.php" style="color:#c084fc;">Mua sắm ngay</a></p>
  <?php else: ?>
    <?php foreach ($groupedOrders as $code => $order): ?>
      <div class="order-box" style="border:1px solid #211c2e; border-radius:16px; padding:16px; margin-bottom:20px;">
        <div class="order-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; border-bottom:1px solid #1f1b2b; padding-bottom:10px; margin-bottom:10px;">
          <span class="code" style="font-weight:700; color:#c084fc;">📋 #<?= htmlspecialchars($code) ?></span>
          <span class="date" style="color:#7d728f; font-size:0.9rem;"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
          <span class="total" style="font-weight:700;"><?= number_format($order['total'], 0) ?>đ</span>
          <span style="background:#0b1f1a; color:#34d399; padding:4px 16px; border-radius:40px; font-size:0.8rem;">✅ Đã thanh toán</span>
        </div>
        <?php foreach ($order['items'] as $item): ?>
          <div class="order-item" style="display:flex; align-items:center; gap:16px; padding:8px 0; border-bottom:1px solid #1f1b2b;">
            <img src="/assets/uploads/<?= htmlspecialchars($item['image'] ?? 'default.jpg') ?>" style="width:50px; height:50px; object-fit:cover; border-radius:10px; background:#1e1730;">
            <div style="flex:1;"><div style="font-weight:600;"><?= htmlspecialchars($item['name']) ?></div><div style="color:#7d728f; font-size:0.9rem;">Số lượng: <?= $item['quantity'] ?></div></div>
            <div style="font-weight:600; color:#c084fc;"><?= number_format($item['total'], 0) ?>đ</div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
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
