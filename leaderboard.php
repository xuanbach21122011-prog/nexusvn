<?php
include 'config.php';
$stmt = $pdo->query("SELECT username, total_recharge FROM users ORDER BY total_recharge DESC LIMIT 10");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bảng xếp hạng – NEXUS VN</title>
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
      <?php else: ?>
        <a href="login.php" class="btn-ghost"><i class="fas fa-user"></i> Đăng nhập</a>
        <a href="register.php" class="btn-glow"><i class="fas fa-user-plus"></i> Đăng ký</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<div class="leaderboard-container">
  <h2><i class="fas fa-trophy"></i> Bảng xếp hạng người nạp nhiều nhất</h2>
  <?php if (empty($users)): ?>
    <p style="text-align:center; color:#7d728f;">Chưa có ai nạp tiền.</p>
  <?php else: ?>
    <?php foreach ($users as $index => $u): ?>
      <div class="rank-item">
        <span class="rank <?= $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) ?>">#<?= $index + 1 ?></span>
        <span class="name"><?= htmlspecialchars($u['username']) ?></span>
        <span class="amount"><?= number_format($u['total_recharge'], 0) ?>đ</span>
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
