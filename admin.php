<?php
include 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['desc'];
    $image = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['image']['tmp_name'];
        $nameFile = time() . '_' . basename($_FILES['image']['name']);
        $dest = UPLOAD_DIR . $nameFile;
        if (move_uploaded_file($tmp, $dest)) {
            $image = $nameFile;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, price, image, description) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $price, $image, $desc]);
    header('Location: /admin.php?msg=added');
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: /admin.php?msg=deleted');
    exit;
}

$editProduct = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $editProduct = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['desc'];
    $image = $_POST['old_image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['image']['tmp_name'];
        $nameFile = time() . '_' . basename($_FILES['image']['name']);
        $dest = UPLOAD_DIR . $nameFile;
        if (move_uploaded_file($tmp, $dest)) {
            $image = $nameFile;
        }
    }

    $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, image=?, description=? WHERE id=?");
    $stmt->execute([$name, $price, $image, $desc, $id]);
    header('Location: /admin.php?msg=updated');
    exit;
}

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị - NEXUS VN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .admin-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .admin-card { background: #fff; border-radius: 24px; padding: 30px; box-shadow: 0 8px 30px rgba(0,0,0,0.02); border: 1px solid #e9edf2; margin-bottom: 30px; }
        .admin-card h2 { margin-bottom: 20px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 4px; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 12px; font-size: 1rem; }
        .btn-submit { background: #2563eb; color: #fff; border: none; padding: 12px 32px; border-radius: 40px; font-weight: 600; cursor: pointer; }
        .btn-submit:hover { background: #1d4ed8; }
        .btn-edit { background: #f59e0b; color: #fff; border: none; padding: 6px 16px; border-radius: 40px; cursor: pointer; }
        .btn-del { background: #ef4444; color: #fff; border: none; padding: 6px 16px; border-radius: 40px; cursor: pointer; }
        .btn-cancel { background: #6b7280; color: #fff; border: none; padding: 12px 32px; border-radius: 40px; text-decoration: none; display: inline-block; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border-bottom: 1px solid #e9edf2; text-align: left; }
        th { background: #f8fafc; }
        .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 12px; background: #f1f5f9; }
        .msg { padding: 12px; border-radius: 12px; margin-bottom: 16px; }
        .msg-success { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
<header>
    <div class="container">
        <div class="logo"><i class="fas fa-bolt"></i> NEXUS VN</div>
        <nav>
            <a href="/">Trang chủ</a>
            <a href="/admin.php">📋 Quản trị</a>
            <a href="/logout.php" class="btn-logout">Đăng xuất</a>
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
                <button type="submit" name="update_product" class="btn-submit">Cập nhật</button>
                <a href="/admin.php" class="btn-cancel">Huỷ</a>
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
                <button type="submit" name="add_product" class="btn-submit">Thêm sản phẩm</button>
            </form>
        <?php endif; ?>
    </div>
    <div class="admin-card">
        <h2>📋 Danh sách sản phẩm</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Tên</th>
                    <th>Giá</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><img src="/assets/uploads/<?= htmlspecialchars($p['image'] ?? 'default.jpg') ?>" class="product-img"></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= number_format($p['price'], 0) ?>đ</td>
                    <td>
                        <a href="/admin.php?edit=<?= $p['id'] ?>" class="btn-edit">Sửa</a>
                        <a href="/admin.php?delete=<?= $p['id'] ?>" class="btn-del" onclick="return confirm('Xoá sản phẩm này?')">Xoá</a>
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
        <div class="social">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-telegram"></i></a>
            <a href="#"><i class="fab fa-github"></i></a>
        </div>
    </div>
</footer>
</body>
</html>