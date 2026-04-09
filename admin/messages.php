<?php
require_once __DIR__ . '/auth.php';
require_admin();

$db = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id'], $_POST['status'])) {
    $update = $db->prepare('UPDATE contact_messages SET status = ? WHERE id = ?');
    $update->execute([trim($_POST['status']), (int)$_POST['message_id']]);
    header('Location: ' . asset_url('admin/messages.php'));
    exit;
}

$messages = [];
try {
    $messages = $db->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
} catch (Throwable $e) {
    $messages = [];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Customer Messages</title>
    <link rel="stylesheet" href="<?= asset_url('assets/css/styles.css') ?>" />
</head>
<body>
    <main class="section">
        <div class="banner">
            <h2>Customer Messages</h2>
            <p>Review contact form messages submitted by logged-in customers.</p>
        </div>
        <?php if (empty($messages)): ?>
            <div class="notice">No customer messages yet.</div>
        <?php else: ?>
            <div class="admin-message-list">
                <?php foreach ($messages as $message): ?>
                    <article class="hero-card">
                        <div class="section-heading">
                            <div>
                                <h3><?= htmlspecialchars($message['name']) ?></h3>
                                <p><?= htmlspecialchars($message['email']) ?><?php if (!empty($message['phone'])): ?> | <?= htmlspecialchars($message['phone']) ?><?php endif; ?></p>
                            </div>
                            <form method="post" class="admin-message-status">
                                <input type="hidden" name="message_id" value="<?= (int)$message['id'] ?>" />
                                <select name="status">
                                    <?php foreach (['New', 'Read', 'Replied'] as $status): ?>
                                        <option value="<?= $status ?>" <?= $message['status'] === $status ? 'selected' : '' ?>><?= $status ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-secondary" type="submit">Save</button>
                            </form>
                        </div>
                        <p><?= nl2br(htmlspecialchars($message['message'])) ?></p>
                        <small><?= htmlspecialchars($message['created_at']) ?></small>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <p style="margin-top:1rem;"><a href="<?= asset_url('admin/index.php') ?>">Back</a></p>
    </main>
</body>
</html>
