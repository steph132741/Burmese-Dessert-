<?php
require_once __DIR__ . '/auth.php';
require_admin();

$stmt = db()->query('SELECT * FROM products ORDER BY id DESC');
$products = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Products</title>
    <link rel="stylesheet" href="<?= asset_url('assets/css/styles.css') ?>" />
</head>
<body>
    <main class="section">
        <div class="banner">
            <h2>Products</h2>
            <p><a class="btn btn-primary" href="<?= asset_url('admin/product_edit.php') ?>">Add Product</a></p>
        </div>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Featured</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><img src="<?= htmlspecialchars(product_image_url($product['image'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width:64px;height:64px;object-fit:cover;border-radius:12px;"></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= format_money($product['price']) ?></td>
                        <td><?= (int)$product['stock'] ?></td>
                        <td><?= $product['is_featured'] ? 'Yes' : 'No' ?></td>
                        <td>
                            <a href="<?= asset_url('admin/product_edit.php') ?>?id=<?= (int)$product['id'] ?>">Edit</a>
                            | <a href="<?= asset_url('product.php') ?>?id=<?= (int)$product['id'] ?>" target="_blank">Customer View</a>
                            | <a href="<?= asset_url('admin/product_edit.php') ?>?delete=<?= (int)$product['id'] ?>" onclick="return confirm('Delete this product?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p style="margin-top:1rem;"><a href="<?= asset_url('admin/index.php') ?>">Back</a></p>
    </main>
    <script src="<?= asset_url('assets/js/app.js') ?>"></script>
</body>
</html>
