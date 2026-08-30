<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy danh sách đơn hàng của user
$stmt = $pdo->prepare("SELECT o.*, p.name as product_name, p.image FROM orders o JOIN products p ON o.product_id = p.id WHERE o.user_id = ? ORDER BY o.id DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

// Nhóm đơn hàng theo order_code
$groupedOrders = [];
foreach ($orders as $order) {
    $code = $order['order_code'] ?? '#' . $order['id'];
    if (!isset($groupedOrders[$code])) {
        $groupedOrders[$code] = [
            'id' => $order['id'],
            'order_code' => $code,
            'created_at' => $order['created_at'],
            'total' => $order['total'],
            'status' => $order['status'],
            'items' => []
        ];
    }
    $groupedOrders[$code]['items'][] = [
        'name' => $order['product_name'],
        'image' => $order['image'],
        'quantity' => $order['quantity'],
        'total' => $order['total']
    ];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử mua hàng - NEXUS VN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .history-container { max-width: 900px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 28px; border: 1px solid #e9edf2; }
        .history-container h2 { margin-bottom: 24px; }
        .order-box { border: 1px solid #e9edf2; border-radius: 16px; padding: 16px; margin-bottom: 20px; }
        .order-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; border-bottom: 1px solid #e9edf2; padding-bottom: 10px; margin-bottom: 10px; }
        .order-header .code { font-weight: 700; color: #2563eb; }
        .order-header .date { color: #64748b; font-size: 0.9rem; }
        .order-header .total { font-weight: 700; }
        .order-item { display: flex; align-items: center; gap: 16px; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .order-item img { width: 50px; height: 50px; object-fit: cover; border-radius: 10px; background: #f1f5f9; }
        .order-item .info { flex: 1; }
        .order-item .info .name { font-weight: 600; }
        .order-item .info .qty { color: #64748b; font-size: 0.9rem; }
        .order-item .price { font-weight: 600; color: #2563eb; }
        .status-badge { padding: 4px 16px; border-radius: 40px; font-size: 0.8rem; font-weight: 600; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-shipped { background: #dbeafe; color: #1e40af; }
        .status-completed { background: #e0e7ff; color: #3730a3; }
        .empty-history { text-align: center; padding: 40px; color: #94a3b8; }
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
                <span class="user-info">👤 <?= htmlspecialchars($_SESSION['username']) ?> (<?= number_format($_SESSION['balance'] ?? 0, 0) ?>đ)</span>
                <a href="logout.php" class="btn-logout">Đăng xuất</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<div class="history-container">
    <h2>📜 Lịch sử mua hàng</h2>

    <?php if (empty($groupedOrders)): ?>
        <div class="empty-history">
            <p>Bạn chưa có đơn hàng nào.</p>
            <a href="index.php" style="color:#2563eb;">🛒 Mua sắm ngay</a>
        </div>
    <?php else: ?>
        <?php foreach ($groupedOrders as $code => $order): ?>
            <div class="order-box">
                <div class="order-header">
                    <span class="code">📋 #<?= htmlspecialchars($code) ?></span>
                    <span class="date"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                    <span class="total"><?= number_format($order['total'], 0) ?>đ</span>
                    <span class="status-badge status-<?= $order['status'] ?>">
                        <?php if ($order['status'] == 'paid') echo '✅ Đã thanh toán'; ?>
                        <?php if ($order['status'] == 'pending') echo '⏳ Chờ xử lý'; ?>
                        <?php if ($order['status'] == 'shipped') echo '🚚 Đang giao'; ?>
                        <?php if ($order['status'] == 'completed') echo '✅ Hoàn thành'; ?>
                    </span>
                </div>
                <?php foreach ($order['items'] as $item): ?>
                    <div class="order-item">
                        <img src="assets/uploads/<?= htmlspecialchars($item['image'] ?? 'default.jpg') ?>" alt="">
                        <div class="info">
                            <div class="name"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="qty">Số lượng: <?= $item['quantity'] ?></div>
                        </div>
                        <div class="price"><?= number_format($item['total'], 0) ?>đ</div>
                    </div>
                <?php endforeach; ?>
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