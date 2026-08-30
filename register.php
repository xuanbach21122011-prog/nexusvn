<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password']));
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        $error = "❌ Tên đăng nhập hoặc email đã được sử dụng!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $email]);
        header('Location: login.php?msg=registered');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký – NEXUS VN</title>
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
            <a href="login.php" class="btn-ghost"><i class="fas fa-user"></i> Đăng nhập</a>
            <a href="register.php" class="btn-glow"><i class="fas fa-user-plus"></i> Đăng ký</a>
        </nav>
    </div>
</header>

<div class="auth-box">
    <h2>🔹 <span style="color: #c084fc;">Đăng ký</span> NEXUS VN</h2>
    <?php if (isset($error)): ?>
        <div class="error" style="color:#ef4444; background:#1a0b0b; padding:12px; border-radius:12px; margin-bottom:16px; text-align:center;"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST" action="register.php">
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit"><i class="fas fa-user-plus"></i> Đăng ký</button>
    </form>
    <div class="links">
        <a href="login.php"><i class="fas fa-arrow-left"></i> Đã có tài khoản? Đăng nhập</a>
    </div>
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
