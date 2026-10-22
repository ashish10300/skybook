<?php
/**
 * SkyBook - Confirmed Bookings (My Bookings)
 * Displays strictly completed reservations with status = 'booked'
 */
$pageTitle = 'My Confirmed Bookings';
require_once dirname(__DIR__) . '/includes/header.php';

// Must be logged in
require_login();

$db = get_db();
$currentUser = current_user();

// Retrieve confirmed bookings for the current user
$stmt = $db->prepare("
    SELECT b.*, f.flight_number, f.airline, f.source, f.source_code, f.destination, f.destination_code,
           f.departure_time, f.arrival_time, f.duration,
           p.transaction_id, p.payment_method, p.paid_at
    FROM bookings b
    JOIN flights f ON b.flight_id = f.id
    LEFT JOIN payments p ON b.id = p.booking_id
    WHERE b.user_id = ? AND b.status = 'booked'
    ORDER BY b.created_at DESC
");
$stmt->execute([$currentUser['id']]);
$bookings = $stmt->fetchAll();
?>

<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    <!-- Page Header -->
    <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom: 2.5rem; flex-wrap:wrap; gap:16px;">
        <div>
            <span class="hero-tag" style="margin-bottom:8px;">Passenger Itinerary</span>
            <h1 style="font-size: 2.25rem; margin-bottom: 6px;">My Confirmed Bookings</h1>
            <p class="lead" style="margin:0; font-size: 0.95rem;">
                Official confirmed flight reservations issued with verified payment records and virtual boarding passes.
            </p>
        </div>

        <a href="<?= base_url('pages/search.php') ?>" class="btn btn-outline btn-sm">
            + Book Another Flight
        </a>
    </div>

    <?php if (empty($bookings)): ?>
        <!-- Professional Empty State -->
        <div class="empty-state">
            <div class="empty-icon">✈️</div>
            <h3>No Confirmed Reservations Yet</h3>
            <p>
                You don't have any confirmed flights under your account. Search our international schedules and experience seamless flight reservation today.
            </p>
            <a href="<?= base_url('pages/search.php') ?>" class="btn btn-primary">
                Search Flights Now &rarr;
            </a>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($bookings as $b): 
                $airlineMeta = get_airline_meta($b['airline']);
            ?>
                <div class="form-card" style="padding: 1.75rem; margin-bottom: 0;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px; flex-wrap:wrap; gap:12px;">
                        <div>
                            <span class="status-pill status-booked" style="margin-bottom:4px;">Confirmed &bull; Paid</span>
                            <div style="font-family:monospace; font-size:1.35rem; font-weight:800; color:var(--accent);">
                                <?= htmlspecialchars($b['booking_reference']) ?>
                            </div>
                        </div>

                        <div style="text-align:right;">
                            <div style="font-size:1.35rem; font-weight:800; color:#fff;">
                                <?= format_currency($b['total_amount']) ?>
                            </div>
                            <span style="font-size:0.8rem; color:var(--text-dim);">
                                Paid: <?= format_date($b['paid_at'] ?? $b['created_at'], 'd M Y, H:i') ?>
                            </span>
                        </div>
                    </div>

                    <!-- Flight Information Details -->
                    <div class="draft-info-grid" style="margin-bottom: 1.5rem;">
                        <div>
                            <div style="font-weight:700; font-size:1.15rem; color:#fff; margin-bottom:4px;">
                                <?= htmlspecialchars($b['source']) ?> (<?= htmlspecialchars($b['source_code']) ?>) &rarr; 
                                <?= htmlspecialchars($b['destination']) ?> (<?= htmlspecialchars($b['destination_code']) ?>)
                            </div>
                            <div style="font-size:0.875rem; color:var(--text-muted);">
                                <?= htmlspecialchars($b['airline']) ?> &bull; Flight <?= htmlspecialchars($b['flight_number']) ?> &bull; 
                                Departure: <strong><?= date('H:i', strtotime($b['departure_time'])) ?></strong> &bull; <?= format_date($b['travel_date']) ?>
                            </div>
                        </div>

                        <div>
                            <span style="font-size:0.75rem; text-transform:uppercase; color:var(--text-dim); display:block; font-weight:600;">Travelers &amp; Cabin</span>
                            <div style="font-size:0.95rem; font-weight:600; color:#fff;">
                                <?= $b['passenger_count'] ?> Traveler(s) &bull; <?= htmlspecialchars($b['travel_class']) ?>
                            </div>
                        </div>

                        <div>
                            <span style="font-size:0.75rem; text-transform:uppercase; color:var(--text-dim); display:block; font-weight:600;">Transaction Ref</span>
                            <div style="font-family:monospace; font-size:0.85rem; color:var(--text-muted);">
                                <?= htmlspecialchars($b['transaction_id'] ?? 'TXN-CONFIRMED') ?>
                            </div>
                        </div>
                    </div>

                    <!-- Action Link -->
                    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border-subtle); padding-top:14px; flex-wrap:wrap; gap:12px;">
                        <div style="font-size:0.85rem; color:var(--text-dim);">
                            Payment Method: <?= htmlspecialchars($b['payment_method'] ?? 'Demo Card') ?>
                        </div>

                        <div style="display:flex; gap:10px;">
                            <a href="<?= base_url('pages/review.php?booking_ref=' . urlencode($b['booking_reference'])) ?>" class="btn btn-secondary btn-sm">
                                View Summary
                            </a>
                            <a href="<?= base_url('pages/ticket.php?booking_ref=' . urlencode($b['booking_reference'])) ?>" class="btn btn-primary btn-sm">
                                🎟️ Boarding Pass / E-Ticket &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
