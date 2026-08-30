<?php include 'config.php'; ?>
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
<div class="auth-box">
  <h2>🔹 <span>Đăng ký</span> NEXUS VN</h2>
  <form method="POST" action="register.php">
    <input type="text" name="username" placeholder="Tên đăng nhập" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Mật khẩu" required>
    <button type="submit"><i class="fas fa-user-plus"></i> Đăng ký</button>
  </form>
  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = trim($_POST['username']);
      $password = md5(trim($_POST['password']));
      $email = trim($_POST['email']);
      $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
      $stmt->execute([$username, $email]);
      if ($stmt->rowCount() > 0) {
          echo '<p class="error">❌ Tên đăng nhập hoặc email đã được sử dụng!</p>';
      } else {
          $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
          $stmt->execute([$username, $password, $email]);
          header('Location: login.php?msg=registered');
          exit;
      }
  }
  ?>
  <div class="links">
    <a href="login.php">⬅️ Đã có tài khoản? Đăng nhập</a>
  </div>
</div>
</body>
</html>
