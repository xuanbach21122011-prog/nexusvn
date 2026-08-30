<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password']));
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        $error = "Tên đăng nhập hoặc email đã được sử dụng!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $email]);
        header('Location: /login.php?msg=registered');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - NEXUS VN</title>
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
        .auth-box .links { text-align: center; margin-top: 16px; }
        .auth-box .links a { color: #3b82f6; text-decoration: none; }
    </style>
</head>
<body>
<div class="auth-box">
    <h2>🔹 <span>Đăng ký</span> NEXUS VN</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit">Đăng ký</button>
    </form>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <div class="links">
        <a href="/login.php">⬅️ Đã có tài khoản? Đăng nhập</a>
    </div>
</div>
</body>
</html>