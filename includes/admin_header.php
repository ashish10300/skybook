<?php
/**
 * SkyBook - Admin Navigation & Layout Header
 */
require_once __DIR__ . '/functions.php';

// Enforce admin authorization on all admin pages
require_admin();

$currentUser = current_user();
$flashes = get_flashes();
$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | SkyBook Admin' : 'SkyBook Control Center' ?></title>
    
    <!-- Base Styles & Admin Theme -->
    <link rel="stylesheet" href="<?= asset_url('css/main.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('css/admin.css') ?>">
</head>
<body>
<div class="admin-layout">
    <!-- Mobile Sidebar Backdrop Overlay -->
    <div class="admin-sidebar-overlay" id="admin-sidebar-overlay"></div>

    <!-- Admin Sidebar (Desktop Sticky + Mobile Drawer) -->
    <aside class="admin-sidebar" id="admin-sidebar">
        <div class="admin-brand">
            <div style="display:flex; align-items:center; gap:10px;">
                <img src="<?= base_url('assets/img/logo.png') ?>" alt="SkyBook Logo" style="height: 32px; width: auto; object-fit: contain;">
                <div>
                    <span style="font-family: var(--font-heading); font-weight:800; font-size:1.15rem; color:#fff;">ky<span style="color:var(--accent);">Book</span></span>
                    <span class="admin-badge">Admin</span>
                </div>
            </div>
            <!-- Mobile Close Button -->
            <button type="button" class="admin-sidebar-close" id="admin-sidebar-close" aria-label="Close Sidebar">&times;</button>
        </div>

        <ul class="admin-nav">
            <li class="admin-nav-item <?= ($currentScript === 'index.php') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/index.php') ?>">
                    <span class="admin-nav-icon">📊</span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="admin-nav-item <?= ($currentScript === 'flights.php') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/flights.php') ?>">
                    <span class="admin-nav-icon">✈️</span>
                    <span>Flight Schedule</span>
                </a>
            </li>
            <li class="admin-nav-item <?= ($currentScript === 'bookings.php') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/bookings.php') ?>">
                    <span class="admin-nav-icon">📋</span>
                    <span>All Bookings</span>
                </a>
            </li>
            <li class="admin-nav-item <?= ($currentScript === 'passengers.php') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/passengers.php') ?>">
                    <span class="admin-nav-icon">👥</span>
                    <span>Passengers</span>
                </a>
            </li>
            <li class="admin-nav-item <?= ($currentScript === 'users.php') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/users.php') ?>">
                    <span class="admin-nav-icon">👤</span>
                    <span>Registered Users</span>
                </a>
            </li>
            <li class="admin-nav-item <?= ($currentScript === 'payments.php') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/payments.php') ?>">
                    <span class="admin-nav-icon">💳</span>
                    <span>Payment Ledger</span>
                </a>
            </li>

            <li style="margin-top: 1.5rem; padding: 0 12px; font-size: 0.72rem; text-transform: uppercase; color: var(--text-dim); font-weight:700;">
                Portals
            </li>
            <li class="admin-nav-item">
                <a href="<?= base_url('index.php') ?>" target="_blank">
                    <span class="admin-nav-icon">🌐</span>
                    <span>View Public Site &nearr;</span>
                </a>
            </li>
        </ul>

        <div class="admin-sidebar-footer">
            <div class="admin-user-info">
                <div class="avatar" style="background: linear-gradient(135deg, #f59e0b, #ef4444);">
                    A
                </div>
                <div>
                    <div class="admin-user-name"><?= htmlspecialchars(explode(' ', $currentUser['full_name'])[0]) ?></div>
                    <div class="admin-user-role">Administrator</div>
                </div>
            </div>
            <a href="<?= base_url('admin/logout.php') ?>" title="Logout" style="color: #f87171; font-size: 1.1rem; display: flex;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
            </a>
        </div>
    </aside>

    <!-- Admin Main Body -->
    <div class="admin-main">
        <header class="admin-topbar">
            <div class="admin-topbar-left">
                <!-- Hamburger Button (Mobile & Tablet) -->
                <button type="button" class="admin-mobile-toggle" id="admin-mobile-toggle" aria-label="Open Navigation">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <div class="admin-topbar-title-wrap">
                    <span class="admin-topbar-brand-mobile">
                        <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" style="height:22px; width:auto;">
                        <span>ky<span style="color:var(--accent);">Book</span></span>
                        <span class="admin-badge">Admin</span>
                    </span>
                    <span class="admin-topbar-title">SkyBook Admin Console</span>
                </div>
            </div>

            <div class="admin-topbar-actions">
                <span class="status-pill status-booked">
                    <span class="live-pulse"></span>
                    <span class="status-pill-text">Live</span>
                </span>
                <a href="<?= base_url('index.php') ?>" class="btn btn-outline btn-sm topbar-site-link" target="_blank" title="View Public Website">
                    <span>Site &nearr;</span>
                </a>
                <a href="<?= base_url('admin/logout.php') ?>" class="btn btn-outline btn-sm topbar-logout-btn" title="Logout">
                    Logout
                </a>
            </div>
        </header>

        <!-- Admin Alerts -->
        <?php if (!empty($flashes)): ?>
            <div style="padding: 1.25rem 1.5rem 0;">
                <?php foreach ($flashes as $flash): ?>
                    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                        <span><?= htmlspecialchars($flash['message']) ?></span>
                        <button type="button" class="alert-close">&times;</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <main class="admin-content">
