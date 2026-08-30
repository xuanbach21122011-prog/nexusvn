<?php
include 'config.php';

$stmt = $pdo->query("SELECT username, total_recharge FROM users ORDER BY total_recharge DESC LIMIT 10");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng xếp hạng - NEXUS VN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .leaderboard-container { max-width: 700px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 28px; border: 1px solid #e9edf2; }
        .leaderboard-container h2 { text-align: center; margin-bottom: 24px; }
        .rank-item { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e9edf2; padding: 12px 0; }
        .rank-item .rank { font-weight: 700; font-size: 1.2rem; min-width: 40px; }
        .rank-item .rank.gold { color: #f59e0b; }
        .rank-item .rank.silver { color: #94a3b8; }
        .rank-item .rank.bronze { color: #d97706; }
        .rank-item .name { flex: 1; margin-left: 12px; }
        .rank-item .amount { font-weight: 700; color: #2563eb; }
    </style>
</head>
<body>
<header>
    <div class="container">
        <div class="logo"><i class="fas fa-bolt"></i> NEXUS VN</div>
        <nav>
            <a href="index.php">Trang chủ</a>
            <a href="cart.php">🛒 Giỏ hàng</a>
            <a href="leaderboard.php">🏆 Bảng xếp hạng</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="user-info">👤 <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="logout.php" class="btn-logout">Đăng xuất</a>
            <?php else: ?>
                <a href="login.php">Đăng nhập</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<div class="leaderboard-container">
    <h2>🏆 Bảng xếp hạng người nạp nhiều nhất</h2>

    <?php if (empty($users)): ?>
        <p style="text-align:center; color:#94a3b8;">Chưa có ai nạp tiền.</p>
    <?php else: ?>
        <?php foreach ($users as $index => $u): ?>
            <div class="rank-item">
                <span class="rank 
                    <?= $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) ?>">
                    #<?= $index + 1 ?>
                </span>
                <span class="name"><?= htmlspecialchars($u['username']) ?></span>
                <span class="amount"><?= number_format($u['total_recharge'], 0) ?>đ</span>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
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