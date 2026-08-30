<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recharge'])) {
    $amount = (float)$_POST['amount'];
    $method = $_POST['method'];
    $code = $_POST['code'] ?? '';

    if ($amount <= 0) {
        $error = "Số tiền phải lớn hơn 0!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO recharges (user_id, amount, method, code, status) VALUES (?, ?, ?, ?, 'success')");
        $stmt->execute([$user_id, $amount, $method, $code]);
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$amount, $user_id]);
        $stmt = $pdo->prepare("UPDATE users SET total_recharge = total_recharge + ? WHERE id = ?");
        $stmt->execute([$amount, $user_id]);
        $_SESSION['balance'] += $amount;
        $success = "✅ Nạp tiền thành công! Số dư hiện tại: " . number_format($_SESSION['balance'], 0) . "đ";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nạp tiền – NEXUS VN</title>
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

<div class="payment-container">
  <h2><i class="fas fa-coins"></i> Nạp tiền vào tài khoản</h2>
  <div style="background:#1e1730; padding:12px; border-radius:12px; margin-bottom:20px; text-align:center;">
    💰 Số dư hiện tại: <strong><?= number_format($_SESSION['balance'] ?? 0, 0) ?>đ</strong>
  </div>
  <?php if (isset($success)): ?>
    <div style="color:#34d399; background:#0b1f1a; padding:12px; border-radius:12px; margin-bottom:16px; text-align:center;"><?= $success ?></div>
  <?php endif; ?>
  <?php if (isset($error)): ?>
    <div style="color:#ef4444; background:#1a0b0b; padding:12px; border-radius:12px; margin-bottom:16px; text-align:center;"><?= $error ?></div>
  <?php endif; ?>
  <div style="background:#1e1730; padding:16px; border-radius:12px; border:1px dashed #2a2535; margin-bottom:16px;">
    <p><strong>🏦 Ngân hàng MB Bank</strong></p>
    <p>Số tài khoản: <strong>0868870531</strong></p>
    <p>Chủ tài khoản: <strong>DUONG GIA HAN</strong></p>
    <p>Nội dung chuyển: <strong>NEXUS_VN + [Tên đăng nhập]</strong></p>
    <p style="font-size:0.85rem; color:#7d728f;">* Sau khi chuyển, nhập mã giao dịch để cộng tiền tự động</p>
  </div>
  <form method="POST">
    <div class="form-group">
      <label style="color:#d0c9de;">Số tiền nạp (VNĐ)</label>
      <input type="number" name="amount" placeholder="Nhập số tiền" required style="width:100%; padding:12px; border:1px solid #2a2535; border-radius:12px; background:#1e1730; color:#f0edf5;">
    </div>
    <div class="form-group">
      <label style="color:#d0c9de;">Phương thức</label>
      <select name="method" style="width:100%; padding:12px; border:1px solid #2a2535; border-radius:12px; background:#1e1730; color:#f0edf5;">
        <option value="bank">Chuyển khoản ngân hàng</option>
        <option value="card">Thẻ cào</option>
      </select>
    </div>
    <div class="form-group">
      <label style="color:#d0c9de;">Mã giao dịch / Mã thẻ</label>
      <input type="text" name="code" placeholder="Nhập mã" style="width:100%; padding:12px; border:1px solid #2a2535; border-radius:12px; background:#1e1730; color:#f0edf5;">
    </div>
    <button type="submit" name="recharge" class="btn-recharge"><i class="fas fa-plus-circle"></i> Nạp tiền</button>
  </form>
  <a href="index.php" style="display:block; text-align:center; margin-top:16px; color:#c084fc;">← Quay lại trang chủ</a>
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
