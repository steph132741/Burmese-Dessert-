<?php
require_once __DIR__ . '/auth.php';
require_admin();

$stmt = db()->query('SELECT * FROM orders ORDER BY created_at DESC');
$orders = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Orders</title>
    <link rel="stylesheet" href="/burmese-desserts/assets/css/styles.css" />
</head>
<body>
    <main class="section">
        <div class="banner">
            <h2>Orders</h2>
        </div>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= (int)$order['id'] ?></td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td><?= htmlspecialchars($order['status']) ?></td>
                        <td><?= format_money($order['total']) ?></td>
                        <td><a href="/burmese-desserts/admin/order_view.php?id=<?= (int)$order['id'] ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p style="margin-top:1rem;"><a href="/burmese-desserts/admin/index.php">Back</a></p>
    </main>
</body>
</html>
