<?php
require_once __DIR__ . '/../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . asset_url('checkout.php'));
    exit;
}

require_user_login();
$user = current_user();

$items = get_cart_items();
if (empty($items)) {
    header('Location: ' . asset_url('shop.php'));
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
    set_flash('error', 'Please complete the checkout form before placing your order.');
    header('Location: ' . asset_url('checkout.php'));
    exit;
}
$deliveryFee = delivery_fee($deliveryMethod, $subtotal);
$total = $subtotal + $deliveryFee;
$publicToken = bin2hex(random_bytes(8));
$status = 'Preparing';

$db = db();
$db->beginTransaction();

try {
    $stmt = $db->prepare('INSERT INTO orders (user_id, customer_name, email, phone, address, city, note, delivery_method, delivery_fee, status, public_token, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$user['id'], $name, $email, $phone, $address, $city, $note, $deliveryMethod, $deliveryFee, $status, $publicToken, $total]);
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
    set_flash('success', 'Your order has been received and is now being prepared.');
    header('Location: ' . asset_url('order_status.php') . '?token=' . $publicToken . '&success=1');
    exit;
} catch (Throwable $e) {
    $db->rollBack();
    set_flash('error', 'We could not save your order. Please try again.');
    header('Location: ' . asset_url('checkout.php'));
    exit;
}
