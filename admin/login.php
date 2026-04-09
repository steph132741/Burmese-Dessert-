<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: /burmese-desserts/admin/index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT * FROM admin_users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        login_admin($admin);
        header('Location: ' . asset_url('admin/index.php'));
        exit;
    }

    $error = 'Invalid username or password.';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login</title>
    <link rel="stylesheet" href="<?= asset_url('assets/css/styles.css') ?>" />
</head>
<body>
    <main class="section auth-shell auth-shell-admin">
        <div class="auth-card hero-card">
            <p class="admin-eyebrow">Admin Panel</p>
            <h2>Admin Login</h2>
            <p>Sign in to manage orders, products, and customer messages.</p>
            <?php if ($error): ?>
                <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input id="username" name="username" required />
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" required />
                </div>
                <button class="btn btn-primary auth-submit" type="submit">Login to Admin</button>
            </form>
            <a class="auth-back-link" href="<?= asset_url('login.php') ?>">Back to User Login</a>
        </div>
    </main>
    <script src="<?= asset_url('assets/js/app.js') ?>"></script>
</body>
</html>
