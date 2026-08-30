<?php
include 'config.php';

$error = '';
$success = '';
if (isset($_GET['msg']) && $_GET['msg'] == 'registered') {
    $success = "✅ Đăng ký thành công! Hãy đăng nhập.";
}
if (isset($_GET['msg']) && $_GET['msg'] == 'password_reset') {
    $success = "✅ Mật khẩu đã được đặt lại thành công! Hãy đăng nhập.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password']));

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $admin = $stmt->fetch();
    if ($admin) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: /admin.php');
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['balance'] = $user['balance'];
        header('Location: /');
        exit;
    }

    $error = "❌ Tên đăng nhập hoặc mật khẩu không đúng!";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - NEXUS VN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .auth-box { max-width: 420px; margin: 80px auto; background: #fff; padding: 40px; border-radius: 28px; box-shadow: 0 8px 30px rgba(0,0,0,0.02); border: 1px solid #e9edf2; }
        .auth-box h2 { text-align: center; margin-bottom: 24px; }
        .auth-box h2 span { color: #3b82f6; }
        .auth-box input { width: 100%; padding: 12px; margin: 8px 0; border-radius: 12px; border: 1px solid #d1d5db; font-size: 1rem; }
        .auth-box button { width: 100%; padding: 12px; background: #2563eb; color: #fff; border: none; border-radius: 40px; font-weight: 700; font-size: 1rem; cursor: pointer; margin-top: 10px; }
        .auth-box button:hover { background: #1d4ed8; }
        .auth-box .error { color: #ef4444; text-align: center; margin-top: 10px; }
        .auth-box .success { color: #065f46; background: #d1fae5; padding: 12px; border-radius: 12px; margin-bottom: 16px; text-align: center; }
        .auth-box .links { display: flex; justify-content: space-between; margin-top: 16px; }
        .auth-box .links a { color: #3b82f6; text-decoration: none; }
        .auth-box .register-link { text-align: center; margin-top: 16px; border-top: 1px solid #e9edf2; padding-top: 16px; }
        .auth-box .register-link a { color: #3b82f6; text-decoration: none; }
    </style>
</head>
<body>
<div class="auth-box">
    <h2>🔹 <span>NEXUS</span> VN</h2>
    <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit">Đăng nhập</button>
    </form>
    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <div class="links">
        <a href="/register.php">📝 Đăng ký</a>
        <a href="/forgot_password.php">🔑 Quên mật khẩu?</a>
    </div>
    <div class="register-link">
        <a href="/register.php">⬅️ Chưa có tài khoản? Đăng ký ngay</a>
    </div>
</div>
</body>
</html>