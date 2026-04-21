<?php
require_once __DIR__ . '/includes/header.php';
$query = trim($_GET['q'] ?? '');
$featuredOnly = ($_GET['featured'] ?? '') === '1';
$sort = $_GET['sort'] ?? 'name';
$orderMap = [
    'name' => 'name ASC',
    'price_low' => 'price ASC',
    'price_high' => 'price DESC',
    'latest' => 'id DESC',
];
$orderBy = $orderMap[$sort] ?? $orderMap['name'];

$sql = 'SELECT * FROM products WHERE 1 = 1';
$params = [];
if ($query !== '') {
    $sql .= ' AND (name LIKE ? OR short_description LIKE ? OR description LIKE ?)';
    $searchTerm = '%' . $query . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}
if ($featuredOnly) {
    $sql .= ' AND is_featured = 1';
}
$sql .= " ORDER BY $orderBy";

$stmt = db()->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<section class="section">
    <div class="banner animate">
        <h2>Shop Burmese Desserts</h2>
        <p>Seasonal sweets, lotus tea pairings, and gift boxes.</p>
    </div>
    <form class="shop-toolbar animate" method="get">
        <div class="shop-search">
            <label for="shop-search">Search products</label>
            <input id="shop-search" type="search" name="q" value="<?= htmlspecialchars($query) ?>" placeholder="Search desserts, ingredients, or gift ideas">
        </div>
        <div class="shop-filter-row">
            <div class="shop-sort">
                <label class="sr-only" for="shop-sort">Sort products</label>
                <select id="shop-sort" name="sort">
                    <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Sort: Name</option>
                    <option value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>>Sort: Latest</option>
                    <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                </select>
            </div>
            <label class="filter-check" for="featured-only">
                <input id="featured-only" type="checkbox" name="featured" value="1" <?= $featuredOnly ? 'checked' : '' ?>>
                <span>Featured only</span>
            </label>
            <button class="btn btn-primary" type="submit">Apply</button>
            <a class="btn btn-secondary" href="<?= asset_url('shop.php') ?>">Reset</a>
        </div>
    </form>
    <div class="results-copy animate">
        <p><?= count($products) ?> dessert<?= count($products) === 1 ? '' : 's' ?> found<?= $query !== '' ? ' for "' . htmlspecialchars($query) . '"' : '' ?>.</p>
    </div>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <article class="product-card animate">
                <?php if ($product['is_featured']): ?>
                    <span class="badge">Featured</span>
                <?php endif; ?>
                <a href="<?= asset_url('product.php') ?>?id=<?= (int)$product['id'] ?>">
                    <img src="<?= htmlspecialchars(product_image_url($product['image'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
                </a>
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p><?= htmlspecialchars($product['short_description']) ?></p>
                <div class="price"><?= format_money($product['price']) ?></div>
                <p class="stock-pill <?= (int)$product['stock'] <= LOW_STOCK_THRESHOLD ? 'stock-pill-low' : '' ?> <?= (int)$product['stock'] <= 0 ? 'stock-pill-out' : '' ?>">
                    <?= htmlspecialchars(stock_label($product)) ?>
                </p>
                <?php if ((int)$product['stock'] > 0): ?>
                    <form class="add-to-cart" method="post" action="<?= asset_url('actions/add_to_cart.php') ?>">
                        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>" />
                        <button class="btn btn-primary" type="submit">Add to Cart</button>
                    </form>
                <?php else: ?>
                    <button class="btn btn-secondary" type="button" disabled>Out of Stock</button>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
    <?php if (empty($products)): ?>
        <div class="notice" style="margin-top:1.5rem;">No products matched your search. Try a different keyword or clear the filters.</div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
