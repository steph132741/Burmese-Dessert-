<?php require_once __DIR__ . '/../config/bootstrap.php'; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= asset_url('assets/css/styles.css') ?>" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="site-header">
        <div class="nav-wrap">
            <a class="logo" href="<?= asset_url('index.php') ?>">
                <img class="logo-icon" src="<?= asset_url('assets/img/logo-lotus.svg') ?>" alt="Golden Lotus logo" />
                <span class="logo-text">Golden Lotus</span>
            </a>
            <button
                class="nav-toggle"
                type="button"
                aria-expanded="false"
                aria-controls="site-nav"
                aria-label="Open navigation"
            >
                <span></span>
                <span></span>
                <span></span>
            </button>
            <nav id="site-nav" class="nav-links">
                <a href="<?= asset_url('shop.php') ?>">Shop</a>
                <a href="<?= asset_url('about.php') ?>">Story</a>
                <a href="<?= asset_url('contact.php') ?>">Contact</a>
                <?php if (is_user_logged_in()): ?>
                    <a href="<?= asset_url('logout.php') ?>">Logout</a>
                <?php else: ?>
                    <a href="<?= asset_url('login.php') ?>">Login</a>
                    <a href="<?= asset_url('register.php') ?>">Register</a>
                <?php endif; ?>
                <a class="cart-pill" href="<?= asset_url('cart.php') ?>">Cart <span><?= cart_count() ?></span></a>
            </nav>
        </div>
    </header>
    <main>
        <?php if ($flash = get_flash()): ?>
            <div class="flash flash-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?> 
