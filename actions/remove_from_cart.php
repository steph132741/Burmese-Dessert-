<?php
require_once __DIR__ . '/../config/bootstrap.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
}

set_flash('success', 'Item removed from cart.');
header('Location: /burmese-desserts/cart.php');
exit;
