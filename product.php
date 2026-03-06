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
    <div class="checkout-grid">
        <div>
            <img style="width:100%;border-radius:20px;box-shadow:0 18px 36px rgba(43,26,22,0.12);" src="/burmese-desserts/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
        </div>
        <div>
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <p><?= htmlspecialchars($product['description']) ?></p>
            <p class="price"><?= format_money($product['price']) ?></p>
            <form class="add-to-cart" method="post" action="/burmese-desserts/actions/add_to_cart.php">
                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>" />
                <div class="form-group">
                    <label for="qty">Quantity</label>
                    <input id="qty" name="qty" type="number" min="1" value="1" />
                </div>
                <button class="btn btn-primary" type="submit">Add to Cart</button>
            </form>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
