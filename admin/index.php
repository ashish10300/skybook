<?php
/**
 * SkyBook - Admin Dashboard Console
 */
$pageTitle = 'Dashboard Overview';
require_once dirname(__DIR__) . '/includes/admin_header.php';

$db = get_db();

// Fetch KPI Metrics directly from MariaDB
try {
    // 1. Total registered users
    $totalUsers = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();

    // 2. Total flights
    $totalFlights = (int)$db->query("SELECT COUNT(*) FROM flights")->fetchColumn();
    $activeFlights = (int)$db->query("SELECT COUNT(*) FROM flights WHERE status = 'active'")->fetchColumn();

    // 3. Confirmed Bookings (status = 'booked')
    $totalConfirmedBookings = (int)$db->query("SELECT COUNT(*) FROM bookings WHERE status = 'booked'")->fetchColumn();

    // 4. Pending Bookings / Drafts (status = 'pending')
    $totalPendingBookings = (int)$db->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();

    // 5. Total Passengers recorded
    $totalPassengers = (int)$db->query("SELECT COUNT(*) FROM passengers")->fetchColumn();

    // 6. Total Revenue (sum of completed payments)
    $revenueStmt = $db->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payment_status = 'completed'");
    $totalRevenue = (float)$revenueStmt->fetchColumn();

    // 7. Recent bookings feed (pending and booked)
    $recentStmt = $db->query("
        SELECT b.*, u.full_name, u.email, f.flight_number, f.source_code, f.destination_code, f.airline
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN flights f ON b.flight_id = f.id
        ORDER BY b.created_at DESC
        LIMIT 6
    ");
    $recentBookings = $recentStmt->fetchAll();

} catch (Exception $e) {
    die("Error retrieving metrics: " . htmlspecialchars($e->getMessage()));
}
?>

<!-- Admin Page Header -->
<div class="admin-page-header">
    <div class="admin-page-title">
        <h1>Operations Command Center</h1>
        <p>Live inventory statistics and financial transactions across the SkyBook platform.</p>
    </div>

    <div style="display:flex; gap:12px;">
        <a href="<?= base_url('admin/flights.php') ?>" class="btn btn-primary btn-sm">
            + Schedule New Flight
        </a>
    </div>
</div>

<!-- KPI Stat Cards Grid -->
<div class="stats-grid">
    <!-- Stat 1: Total Revenue -->
    <div class="stat-card card-success">
        <div class="stat-header">
            <span class="stat-label">Total Revenue</span>
            <div class="stat-icon" style="color:var(--success);">💰</div>
        </div>
        <div class="stat-value" style="color:var(--success);"><?= format_currency($totalRevenue) ?></div>
        <div class="stat-sub">From completed payment transactions</div>
    </div>

    <!-- Stat 2: Confirmed Bookings -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Confirmed Bookings</span>
            <div class="stat-icon">🎟️</div>
        </div>
        <div class="stat-value"><?= number_format($totalConfirmedBookings) ?></div>
        <div class="stat-sub">Status: <code>booked</code></div>
    </div>

    <!-- Stat 3: Pending Drafts -->
    <div class="stat-card card-warning">
        <div class="stat-header">
            <span class="stat-label">Pending Drafts</span>
            <div class="stat-icon" style="color:var(--warning);">⏳</div>
        </div>
        <div class="stat-value" style="color:var(--warning);"><?= number_format($totalPendingBookings) ?></div>
        <div class="stat-sub">Status: <code>pending</code></div>
    </div>

    <!-- Stat 4: Active Flights -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Scheduled Flights</span>
            <div class="stat-icon">✈️</div>
        </div>
        <div class="stat-value"><?= number_format($totalFlights) ?></div>
        <div class="stat-sub"><?= $activeFlights ?> active in inventory</div>
    </div>

    <!-- Stat 5: Registered Users -->
    <div class="stat-card card-purple">
        <div class="stat-header">
            <span class="stat-label">Registered Users</span>
            <div class="stat-icon" style="color:#c084fc;">👤</div>
        </div>
        <div class="stat-value"><?= number_format($totalUsers) ?></div>
        <div class="stat-sub">Accounts in database</div>
    </div>

    <!-- Stat 6: Total Passengers -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Passenger Rosters</span>
            <div class="stat-icon">👥</div>
        </div>
        <div class="stat-value"><?= number_format($totalPassengers) ?></div>
        <div class="stat-sub">Individual ticket records</div>
    </div>
</div>

<!-- Recent Reservations Activity Table -->
<div class="form-card" style="padding:1.5rem; margin-top:2rem;">
    <div class="admin-card-header">
        <div>
            <h3 style="font-size:1.15rem; margin:0 0 4px 0;">Recent Reservation Transactions</h3>
            <span style="font-size:0.8rem; color:var(--text-dim);">Live record feed from the unified bookings table</span>
        </div>

        <a href="<?= base_url('admin/bookings.php') ?>" class="btn btn-outline btn-sm">
            View All Bookings &rarr;
        </a>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>PNR Ref</th>
                    <th>Traveler</th>
                    <th>Flight / Airline</th>
                    <th>Route</th>
                    <th>Date</th>
                    <th>Pax</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentBookings)): ?>
                    <tr>
                        <td colspan="9" style="text-align:center; padding:2rem; color:var(--text-dim);">
                            No reservations recorded yet in the database.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentBookings as $b): ?>
                        <tr>
                            <td>
                                <strong style="font-family:monospace; color:var(--accent);">
                                    <?= htmlspecialchars($b['booking_reference']) ?>
                                </strong>
                            </td>
                            <td>
                                <div style="font-weight:600;"><?= htmlspecialchars($b['full_name']) ?></div>
                                <div style="font-size:0.75rem; color:var(--text-dim);"><?= htmlspecialchars($b['email']) ?></div>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($b['airline']) ?></div>
                                <div style="font-size:0.75rem; color:var(--text-dim);"><?= htmlspecialchars($b['flight_number']) ?></div>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($b['source_code']) ?></strong> &rarr; <strong><?= htmlspecialchars($b['destination_code']) ?></strong>
                            </td>
                            <td><?= format_date($b['travel_date'], 'd M Y') ?></td>
                            <td><?= $b['passenger_count'] ?></td>
                            <td><strong><?= format_currency($b['total_amount']) ?></strong></td>
                            <td>
                                <span class="status-pill status-<?= $b['status'] === 'booked' ? 'booked' : 'pending' ?>">
                                    <?= $b['status'] === 'booked' ? 'Confirmed' : 'Pending Draft' ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= base_url('pages/review.php?booking_ref=' . urlencode($b['booking_reference'])) ?>" target="_blank" class="btn btn-outline btn-sm" style="padding:4px 8px; font-size:0.75rem;">
                                    Inspect &nearr;
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
