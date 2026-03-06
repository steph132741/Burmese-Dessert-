<?php
require_once __DIR__ . '/auth.php';
require_admin();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/burmese-desserts/assets/css/styles.css" />
</head>
<body>
    <main class="section">
        <div class="banner">
            <h2>Admin Dashboard</h2>
            <p>Welcome, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>.</p>
        </div>
        <div class="product-grid">
            <a class="product-card" href="/burmese-desserts/admin/products.php">
                <h3>Manage Products</h3>
                <p>Add, edit, and delete desserts.</p>
            </a>
            <a class="product-card" href="/burmese-desserts/admin/orders.php">
                <h3>Manage Orders</h3>
                <p>Update order status and send messages.</p>
            </a>
            <a class="product-card" href="/burmese-desserts/admin/logout.php">
                <h3>Logout</h3>
                <p>Sign out of admin.</p>
            </a>
        </div>
    </main>
</body>
</html>
