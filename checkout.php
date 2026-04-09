<?php
require_once __DIR__ . '/config/bootstrap.php';
require_user_login();
$user = current_user();
$items = get_cart_items();
$subtotal = cart_total();
$selectedMethod = 'delivery';
$fee = delivery_fee($selectedMethod, $subtotal);
$grandTotal = $subtotal + $fee;
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <h2>Checkout</h2>
    <?php if (empty($items)): ?>
        <div class="notice">Your cart is empty. Add items before checking out.</div>
        <a class="btn btn-primary" href="<?= asset_url('shop.php') ?>">Go to Shop</a>
    <?php else: ?>
        <div class="checkout-grid">
            <form method="post" action="<?= asset_url('actions/place_order.php') ?>">
                <div class="form-group">
                    <label for="name">Full name</label>
                    <input id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required />
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required />
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required />
                </div>
                <div class="form-group delivery-field">
                    <label for="address">Delivery address</label>
                    <textarea id="address" name="address" rows="3" required></textarea>
                </div>
                <div class="form-group delivery-field">
                    <label for="city">City</label>
                    <input id="city" name="city" required />
                </div>
                <div class="form-group">
                    <label for="note">Order note (optional)</label>
                    <textarea id="note" name="note" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Pickup or Delivery</label>
                    <div class="radio-group delivery-options">
                        <label class="delivery-option" for="pickup">
                            <input id="pickup" type="radio" name="delivery_method" value="pickup" />
                            <span class="delivery-option-copy">
                                <strong>Pickup</strong>
                                <small>Free</small>
                            </span>
                        </label>
                        <label class="delivery-option" for="delivery">
                            <input id="delivery" type="radio" name="delivery_method" value="delivery" checked />
                            <span class="delivery-option-copy">
                                <strong>Delivery</strong>
                                <small><?= format_money(DELIVERY_FEE) ?>, free over <?= format_money(FREE_DELIVERY_THRESHOLD) ?></small>
                            </span>
                        </label>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">Place Order</button>
            </form>
            <div class="cart-summary">
                <h3>Order Summary</h3>
                <ul style="list-style:none;display:grid;gap:0.6rem;margin:1rem 0;">
                    <?php foreach ($items as $item): ?>
                        <li style="display:flex;justify-content:space-between;gap:1rem;">
                            <span><?= htmlspecialchars($item['product']['name']) ?> x <?= (int)$item['qty'] ?></span>
                            <span><?= format_money($item['line_total']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <hr style="border:none;border-top:1px solid rgba(43,26,22,0.12);margin:1rem 0;" />
                <div style="display:flex;justify-content:space-between;">
                    <span>Subtotal</span>
                    <span id="subtotal" data-value="<?= $subtotal ?>"><?= format_money($subtotal) ?></span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span>Delivery fee</span>
                    <span id="delivery-fee" data-fee="<?= DELIVERY_FEE ?>" data-threshold="<?= FREE_DELIVERY_THRESHOLD ?>" data-currency="<?= CURRENCY_SYMBOL ?>"><?= format_money($fee) ?></span>
                </div>
                <div style="display:flex;justify-content:space-between;font-weight:700;margin-top:0.6rem;">
                    <span>Total</span>
                    <span id="grand-total" data-currency="<?= CURRENCY_SYMBOL ?>"><?= format_money($grandTotal) ?></span>
                </div>
            </div>
        </div>
        <div class="notice">Delivery is confirmed manually by our team after checkout. Use a clear township, landmark, and phone number so we can contact you quickly.</div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
