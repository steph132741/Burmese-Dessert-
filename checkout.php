<?php
require_once __DIR__ . '/includes/header.php';
$items = get_cart_items();
$subtotal = cart_total();
$selectedMethod = 'delivery';
$fee = delivery_fee($selectedMethod, $subtotal);
$grandTotal = $subtotal + $fee;
?>

<section class="section">
    <h2>Checkout</h2>
    <?php if (empty($items)): ?>
        <div class="notice">Your cart is empty. Add items before checking out.</div>
        <a class="btn btn-primary" href="/burmese-desserts/shop.php">Go to Shop</a>
    <?php else: ?>
        <div class="checkout-grid">
            <form method="post" action="/burmese-desserts/actions/place_order.php">
                <div class="form-group">
                    <label for="name">Full name</label>
                    <input id="name" name="name" required />
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" required />
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" required />
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
                    <div class="radio-group" style="display:grid;gap:0.6rem;">
                        <label for="pickup">
                            <input id="pickup" type="radio" name="delivery_method" value="pickup" />
                            Pickup (free)
                        </label>
                        <label for="delivery">
                            <input id="delivery" type="radio" name="delivery_method" value="delivery" checked />
                            Delivery (<?= format_money(DELIVERY_FEE) ?>, free over <?= format_money(FREE_DELIVERY_THRESHOLD) ?>)
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
        <?php if (GOOGLE_MAPS_API_KEY !== ''): ?>
            <section class="section">
                <h3>Track Your Location</h3>
                <p>Allow location access to center the map near you.</p>
                <div id="map" style="width:100%;height:320px;border-radius:16px;box-shadow:0 12px 24px rgba(43,26,22,0.12);"></div>
            </section>
            <script src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars(GOOGLE_MAPS_API_KEY) ?>&callback=initMap" async defer></script>
        <?php else: ?>
            <div class="notice">Add your Google Maps API key in <code>config/bootstrap.php</code> to enable live location tracking.</div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
