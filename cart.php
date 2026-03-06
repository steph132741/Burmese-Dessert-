<?php
require_once __DIR__ . '/includes/header.php';
$items = get_cart_items();
?>

<section class="section">
    <h2>Your Cart</h2>
    <?php if (empty($items)): ?>
        <div class="notice">Your cart is empty. Browse the shop to add desserts.</div>
        <a class="btn btn-primary" href="/burmese-desserts/shop.php">Go to Shop</a>
    <?php else: ?>
        <form method="post" action="/burmese-desserts/actions/update_cart.php">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Dessert</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product']['name']) ?></td>
                            <td><?= format_money($item['product']['price']) ?></td>
                            <td>
                                <input type="number" name="qty[<?= (int)$item['product']['id'] ?>]" min="1" value="<?= (int)$item['qty'] ?>" />
                            </td>
                            <td><?= format_money($item['line_total']) ?></td>
                            <td>
                                <a href="/burmese-desserts/actions/remove_from_cart.php?id=<?= (int)$item['product']['id'] ?>">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="margin-top:1rem;display:flex;gap:1rem;align-items:center;">
                <button class="btn btn-secondary" type="submit">Update Cart</button>
                <a class="btn btn-primary" href="/burmese-desserts/checkout.php">Checkout</a>
            </div>
        </form>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
