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
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1