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
    <button type="submit"><i class="fas fa-arrow-right"></i> Đăng nhập</button>
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
    <a href="register.php"><i class="fas fa-user-plus"></i> Đăng ký</a>
    <a href="forgot_password.php"><i class="fas fa-key"></i> Quên mật khẩu?</a>
  </div>
  <div class="register-link">
    <a href="register.php">⬅️ Chưa có tài khoản? Đăng ký ngay</a>
  </div>
</div>
</body>
</html>
