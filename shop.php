<?php
require_once __DIR__ . '/includes/header.php';
$stmt = db()->query("SELECT * FROM products ORDER BY name ASC");
$products = $stmt->fetchAll();
?>

<section class="section">
    <div class="banner animate">
        <h2>Shop Burmese Desserts</h2>
        <p>Seasonal sweets, lotus tea pairings, and gift boxes.</p>
    </div>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <article class="product-card animate">
                <?php if ($product['is_featured']): ?>
                    <span class="badge">Featured</span>
                <?php endif; ?>
                <a href="/burmese-desserts/product.php?id=<?= (int)$product['id'] ?>">
                    <img src="/burmese-desserts/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
                </a>
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p><?= htmlspecialchars($product['short_description']) ?></p>
                <div class="price"><?= format_money($product['price']) ?></div>
                <form class="add-to-cart" method="post" action="/burmese-desserts/actions/add_to_cart.php">
                    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>" />
                    <button class="btn btn-primary" type="submit">Add to Cart</button>
                </form>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
