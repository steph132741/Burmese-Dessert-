<?php
require_once __DIR__ . '/../config/bootstrap.php';

$username = 'admin';
$password = 'admin123';

$stmt = db()->prepare('SELECT id FROM admin_users WHERE username = ?');
$stmt->execute([$username]);
if ($stmt->fetch()) {
    echo 'Admin already exists.';
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);
$insert = db()->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)');
$insert->execute([$username, $hash]);

echo 'Admin created. Username: admin | Password: admin123';
