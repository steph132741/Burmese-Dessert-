    </main>
    <footer class="site-footer">
        <div class="footer-inner">
            <div>
                <h4>Golden Lotus Desserts</h4>
                <p>Traditional Burmese sweets, handcrafted in small batches.</p>
            </div>
            <div>
                <h4>Visit</h4>
                <p><?= htmlspecialchars(STORE_ADDRESS) ?></p>
                <p><?= htmlspecialchars(STORE_HOURS) ?></p>
            </div>
            <div>
                <h4>Contact</h4>
                <p><?= htmlspecialchars(STORE_EMAIL) ?></p>
                <p><?= htmlspecialchars(STORE_PHONE) ?></p>
            </div>
        </div>
        <nav class="footer-links" aria-label="Footer links">
            <a class="footer-link-pill" href="<?= asset_url('privacy.php') ?>">Privacy Policy</a>
            <a class="footer-link-pill" href="<?= asset_url('contact.php') ?>">Contact</a>
        </nav>
        <p class="tiny">© <?= date('Y') ?> Golden Lotus. All rights reserved.</p>
    </footer>
    <div
        id="cookie-banner"
        class="cookie-banner"
        data-cookie-user="<?= is_user_logged_in() ? '1' : '0' ?>"
        hidden
    >
        <div class="cookie-banner-inner">
            <div class="cookie-banner-copy">
                <strong>Cookie Notice</strong>
                <p>We use cookies to improve your experience after login. Please choose whether you want to accept or decline cookies.</p>
            </div>
            <div class="cookie-banner-actions">
                <button type="button" class="btn btn-primary" data-cookie-action="accept">Accept</button>
                <button type="button" class="btn btn-secondary" data-cookie-action="decline">Decline</button>
            </div>
        </div>
    </div>
    <div id="toast" class="toast"></div>
    <script src="<?= asset_url('assets/js/app.js') ?>"></script>
</body>
</html>
