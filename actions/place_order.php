<?php
require_once __DIR__ . '/../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /burmese-desserts/checkout.php');
    exit;
}

$items = get_cart_items();
if (empty($items)) {
    header('Location: /burmese-desserts/shop.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');
$note = trim($_POST['note'] ?? '');

$subtotal = cart_total();
$deliveryMethod = $_POST['delivery_method'] ?? 'pickup';
$deliveryMethod = $deliveryMethod === 'delivery' ? 'delivery' : 'pickup';
$requiresAddress = $deliveryMethod === 'delivery';
if ($name === '' || $email === '' || $phone === '' || ($requiresAddress && ($address === '' || $city === ''))) {
    header('Location: /burmese-desserts/checkout.php');
    exit;
}
$deliveryFee = delivery_fee($deliveryMethod, $subtotal);
$total = $subtotal + $deliveryFee;
$publicToken = bin2hex(random_bytes(8));
$status = 'Preparing';

$db = db();
$db->beginTransaction();

try {
    $stmt = $db->prepare('INSERT INTO orders (customer_name, email, phone, address, city, note, delivery_method, delivery_fee, status, public_token, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$name, $email, $phone, $address, $city, $note, $deliveryMethod, $deliveryFee, $status, $publicToken, $total]);
    $orderId = (int)$db->lastInsertId();

    $itemStmt = $db->prepare('INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity, line_total) VALUES (?, ?, ?, ?, ?, ?)');

    foreach ($items as $item) {
        $product = $item['product'];
        $itemStmt->execute([
            $orderId,
            $product['id'],
            $product['name'],
            $product['price'],
            $item['qty'],
            $item['line_total'],
        ]);
    }

    $db->commit();
    $_SESSION['cart'] = [];
    set_flash('success', 'Order placed. Save your tracking token: ' . $publicToken);
    header('Location: /burmese-desserts/order_status.php?token=' . $publicToken . '&success=1');
    exit;
} catch (Throwable $e) {
    $db->rollBack();
    header('Location: /burmese-desserts/checkout.php');
    exit;
}
