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
                <p><a href="<?= asset_url('order_status.php') ?>">Track an order</a></p>
            </div>
        </div>
        <p class="tiny">© <?= date('Y') ?> Golden Lotus. All rights reserved.</p>
    </footer>
    <div id="toast" class="toast"></div>
    <script src="<?= asset_url('assets/js/app.js') ?>"></script>
</body>
</html>
