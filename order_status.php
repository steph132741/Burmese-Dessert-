<?php
require_once __DIR__ . '/includes/header.php';

$token = trim($_POST['token'] ?? ($_GET['token'] ?? ''));
$success = isset($_GET['success']);
$order = null;

if ($token !== '') {
    $stmt = db()->prepare('SELECT * FROM orders WHERE public_token = ? LIMIT 1');
    $stmt->execute([$token]);
    $order = $stmt->fetch();
}

$items = [];
$messages = [];
if ($order) {
    $itemStmt = db()->prepare('SELECT * FROM order_items WHERE order_id = ?');
    $itemStmt->execute([$order['id']]);
    $items = $itemStmt->fetchAll();

    $msgStmt = db()->prepare('SELECT * FROM order_messages WHERE order_id = ? ORDER BY created_at DESC');
    $msgStmt->execute([$order['id']]);
    $messages = $msgStmt->fetchAll();
}
?>

<section class="section">
    <div class="banner">
        <h2>Order Status</h2>
        <?php if ($success): ?>
            <p><strong>Your order has been received and is now being prepared.</strong></p>
        <?php endif; ?>
        <p>Enter your tracking token from checkout to see the latest status and messages from the shop.</p>
    </div>
    <form class="hero-card order-lookup animate" method="post">
        <div class="form-group">
            <label for="token">Tracking token</label>
            <input id="token" name="token" value="<?= htmlspecialchars($token) ?>" placeholder="Paste your order token here" required>
        </div>
        <button class="btn btn-primary" type="submit">Track Order</button>
    </form>

    <?php if ($token !== '' && !$order): ?>
        <div class="notice" style="margin-top:1rem;">We could not find an order for that token. Please check the link you received after checkout.</div>
    <?php endif; ?>

    <?php if ($order): ?>
        <div class="checkout-grid" style="margin-top:1.5rem;">
            <div>
                <div class="hero-card">
                    <h3>Order #<?= (int)$order['id'] ?></h3>
                    <p>Your order is currently: <strong><?= htmlspecialchars($order['status']) ?></strong></p>
                    <p>Method: <?= htmlspecialchars(ucfirst($order['delivery_method'])) ?></p>
                    <p>Total: <?= format_money($order['total']) ?></p>
                    <p>Placed on: <?= htmlspecialchars($order['created_at']) ?></p>
                </div>
                <div class="hero-card" style="margin-top:1.5rem;">
                    <h3>Items</h3>
                    <ul style="list-style:none;display:grid;gap:0.6rem;">
                        <?php foreach ($items as $item): ?>
                            <li style="display:flex;justify-content:space-between;gap:1rem;">
                                <span><?= htmlspecialchars($item['product_name']) ?> x <?= (int)$item['quantity'] ?></span>
                                <span><?= format_money($item['line_total']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
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
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
