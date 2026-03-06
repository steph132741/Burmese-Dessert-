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
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['username'];
        header('Location: /burmese-desserts/admin/index.php');
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
    <link rel="stylesheet" href="/burmese-desserts/assets/css/styles.css" />
</head>
<body>
    <main class="section">
        <div class="hero-card" style="max-width:420px;margin:2rem auto;">
            <h2>Admin Login</h2>
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
                <button class="btn btn-primary" type="submit">Sign in</button>
            </form>
        </div>
    </main>
</body>
</html>
