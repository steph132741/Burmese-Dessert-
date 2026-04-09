<?php
require_once __DIR__ . '/../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . asset_url('contact.php'));
    exit;
}

require_user_login();

$user = current_user();
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $message === '') {
    set_flash('error', 'Please complete the message form before sending.');
    header('Location: ' . asset_url('contact.php'));
    exit;
}

$stmt = db()->prepare('INSERT INTO contact_messages (user_id, name, email, phone, message) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([
    $user['id'],
    $name,
    $email,
    $phone,
    $message,
]);

set_flash('success', 'Your message has been received. Our team will reply soon.');
header('Location: ' . asset_url('contact.php') . '?sent=1');
exit;
