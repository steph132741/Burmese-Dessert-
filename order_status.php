<?php
require_once __DIR__ . '/includes/header.php';

$token = $_GET['token'] ?? '';
$success = isset($_GET['success']);
$stmt = db()->prepare('SELECT * FROM orders WHERE public_token = ? LIMIT 1');
$stmt->execute([$token]);
$order = $stmt->fetch();

if (!$order) {
    echo '<section class="section"><div class="notice">Order not found.</div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$itemStmt = db()->prepare('SELECT * FROM order_items WHERE order_id = ?');
$itemStmt->execute([$order['id']]);
$items = $itemStmt->fetchAll();

$msgStmt = db()->prepare('SELECT * FROM order_messages WHERE order_id = ? ORDER BY created_at DESC');
$msgStmt->execute([$order['id']]);
$messages = $msgStmt->fetchAll();
?>

<section class="section">
    <div class="banner">
        <h2>Order Status</h2>
        <?php if ($success): ?>
            <p><strong>Your order was placed successfully.</strong></p>
        <?php endif; ?>
        <p>Your order is currently: <strong><?= htmlspecialchars($order['status']) ?></strong></p>
    </div>
    <div class="checkout-grid">
        <div>
            <h3>Items</h3>
            <ul style="list-style:none;display:grid;gap:0.6rem;">
                <?php foreach ($items as $item): ?>
                    <li style="display:flex;justify-content:space-between;gap:1rem;">
                        <span><?= htmlspecialchars($item['product_name']) ?> x <?= (int)$item['quantity'] ?></span>
                        <span><?= format_money($item['line_total']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div style="margin-top:1rem;font-weight:700;">Total: <?= format_money($order['total']) ?></div>
        </div>
        <div class="hero-card">
            <h3>Messages from the shop</h3>
            <?php if (empty($messages)): ?>
                <p>No updates yet. We’ll message you when the order is on the way.</p>
            <?php else: ?>
                <ul style="list-style:none;display:grid;gap:0.8rem;">
                    <?php foreach ($messages as $msg): ?>
                        <li>
                            <strong><?= htmlspecialchars($msg['subject']) ?></strong>
                            <p><?= htmlspecialchars($msg['message']) ?></p>
                            <small><?= htmlspecialchars($msg['created_at']) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
