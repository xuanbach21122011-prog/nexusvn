<?php
include 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// ... (giữ nguyên các xử lý add/update/delete product)

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản trị – NEXUS VN</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .admin-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
    .admin-card { background: #12101a; border-radius: 24px; padding: 30px; border: 1px solid #211c2e; margin-bottom: 30px; }
    .admin-card h2 { color: #f0edf5; margin-bottom: 20px; }
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 4px; color: #d0c9de; }
    .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #2a2535; border-radius: 12px; background: #1e1730; color: #f0edf5; }
    .btn-submit { background: linear-gradient(135deg, #a855f7, #ec4899); color: #fff; border: none; padding: 12px 32px; border-radius: 40px; font-weight: 600; cursor: pointer; }
    .btn-submit:hover { transform: scale(1.02); }
    .btn-edit { background: #f59e0b; color: #fff; border: none; padding: 6px 16px; border-radius: 40px; cursor: pointer; }
    .btn-del { background: #ef4444; color: #fff; border: none; padding: 6px 16px; border-radius: 40px; cursor: pointer; }
    .btn-cancel { background: #6b7280; color: #fff; border: none; padding: 12px 32px; border-radius: 40px; text-decoration: none; display: inline-block; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 12px; border-bottom: 1px solid #1f1b2b; text-align: left; color: #d0c9de; }
    th { background: #1e1730; color: #f0edf5; }
    .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 12px; background: #1e1730; }
    .msg { padding: 12px; border-radius: 12px; margin-bottom: 16px; }
    .msg-success { background: #0b1f1a; color: #34d399; }
  </style>
</head>
<body>
<header>
  <div class="container">
    <div class="logo"><i class="fas fa-bolt"></i> NEXUS VN</div>
    <nav>
      <a href="index.php"><i class="fas fa-home"></i> Trang chủ</a>
      <a href="admin.php"><i class="fas fa-cog"></i> Quản trị</a>
      <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
    </nav>
  </div>
</header>
<div class="admin-container">
  <?php if (isset($_GET['msg'])): ?>
    <div class="msg msg-success">
      <?php if ($_GET['msg'] == 'added') echo "✅ Thêm sản phẩm thành công!"; ?>
      <?php if ($_GET['msg'] == 'deleted') echo "✅ Xoá sản phẩm thành công!"; ?>
      <?php if ($_GET['msg'] == 'updated') echo "✅ Cập nhật sản phẩm thành công!"; ?>
    </div>
  <?php endif; ?>
  <!-- Form thêm/sửa sản phẩm -->
  <div class="admin-card">
    <?php if (isset($editProduct)): ?>
      <h2>✏️ Sửa sản phẩm</h2>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
        <input type="hidden" name="old_image" value="<?= $editProduct['image'] ?>">
        <div class="form-group">
          <label>Tên sản phẩm</label>
          <input type="text" name="name" value="<?= htmlspecialchars($editProduct['name']) ?>" required>
        </div>
        <div class="form-group">
          <label>Giá (VNĐ)</label>
          <input type="number" name="price" value="<?= $editProduct['price'] ?>" required>
        </div>
        <div class="form-group">
          <label>Mô tả</label>
          <textarea name="desc" rows="3"><?= htmlspecialchars($editProduct['description']) ?></textarea>
        </div>
        <div class="form-group">
          <label>Ảnh (để trống nếu giữ ảnh cũ)</label>
          <input type="file" name="image" accept="image/*">
        </div>
        <button type="submit" name="update_product" class="btn-submit"><i class="fas fa-save"></i> Cập nhật</button>
        <a href="admin.php" class="btn-cancel"><i class="fas fa-times"></i> Huỷ</a>
      </form>
    <?php else: ?>
      <h2>➕ Thêm sản phẩm mới</h2>
      <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label>Tên sản phẩm</label>
          <input type="text" name="name" required>
        </div>
        <div class="form-group">
          <label>Giá (VNĐ)</label>
          <input type="number" name="price" required>
        </div>
        <div class="form-group">
          <label>Mô tả</label>
          <textarea name="desc" rows="3"></textarea>
        </div>
        <div class="form-group">
          <label>Ảnh sản phẩm</label>
          <input type="file" name="image" accept="image/*">
        </div>
        <button type="submit" name="add_product" class="btn-submit"><i class="fas fa-plus"></i> Thêm sản phẩm</button>
      </form>
    <?php endif; ?>
  </div>
  <!-- Danh sách sản phẩm -->
  <div class="admin-card">
    <h2>📋 Danh sách sản phẩm</h2>
    <table>
      <thead>
        <tr><th>ID</th><th>Ảnh</th><th>Tên</th><th>Giá</th><th>Hành động</th></tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td><?= $p['id'] ?></td>
          <td><img src="/assets/uploads/<?= htmlspecialchars($p['image'] ?? 'default.jpg') ?>" class="product-img"></td>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td><?= number_format($p['price'], 0) ?>đ</td>
          <td>
            <a href="admin.php?edit=<?= $p['id'] ?>" class="btn-edit"><i class="fas fa-edit"></i> Sửa</a>
            <a href="admin.php?delete=<?= $p['id'] ?>" class="btn-del" onclick="return confirm('Xoá sản phẩm này?')"><i class="fas fa-trash"></i> Xoá</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<footer>
  <div class="container">
    <span>&copy; 2026 NEXUS VN. All rights reserved.</span>
    <div class="footer-social">
      <a href="#"><i class="fab fa-facebook"></i></a>
      <a href="#"><i class="fab fa-telegram"></i></a>
      <a href="#"><i class="fab fa-github"></i></a>
    </div>
  </div>
</footer>
</body>
</html>
