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
    header('Location: cart.php');
    exit;
}

if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header('Location: cart.php');
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
    header('Location: cart.php');
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
  <title>Giỏ hàng – NEXUS VN</title>
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

<div class="cart-container">
  <h2><i class="fas fa-shopping-cart"></i> Giỏ hàng của bạn</h2>
  <?php if (empty($cartItems)): ?>
    <p class="empty-msg">Giỏ hàng trống. <a href="index.php" style="color:#c084fc;">Tiếp tục mua sắm</a></p>
  <?php else: ?>
    <form method="POST">
      <?php foreach ($cartItems as $item): ?>
        <div class="cart-item">
          <img src="/assets/uploads/<?= htmlspecialchars($item['product']['image'] ?? 'default.jpg') ?>" alt="">
          <div class="info">
            <h3><?= htmlspecialchars($item['product']['name']) ?></h3>
            <span class="price"><?= number_format($item['product']['price'], 0) ?>đ</span>
          </div>
          <input type="number" name="quantity[<?= $item['product']['id'] ?>]" value="<?= $item['qty'] ?>" min="0" style="width:60px; padding:6px; border-radius:8px; border:1px solid #2a2535; background:#1e1730; color:#f0edf5; text-align:center;">
          <span><?= number_format($item['subtotal'], 0) ?>đ</span>
          <a href="cart.php?remove=<?= $item['product']['id'] ?>" class="btn-remove"><i class="fas fa-trash"></i> Xoá</a>
        </div>
      <?php endforeach; ?>
      <div style="margin-top:16px;">
        <button type="submit" name="update_cart" class="btn-checkout" style="background:#6b7280;"><i class="fas fa-sync-alt"></i> Cập nhật giỏ</button>
      </div>
    </form>
    <div class="cart-total">Tổng: <?= number_format($total, 0) ?>đ</div>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="checkout.php" class="btn-checkout"><i class="fas fa-credit-card"></i> Tiến hành thanh toán</a>
    <?php else: ?>
      <p>Vui lòng <a href="login.php" style="color:#c084fc;">đăng nhập</a> để thanh toán.</p>
    <?php endif; ?>
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
