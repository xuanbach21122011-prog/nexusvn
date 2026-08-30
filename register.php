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
  <style>
    .auth-box { max-width: 420px; margin: 80px auto; background: #12101a; padding: 40px; border-radius: 28px; border: 1px solid #211c2e; }
    .auth-box h2 { text-align: center; margin-bottom: 24px; color: #f0edf5; }
    .auth-box h2 span { color: #c084fc; }
    .auth-box input { width: 100%; padding: 12px; margin: 8px 0; border-radius: 12px; border: 1px solid #2a2535; background: #1e1730; color: #f0edf5; }
    .auth-box input::placeholder { color: #7d728f; }
    .auth-box button { width: 100%; padding: 12px; background: linear-gradient(135deg, #a855f7, #ec4899); border: none; border-radius: 40px; color: #fff; font-weight: 700; font-size: 1rem; cursor: pointer; margin-top: 10px; }
    .auth-box button:hover { transform: scale(1.02); }
    .auth-box .error { color: #ef4444; text-align: center; margin-top: 10px; }
    .auth-box .links { text-align: center; margin-top: 16px; }
    .auth-box .links a { color: #c084fc; text-decoration: none; }
  </style>
</head>
<body>
<div class="auth-box">
  <h2>🔹 <span>Đăng ký</span> NEXUS VN</h2>
  <form method="POST" action="register.php">
    <input type="text" name="username" placeholder="Tên đăng nhập" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Mật khẩu" required>
    <button type="submit">Đăng ký</button>
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
