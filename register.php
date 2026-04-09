<?php
require_once __DIR__ . '/config/bootstrap.php';

if (is_user_logged_in()) {
    header('Location: ' . asset_url('index.php'));
    exit;
}

$redirectTarget = safe_redirect_target($_GET['redirect'] ?? ($_POST['redirect'] ?? ''));
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = db()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'That email is already registered.';
        } else {
            $insert = db()->prepare('INSERT INTO users (name, email, phone, password_hash) VALUES (?, ?, ?, ?)');
            $insert->execute([$name, $email, $phone, password_hash($password, PASSWORD_DEFAULT)]);

            $userId = (int)db()->lastInsertId();
            login_user([
                'id' => $userId,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
            ]);
            set_flash('success', 'Welcome, ' . $name . '! Your account has been created successfully.');
            header('Location: ' . $redirectTarget);
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Create Account</title>
    <link rel="stylesheet" href="<?= asset_url('assets/css/styles.css') ?>" />
</head>
<body>
    <main class="section auth-shell">
        <div class="auth-card hero-card">
            <p class="admin-eyebrow">Golden Lotus</p>
            <h2>Create Account</h2>
            <p>Register to order desserts and send messages to the shop team.</p>
            <?php if ($error): ?>
                <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectTarget) ?>" />
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input id="name" name="name" required />
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input id="email" name="email" type="email" required />
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" />
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" required />
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input id="confirm_password" name="confirm_password" type="password" required />
                </div>
                <button class="btn btn-primary auth-submit" type="submit">Register</button>
            </form>
            <p class="auth-meta">Already have an account? <a href="<?= asset_url('login.php') ?>">Login here</a></p>
        </div>
    </main>
    <script src="<?= asset_url('assets/js/app.js') ?>"></script>
</body>
</html>
