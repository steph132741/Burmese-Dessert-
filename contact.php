<?php
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="banner">
        <h2>Contact & Visit</h2>
        <p>We’d love to help with catering, gifting, and custom orders.</p>
    </div>
    <div class="checkout-grid">
        <div>
            <h3>Yangon Atelier</h3>
            <p>No. 88 Shwe Dagon Rd, Yangon</p>
            <p>Daily 9:00am - 8:00pm</p>
            <p>hello@goldenlotus.mm</p>
            <p>+95 9 700 123 456</p>
        </div>
        <form>
            <div class="form-group">
                <label for="cname">Name</label>
                <input id="cname" placeholder="Your name" />
            </div>
            <div class="form-group">
                <label for="cemail">Email</label>
                <input id="cemail" type="email" placeholder="you@email.com" />
            </div>
            <div class="form-group">
                <label for="cmsg">Message</label>
                <textarea id="cmsg" rows="4" placeholder="Tell us about your order"></textarea>
            </div>
            <button class="btn btn-secondary" type="button">Send Message</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
