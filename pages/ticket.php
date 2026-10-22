<?php
/**
 * SkyBook - Virtual Boarding Pass & Official E-Ticket
 */
$pageTitle = 'Virtual Boarding Pass';
$extraCss = ['ticket.css'];
require_once dirname(__DIR__) . '/includes/functions.php';

// Must be logged in
require_login();

$db = get_db();
$currentUser = current_user();
$bookingRef = trim($_GET['booking_ref'] ?? '');

if (empty($bookingRef)) {
    set_flash('error', 'No booking reference specified.');
    header('Location: ' . base_url('pages/my-bookings.php'));
    exit;
}

// Fetch booking & flight details
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
    set_flash('error', 'Boarding pass not found or authorization denied.');
    header('Location: ' . base_url('pages/my-bookings.php'));
    exit;
}

// Fetch passengers
$paxStmt = $db->prepare("SELECT * FROM passengers WHERE booking_id = ? ORDER BY id ASC");
$paxStmt->execute([$booking['id']]);
$passengers = $paxStmt->fetchAll();

// Simulated boarding gate & seat sequence
$gates = ['A4', 'B12', 'C28', 'D07', 'E19'];
$gateAssignment = $gates[$booking['id'] % count($gates)];
$terminals = ['T1', 'T2', 'T3', 'T4', 'T5'];
$terminalAssignment = $terminals[$booking['id'] % count($terminals)];
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="container ticket-wrapper">
    <!-- Top Action Bar -->
    <div class="ticket-actions-bar">
        <a href="<?= base_url('pages/my-bookings.php')?>" class="btn btn-outline btn-sm">
            &larr; Back to Bookings
        </a>

        <div class="ticket-actions-btns">
            <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()">
                🖨️ Print Boarding Pass
            </button>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                📥 Download PDF
            </button>
        </div>
    </div>

    <?php foreach ($passengers as $idx => $pax): 
        $seatNumber = (12 + $idx) . chr(65 + ($idx % 6)); // e.g. 12A, 13B
   ?>
        <!-- Virtual Boarding Pass Card -->
        <div class="boarding-pass">
            <!-- Left Main Section -->
            <div class="ticket-main">
                <div>
                    <!-- Header -->
                    <div class="ticket-header">
                        <div class="ticket-brand">
                            <div class="brand-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.3c.4-.2.6-.6.5-1.1z"/>
                                </svg>
                            </div>
                            <div class="brand-name-group">
                                <span class="brand-name-main">Sky<span style="color:var(--accent);">Book</span></span>
                                <span class="ticket-airline-tag">&bull; <?= htmlspecialchars($booking['airline'])?></span>
                            </div>
                        </div>

                        <div class="ticket-header-meta">
                            <span class="ticket-type-tag">Official Boarding Pass</span>
                            <span class="status-pill status-<?= $booking['status'] === 'booked' ? 'booked' : 'pending'?>">
                                <?= strtoupper($booking['status'])?>
                            </span>
                        </div>
                    </div>

                    <!-- Route Section -->
                    <div class="ticket-route">
                        <div class="ticket-origin">
                            <div class="ticket-city-code"><?= htmlspecialchars($booking['source_code'])?></div>
                            <div class="ticket-city-name"><?= htmlspecialchars($booking['source'])?></div>
                            <div class="ticket-time">
                                <?= date('H:i', strtotime($booking['departure_time']))?>
                            </div>
                        </div>

                        <div class="ticket-path">
                            <span class="ticket-duration"><?= htmlspecialchars($booking['duration'])?></span>
                            <div class="route-line">
                                <span class="route-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="transform: rotate(90deg);"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.3c.4-.2.6-.6.5-1.1z"/></svg>
                                </span>
                            </div>
                            <span class="route-badge">Non-stop Direct</span>
                        </div>

                        <div class="ticket-destination">
                            <div class="ticket-city-code"><?= htmlspecialchars($booking['destination_code'])?></div>
                            <div class="ticket-city-name"><?= htmlspecialchars($booking['destination'])?></div>
                            <div class="ticket-time">
                                <?= date('H:i', strtotime($booking['arrival_time']))?>
                            </div>
                        </div>
                    </div>

                    <!-- Passenger & Flight Grid -->
                    <div class="ticket-grid">
                        <div class="ticket-field">
                            <label>Passenger Name</label>
                            <span><?= htmlspecialchars($pax['title'])?> <?= htmlspecialchars($pax['first_name'])?> <?= htmlspecialchars($pax['last_name'])?></span>
                        </div>

                        <div class="ticket-field">
                            <label>Flight No</label>
                            <span><?= htmlspecialchars($booking['flight_number'])?></span>
                        </div>

                        <div class="ticket-field">
                            <label>Departure Date</label>
                            <span><?= format_date($booking['travel_date'])?></span>
                        </div>

                        <div class="ticket-field">
                            <label>Cabin Class</label>
                            <span><?= htmlspecialchars($booking['travel_class'])?></span>
                        </div>

                        <div class="ticket-field">
                            <label>Passport / ID</label>
                            <span style="font-family:monospace;"><?= htmlspecialchars($pax['passport_number'])?></span>
                        </div>

                        <div class="ticket-field">
                            <label>Terminal / Gate</label>
                            <span><?= $terminalAssignment?> &bull; Gate <?= $gateAssignment?></span>
                        </div>

                        <div class="ticket-field">
                            <label>Allocated Seat</label>
                            <span class="ticket-seat-highlight"><?= $seatNumber?></span>
                        </div>

                        <div class="ticket-field">
                            <label>Boarding Time</label>
                            <span><?= date('H:i', strtotime($booking['departure_time']) - 2700)?></span>
                        </div>
                    </div>
                </div>

                <!-- Barcode & Reference Section -->
                <div class="ticket-barcode-wrap">
                    <div class="mock-barcode" aria-hidden="true">
                        <span style="width:2px;"></span><span style="width:4px;"></span><span style="width:1px;"></span>
                        <span style="width:5px;"></span><span style="width:2px;"></span><span style="width:3px;"></span>
                        <span style="width:6px;"></span><span style="width:1px;"></span><span style="width:4px;"></span>
                        <span style="width:2px;"></span><span style="width:5px;"></span><span style="width:2px;"></span>
                        <span style="width:1px;"></span><span style="width:6px;"></span><span style="width:3px;"></span>
                        <span style="width:2px;"></span><span style="width:4px;"></span><span style="width:1px;"></span>
                        <span style="width:5px;"></span><span style="width:2px;"></span><span style="width:3px;"></span>
                        <span style="width:4px;"></span><span style="width:1px;"></span><span style="width:5px;"></span>
                    </div>

                    <div class="ref-box">
                        <small>Booking Ref (PNR)</small>
                        <strong><?= htmlspecialchars($booking['booking_reference'])?></strong>
                    </div>
                </div>
            </div>

            <!-- Right Stub Section -->
            <div class="ticket-stub">
                <div class="stub-inner">
                    <div class="stub-header">
                        <div class="stub-title">Flight Boarding Pass</div>
                        <div class="stub-flight"><?= htmlspecialchars($booking['flight_number'])?></div>
                    </div>

                    <div class="stub-route">
                        <span><?= htmlspecialchars($booking['source_code'])?></span>
                        <span class="stub-arrow">&rarr;</span>
                        <span><?= htmlspecialchars($booking['destination_code'])?></span>
                    </div>

                    <div class="stub-info-list">
                        <div class="stub-info-item">
                            <span>Traveler</span>
                            <span><?= htmlspecialchars($pax['last_name'])?>, <?= substr($pax['first_name'], 0, 1)?>.</span>
                        </div>
                        <div class="stub-info-item">
                            <span>Date</span>
                            <span><?= date('d M Y', strtotime($booking['travel_date']))?></span>
                        </div>
                        <div class="stub-info-item">
                            <span>Boarding</span>
                            <span><?= date('H:i', strtotime($booking['departure_time']) - 2700)?></span>
                        </div>
                        <div class="stub-info-item">
                            <span>Seat</span>
                            <span class="ticket-seat-highlight"><?= $seatNumber?></span>
                        </div>
                        <div class="stub-info-item">
                            <span>Gate</span>
                            <span><?= $gateAssignment?></span>
                        </div>
                    </div>
                </div>

                <!-- QR Matrix -->
                <div class="stub-qr-section">
                    <div class="stub-qr">
                        <div class="qr-matrix"></div>
                    </div>
                    <div class="stub-pnr-tag">
                        <?= htmlspecialchars($booking['booking_reference'])?>-<?= $idx + 1?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;?>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php';?>


