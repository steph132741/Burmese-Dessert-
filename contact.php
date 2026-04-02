<?php
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="banner">
        <h2>Contact & Visit</h2>
        <p>We’d love to help with catering, gifting, and custom orders.</p>
    </div>
    <div class="checkout-grid">
        <div class="hero-card">
            <h3>Yangon Atelier</h3>
            <p><?= htmlspecialchars(STORE_ADDRESS) ?></p>
            <p><?= htmlspecialchars(STORE_HOURS) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars(STORE_EMAIL) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars(STORE_PHONE) ?></p>
            <p><strong>Support:</strong> Same-day order questions, event dessert planning, and wholesale tasting requests.</p>
            <div class="contact-actions">
                <a class="btn btn-primary" href="mailto:<?= htmlspecialchars(STORE_EMAIL) ?>">Email Us</a>
                <a class="btn btn-secondary" href="<?= asset_url('order_status.php') ?>">Track an Order</a>
            </div>
        </div>
        <form class="hero-card">
            <div class="form-group">
                <label for="cname">Name</label>
                <input id="cname" placeholder="Your name" />
            </div>
            <div class="form-group">
                <label for="cemail">Email</label>
                <input id="cemail" type="email" placeholder="you@email.com" />
            </div>
            <div class="form-group">
                <label for="cphone">Phone</label>
                <input id="cphone" placeholder="+95 9..." />
            </div>
            <div class="form-group">
                <label for="cmsg">Message</label>
                <textarea id="cmsg" rows="4" placeholder="Tell us about your order"></textarea>
            </div>
            <button class="btn btn-secondary" type="button" data-demo-message="Thanks! Your message has been noted. Connect this button to your preferred mail or database flow next.">Send Message</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
