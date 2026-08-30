<?php
include 'config.php';

$token = $_GET['token'] ?? '';
if (empty($token)) {
    die("Token không hợp lệ.");
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expiry > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    die("Token đã hết hạn hoặc không hợp lệ.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = md5($_POST['password']);
    $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
    $stmt->execute([$newPassword, $user['id']]);
    header('Location: login.php?msg=password_reset');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - NEXUS VN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .auth-box { max-width: 420px; margin: 80px auto; background: #fff; padding: 40px; border-radius: 28px; box-shadow: 0 8px 30px rgba(0,0,0,0.02); border: 1px solid #e9edf2; }
        .auth-box h2 { text-align: center; margin-bottom: 24px; }
        .auth-box input { width: 100%; padding: 12px; margin: 8px 0; border-radius: 12px; border: 1px solid #d1d5db; font-size: 1rem; }
        .auth-box button { width: 100%; padding: 12px; background: #2563eb; color: #fff; border: none; border-radius: 40px; font-weight: 700; font-size: 1rem; cursor: pointer; margin-top: 10px; }
        .auth-box button:hover { background: #1d4ed8; }
        .auth-box .links { text-align: center; margin-top: 16px; }
        .auth-box .links a { color: #3b82f6; text-decoration: none; }
    </style>
</head>
<body>
<div class="auth-box">
    <h2>🔹 Đặt lại mật khẩu</h2>
    <form method="POST">
        <input type="password" name="password" placeholder="Mật khẩu mới" required>
        <button type="submit">Cập nhật</button>
    </form>
    <div class="links">
        <a href="login.php">⬅️ Đăng nhập</a>
    </div>
</div>
</body>
</html>