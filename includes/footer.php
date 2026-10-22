<?php
/**
 * SkyBook - Site Global Footer
 */
?>
    </main>

    <!-- Main Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Brand Information -->
                <div class="footer-col footer-brand">
                    <a href="<?= base_url('index.php') ?>" class="brand-logo">
                        <img src="<?= base_url('assets/img/logo.png') ?>" alt="SkyBook Logo" style="height: 36px; width: auto; object-fit: contain;">
                        Sky<span>Book</span>
                    </a>
                    <p>
                        Elevating global travel. Book seamless flights across the world's top destinations with SkyBook.
                    </p>
                </div>

                <!-- Navigation Links -->
                <div class="footer-col">
                    <h4>Navigation</h4>
                    <ul class="footer-links">
                        <li><a href="<?= base_url('index.php') ?>">Home</a></li>
                        <li><a href="<?= base_url('pages/explore.php') ?>">Explore World</a></li>
                        <li><a href="<?= base_url('pages/search.php') ?>">Search Flights</a></li>
                        <li><a href="<?= base_url('pages/about.php') ?>">About Platform</a></li>
                        <li><a href="<?= base_url('pages/contact.php') ?>">Customer Support</a></li>
                    </ul>
                </div>

                <!-- Traveler Services -->
                <div class="footer-col">
                    <h4>Traveler Area</h4>
                    <ul class="footer-links">
                        <?php if (is_logged_in()): ?>
                            <li><a href="<?= base_url('pages/drafts.php') ?>">Saved Drafts</a></li>
                            <li><a href="<?= base_url('pages/my-bookings.php') ?>">Confirmed Bookings</a></li>
                            <li><a href="<?= base_url('pages/profile.php') ?>">Account Profile</a></li>
                        <?php else: ?>
                            <li><a href="<?= base_url('pages/login.php') ?>">Sign In</a></li>
                            <li><a href="<?= base_url('pages/register.php') ?>">Register Account</a></li>
                        <?php endif; ?>
                        <li><a href="<?= base_url('admin/login.php') ?>">Admin Portal</a></li>
                    </ul>
                </div>

                <!-- Global Hubs -->
                <div class="footer-col">
                    <h4>Global Hubs</h4>
                    <ul class="footer-links">
                        <li><a href="<?= base_url('pages/results.php?source=JFK&destination=LHR') ?>">New York → London</a></li>
                        <li><a href="<?= base_url('pages/results.php?source=LHR&destination=DXB') ?>">London → Dubai</a></li>
                        <li><a href="<?= base_url('pages/results.php?source=DXB&destination=SIN') ?>">Dubai → Singapore</a></li>
                        <li><a href="<?= base_url('pages/results.php?source=SIN&destination=HND') ?>">Singapore → Tokyo</a></li>
                        <li><a href="<?= base_url('pages/results.php?source=BOM&destination=DXB') ?>">Mumbai → Dubai</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div>
                    &copy; <?= date('Y') ?> SkyBook Aviation Systems. All rights reserved.
                </div>
                <div style="color: var(--text-dim); font-size: 0.85rem; display: flex; gap: 16px;">
                    <a href="#" style="color: var(--text-dim);">Privacy Policy</a>
                    <a href="#" style="color: var(--text-dim);">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Global JavaScript -->
    <script src="<?= asset_url('js/main.js') ?>"></script>
    <?php if (!empty($extraJs)): ?>
        <?php foreach ($extraJs as $jsFile): ?>
            <script src="<?= asset_url('js/' . $jsFile) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
