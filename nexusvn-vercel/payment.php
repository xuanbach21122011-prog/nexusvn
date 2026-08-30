<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recharge'])) {
    $amount = (float)$_POST['amount'];
    $method = $_POST['method'];
    $code = $_POST['code'] ?? '';

    if ($amount <= 0) {
        $error = "Số tiền phải lớn hơn 0!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO recharges (user_id, amount, method, code, status) VALUES (?, ?, ?, ?, 'success')");
        $stmt->execute([$user_id, $amount, $method, $code]);

        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$amount, $user_id]);

        $stmt = $pdo->prepare("UPDATE users SET total_recharge = total_recharge + ? WHERE id = ?");
        $stmt->execute([$amount, $user_id]);

        $_SESSION['balance'] += $amount;

        $success = "✅ Nạp tiền thành công! Số dư hiện tại: " . number_format($_SESSION['balance'], 0) . "đ";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nạp tiền - NEXUS VN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .payment-container { max-width: 500px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 28px; border: 1px solid #e9edf2; }
        .payment-container h2 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 4px; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 12px; font-size: 1rem; }
        .btn-recharge { background: #2563eb; color: #fff; border: none; padding: 12px; border-radius: 40px; font-weight: 600; cursor: pointer; width: 100%; }
        .btn-recharge:hover { background: #1d4ed8; }
        .success { color: #065f46; background: #d1fae5; padding: 12px; border-radius: 12px; margin-bottom: 16px; text-align: center; }
        .error { color: #ef4444; background: #fef2f2; padding: 12px; border-radius: 12px; margin-bottom: 16px; text-align: center; }
        .balance-info { background: #f1f5f9; padding: 12px; border-radius: 12px; margin-bottom: 20px; text-align: center; font-weight: 700; }
        .bank-info { background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px dashed #d1d5db; margin-bottom: 16px; }
        .bank-info p { margin: 4px 0; }
        .bank-info strong { color: #2563eb; }
    </style>
</head>
<body>
<header>
    <div class="container">
        <div class="logo"><i class="fas fa-bolt"></i> NEXUS VN</div>
        <nav>
            <a href="/">Trang chủ</a>
            <a href="/cart.php">🛒 Giỏ hàng</a>
            <a href="/leaderboard.php">🏆 Bảng xếp hạng</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="user-info">👤 <?= htmlspecialchars($_SESSION['username']) ?> (<?= number_format($_SESSION['balance'] ?? 0, 0) ?>đ)</span>
                <a href="/logout.php" class="btn-logout">Đăng xuất</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<div class="payment-container">
    <h2>💳 Nạp tiền vào tài khoản</h2>
    <div class="balance-info">
        💰 Số dư hiện tại: <strong><?= number_format($_SESSION['balance'] ?? 0, 0) ?>đ</strong>
    </div>
    <?php if (isset($success)): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <div class="bank-info">
        <p><strong>🏦 Ngân hàng MB Bank</strong></p>
        <p>Số tài khoản: <strong>0868870531</strong></p>
        <p>Chủ tài khoản: <strong>DUONG GIA HAN</strong></p>
        <p>Nội dung chuyển: <strong>NEXUS_VN + [Tên đăng nhập của bạn]</strong></p>
        <p style="font-size:0.85rem; color:#64748b;">* Sau khi chuyển khoản, nhập mã giao dịch bên dưới để cộng tiền tự động</p>
    </div>
    <form method="POST">
        <div class="form-group">
            <label>Số tiền nạp (VNĐ)</label>
            <input type="number" name="amount" placeholder="Nhập số tiền" required>
        </div>
        <div class="form-group">
            <label>Phương thức</label>
            <select name="method">
                <option value="bank">Chuyển khoản ngân hàng</option>
                <option value="card">Thẻ cào</option>
            </select>
        </div>
        <div class="form-group">
            <label>Mã giao dịch / Mã thẻ</label>
            <input type="text" name="code" placeholder="Nhập mã giao dịch hoặc mã thẻ cào">
        </div>
        <button type="submit" name="recharge" class="btn-recharge">Nạp tiền</button>
    </form>
    <a href="/" style="display:block; text-align:center; margin-top:16px; color:#3b82f6;">← Quay lại trang chủ</a>
</div>

<footer>
    <div class="container">
        <span>&copy; 2026 NEXUS VN. All rights reserved.</span>
        <div class="social">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-telegram"></i></a>
            <a href="#"><i class="fab fa-github"></i></a>
        </div>
    </div>
</footer>
</body>
</html>