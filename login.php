<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng nhập – NEXUS VN</title>
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
    .auth-box .success { color: #34d399; background: #0b1f1a; padding: 12px; border-radius: 12px; margin-bottom: 16px; text-align: center; }
    .auth-box .links { display: flex; justify-content: space-between; margin-top: 16px; }
    .auth-box .links a { color: #c084fc; text-decoration: none; }
    .auth-box .register-link { text-align: center; margin-top: 16px; border-top: 1px solid #1f1b2b; padding-top: 16px; }
    .auth-box .register-link a { color: #c084fc; text-decoration: none; }
  </style>
</head>
<body>
<div class="auth-box">
  <h2>🔹 <span>NEXUS</span> VN</h2>
  <?php
  if (isset($_GET['msg']) && $_GET['msg'] == 'registered') {
      echo '<div class="success">✅ Đăng ký thành công! Hãy đăng nhập.</div>';
  }
  if (isset($_GET['msg']) && $_GET['msg'] == 'password_reset') {
      echo '<div class="success">✅ Mật khẩu đã được đặt lại thành công! Hãy đăng nhập.</div>';
  }
  ?>
  <form method="POST" action="login.php">
    <input type="text" name="username" placeholder="Tên đăng nhập" required>
    <input type="password" name="password" placeholder="Mật khẩu" required>
    <button type="submit">Đăng nhập</button>
  </form>
  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = trim($_POST['username']);
      $password = md5(trim($_POST['password']));
      $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
      $stmt->execute([$username, $password]);
      $admin = $stmt->fetch();
      if ($admin) {
          $_SESSION['admin_id'] = $admin['id'];
          $_SESSION['admin_username'] = $admin['username'];
          header('Location: admin.php');
          exit;
      }
      $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
      $stmt->execute([$username, $password]);
      $user = $stmt->fetch();
      if ($user) {
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['username'] = $user['username'];
          $_SESSION['balance'] = $user['balance'];
          header('Location: index.php');
          exit;
      }
      echo '<p class="error">❌ Tên đăng nhập hoặc mật khẩu không đúng!</p>';
  }
  ?>
  <div class="links">
    <a href="register.php">📝 Đăng ký</a>
    <a href="forgot_password.php">🔑 Quên mật khẩu?</a>
  </div>
  <div class="register-link">
    <a href="register.php">⬅️ Chưa có tài khoản? Đăng ký ngay</a>
  </div>
</div>
</body>
</html>
