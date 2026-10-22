<?php
/**
 * SkyBook - Admin Passenger Manifest Directory
 */
$pageTitle = 'Passenger Manifests';
require_once dirname(__DIR__) . '/includes/admin_header.php';

$db = get_db();

// Retrieve all passengers with linked booking and flight info
$paxStmt = $db->query("
    SELECT p.*, b.booking_reference, b.status AS booking_status, b.travel_date,
           f.flight_number, f.airline, f.source_code, f.destination_code
    FROM passengers p
    JOIN bookings b ON p.booking_id = b.id
    JOIN flights f ON b.flight_id = f.id
    ORDER BY p.id DESC
");
$passengers = $paxStmt->fetchAll();
?>

<!-- Page Header -->
<div class="admin-page-header">
    <div class="admin-page-title">
        <h1>Passenger Manifest Directory</h1>
        <p>Comprehensive register of all travelers attached to active flight reservations and draft rosters.</p>
    </div>
</div>

<!-- Toolbar -->
<div class="admin-toolbar">
    <div>
        <span style="font-weight:700; color:#fff;">Total Passengers: <?= count($passengers) ?></span>
    </div>
    <div>
        <input type="text" id="admin-table-search" class="admin-search-input" placeholder="Search passenger name or passport...">
    </div>
</div>

<!-- Passenger Table -->
<div class="form-card" style="padding:1.5rem;">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Pax ID</th>
                    <th>Full Name</th>
                    <th>Booking Ref</th>
                    <th>Flight / Route</th>
                    <th>Type</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Nationality</th>
                    <th>Passport No</th>
                    <th>Passport Expiry</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($passengers)): ?>
                    <tr>
                        <td colspan="11" style="text-align:center; padding:2rem; color:var(--text-dim);">
                            No passenger records in database.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($passengers as $p): ?>
                        <tr>
                            <td><strong style="color:var(--text-dim);">#<?= $p['id'] ?></strong></td>
                            <td>
                                <strong style="color:#fff;">
                                    <?= htmlspecialchars($p['title']) ?> <?= htmlspecialchars($p['first_name']) ?> <?= htmlspecialchars($p['last_name']) ?>
                                </strong>
                            </td>
                            <td>
                                <a href="<?= base_url('pages/review.php?booking_ref=' . urlencode($p['booking_reference'])) ?>" target="_blank" style="font-family:monospace; font-weight:700;">
                                    <?= htmlspecialchars($p['booking_reference']) ?> &nearr;
                                </a>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($p['airline']) ?> &bull; <?= htmlspecialchars($p['flight_number']) ?></div>
                                <div style="font-size:0.75rem; color:var(--accent);">
                                    <?= htmlspecialchars($p['source_code']) ?> &rarr; <?= htmlspecialchars($p['destination_code']) ?>
                                </div>
                            </td>
                            <td><span class="fare-class-pill"><?= ucfirst($p['passenger_type']) ?></span></td>
                            <td><?= htmlspecialchars($p['gender']) ?></td>
                            <td><?= format_date($p['date_of_birth'], 'd M Y') ?></td>
                            <td><?= htmlspecialchars($p['nationality']) ?></td>
                            <td>
                                <span style="font-family:monospace; color:#fff; font-weight:600;"><?= htmlspecialchars($p['passport_number']) ?></span>
                            </td>
                            <td><?= format_date($p['passport_expiry'], 'd M Y') ?></td>
                            <td>
                                <span class="status-pill status-<?= $p['booking_status'] === 'booked' ? 'booked' : 'pending' ?>">
                                    <?= $p['booking_status'] === 'booked' ? 'Confirmed' : 'Draft' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
