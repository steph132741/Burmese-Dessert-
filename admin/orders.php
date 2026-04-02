<?php
require_once __DIR__ . '/auth.php';
require_admin();

$statusFilter = trim($_GET['status'] ?? '');
$search = trim($_GET['search'] ?? '');

$sql = 'SELECT * FROM orders WHERE 1 = 1';
$params = [];
if ($statusFilter !== '') {
    $sql .= ' AND status = ?';
    $params[] = $statusFilter;
}
if ($search !== '') {
    $sql .= ' AND (customer_name LIKE ? OR email LIKE ? OR phone LIKE ? OR public_token LIKE ?)';
    $term = '%' . $search . '%';
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
}
$sql .= ' ORDER BY created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Orders</title>
    <link rel="stylesheet" href="<?= asset_url('assets/css/styles.css') ?>" />
</head>
<body>
    <main class="section">
        <div class="banner">
            <h2>Orders</h2>
        </div>
        <form class="shop-toolbar" method="get">
            <div class="shop-search">
                <input type="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search customer, phone, email, or token">
            </div>
            <div class="shop-filters">
                <select name="status">
                    <option value="">All statuses</option>
                    <?php foreach (['Preparing', 'Baking', 'Ready for pickup', 'Out for delivery', 'Completed'] as $status): ?>
                        <option value="<?= htmlspecialchars($status) ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= htmlspecialchars($status) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-primary" type="submit">Filter</button>
                <a class="btn btn-secondary" href="<?= asset_url('admin/orders.php') ?>">Reset</a>
            </div>
        </form>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Placed</th>
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
                        <td><?= htmlspecialchars($order['created_at']) ?></td>
                        <td><a href="<?= asset_url('admin/order_view.php') ?>?id=<?= (int)$order['id'] ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p style="margin-top:1rem;"><a href="<?= asset_url('admin/index.php') ?>">Back</a></p>
    </main>
    <script src="<?= asset_url('assets/js/app.js') ?>"></script>
</body>
</html>
