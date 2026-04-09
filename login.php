<?php
require_once __DIR__ . '/config/bootstrap.php';

if (is_user_logged_in()) {
    header('Location: ' . asset_url('index.php'));
    exit;
}

$redirectTarget = safe_redirect_target($_GET['redirect'] ?? ($_POST['redirect'] ?? ''));
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        login_user($user);
        set_flash('success', 'Welcome, ' . $user['name'] . '! You have successfully logged in.');
        header('Location: ' . $redirectTarget);
        exit;
    }

    $error = 'Invalid email or password.';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Login</title>
    <link rel="stylesheet" href="<?= asset_url('assets/css/styles.css') ?>" />
</head>
<body>
    <main class="section auth-shell">
        <div class="auth-card hero-card">
            <p class="admin-eyebrow">Golden Lotus</p>
            <h2>Welcome Back</h2>
            <p>Login before placing orders or sending a message to the shop.</p>
            <?php if ($error): ?>
                <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectTarget) ?>" />
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input id="email" name="email" type="email" required />
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" required />
                </div>
                <button class="btn btn-primary auth-submit" type="submit">Login</button>
            </form>
            <p class="auth-meta">Don’t have an account? <a href="<?= asset_url('register.php') ?>">Create one here</a></p>
            <a class="btn btn-secondary auth-alt" href="<?= asset_url('admin/login.php') ?>">Admin Login</a>
        </div>
    </main>
    <script src="<?= asset_url('assets/js/app.js') ?>"></script>
</body>
</html>
