<?php
$isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_set_cookie_params([
    'httponly' => true,
    'secure' => $isHttps,
    'samesite' => 'Lax',
]);
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

    ensure_schema($pdo);

    return $pdo;
}

function ensure_schema(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            email VARCHAR(160) NOT NULL UNIQUE,
            phone VARCHAR(50) DEFAULT '',
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            name VARCHAR(120) NOT NULL,
            email VARCHAR(160) NOT NULL,
            phone VARCHAR(50) DEFAULT '',
            message TEXT NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'New',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    ensure_column($pdo, 'orders', 'user_id', "ALTER TABLE orders ADD COLUMN user_id INT NULL AFTER id");
    ensure_column($pdo, 'orders', 'note', "ALTER TABLE orders ADD COLUMN note TEXT NULL AFTER city");
    ensure_column($pdo, 'orders', 'delivery_method', "ALTER TABLE orders ADD COLUMN delivery_method VARCHAR(20) NOT NULL DEFAULT 'pickup' AFTER note");
    ensure_column($pdo, 'orders', 'delivery_fee', "ALTER TABLE orders ADD COLUMN delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER delivery_method");
    ensure_column($pdo, 'orders', 'status', "ALTER TABLE orders ADD COLUMN status VARCHAR(40) NOT NULL DEFAULT 'Preparing' AFTER delivery_fee");
    ensure_column($pdo, 'orders', 'public_token', "ALTER TABLE orders ADD COLUMN public_token VARCHAR(40) NOT NULL DEFAULT '' AFTER status");

    ensure_column($pdo, 'order_items', 'product_name', "ALTER TABLE order_items ADD COLUMN product_name VARCHAR(120) NOT NULL DEFAULT '' AFTER product_id");
    ensure_column($pdo, 'order_items', 'unit_price', "ALTER TABLE order_items ADD COLUMN unit_price DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER product_name");
    ensure_column($pdo, 'order_items', 'line_total', "ALTER TABLE order_items ADD COLUMN line_total DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER quantity");
}

function ensure_column(PDO $pdo, string $table, string $column, string $sql): void
{
    $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
    $stmt->execute([$column]);
    if (!$stmt->fetch()) {
        $pdo->exec($sql);
    }
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

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_phone'] = $user['phone'] ?? '';
    unset($_SESSION['admin_id'], $_SESSION['admin_name']);
}

function login_admin(array $admin): void
{
    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int)$admin['id'];
    $_SESSION['admin_name'] = $admin['username'];
}

function logout_user(): void
{
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_phone']);
}

function is_user_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

function current_user(): ?array
{
    if (!is_user_logged_in()) {
        return null;
    }

    return [
        'id' => (int)$_SESSION['user_id'],
        'name' => (string)($_SESSION['user_name'] ?? ''),
        'email' => (string)($_SESSION['user_email'] ?? ''),
        'phone' => (string)($_SESSION['user_phone'] ?? ''),
    ];
}

function require_user_login(): void
{
    if (is_user_logged_in()) {
        return;
    }

    set_flash('error', 'Please log in before placing an order or sending a message.');
    $target = urlencode($_SERVER['REQUEST_URI'] ?? asset_url('index.php'));
    header('Location: ' . asset_url('login.php') . '?redirect=' . $target);
    exit;
}

function safe_redirect_target(?string $target): string
{
    $target = trim((string)$target);
    if ($target === '' || strpos($target, BASE_PATH . '/') !== 0) {
        return asset_url('index.php');
    }

    return $target;
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
