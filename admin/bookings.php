<?php
/**
 * SkyBook - Admin Booking Ledger (Pending & Booked)
 */
$pageTitle = 'All Reservations & Drafts';
require_once dirname(__DIR__) . '/includes/admin_header.php';

$db = get_db();

$statusFilter = trim($_GET['status'] ?? 'all');

$sql = "
    SELECT b.*, u.full_name, u.email, u.phone,
           f.flight_number, f.airline, f.source, f.source_code, f.destination, f.destination_code,
           f.departure_time, f.arrival_time
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN flights f ON b.flight_id = f.id
";

if ($statusFilter === 'booked' || $statusFilter === 'pending') {
    $sql .= " WHERE b.status = :status";
    $stmt = $db->prepare($sql . " ORDER BY b.created_at DESC");
    $stmt->execute([':status' => $statusFilter]);
} else {
    $stmt = $db->query($sql . " ORDER BY b.created_at DESC");
}

$bookings = $stmt->fetchAll();
?>

<!-- Page Header -->
<div class="admin-page-header">
    <div class="admin-page-title">
        <h1>Flight Reservations Ledger</h1>
        <p>Comprehensive overview of all bookings in the system, encompassing completed tickets and saved drafts.</p>
    </div>
</div>

<!-- Toolbar & Status Filter Tabs -->
<div class="admin-toolbar">
    <div style="display:flex; gap:8px;">
        <a href="<?= base_url('admin/bookings.php') ?>" class="btn btn-sm <?= ($statusFilter === 'all') ? 'btn-primary' : 'btn-outline' ?>">
            All (<?= count($bookings) ?>)
        </a>
        <a href="<?= base_url('admin/bookings.php?status=booked') ?>" class="btn btn-sm <?= ($statusFilter === 'booked') ? 'btn-primary' : 'btn-outline' ?>">
            Confirmed Only
        </a>
        <a href="<?= base_url('admin/bookings.php?status=pending') ?>" class="btn btn-sm <?= ($statusFilter === 'pending') ? 'btn-primary' : 'btn-outline' ?>">
            Pending Drafts Only
        </a>
    </div>

    <div>
        <input type="text" id="admin-table-search" class="admin-search-input" placeholder="Search reference, traveler, or route...">
    </div>
</div>

<!-- Bookings Table -->
<div class="form-card" style="padding:1.5rem;">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Booking Ref</th>
                    <th>Traveler</th>
                    <th>Flight / Airline</th>
                    <th>Route</th>
                    <th>Travel Date</th>
                    <th>Cabin</th>
                    <th>Pax</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="11" style="text-align:center; padding:2rem; color:var(--text-dim);">
                            No reservations found matching the selected filter.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td>
                                <strong style="font-family:monospace; color:var(--accent);">
                                    <?= htmlspecialchars($b['booking_reference']) ?>
                                </strong>
                            </td>
                            <td>
                                <div style="font-weight:600; color:#fff;"><?= htmlspecialchars($b['full_name']) ?></div>
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
                            <td><span class="fare-class-pill"><?= htmlspecialchars($b['travel_class']) ?></span></td>
                            <td><?= $b['passenger_count'] ?></td>
                            <td><strong style="color:#fff;"><?= format_currency($b['total_amount']) ?></strong></td>
                            <td>
                                <span class="status-pill status-<?= $b['status'] === 'booked' ? 'booked' : 'pending' ?>">
                                    <?= $b['status'] === 'booked' ? 'Confirmed' : 'Pending Draft' ?>
                                </span>
                            </td>
                            <td><?= format_date($b['created_at'], 'd M Y, H:i') ?></td>
                            <td>
                                <a href="<?= base_url('pages/review.php?booking_ref=' . urlencode($b['booking_reference'])) ?>" target="_blank" class="btn btn-outline btn-sm" style="padding:4px 8px; font-size:0.75rem;">
                                    View &nearr;
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
