<?php
/**
 * SkyBook - Booking Review & Draft Inspection
 */
$pageTitle = 'Review Flight Booking';
require_once dirname(__DIR__) . '/includes/functions.php';

// Must be logged in
require_login();

$db = get_db();
$currentUser = current_user();
$bookingRef = trim($_GET['booking_ref'] ?? '');

if (empty($bookingRef)) {
    set_flash('error', 'No booking reference specified.');
    header('Location: ' . base_url('pages/drafts.php'));
    exit;
}

// Fetch booking with strict ownership check
$bookingStmt = $db->prepare("
    SELECT b.*, f.flight_number, f.airline, f.source, f.source_code, f.destination, f.destination_code, 
           f.departure_time, f.arrival_time, f.duration, f.price AS current_flight_price, f.available_seats, f.status AS flight_status
    FROM bookings b
    JOIN flights f ON b.flight_id = f.id
    WHERE b.booking_reference = ? AND (b.user_id = ? OR ? = 'admin')
    LIMIT 1
");
$bookingStmt->execute([$bookingRef, $currentUser['id'], $currentUser['role']]);
$booking = $bookingStmt->fetch();

if (!$booking) {
    set_flash('error', 'Booking record not found or access denied.');
    header('Location: ' . base_url('pages/drafts.php'));
    exit;
}

// Check flight availability and price changes (Section 20: Draft Validation)
$priceNotice = '';
if ($booking['flight_status'] !== 'active') {
    set_flash('error', 'Notice: This flight has been delayed or cancelled by the airline. Please select another flight.');
} elseif ($booking['available_seats'] < $booking['passenger_count']) {
    set_flash('error', 'Warning: Remaining available seats on this flight are less than your passenger count.');
}

// Verify price sync
$unitPrice = (float)$booking['current_flight_price'];
$expectedTotals = calculate_booking_totals($unitPrice, $booking['passenger_count']);
if (abs($booking['total_amount'] - $expectedTotals['total_amount']) > 0.01) {
    // Flight price updated since draft was created
    $priceNotice = "Flight fare was adjusted by airline from " . format_currency($booking['total_amount']) . " to " . format_currency($expectedTotals['total_amount']) . ". Your draft has been updated to reflect current database pricing.";
    $updateDraft = $db->prepare("UPDATE bookings SET base_amount = ?, taxes = ?, total_amount = ? WHERE id = ?");
    $updateDraft->execute([$expectedTotals['base_amount'], $expectedTotals['taxes'], $expectedTotals['total_amount'], $booking['id']]);
    $booking['base_amount'] = $expectedTotals['base_amount'];
    $booking['taxes'] = $expectedTotals['taxes'];
    $booking['total_amount'] = $expectedTotals['total_amount'];
}

// Fetch passengers
$paxStmt = $db->prepare("SELECT * FROM passengers WHERE booking_id = ? ORDER BY id ASC");
$paxStmt->execute([$booking['id']]);
$passengers = $paxStmt->fetchAll();
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="container" style="padding-top: 2rem; padding-bottom: 5rem;">
    <!-- Booking Stepper -->
    <div class="booking-stepper">
        <div class="step-item done">
            <span class="step-num">&check;</span>
            <span>Flight Selected</span>
        </div>
        <div class="step-divider"></div>
        <div class="step-item done">
            <span class="step-num">&check;</span>
            <span>Passenger Details</span>
        </div>
        <div class="step-divider"></div>
        <div class="step-item active">
            <span class="step-num">3</span>
            <span>Review Booking</span>
        </div>
        <div class="step-divider"></div>
        <div class="step-item">
            <span class="step-num">4</span>
            <span>Payment</span>
        </div>
        <div class="step-divider"></div>
        <div class="step-item">
            <span class="step-num">5</span>
            <span>E-Ticket</span>
        </div>
    </div>

    <!-- Booking Reference Banner -->
    <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.25rem 1.75rem; margin-bottom: 2rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
        <div>
            <span style="font-size:0.8rem; text-transform:uppercase; color:var(--text-dim); letter-spacing:0.06em; font-weight:700;">Booking Reference</span>
            <div style="font-family:monospace; font-size:1.5rem; font-weight:800; color:var(--accent); letter-spacing:0.05em;">
                <?= htmlspecialchars($booking['booking_reference']) ?>
            </div>
        </div>

        <div style="display:flex; align-items:center; gap: 12px;">
            <span class="status-pill status-<?= $booking['status'] === 'booked' ? 'booked' : 'pending' ?>">
                Status: <?= $booking['status'] === 'booked' ? 'Confirmed (Booked)' : 'Pending Draft' ?>
            </span>
            <span style="font-size:0.85rem; color:var(--text-dim);">
                Created: <?= format_date($booking['created_at'], 'd M Y, H:i') ?>
            </span>
        </div>
    </div>

    <?php if (!empty($priceNotice)): ?>
        <div class="alert alert-warning">
            <span><?= htmlspecialchars($priceNotice) ?></span>
        </div>
    <?php endif; ?>

    <div class="booking-layout">
        <!-- Left: Flight Details & Passenger Roster -->
        <div>
            <!-- Flight Overview Card -->
            <div class="form-card" style="margin-bottom: 2rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; border-bottom:1px solid var(--border-subtle); padding-bottom:12px;">
                    <h3 style="font-size: 1.2rem; margin:0;">Flight Itinerary</h3>
                    <span class="fare-class-pill"><?= htmlspecialchars($booking['travel_class']) ?> Cabin</span>
                </div>

                <div class="itinerary-route-grid" style="margin-bottom:1.75rem;">
                    <div>
                        <div style="font-family:var(--font-heading); font-size:2rem; font-weight:800; color:#fff;">
                            <?= date('H:i', strtotime($booking['departure_time'])) ?>
                        </div>
                        <div style="font-weight:700; color:var(--accent); font-size:1.1rem;"><?= htmlspecialchars($booking['source_code']) ?></div>
                        <div style="color:var(--text-muted); font-size:0.875rem;"><?= htmlspecialchars($booking['source']) ?></div>
                        <div style="color:var(--text-dim); font-size:0.8rem; margin-top:4px;"><?= format_date($booking['travel_date']) ?></div>
                    </div>

                    <div style="text-align:center;">
                        <span style="font-size:0.8rem; color:var(--text-muted); display:block; margin-bottom:4px;"><?= htmlspecialchars($booking['duration']) ?></span>
                        <div class="route-line" style="width: 140px;">
                            <span class="route-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="transform: rotate(90deg); color: var(--text-dim);"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.3c.4-.2.6-.6.5-1.1z"/></svg>
                            </span>
                        </div>
                        <span style="font-size:0.75rem; color:var(--success); margin-top:4px; display:block;">Direct Scheduled</span>
                    </div>

                    <div style="text-align:right;">
                        <div style="font-family:var(--font-heading); font-size:2rem; font-weight:800; color:#fff;">
                            <?= date('H:i', strtotime($booking['arrival_time'])) ?>
                        </div>
                        <div style="font-weight:700; color:var(--accent); font-size:1.1rem;"><?= htmlspecialchars($booking['destination_code']) ?></div>
                        <div style="color:var(--text-muted); font-size:0.875rem;"><?= htmlspecialchars($booking['destination']) ?></div>
                        <div style="color:var(--text-dim); font-size:0.8rem; margin-top:4px;"><?= format_date($booking['travel_date']) ?></div>
                    </div>
                </div>

                <div style="background:var(--bg-input); padding:12px 16px; border-radius:var(--radius-md); display:flex; justify-content:space-between; align-items:center; font-size:0.9rem;">
                    <div>
                        <strong>Airline:</strong> <?= htmlspecialchars($booking['airline']) ?> &bull; 
                        <strong>Flight:</strong> <?= htmlspecialchars($booking['flight_number']) ?>
                    </div>
                    <div>
                        <strong>Seats Remaining:</strong> <?= (int)$booking['available_seats'] ?>
                    </div>
                </div>
            </div>

            <!-- Passengers Roster -->
            <div class="form-card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; border-bottom:1px solid var(--border-subtle); padding-bottom:12px;">
                    <h3 style="font-size: 1.2rem; margin:0;">
                        Travelers Roster (<?= count($passengers) ?>)
                    </h3>
                    
                    <!-- Edit Passengers Link -->
                    <a href="<?= base_url('pages/passenger.php?draft_ref=' . urlencode($booking['booking_reference'])) ?>" class="btn btn-outline btn-sm">
                        ✏️ Edit Passenger Details
                    </a>
                </div>

                <div style="display:flex; flex-direction:column; gap: 16px;">
                    <?php foreach ($passengers as $idx => $pax): ?>
                        <div style="background:var(--bg-input); border:1px solid var(--border-subtle); border-radius:var(--radius-md); padding:16px;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                                <strong style="color:#fff; font-size:1.05rem;">
                                    <?= htmlspecialchars($pax['title']) ?> <?= htmlspecialchars($pax['first_name']) ?> <?= htmlspecialchars($pax['last_name']) ?>
                                    <?= ($idx === 0) ? '<span style="color:var(--accent); font-size:0.75rem;">(Lead Passenger)</span>' : '' ?>
                                </strong>
                                <span class="status-pill status-active" style="font-size:0.7rem;"><?= htmlspecialchars($pax['gender']) ?></span>
                            </div>

                            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:10px; font-size:0.85rem; color:var(--text-muted);">
                                <div><strong>Date of Birth:</strong> <?= format_date($pax['date_of_birth'], 'd M Y') ?></div>
                                <div><strong>Nationality:</strong> <?= htmlspecialchars($pax['nationality']) ?></div>
                                <div><strong>Passport No:</strong> <span style="font-family:monospace; color:#fff;"><?= htmlspecialchars($pax['passport_number']) ?></span></div>
                                <div><strong>Passport Expiry:</strong> <?= format_date($pax['passport_expiry'], 'd M Y') ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Right: Fare Summary & Actions -->
        <div>
            <div class="summary-card">
                <h3 class="summary-title">Financial Summary</h3>

                <div class="fare-breakdown">
                    <div class="fare-row">
                        <span>Base Fare (<?= $booking['passenger_count'] ?> pax)</span>
                        <span><?= format_currency($booking['base_amount']) ?></span>
                    </div>

                    <div class="fare-row">
                        <span>Aviation Taxes &amp; Security (12%)</span>
                        <span><?= format_currency($booking['taxes']) ?></span>
                    </div>

                    <div class="fare-row total">
                        <span>Total Due</span>
                        <span style="color:var(--accent);"><?= format_currency($booking['total_amount']) ?></span>
                    </div>
                </div>

                <?php if ($booking['status'] === 'pending'): ?>
                    <div style="display:flex; flex-direction:column; gap:12px; margin-top:1.5rem;">
                        <a href="<?= base_url('pages/payment.php?booking_ref=' . urlencode($booking['booking_reference'])) ?>" class="btn btn-primary btn-block btn-lg" id="proceed-to-payment-btn">
                            Proceed to Payment &rarr;
                        </a>

                        <a href="<?= base_url('pages/drafts.php') ?>" class="btn btn-secondary btn-block">
                            Save as Draft &amp; Exit
                        </a>
                    </div>
                <?php else: ?>
                    <a href="<?= base_url('pages/confirmation.php?booking_ref=' . urlencode($booking['booking_reference'])) ?>" class="btn btn-primary btn-block btn-lg">
                        View Boarding Pass &rarr;
                    </a>
                <?php endif; ?>

                <div style="margin-top:1.5rem; padding-top:1rem; border-top:1px solid var(--border-subtle); font-size:0.8rem; color:var(--text-dim); line-height:1.6;">
                    🔒 <strong>Secure Reservation:</strong> This booking is securely saved. Leaving this page will keep your booking under <strong>Drafts</strong> with status <code>pending</code> until paid or deleted.
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
