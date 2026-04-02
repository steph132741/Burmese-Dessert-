<?php
session_start();

define('APP_NAME', 'Golden Lotus Desserts');

define('CURRENCY', 'MMK');

define('CURRENCY_SYMBOL', 'Ks');

define('BASE_PATH', '/burmese-desserts');

define('STORE_EMAIL', 'orders@goldenlotusdesserts.mm');
define('STORE_PHONE', '+95 9 777 880 221');
define('STORE_ADDRESS', 'No. 42 Merchant Street, Kyauktada Township, Yangon');
define('STORE_HOURS', 'Daily 9:00am - 7:30pm');

// Delivery settings
define('DELIVERY_FEE', 2000);
define('FREE_DELIVERY_THRESHOLD', 30000);

function db()
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $config = require __DIR__ . '/db.php';
    $dsn = "mysql:host={$config['host']};dbname={$config['db']};charset={$config['charset']}";

    try {
        $pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo 'Database connection failed. Please check config/db.php.';
        exit;
    }

    return $pdo;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function cart_count(): int
{
    if (empty($_SESSION['cart'])) {
        return 0;
    }
    $count = 0;
    foreach ($_SESSION['cart'] as $qty) {
        $count += (int)$qty;
    }
    return $count;
}

function format_money($amount): string
{
    return CURRENCY_SYMBOL . ' ' . number_format((float)$amount, 0);
}

function get_cart_items(): array
{
    if (empty($_SESSION['cart'])) {
        return [];
    }

    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = db()->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    $items = [];
    foreach ($products as $product) {
        $pid = $product['id'];
        $qty = (int)$_SESSION['cart'][$pid];
        $line = $qty * (float)$product['price'];
        $items[] = [
            'product' => $product,
            'qty' => $qty,
            'line_total' => $line,
        ];
    }

    return $items;
}

function cart_total(): float
{
    $total = 0;
    foreach (get_cart_items() as $item) {
        $total += $item['line_total'];
    }
    return $total;
}


function delivery_fee(string $method, float $subtotal): float
{
    if ($method !== 'delivery') {
        return 0;
    }
    if ($subtotal >= FREE_DELIVERY_THRESHOLD) {
        return 0;
    }
    return DELIVERY_FEE;
}

function asset_url(string $path): string
{
    $cleanPath = ltrim($path, '/');
    return BASE_PATH . '/' . $cleanPath;
}

function product_image_url(?string $path): string
{
    $path = trim((string)$path);
    if ($path === '') {
        return asset_url('assets/img/mont-let-saung.svg');
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    $url = asset_url($path);
    $absolutePath = dirname(__DIR__) . '/' . ltrim($path, '/');
    if (is_file($absolutePath)) {
        return $url . '?v=' . filemtime($absolutePath);
    }
    return $url;
}
