<?php
// =====================================================
// CẤU HÌNH DATABASE - DÙNG CHO RENDER + AIVEN
// =====================================================

// Ưu tiên lấy từ biến môi trường, nếu không có thì dùng giá trị mặc định (fallback)
$host = getenv('DB_HOST') ?: 'pg-35afdf80-xuanbach21122011-9329.l.aivencloud.com';
$port = getenv('DB_PORT') ?: '18543';
$dbname = getenv('DB_NAME') ?: 'defaultdb';
$user = getenv('DB_USER') ?: 'avnadmin';
$pass = getenv('DB_PASS') ?: 'AVNS_gMFjbX8aO2_bbVVOK6b';

// Kết nối PostgreSQL
try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Kết nối database thất bại: " . $e->getMessage());
}

// Khởi động session (để đăng nhập, giỏ hàng, ...)
session_start();

// Đường dẫn gốc của web (dùng cho Render)
define('BASE_URL', '/');

// Thư mục lưu ảnh (tương đối)
define('UPLOAD_DIR', __DIR__ . '/assets/uploads/');

// Bật hiển thị lỗi (tắt khi chạy ổn định)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>