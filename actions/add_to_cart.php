<?php
require_once __DIR__ . '/../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . asset_url('shop.php'));
    exit;
}

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$qty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;

$stmt = db()->prepare('SELECT id, name, stock FROM products WHERE id = ?');
$stmt->execute([$productId]);
$product = $stmt->fetch();
if (!$product) {
    header('Location: ' . asset_url('shop.php'));
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$available = max(0, (int)$product['stock']);
$requestedTotal = ($_SESSION['cart'][$productId] ?? 0) + $qty;
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

if ($available <= 0) {
    $message = 'Sorry, only 0 left in stock.';
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $message,
            'cart_count' => cart_count(),
        ]);
        exit;
    }
    set_flash('error', $message);
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? asset_url('shop.php')));
    exit;
}

if ($requestedTotal > $available) {
    $message = 'Sorry, only ' . $available . ' left in stock.';
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $message,
            'cart_count' => cart_count(),
        ]);
        exit;
    }
    set_flash('error', $message);
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? asset_url('shop.php')));
    exit;
}

$_SESSION['cart'][$productId] = $requestedTotal;

if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Added to cart successfully.',
        'cart_count' => cart_count(),
    ]);
    exit;
}

set_flash('success', 'Added to cart successfully.');
$back = $_SERVER['HTTP_REFERER'] ?? asset_url('shop.php');
header('Location: ' . $back);
exit;
