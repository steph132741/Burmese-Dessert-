<?php
require_once __DIR__ . '/auth.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = db()->prepare('SELECT * FROM orders WHERE id = ?');
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: /burmese-desserts/admin/orders.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['status'])) {
        $status = trim($_POST['status']);
        $update = db()->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $update->execute([$status, $id]);
    }

    if (!empty($_POST['subject']) && !empty($_POST['message'])) {
        $insert = db()->prepare('INSERT INTO order_messages (order_id, subject, message) VALUES (?, ?, ?)');
        $insert->execute([$id, trim($_POST['subject']), trim($_POST['message'])]);
    }

    header('Location: /burmese-desserts/admin/order_view.php?id=' . $id);
    exit;
}

$itemStmt = db()->prepare('SELECT * FROM order_items WHERE order_id = ?');
$itemStmt->execute([$id]);
$items = $itemStmt->fetchAll();

$msgStmt = db()->prepare('SELECT * FROM order_messages WHERE order_id = ? ORDER BY created_at DESC');
$msgStmt->execute([$id]);
$messages = $msgStmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Order #<?= (int)$order['id'] ?></title>
    <link rel="stylesheet" href="/burmese-desserts/assets/css/styles.css" />
</head>
<body>
    <main class="section">
        <div class="banner">
            <h2>Order #<?= (int)$order['id'] ?></h2>
            <p>Status: <?= htmlspecialchars($order['status']) ?></p>
            <p>Public link: <a href="/burmese-desserts/order_status.php?token=<?= htmlspecialchars($order['public_token']) ?>" target="_blank">Open customer status</a></p>
        </div>

        <div class="checkout-grid">
            <div>
                <h3>Customer</h3>
                <p><?= htmlspecialchars($order['customer_name']) ?></p>
                <p><?= htmlspecialchars($order['email']) ?></p>
                <p><?= htmlspecialchars($order['phone']) ?></p>
                <p><?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city']) ?></p>
                <p>Method: <?= htmlspecialchars($order['delivery_method']) ?></p>
                <p>Delivery fee: <?= format_money($order['delivery_fee']) ?></p>
                <p>Total: <?= format_money($order['total']) ?></p>

                <h3 style="margin-top:1rem;">Items</h3>
                <ul style="list-style:none;display:grid;gap:0.6rem;">
                    <?php foreach ($items as $item): ?>
                        <li style="display:flex;justify-content:space-between;gap:1rem;">
                            <span><?= htmlspecialchars($item['product_name']) ?> x <?= (int)$item['quantity'] ?></span>
                            <span><?= format_money($item['line_total']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div>
                <div class="hero-card">
                    <h3>Update Status</h3>
                    <form method="post">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <?php foreach (['Preparing', 'Baking', 'Ready for pickup', 'Out for delivery', 'Completed'] as $st): ?>
                                    <option value="<?= $st ?>" <?= $order['status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button class="btn btn-primary" type="submit">Save Status</button>
                    </form>
                </div>

                <div class="hero-card" style="margin-top:1.5rem;">
                    <h3>Send Message to Customer</h3>
                    <form method="post">
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input id="subject" name="subject" required />
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="4" required></textarea>
                        </div>
                        <button class="btn btn-secondary" type="submit">Send Message</button>
                    </form>
                </div>

                <?php if (!empty($messages)): ?>
                    <div class="hero-card" style="margin-top:1.5rem;">
                        <h3>Message History</h3>
                        <ul style="list-style:none;display:grid;gap:0.8rem;">
                            <?php foreach ($messages as $msg): ?>
                                <li>
                                    <strong><?= htmlspecialchars($msg['subject']) ?></strong>
                                    <p><?= htmlspecialchars($msg['message']) ?></p>
                                    <small><?= htmlspecialchars($msg['created_at']) ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <p style="margin-top:1rem;"><a href="/burmese-desserts/admin/orders.php">Back</a></p>
    </main>
</body>
</html>