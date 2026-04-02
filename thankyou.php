<?php
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="banner">
        <h2>Thank you for your order!</h2>
        <p>We have received your request and will reach out shortly to confirm delivery details.</p>
    </div>
    <div class="hero-cta">
        <a class="btn btn-primary" href="<?= asset_url('shop.php') ?>">Continue Shopping</a>
        <a class="btn btn-secondary" href="<?= asset_url('order_status.php') ?>">Track an Order</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
