<?php
require_once __DIR__ . '/../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . asset_url('cart.php'));
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$qtys = $_POST['qty'] ?? [];
$productIds = array_map('intval', array_keys($qtys));
$stockMap = [];
if (!empty($productIds)) {
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = db()->prepare("SELECT id, stock FROM products WHERE id IN ($placeholders)");
    $stmt->execute($productIds);
    foreach ($stmt->fetchAll() as $product) {
        $stockMap[(int)$product['id']] = max(0, (int)$product['stock']);
    }
}

foreach ($qtys as $id => $qty) {
    $id = (int)$id;
    $qty = max(1, (int)$qty);
    $available = $stockMap[$id] ?? 0;
    if ($available <= 0) {
        unset($_SESSION['cart'][$id]);
        set_flash('error', 'Sorry, only 0 left in stock.');
        continue;
    }

    if ($qty > $available) {
        $_SESSION['cart'][$id] = $available;
        set_flash('error', 'Sorry, only ' . $available . ' left in stock.');
        continue;
    }

    $_SESSION['cart'][$id] = $qty;
}

if (empty($_SESSION['flash'])) {
    set_flash('success', 'Cart updated.');
}
header('Location: ' . asset_url('cart.php'));
exit;
