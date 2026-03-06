<?php
require_once __DIR__ . '/../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /burmese-desserts/cart.php');
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$qtys = $_POST['qty'] ?? [];
foreach ($qtys as $id => $qty) {
    $id = (int)$id;
    $qty = max(1, (int)$qty);
    $_SESSION['cart'][$id] = $qty;
}

set_flash('success', 'Cart updated.');
header('Location: /burmese-desserts/cart.php');
exit;
