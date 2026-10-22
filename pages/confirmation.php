<?php
/**
 * SkyBook - Booking Confirmation
 */
$pageTitle = 'Booking Confirmed';
require_once dirname(__DIR__) . '/includes/functions.php';

// Must be logged in
require_login();

$db = get_db();
$currentUser = current_user();
$bookingRef = trim($_GET['booking_ref'] ?? '');

if (empty($bookingRef)) {
    set_flash('error', 'No booking reference provided.');
    header('Location: ' . base_url('pages/my-bookings.php'));
    exit;
}

// Fetch booking & payment details
$bookingStmt = $db->prepare("
    SELECT b.*, f.flight_number, f.airline, f.source, f.source_code, f.destination, f.destination_code, 
           f.departure_time, f.arrival_time, f.duration,
           p.transaction_id, p.payment_method, p.paid_at
    FROM bookings b
    JOIN flights f ON b.flight_id = f.id
    LEFT JOIN payments p ON b.id = p.booking_id
    WHERE b.booking_reference = ? AND (b.user_id = ? OR ? = 'admin')
    LIMIT 1
");
$bookingStmt->execute([$bookingRef, $currentUser['id'], $currentUser['role']]);
$booking = $bookingStmt->fetch();

if (!$booking) {
    set_flash('error', 'Booking record not found.');
    header('Location: ' . base_url('pages/my-bookings.php'));
    exit;
}

// Fetch passengers
$paxStmt = $db->prepare("SELECT * FROM passengers WHERE booking_id = ? ORDER BY id ASC");
$paxStmt->execute([$booking['id']]);
$passengers = $paxStmt->fetchAll();
$leadPax = $passengers[0] ?? null;
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    <!-- Confirmation Banner -->
    <div class="confirm-wrapper" style="text-align: center; margin-bottom: 3rem;">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--success); margin: 0 auto 1.25rem;">
            &check;
        </div>
        <span class="status-pill status-booked" style="margin-bottom: 10px;">Payment Verified &bull; Confirmed</span>
        <h1 style="font-size: 2.25rem; margin-bottom: 8px;">Your Flight Is Officially Booked!</h1>
        <p class="lead" style="font-size: 1rem;">
            A confirmation has been recorded in the database. Your virtual boarding pass is ready for check-in and travel.
        </p>
    </div>

    <!-- Confirmation Card -->
    <div class="confirm-wrapper">
        <div class="form-card" style="padding: 2.5rem;">
            <!-- Header Reference Box -->
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1.5rem; margin-bottom: 1.75rem; flex-wrap:wrap; gap:16px;">
                <div>
                    <span style="font-size:0.75rem; text-transform:uppercase; color:var(--text-dim); letter-spacing:0.06em; font-weight:700;">Booking Reference (PNR)</span>
                    <div style="font-family:monospace; font-size:2rem; font-weight:800; color:var(--accent);">
                        <?= htmlspecialchars($booking['booking_reference']) ?>
                    </div>
                </div>

                <div style="text-align:right;">
                    <span style="font-size:0.75rem; text-transform:uppercase; color:var(--text-dim); letter-spacing:0.06em; font-weight:700;">Transaction ID</span>
                    <div style="font-family:monospace; font-size:1.1rem; color:#fff;">
                        <?= htmlspecialchars($booking['transaction_id'] ?? 'TXN-SIMULATED') ?>
                    </div>
                    <span style="font-size:0.8rem; color:var(--text-dim);">Method: <?= htmlspecialchars($booking['payment_method'] ?? 'Demo Card') ?></span>
                </div>
            </div>

            <!-- Flight Segment -->
            <div style="background:var(--bg-input); border:1px solid var(--border-subtle); border-radius:var(--radius-md); padding: 1.5rem; margin-bottom: 1.75rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                    <div>
                        <strong style="color:#fff; font-size:1.15rem;"><?= htmlspecialchars($booking['airline']) ?></strong>
                        <span style="color:var(--text-dim); font-size:0.9rem;"> &bull; Flight <?= htmlspecialchars($booking['flight_number']) ?></span>
                    </div>
                    <span class="fare-class-pill"><?= htmlspecialchars($booking['travel_class']) ?> Cabin</span>
                </div>

                <div class="itinerary-route-grid">
                    <div>
                        <div style="font-size:1.75rem; font-weight:800; color:#fff;"><?= date('H:i', strtotime($booking['departure_time'])) ?></div>
                        <div style="font-weight:700; color:var(--accent);"><?= htmlspecialchars($booking['source_code']) ?></div>
                        <div style="font-size:0.85rem; color:var(--text-muted);"><?= htmlspecialchars($booking['source']) ?></div>
                        <div style="font-size:0.8rem; color:var(--text-dim); margin-top:2px;"><?= format_date($booking['travel_date']) ?></div>
                    </div>

                    <div style="text-align:center;">
                        <span style="font-size:0.75rem; color:var(--text-dim);"><?= htmlspecialchars($booking['duration']) ?></span>
                        <div class="route-line" style="width:120px;">
                            <span class="route-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="transform: rotate(90deg); color: var(--text-dim);"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.3c.4-.2.6-.6.5-1.1z"/></svg>
                            </span>
                        </div>
                    </div>

                    <div style="text-align:right;">
                        <div style="font-size:1.75rem; font-weight:800; color:#fff;"><?= date('H:i', strtotime($booking['arrival_time'])) ?></div>
                        <div style="font-weight:700; color:var(--accent);"><?= htmlspecialchars($booking['destination_code']) ?></div>
                        <div style="font-size:0.85rem; color:var(--text-muted);"><?= htmlspecialchars($booking['destination']) ?></div>
                        <div style="font-size:0.8rem; color:var(--text-dim); margin-top:2px;"><?= format_date($booking['travel_date']) ?></div>
                    </div>
                </div>
            </div>

            <!-- Passengers Summary -->
            <div style="margin-bottom: 1.75rem;">
                <h4 style="font-size:0.95rem; text-transform:uppercase; color:var(--text-dim); margin-bottom:10px;">Travelers Booked (<?= count($passengers) ?>)</h4>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <?php foreach ($passengers as $pax): ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 14px; background:var(--bg-input); border-radius:var(--radius-sm); font-size:0.9rem;">
                            <span>
                                <strong><?= htmlspecialchars($pax['title']) ?> <?= htmlspecialchars($pax['first_name']) ?> <?= htmlspecialchars($pax['last_name']) ?></strong>
                                <span style="color:var(--text-dim); font-size:0.8rem;">(<?= htmlspecialchars($pax['nationality']) ?>)</span>
                            </span>
                            <span style="font-family:monospace; color:var(--accent); font-size:0.85rem;">
                                Passport: <?= htmlspecialchars($pax['passport_number']) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Financial Total -->
            <div style="display:flex; justify-content:space-between; align-items:center; padding:1rem 0; border-top:1px solid var(--border-subtle); margin-bottom:2rem;">
                <span style="font-size:1.1rem; font-weight:600; color:var(--text-muted);">Total Paid</span>
                <span style="font-size:1.75rem; font-weight:800; color:var(--success);">
                    <?= format_currency($booking['total_amount']) ?>
                </span>
            </div>

            <!-- Action Buttons -->
            <div style="display:flex; gap:14px; flex-wrap:wrap;">
                <a href="<?= base_url('pages/ticket.php?booking_ref=' . urlencode($booking['booking_reference'])) ?>" class="btn btn-primary btn-lg" style="flex:1;">
                    🎟️ View Virtual Boarding Pass / E-Ticket &rarr;
                </a>
                <a href="<?= base_url('pages/my-bookings.php') ?>" class="btn btn-secondary btn-lg">
                    Go to My Bookings
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
