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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>NEXUS VN · Công cụ kỹ thuật số</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header>
  <div class="container header-inner">
    <div class="logo"><i class="fas fa-bolt"></i> NEXUS VN</div>
    <nav class="nav-links">
      <a href="/">Trang chủ</a>
      <a href="/cart.php">🛒 Giỏ hàng</a>
      <a href="/leaderboard.php">🏆 Bảng xếp hạng</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="/payment.php">💳 Nạp tiền</a>
        <span class="user-info">👤 <?= htmlspecialchars($_SESSION['username']) ?> (<?= number_format($_SESSION['balance'] ?? 0, 0) ?>đ)</span>
        <a href="/logout.php" class="btn-logout">Đăng xuất</a>
      <?php else: ?>
        <a href="/login.php">Đăng nhập</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main>
  <div class="container hero">
    <div class="hero-content">
      <div class="badge"><i class="fas fa-rocket"></i> Early Access 2026</div>
      <h1>Nâng cấp trải nghiệm <br>với <span>công cụ số</span> chất lượng</h1>
      <p>NEXUS VN – nơi cung cấp key hack, phần mềm, tiện ích chuyên sâu dành cho game thủ và người dùng hiện đại.</p>
      <div class="hero-buttons">
        <a href="#products" class="btn-glow"><i class="fas fa-eye"></i> Xem sản phẩm</a>
        <a href="/login.php" class="btn-ghost" style="padding:14px 28px;"><i class="fas fa-user"></i> Đăng nhập</a>
      </div>
      <div class="hero-stats">
        <div class="stat"><span class="num"><?= count($products) ?>+</span><span class="label">Sản phẩm</span></div>
        <div class="stat"><span class="num">4.9⭐</span><span class="label">Đánh giá</span></div>
        <div class="stat"><span class="num">3.2k</span><span class="label">Người dùng</span></div>
      </div>
    </div>
    <div class="hero-visual">
      <div class="code-block">
        <span class="comment">// NEXUS VN Engine v2.0</span><br>
        <span class="keyword">const</span> tool = <span class="string">'KeyManager'</span>;<br>
        <span class="keyword">let</span> result = <span class="keyword">await</span> tool.run();<br>
        <span class="comment">// output: ✅ Key đã được gửi</span>
      </div>
      <div style="display:flex; gap:12px; justify-content:center;">
        <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#c084fc;"></span>
        <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#f472b6;"></span>
        <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#34d399;"></span>
      </div>
    </div>
  </div>

  <div class="container feature-row">
    <div class="feature-item">
      <i class="fas fa-shield-alt"></i>
      <h4>Bảo mật tuyệt đối</h4>
      <p>Mã hóa và xác thực an toàn</p>
    </div>
    <div class="feature-item">
      <i class="fas fa-bolt"></i>
      <h4>Giao hàng tự động</h4>
      <p>Key được gửi ngay sau thanh toán</p>
    </div>
    <div class="feature-item">
      <i class="fas fa-sync-alt"></i>
      <h4>Cập nhật liên tục</h4>
      <p>Key mới mỗi ngày</p>
    </div>
    <div class="feature-item">
      <i class="fas fa-headset"></i>
      <h4>Hỗ trợ 24/7</h4>
      <p>Đội ngũ sẵn sàng giúp đỡ</p>
    </div>
  </div>

  <div class="container" id="products">
    <div class="section-header">
      <h2>🔥 <span>Key hack</span> nổi bật</h2>
      <p>Chọn sản phẩm phù hợp với nhu cầu của bạn – mỗi key đều được kiểm tra kỹ lưỡng</p>
    </div>

    <div class="grid-products">
      <?php if (count($products) > 0): ?>
        <?php foreach ($products as $product): ?>
          <div class="product-card">
            <?php if ($product['price'] > 200000): ?>
              <span class="hot-badge">🔥 Hot</span>
            <?php elseif ($product['price'] > 100000): ?>
              <span class="popular-badge">⭐ Phổ biến</span>
            <?php endif; ?>
            <div class="icon"><i class="fas fa-key"></i></div>
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <div class="price"><?= number_format($product['price'], 0) ?>đ <small>/ key</small></div>
            <p class="desc"><?= htmlspecialchars($product['description']) ?></p>
            <ul class="features">
              <li><i class="fas fa-check"></i> Bảo hành 24h</li>
              <li><i class="fas fa-check"></i> Giao ngay sau thanh toán</li>
              <li><i class="fas fa-check"></i> Hỗ trợ đổi key</li>
            </ul>
            <form action="/cart.php" method="POST">
              <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
              <button type="submit" name="add_to_cart" class="btn-buy"><i class="fas fa-shopping-bag"></i> Thêm vào giỏ</button>
            </form>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="empty-msg" style="grid-column: 1 / -1; text-align:center; padding:60px 0; color:#94a3b8; font-size:1.1rem;">
          Chưa có sản phẩm nào. <a href="/admin.php" style="color:#2563eb; font-weight:600;">Thêm sản phẩm</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>

<footer>
  <div class="container">
    <div class="footer-grid">
      <div class="footer-col">
        <h4>NEXUS VN</h4>
        <ul>
          <li>Giới thiệu</li>
          <li>Blog</li>
          <li>Hướng dẫn</li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Sản phẩm</h4>
        <ul>
          <li>Key game</li>
          <li>Phần mềm</li>
          <li>Tiện ích</li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Hỗ trợ</h4>
        <ul>
          <li>FAQ</li>
          <li>Liên hệ</li>
          <li>Chính sách bảo mật</li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Kết nối</h4>
        <ul>
          <li>Facebook</li>
          <li>Telegram</li>
          <li>GitHub</li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; 2026 NEXUS VN – Công cụ số chất lượng cao.</span>
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
