<?php
/**
 * SkyBook - Admin Payment Transactions (Strictly View-Only)
 */
$pageTitle = 'Financial Ledger';
require_once dirname(__DIR__) . '/includes/admin_header.php';

$db = get_db();

// Retrieve all completed transactions
$paymentsStmt = $db->query("
    SELECT p.*, b.booking_reference, b.passenger_count, b.travel_date,
           u.full_name, u.email,
           f.flight_number, f.airline
    FROM payments p
    JOIN bookings b ON p.booking_id = b.id
    JOIN users u ON b.user_id = u.id
    JOIN flights f ON b.flight_id = f.id
    ORDER BY p.paid_at DESC
");
$payments = $paymentsStmt->fetchAll();

// Calculate total gross volume
$totalVolume = 0;
foreach ($payments as $p) {
    if ($p['payment_status'] === 'completed') {
        $totalVolume += (float)$p['amount'];
    }
}
$avgTicket = count($payments) > 0 ? ($totalVolume / count($payments)) : 0;
?>

<!-- Page Header -->
<div class="admin-page-header">
    <div class="admin-page-title">
        <h1>Payment Ledger</h1>
        <p>Financial records generated upon checkout.</p>
    </div>
</div>

<!-- Ledger Stat Row -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 1.5rem;">
    <div class="stat-card card-success">
        <div class="stat-header">
            <span class="stat-label">Gross Volume</span>
            <span style="color:var(--success);">💰</span>
        </div>
        <div class="stat-value" style="color:var(--success);"><?= format_currency($totalVolume) ?></div>
        <div class="stat-sub">Total revenue from bookings</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Transactions</span>
            <span>📑</span>
        </div>
        <div class="stat-value"><?= count($payments) ?></div>
        <div class="stat-sub">Completed payments</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Average Basket</span>
            <span>📊</span>
        </div>
        <div class="stat-value"><?= format_currency($avgTicket) ?></div>
        <div class="stat-sub">Mean booking size</div>
    </div>
</div>

<!-- Toolbar -->
<div class="admin-toolbar">
    <div style="font-size:0.85rem; color:var(--text-dim);">
        <strong>Transactions:</strong> Completed payments are read-only for security purposes.
    </div>

    <div>
        <input type="text" id="admin-table-search" class="admin-search-input" placeholder="Search transaction ID or PNR...">
    </div>
</div>

<!-- Payments Ledger Table -->
<div class="form-card" style="padding:1.5rem;">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Transaction ID</th>
                    <th>Booking Ref (PNR)</th>
                    <th>Payer / Traveler</th>
                    <th>Flight</th>
                    <th>Payment Channel</th>
                    <th>Amount</th>
                    <th>Settlement Status</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="9" style="text-align:center; padding:2rem; color:var(--text-dim);">
                            No payment transactions recorded yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $p): ?>
                        <tr>
                            <td><strong style="color:var(--text-dim);">#<?= $p['id'] ?></strong></td>
                            <td>
                                <strong style="font-family:monospace; color:#fff;">
                                    <?= htmlspecialchars($p['transaction_id']) ?>
                                </strong>
                            </td>
                            <td>
                                <a href="<?= base_url('pages/review.php?booking_ref=' . urlencode($p['booking_reference'])) ?>" target="_blank" style="font-family:monospace; font-weight:700; color:var(--accent);">
                                    <?= htmlspecialchars($p['booking_reference']) ?> &nearr;
                                </a>
                            </td>
                            <td>
                                <div style="font-weight:600; color:#fff;"><?= htmlspecialchars($p['full_name']) ?></div>
                                <div style="font-size:0.75rem; color:var(--text-dim);"><?= htmlspecialchars($p['email']) ?></div>
                            </td>
                            <td><?= htmlspecialchars($p['airline']) ?> &bull; <?= htmlspecialchars($p['flight_number']) ?></td>
                            <td><?= htmlspecialchars($p['payment_method']) ?></td>
                            <td>
                                <strong style="color:var(--success); font-size:1rem;">
                                    <?= format_currency($p['amount'], $p['currency']) ?>
                                </strong>
                            </td>
                            <td>
                                <span class="status-pill status-booked">
                                    <?= ucfirst($p['payment_status']) ?>
                                </span>
                            </td>
                            <td><?= format_date($p['paid_at'], 'd M Y, H:i:s') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
