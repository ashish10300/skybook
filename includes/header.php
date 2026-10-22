<?php
/**
 * SkyBook - Site Global Header
 */
require_once __DIR__ . '/functions.php';

$currentUser = current_user();
$isLoggedIn = is_logged_in();
$userDraftCount = $isLoggedIn ? get_user_draft_count($currentUser['id']) : 0;
$flashes = get_flashes();

// Determine active page for navigation highlight
$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | SkyBook' : 'SkyBook — International Flight Booking System' ?></title>
    <meta name="description" content="SkyBook is a modern international flight booking platform providing seamless search, instant e-tickets, and transparent fares.">
    
    <!-- Design System Stylesheets -->
    <link rel="stylesheet" href="<?= asset_url('css/main.css') ?>">
    <?php if (!empty($extraCss)): ?>
        <?php foreach ($extraCss as $cssFile): ?>
            <link rel="stylesheet" href="<?= asset_url('css/' . $cssFile) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>

    <!-- SkyBook Aviation Preloader (Image Loading Animation) -->
    <div id="skybook-preloader" class="skybook-preloader" aria-hidden="true">
        <div class="preloader-content">
            <!-- Pulsing Aviation Radar & Airplane -->
            <div class="preloader-radar">
                <div class="radar-wave"></div>
                <div class="radar-wave delay-1"></div>
                <div class="radar-wave delay-2"></div>
                <div class="radar-plane-box">
                    <svg class="radar-plane" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.3c.4-.2.6-.6.5-1.1z"/>
                    </svg>
                </div>
            </div>

            <!-- Brand Logo -->
            <div class="preloader-brand">
                <span class="brand-text">Sky<span style="color:var(--accent);">Book</span></span>
            </div>

            <!-- Aviation Status Text -->
            <div class="preloader-status" id="preloader-status-text">Preparing Flight Corridors...</div>

            <!-- Progress Track & Percentage -->
            <div class="preloader-progress-track">
                <div class="preloader-progress-bar" id="preloader-progress-bar"></div>
            </div>
            <div class="preloader-percentage" id="preloader-percentage">0%</div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="site-header">
        <div class="container">
            <nav class="navbar">
                <!-- SkyBook Brand Logo -->
                <a href="<?= base_url('index.php') ?>" class="brand-logo" id="nav-brand-logo">
                    <img src="<?= base_url('assets/img/logo.png') ?>" alt="SkyBook Logo" style="height: 36px; width: auto; object-fit: contain;">
                    ky<span>Book</span>
                </a>

                <!-- Desktop Navigation Links -->
                <ul class="nav-links">
                    <li><a href="<?= base_url('index.php') ?>" class="nav-link <?= ($currentScript === 'index.php' || $currentScript === 'home.php') ? 'active' : '' ?>">Home</a></li>
                    <li><a href="<?= base_url('pages/explore.php') ?>" class="nav-link <?= $currentScript === 'explore.php' ? 'active' : '' ?>">Explore</a></li>
                    <li><a href="<?= base_url('pages/search.php') ?>" class="nav-link <?= in_array($currentScript, ['search.php', 'results.php']) ? 'active' : '' ?>">Flights</a></li>
                    <li><a href="<?= base_url('pages/about.php') ?>" class="nav-link <?= $currentScript === 'about.php' ? 'active' : '' ?>">About</a></li>
                    <li><a href="<?= base_url('pages/contact.php') ?>" class="nav-link <?= $currentScript === 'contact.php' ? 'active' : '' ?>">Contact</a></li>

                    <?php if ($isLoggedIn): ?>
                        <li>
                            <a href="<?= base_url('pages/drafts.php') ?>" class="nav-link <?= $currentScript === 'drafts.php' ? 'active' : '' ?>">
                                Drafts
                                <?php if ($userDraftCount > 0): ?>
                                    <span class="badge-counter" id="nav-draft-count"><?= $userDraftCount ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li><a href="<?= base_url('pages/my-bookings.php') ?>" class="nav-link <?= $currentScript === 'my-bookings.php' ? 'active' : '' ?>">My Bookings</a></li>
                    <?php endif; ?>
                </ul>

                <!-- Right Action Bar: User / Guest -->
                <div class="nav-actions">
                    <?php if (!$isLoggedIn): ?>
                        <a href="<?= base_url('pages/login.php') ?>" class="btn btn-outline btn-sm">Login</a>
                        <a href="<?= base_url('pages/register.php') ?>" class="btn btn-primary btn-sm">Register</a>
                    <?php else: ?>
                        <?php if (is_admin()): ?>
                            <a href="<?= base_url('admin/index.php') ?>" class="btn btn-secondary btn-sm" style="border-color: rgba(245, 158, 11, 0.4); color: #fbbf24;">
                                Admin Panel
                            </a>
                        <?php endif; ?>

                        <!-- User Profile Menu -->
                        <div class="user-menu">
                            <button type="button" class="user-btn" id="user-menu-btn">
                                <div class="avatar">
                                    <?= strtoupper(substr($currentUser['full_name'], 0, 1)) ?>
                                </div>
                                <span class="user-menu-btn-text" style="font-size:0.875rem; font-weight:600;">
                                    <?= htmlspecialchars(explode(' ', $currentUser['full_name'])[0]) ?>
                                </span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m6 9 6 6 6-6"/>
                                </svg>
                            </button>
                            <div class="user-dropdown">
                                <div style="padding: 8px 12px; font-size: 0.78rem; color: var(--text-dim);">
                                    Signed in as<br>
                                    <strong style="color: var(--text-main);"><?= htmlspecialchars($currentUser['email']) ?></strong>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a href="<?= base_url('pages/profile.php') ?>" class="dropdown-item">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    Profile
                                </a>
                                <a href="<?= base_url('pages/my-bookings.php') ?>" class="dropdown-item">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-2Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                                    My Bookings
                                </a>
                                <a href="<?= base_url('pages/drafts.php') ?>" class="dropdown-item">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                    Drafts (<?= $userDraftCount ?>)
                                </a>
                                <?php if (is_admin()): ?>
                                    <div class="dropdown-divider"></div>
                                    <a href="<?= base_url('admin/index.php') ?>" class="dropdown-item" style="color: #fbbf24;">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                                        Admin Dashboard
                                    </a>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <a href="<?= base_url('pages/logout.php') ?>" class="dropdown-item" style="color: #f87171;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                                    Logout
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Mobile Hamburger -->
                    <button class="nav-mobile-toggle" aria-label="Toggle navigation">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="4" x2="20" y1="12" y2="12"/>
                            <line x1="4" x2="20" y1="6" y2="6"/>
                            <line x1="4" x2="20" y1="18" y2="18"/>
                        </svg>
                    </button>
                </div>
            </nav>

            <!-- Mobile Drawer Menu -->
            <div class="nav-mobile-menu">
                <a href="<?= base_url('index.php') ?>" class="nav-link <?= ($currentScript === 'index.php') ? 'active' : '' ?>">Home</a>
                <a href="<?= base_url('pages/explore.php') ?>" class="nav-link <?= $currentScript === 'explore.php' ? 'active' : '' ?>">Explore</a>
                <a href="<?= base_url('pages/search.php') ?>" class="nav-link <?= $currentScript === 'search.php' ? 'active' : '' ?>">Flights</a>
                <a href="<?= base_url('pages/about.php') ?>" class="nav-link <?= $currentScript === 'about.php' ? 'active' : '' ?>">About</a>
                <a href="<?= base_url('pages/contact.php') ?>" class="nav-link <?= $currentScript === 'contact.php' ? 'active' : '' ?>">Contact</a>

                <?php if ($isLoggedIn): ?>
                    <a href="<?= base_url('pages/drafts.php') ?>" class="nav-link <?= $currentScript === 'drafts.php' ? 'active' : '' ?>">
                        Drafts (<?= $userDraftCount ?>)
                    </a>
                    <a href="<?= base_url('pages/my-bookings.php') ?>" class="nav-link <?= $currentScript === 'my-bookings.php' ? 'active' : '' ?>">My Bookings</a>
                    <a href="<?= base_url('pages/profile.php') ?>" class="nav-link <?= $currentScript === 'profile.php' ? 'active' : '' ?>">Profile</a>
                    <?php if (is_admin()): ?>
                        <a href="<?= base_url('admin/index.php') ?>" class="nav-link" style="color: #fbbf24;">Admin Dashboard</a>
                    <?php endif; ?>
                    <a href="<?= base_url('pages/logout.php') ?>" class="nav-link" style="color: #f87171;">Logout</a>
                <?php else: ?>
                    <div style="display:flex; gap:10px; margin-top:8px;">
                        <a href="<?= base_url('pages/login.php') ?>" class="btn btn-outline btn-block btn-sm">Login</a>
                        <a href="<?= base_url('pages/register.php') ?>" class="btn btn-primary btn-block btn-sm">Register</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Global Alert / Flash Messages -->
    <?php if (!empty($flashes)): ?>
        <div class="container alerts-wrapper">
            <?php foreach ($flashes as $flash): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                    <span><?= htmlspecialchars($flash['message']) ?></span>
                    <button type="button" class="alert-close">&times;</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <main class="site-main">
