<?php
/**
 * SkyBook - Admin Footer Template
 */
$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
?>
        </main>
    </div>
</div>

<!-- Mobile Bottom Navigation Bar -->
<nav class="admin-bottom-nav">
    <a href="<?= base_url('admin/index.php') ?>" class="bottom-nav-item <?= ($currentScript === 'index.php') ? 'active' : '' ?>">
        <span class="bottom-nav-icon">📊</span>
        <span class="bottom-nav-label">Dashboard</span>
    </a>
    <a href="<?= base_url('admin/flights.php') ?>" class="bottom-nav-item <?= ($currentScript === 'flights.php') ? 'active' : '' ?>">
        <span class="bottom-nav-icon">✈️</span>
        <span class="bottom-nav-label">Flights</span>
    </a>
    <a href="<?= base_url('admin/bookings.php') ?>" class="bottom-nav-item <?= ($currentScript === 'bookings.php') ? 'active' : '' ?>">
        <span class="bottom-nav-icon">📋</span>
        <span class="bottom-nav-label">Bookings</span>
    </a>
    <a href="<?= base_url('admin/passengers.php') ?>" class="bottom-nav-item <?= ($currentScript === 'passengers.php') ? 'active' : '' ?>">
        <span class="bottom-nav-icon">👥</span>
        <span class="bottom-nav-label">Manifest</span>
    </a>
    <button type="button" class="bottom-nav-item bottom-nav-menu-btn" id="admin-bottom-menu-btn" aria-label="Open Full Menu">
        <span class="bottom-nav-icon">☰</span>
        <span class="bottom-nav-label">More</span>
    </button>
</nav>

<script src="<?= asset_url('js/main.js') ?>?v=<?= filemtime(dirname(__DIR__) . '/js/main.js') ?>"></script>
<script src="<?= asset_url('js/admin.js') ?>?v=<?= filemtime(dirname(__DIR__) . '/js/admin.js') ?>"></script>
</body>
</html>
