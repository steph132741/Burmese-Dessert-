<?php
require_once __DIR__ . '/includes/header.php';
$user = current_user();
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
                <?php if (!is_user_logged_in()): ?>
                    <a class="btn btn-secondary" href="<?= asset_url('login.php') ?>">Login to Message</a>
                <?php endif; ?>
            </div>
        </div>
        <?php if (is_user_logged_in()): ?>
            <form class="hero-card" method="post" action="<?= asset_url('actions/contact_submit.php') ?>">
                <div class="form-group">
                    <label for="cname">Name</label>
                    <input id="cname" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required />
                </div>
                <div class="form-group">
                    <label for="cemail">Email</label>
                    <input id="cemail" name="email" type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required />
                </div>
                <div class="form-group">
                    <label for="cphone">Phone</label>
                    <input id="cphone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" />
                </div>
                <div class="form-group">
                    <label for="cmsg">Message</label>
                    <textarea id="cmsg" name="message" rows="4" placeholder="Tell us about your order" required></textarea>
                </div>
                <button class="btn btn-secondary" type="submit">Send Message</button>
            </form>
        <?php else: ?>
            <div class="hero-card">
                <h3>Login Required</h3>
                <p>Please log in before sending a message so our admin team can track and reply properly.</p>
                <a class="btn btn-primary" href="<?= asset_url('login.php') ?>">Go to Login</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
