<?php
require_once __DIR__ . '/includes/header.php';
$stmt = db()->query("SELECT * FROM products WHERE is_featured = 1 ORDER BY id DESC LIMIT 4");
$featured = $stmt->fetchAll();
?>

<section class="hero">
    <div class="animate">
        <h1>Handmade Burmese desserts with golden warmth and heritage.</h1>
        <p>From coconut-scented mont to flaky pastries, each bite is a celebration of tradition, crafted fresh in Yangon.</p>
        <div class="hero-cta">
            <a class="btn btn-primary" href="<?= asset_url('shop.php') ?>">Shop Desserts</a>
            <a class="btn btn-secondary" href="<?= asset_url('about.php') ?>">Our Story</a>
        </div>
    </div>
    <div class="hero-card animate">
        <h3>Today’s pickings</h3>
        <ul>
            <li>Fresh batches every morning</li>
            <li>Natural coconut & palm sugar</li>
            <li>Gift-ready packaging</li>
        </ul>
    </div>
</section>

<section class="section">
    <div class="banner animate">
        <h2>Featured Sweets</h2>
        <p>Small-batch favorites that sell out quickly.</p>
    </div>
    <div class="product-grid">
        <?php foreach ($featured as $product): ?>
            <article class="product-card animate">
                <?php if ($product['is_featured']): ?>
                    <span class="badge">Featured</span>
                <?php endif; ?>
                <img src="<?= htmlspecialchars(product_image_url($product['image'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p><?= htmlspecialchars($product['short_description']) ?></p>
                <div class="price"><?= format_money($product['price']) ?></div>
                <form class="add-to-cart" method="post" action="<?= asset_url('actions/add_to_cart.php') ?>">
                    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>" />
                    <button class="btn btn-primary" type="submit">Add to Cart</button>
                </form>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <div class="checkout-grid">
        <div>
        <h2>Tradition with a modern glow</h2>
        <p>Our kitchen works with local farmers for jaggery, sesame, and coconut. Each dessert is handmade using recipes passed down through generations.</p>
        </div>
        <div class="hero-card">
            <h3>Visit the atelier</h3>
            <p>Stop by for warm mont let saung, fresh lotus tea, and gift boxes for loved ones.</p>
            <a class="btn btn-secondary" href="<?= asset_url('contact.php') ?>">Plan a visit</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>    
