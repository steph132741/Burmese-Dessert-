<?php
require_once __DIR__ . '/auth.php';
require_admin();

$db = db();
$summary = [
    'total_orders' => 0,
    'total_revenue' => 0,
    'preparing_orders' => 0,
    'completed_orders' => 0,
];
$topProducts = [];

try {
    $orderColumns = $db->query('SHOW COLUMNS FROM orders')->fetchAll(PDO::FETCH_COLUMN);
    $orderColumns = array_flip($orderColumns);

    $summaryFields = ['COUNT(*) AS total_orders'];
    $summaryFields[] = isset($orderColumns['total']) ? 'COALESCE(SUM(total), 0) AS total_revenue' : '0 AS total_revenue';
    $summaryFields[] = isset($orderColumns['status']) ? "SUM(CASE WHEN status = 'Preparing' THEN 1 ELSE 0 END) AS preparing_orders" : '0 AS preparing_orders';
    $summaryFields[] = isset($orderColumns['status']) ? "SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) AS completed_orders" : '0 AS completed_orders';

    $summary = $db->query('SELECT ' . implode(', ', $summaryFields) . ' FROM orders')->fetch() ?: $summary;
} catch (Throwable $e) {
    $summary = $summary;
}

try {
    $itemColumns = $db->query('SHOW COLUMNS FROM order_items')->fetchAll(PDO::FETCH_COLUMN);
    $itemColumns = array_flip($itemColumns);
    if (isset($itemColumns['product_name'], $itemColumns['quantity'])) {
        $topProducts = $db->query("
            SELECT product_name, SUM(quantity) AS qty
            FROM order_items
            GROUP BY product_name
            ORDER BY qty DESC, product_name ASC
            LIMIT 5
        ")->fetchAll();
    }
} catch (Throwable $e) {
    $topProducts = [];
}

$chartLabels = [];
$chartValues = [];
foreach ($topProducts as $product) {
    $chartLabels[] = $product['product_name'];
    $chartValues[] = (int)$product['qty'];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="<?= asset_url('assets/css/styles.css') ?>" />
</head>
<body>
    <main class="section admin-shell">
        <section class="admin-hero">
            <div>
                <p class="admin-eyebrow">Golden Lotus Admin</p>
                <h2>Dashboard</h2>
                <p>Welcome back, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>. Manage products, orders, and shop updates from one place.</p>
            </div>
            <div class="admin-hero-actions">
                <a class="btn btn-primary" href="<?= asset_url('admin/products.php') ?>">Manage Products</a>
                <a class="btn btn-secondary" href="<?= asset_url('admin/orders.php') ?>">Manage Orders</a>
                <a class="btn btn-secondary" href="<?= asset_url('shop.php') ?>" target="_blank">View Shop</a>
                <a class="btn btn-secondary" href="<?= asset_url('admin/logout.php') ?>">Logout</a>
            </div>
        </section>

        <section class="admin-metrics">
            <article class="admin-metric">
                <span class="stat-label">Orders</span>
                <strong><?= (int)($summary['total_orders'] ?? 0) ?></strong>
            </article>
            <article class="admin-metric">
                <span class="stat-label">Revenue</span>
                <strong><?= format_money($summary['total_revenue'] ?? 0) ?></strong>
            </article>
            <article class="admin-metric">
                <span class="stat-label">Preparing</span>
                <strong><?= (int)($summary['preparing_orders'] ?? 0) ?></strong>
            </article>
            <article class="admin-metric">
                <span class="stat-label">Completed</span>
                <strong><?= (int)($summary['completed_orders'] ?? 0) ?></strong>
            </article>
        </section>

        <section class="admin-overview-grid">
            <div class="hero-card admin-chart-card">
                <div class="section-heading">
                    <div>
                        <h3>Best-selling desserts</h3>
                        <p>Live chart based on quantities sold in customer orders.</p>
                    </div>
                </div>
                <?php if (!empty($chartLabels)): ?>
                    <canvas
                        id="sales-chart"
                        height="240"
                        data-labels='<?= htmlspecialchars(json_encode($chartLabels), ENT_QUOTES) ?>'
                        data-values='<?= htmlspecialchars(json_encode($chartValues), ENT_QUOTES) ?>'
                    ></canvas>
                <?php else: ?>
                    <div class="admin-empty-state">
                        <strong>No sales data yet</strong>
                        <p>Your chart will appear here after customer orders start coming in.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="hero-card admin-quick-links">
                <h3>Quick Actions</h3>
                <a class="admin-link-card" href="<?= asset_url('admin/product_edit.php') ?>">
                    <strong>Add Product</strong>
                    <span>Create a new dessert and publish it to the shop.</span>
                </a>
                <a class="admin-link-card" href="<?= asset_url('admin/orders.php') ?>">
                    <strong>Review Orders</strong>
                    <span>Check the latest customer activity and update order status.</span>
                </a>
                <a class="admin-link-card" href="<?= asset_url('shop.php') ?>" target="_blank">
                    <strong>Open Shop</strong>
                    <span>Preview the customer-facing storefront in a new tab.</span>
                </a>
            </div>
        </section>
    </main>
    <script src="<?= asset_url('assets/js/app.js') ?>"></script>
</body>
</html>
