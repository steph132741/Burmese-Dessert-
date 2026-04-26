<?php
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = db()->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo '<section class="section"><p>Product not found.</p></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>

<section class="section">
    <div class="checkout-grid product-detail">
        <div class="product-detail-media">
            <img
                class="product-detail-image"
                src="<?= htmlspecialchars(product_image_url($product['image'])) ?>"
                alt="<?= htmlspecialchars($product['name']) ?>"
            />
        </div>
        <div class="product-detail-content">
            <h2 class="product-detail-title"><?= htmlspecialchars($product['name']) ?></h2>
            <p class="product-detail-copy"><?= htmlspecialchars($product['description']) ?></p>
            <p class="price"><?= format_money($product['price']) ?></p>
            <p class="stock-pill <?= (int)$product['stock'] <= LOW_STOCK_THRESHOLD ? 'stock-pill-low' : '' ?> <?= (int)$product['stock'] <= 0 ? 'stock-pill-out' : '' ?>">
                <?= htmlspecialchars(stock_label($product)) ?>
            </p>
            <?php if ((int)$product['stock'] > 0): ?>
                <form class="add-to-cart product-detail-form" method="post" action="<?= asset_url('actions/add_to_cart.php') ?>">
                    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>" />
                    <div class="form-group">
                        <label for="qty">Quantity</label>
                        <input id="qty" name="qty" type="number" min="1" max="<?= (int)$product['stock'] ?>" value="1" />
                    </div>
                    <button class="btn btn-primary" type="submit">Add to Cart</button>
                </form>
            <?php else: ?>
                <div class="notice">This dessert is currently out of stock.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
