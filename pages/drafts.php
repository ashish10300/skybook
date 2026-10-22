<?php
/**
 * SkyBook - Draft Bookings Management
 * Displays unfinished reservations (bookings with status = 'pending')
 */
$pageTitle = 'Saved Booking Drafts';
require_once dirname(__DIR__) . '/includes/functions.php';

// Must be logged in
require_login();

$db = get_db();
$currentUser = current_user();

// Handle Delete Draft Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_draft') {
    $draftRef = trim($_POST['booking_reference'] ?? '');

    if (!empty($draftRef)) {
        // Verify ownership and that status is strictly 'pending'
        $delStmt = $db->prepare("DELETE FROM bookings WHERE booking_reference = ? AND user_id = ? AND status = 'pending'");
        $delStmt->execute([$draftRef, $currentUser['id']]);

        if ($delStmt->rowCount() > 0) {
            set_flash('success', "Draft booking {$draftRef} was deleted successfully.");
        } else {
            set_flash('error', "Draft could not be deleted or does not exist.");
        }
        header('Location: ' . base_url('pages/drafts.php'));
        exit;
    }
}

// Fetch all pending drafts for the user
$stmt = $db->prepare("
    SELECT b.*, f.flight_number, f.airline, f.source, f.source_code, f.destination, f.destination_code,
           f.departure_time, f.arrival_time, f.duration, f.price AS current_flight_price, f.available_seats, f.status AS flight_status
    FROM bookings b
    JOIN flights f ON b.flight_id = f.id
    WHERE b.user_id = ? AND b.status = 'pending'
    ORDER BY b.updated_at DESC
");
$stmt->execute([$currentUser['id']]);
$drafts = $stmt->fetchAll();

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    <!-- Page Header -->
    <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom: 2.5rem; flex-wrap:wrap; gap:16px;">
        <div>
            <span class="hero-tag" style="margin-bottom:8px;">Persistent Reservations</span>
            <h1 style="font-size: 2.25rem; margin-bottom: 6px;">Your Saved Booking Drafts</h1>
            <p class="lead" style="margin:0; font-size: 0.95rem;">
                These reservations are stored as <code>status = 'pending'</code>. You can review, update passengers, or complete payment at any time.
            </p>
        </div>

        <a href="<?= base_url('pages/search.php') ?>" class="btn btn-outline btn-sm">
            + Search New Flights
        </a>
    </div>

    <?php if (empty($drafts)): ?>
        <!-- Professional Empty State -->
        <div class="empty-state">
            <div class="empty-icon">📝</div>
            <h3>No Pending Drafts Found</h3>
            <p>
                When you begin a flight booking, SkyBook automatically secures your progress here. You currently have no unfinished reservations.
            </p>
            <a href="<?= base_url('pages/search.php') ?>" class="btn btn-primary">
                Search Available Flights &rarr;
            </a>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($drafts as $draft): 
                $flightAvailable = ($draft['flight_status'] === 'active' && $draft['available_seats'] >= $draft['passenger_count']);
                $priceChanged = abs((float)$draft['current_flight_price'] * $draft['passenger_count'] * 1.12 - (float)$draft['total_amount']) > 0.01;
            ?>
                <div class="form-card" style="padding: 1.75rem; margin-bottom: 0;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px; flex-wrap:wrap; gap:12px;">
                        <div>
                            <span class="status-pill status-pending" style="margin-bottom:4px;">Pending Draft</span>
                            <div style="font-family:monospace; font-size:1.25rem; font-weight:800; color:var(--accent);">
                                <?= htmlspecialchars($draft['booking_reference']) ?>
                            </div>
                        </div>

                        <div style="text-align:right;">
                            <div style="font-size:1.35rem; font-weight:800; color:#fff;">
                                <?= format_currency($draft['total_amount']) ?>
                            </div>
                            <span style="font-size:0.8rem; color:var(--text-dim);">
                                Last Modified: <?= format_date($draft['updated_at'], 'd M Y, H:i') ?>
                            </span>
                        </div>
                    </div>

                    <!-- Flight Information Row -->
                    <div class="draft-info-grid" style="margin-bottom: 1.5rem;">
                        <div>
                            <div style="font-weight:700; font-size:1.1rem; color:#fff; margin-bottom:4px;">
                                <?= htmlspecialchars($draft['source']) ?> (<?= htmlspecialchars($draft['source_code']) ?>) &rarr; 
                                <?= htmlspecialchars($draft['destination']) ?> (<?= htmlspecialchars($draft['destination_code']) ?>)
                            </div>
                            <div style="font-size:0.85rem; color:var(--text-muted);">
                                <?= htmlspecialchars($draft['airline']) ?> &bull; Flight <?= htmlspecialchars($draft['flight_number']) ?> &bull; <?= format_date($draft['travel_date']) ?>
                            </div>
                        </div>

                        <div>
                            <span style="font-size:0.75rem; text-transform:uppercase; color:var(--text-dim); display:block; font-weight:600;">Travelers &amp; Cabin</span>
                            <div style="font-size:0.95rem; font-weight:600; color:#fff;">
                                <?= $draft['passenger_count'] ?> Traveler(s) &bull; <?= htmlspecialchars($draft['travel_class']) ?>
                            </div>
                        </div>

                        <div>
                            <span style="font-size:0.75rem; text-transform:uppercase; color:var(--text-dim); display:block; font-weight:600;">Inventory Status</span>
                            <div>
                                <?php if ($flightAvailable): ?>
                                    <span style="color:var(--success); font-size:0.85rem; font-weight:600;">
                                        &check; Flight Active (<?= $draft['available_seats'] ?> seats left)
                                    </span>
                                <?php else: ?>
                                    <span style="color:var(--danger); font-size:0.85rem; font-weight:600;">
                                        &cross; Limited / Unavailable
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($priceChanged): ?>
                        <div style="background:rgba(245, 158, 11, 0.1); border:1px solid rgba(245, 158, 11, 0.25); border-radius:var(--radius-sm); padding:8px 12px; margin-bottom:1.25rem; font-size:0.825rem; color:#fbbf24;">
                            ⚠️ Flight fare has updated in the database. Fares will recalibrate upon continuing.
                        </div>
                    <?php endif; ?>

                    <!-- Action Controls -->
                    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border-subtle); padding-top:14px; flex-wrap:wrap; gap:12px;">
                        <div>
                            <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this draft reservation?');" style="display:inline;">
                                <input type="hidden" name="action" value="delete_draft">
                                <input type="hidden" name="booking_reference" value="<?= htmlspecialchars($draft['booking_reference']) ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    🗑️ Delete Draft
                                </button>
                            </form>
                        </div>

                        <div style="display:flex; gap:10px;">
                            <a href="<?= base_url('pages/passenger.php?draft_ref=' . urlencode($draft['booking_reference'])) ?>" class="btn btn-secondary btn-sm">
                                Edit Passengers
                            </a>
                            <a href="<?= base_url('pages/review.php?booking_ref=' . urlencode($draft['booking_reference'])) ?>" class="btn btn-primary btn-sm">
                                Continue Booking &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
