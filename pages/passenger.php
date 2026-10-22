<?php
/**
 * SkyBook - Passenger Details & Draft Creation
 */
$pageTitle = 'Passenger Information';
require_once dirname(__DIR__) . '/includes/functions.php';

// Authentication required before entering passenger details
require_login();

$db = get_db();
$currentUser = current_user();

$flightId = (int)($_GET['flight_id'] ?? 0);
$passengerCount = max(1, min(9, (int)($_GET['passengers'] ?? 1)));
$travelClass = $_GET['travel_class'] ?? 'Economy';
$draftRef = trim($_GET['draft_ref'] ?? '');

$existingBooking = null;
$existingPassengers = [];

// If editing an existing draft
if (!empty($draftRef)) {
    $draftStmt = $db->prepare("SELECT * FROM bookings WHERE booking_reference = ? AND (user_id = ? OR ? = 'admin') LIMIT 1");
    $draftStmt->execute([$draftRef, $currentUser['id'], $currentUser['role']]);
    $existingBooking = $draftStmt->fetch();

    if ($existingBooking) {
        $flightId = $existingBooking['flight_id'];
        $passengerCount = (int)$existingBooking['passenger_count'];
        $travelClass = $existingBooking['travel_class'];

        // Fetch existing passengers
        $paxStmt = $db->prepare("SELECT * FROM passengers WHERE booking_id = ? ORDER BY id ASC");
        $paxStmt->execute([$existingBooking['id']]);
        $existingPassengers = $paxStmt->fetchAll();
    }
}

// Validate flight
$flightStmt = $db->prepare("SELECT * FROM flights WHERE id = ? AND status = 'active' LIMIT 1");
$flightStmt->execute([$flightId]);
$flight = $flightStmt->fetch();

if (!$flight) {
    set_flash('error', 'The requested flight was not found or is no longer available.');
    header('Location: ' . base_url('pages/search.php'));
    exit;
}

if ($flight['available_seats'] < $passengerCount) {
    set_flash('error', "Only {$flight['available_seats']} seats remain on flight {$flight['flight_number']}. Please adjust travelers.");
    header('Location: ' . base_url('pages/results.php?source=' . urlencode($flight['source_code']) . '&destination=' . urlencode($flight['destination_code'])));
    exit;
}

$errors = [];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postPassengers = $_POST['passenger'] ?? [];
    $errors = [];

    // Validate each passenger
    for ($i = 0; $i < $passengerCount; $i++) {
        $p = $postPassengers[$i] ?? [];
        $pNum = $i + 1;

        if (empty($p['first_name']) || empty($p['last_name'])) {
            $errors[] = "Passenger {$pNum}: First and Last Name are required.";
        }
        if (empty($p['date_of_birth'])) {
            $errors[] = "Passenger {$pNum}: Date of birth is required.";
        }
        if (empty($p['passport_number'])) {
            $errors[] = "Passenger {$pNum}: Passport number is required.";
        }
        if (empty($p['passport_expiry'])) {
            $errors[] = "Passenger {$pNum}: Passport expiration date is required.";
        }
    }

    if (empty($errors)) {
        // Calculate financial totals
        $totals = calculate_booking_totals($flight['price'], $passengerCount);
        $bookingRef = $existingBooking ? $existingBooking['booking_reference'] : generate_booking_reference();
        $travelDate = $flight['departure_date'];

        $db->beginTransaction();
        try {
            if ($existingBooking) {
                // Update existing pending booking
                $bId = $existingBooking['id'];
                $updateBooking = $db->prepare("
                    UPDATE bookings 
                    SET flight_id = ?, travel_date = ?, travel_class = ?, passenger_count = ?, base_amount = ?, taxes = ?, total_amount = ?, status = 'pending', updated_at = NOW()
                    WHERE id = ? AND user_id = ?
                ");
                $updateBooking->execute([
                    $flightId, $travelDate, $travelClass, $passengerCount,
                    $totals['base_amount'], $totals['taxes'], $totals['total_amount'],
                    $bId, $currentUser['id']
                ]);

                // Delete previous passengers to refresh cleanly
                $delPax = $db->prepare("DELETE FROM passengers WHERE booking_id = ?");
                $delPax->execute([$bId]);
            } else {
                // Insert new pending booking draft
                $insertBooking = $db->prepare("
                    INSERT INTO bookings (booking_reference, user_id, flight_id, travel_date, travel_class, passenger_count, base_amount, taxes, total_amount, currency, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'USD', 'pending')
                ");
                $insertBooking->execute([
                    $bookingRef, $currentUser['id'], $flightId, $travelDate, $travelClass,
                    $passengerCount, $totals['base_amount'], $totals['taxes'], $totals['total_amount']
                ]);
                $bId = $db->lastInsertId();
            }

            // Insert passenger records
            $insertPax = $db->prepare("
                INSERT INTO passengers (booking_id, passenger_type, title, first_name, last_name, gender, date_of_birth, nationality, passport_number, passport_expiry)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            for ($i = 0; $i < $passengerCount; $i++) {
                $p = $postPassengers[$i];
                $insertPax->execute([
                    $bId,
                    'adult',
                    $p['title'] ?? 'Mr',
                    sanitize($p['first_name']),
                    sanitize($p['last_name']),
                    $p['gender'] ?? 'Male',
                    $p['date_of_birth'],
                    sanitize($p['nationality'] ?? 'United States'),
                    strtoupper(sanitize($p['passport_number'])),
                    $p['passport_expiry']
                ]);
            }

            $db->commit();

            // Saved as pending draft - now proceed to Review
            set_flash('success', 'Passenger information saved to your draft.');
            header('Location: ' . base_url('pages/review.php?booking_ref=' . urlencode($bookingRef)));
            exit;

        } catch (Exception $e) {
            $db->rollBack();
            $errors[] = 'Failed to persist booking draft: ' . $e->getMessage();
        }
    }
}

// Now include header which starts outputting HTML
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
        <div class="step-item active">
            <span class="step-num">2</span>
            <span>Passenger Details</span>
        </div>
        <div class="step-divider"></div>
        <div class="step-item">
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

    <!-- Layout Grid -->
    <div class="booking-layout">
        
        <!-- Left: Passenger Form Section -->
        <div>
            <!-- Flight Overview Banner -->
            <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <span class="hero-tag" style="margin-bottom: 6px;">Selected Flight</span>
                    <h3 style="font-size: 1.25rem; margin: 0 0 4px 0;">
                        <?= htmlspecialchars($flight['airline']) ?> &bull; Flight <?= htmlspecialchars($flight['flight_number']) ?>
                    </h3>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">
                        <?= htmlspecialchars($flight['source']) ?> (<?= htmlspecialchars($flight['source_code']) ?>) &rarr; 
                        <?= htmlspecialchars($flight['destination']) ?> (<?= htmlspecialchars($flight['destination_code']) ?>)
                        &bull; <?= format_date($flight['departure_date']) ?>
                    </div>
                </div>

                <div style="text-align: right;">
                    <div style="font-size: 1.35rem; font-weight: 700; color: var(--accent);">
                        <?= format_currency($flight['price']) ?> <span style="font-size: 0.8rem; color: var(--text-muted);">/ person</span>
                    </div>
                    <span class="fare-class-pill"><?= htmlspecialchars($travelClass) ?></span>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                    <div>
                        <strong>Please resolve the following:</strong>
                        <ul style="margin: 6px 0 0 16px;">
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Draft Auto-Save Notification -->
            <div style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: var(--radius-md); padding: 12px 16px; margin-bottom: 1.75rem; font-size: 0.85rem; color: #fbbf24; display: flex; align-items: center; gap: 10px;">
                <span>💾</span>
                <span><strong>Draft Protection:</strong> Submitting this step saves your booking under <strong>Drafts</strong> with status <code>pending</code>. You can exit anytime and resume later.</span>
            </div>

            <!-- Passenger Forms -->
            <form action="" method="POST" id="passenger-form">
                <?php for ($i = 0; $i < $passengerCount; $i++): 
                    $pIndex = $i;
                    $pData = $existingPassengers[$i] ?? ($_POST['passenger'][$i] ?? []);
                    $isLead = ($i === 0);
                ?>
                    <div class="form-card" style="margin-bottom: 1.5rem;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 10px;">
                            <h3 style="font-size: 1.15rem; margin: 0;">
                                Traveler <?= $i + 1 ?> <?= $isLead ? '<span style="color:var(--accent); font-size:0.85rem; font-weight:600;">(Lead Passenger)</span>' : '' ?>
                            </h3>
                            <span class="status-pill status-active" style="font-size: 0.72rem;">Adult (12+ yrs)</span>
                        </div>

                        <!-- Row 1: Title, First Name, Last Name -->
                        <div class="form-row pax-name-row">
                            <div class="form-group">
                                <label class="form-label">Title *</label>
                                <select name="passenger[<?= $pIndex ?>][title]" class="form-control" required>
                                    <option value="Mr" <?= (($pData['title'] ?? '') === 'Mr') ? 'selected' : '' ?>>Mr</option>
                                    <option value="Mrs" <?= (($pData['title'] ?? '') === 'Mrs') ? 'selected' : '' ?>>Mrs</option>
                                    <option value="Ms" <?= (($pData['title'] ?? '') === 'Ms') ? 'selected' : '' ?>>Ms</option>
                                    <option value="Dr" <?= (($pData['title'] ?? '') === 'Dr') ? 'selected' : '' ?>>Dr</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">First / Given Name *</label>
                                <input type="text" name="passenger[<?= $pIndex ?>][first_name]" class="form-control" placeholder="As on passport" required value="<?= htmlspecialchars($pData['first_name'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Last / Family Name *</label>
                                <input type="text" name="passenger[<?= $pIndex ?>][last_name]" class="form-control" placeholder="As on passport" required value="<?= htmlspecialchars($pData['last_name'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Row 2: Gender, DOB, Nationality -->
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Gender *</label>
                                <select name="passenger[<?= $pIndex ?>][gender]" class="form-control" required>
                                    <option value="Male" <?= (($pData['gender'] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= (($pData['gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= (($pData['gender'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Date of Birth *</label>
                                <input type="date" name="passenger[<?= $pIndex ?>][date_of_birth]" class="form-control" required value="<?= htmlspecialchars($pData['date_of_birth'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nationality *</label>
                                <input type="text" name="passenger[<?= $pIndex ?>][nationality]" class="form-control" placeholder="e.g. United States" required value="<?= htmlspecialchars($pData['nationality'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Row 3: Passport Number, Expiry -->
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Passport Number *</label>
                                <input type="text" name="passenger[<?= $pIndex ?>][passport_number]" class="form-control" placeholder="e.g. US98234125" required value="<?= htmlspecialchars($pData['passport_number'] ?? '') ?>" style="text-transform:uppercase;">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Passport Expiration Date *</label>
                                <input type="date" name="passenger[<?= $pIndex ?>][passport_expiry]" class="form-control" required value="<?= htmlspecialchars($pData['passport_expiry'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>

                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-top: 1.5rem;">
                    <a href="<?= base_url('pages/results.php') ?>" class="btn btn-outline">
                        &larr; Back to Results
                    </a>

                    <button type="submit" class="btn btn-primary btn-lg" id="submit-passengers-btn">
                        Save &amp; Continue to Review &rarr;
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Fare Summary Sidebar -->
        <div>
            <div class="summary-card">
                <h3 class="summary-title">Fare Breakdown</h3>
                
                <?php 
                    $totals = calculate_booking_totals($flight['price'], $passengerCount);
                ?>
                <div class="fare-breakdown">
                    <div class="fare-row">
                        <span>Base Fare (<?= $passengerCount ?> &times; <?= format_currency($flight['price']) ?>)</span>
                        <span><?= format_currency($totals['base_amount']) ?></span>
                    </div>

                    <div class="fare-row">
                        <span>Aviation Taxes &amp; Fees (12%)</span>
                        <span><?= format_currency($totals['taxes']) ?></span>
                    </div>

                    <div class="fare-row total">
                        <span>Total Payable</span>
                        <span style="color: var(--accent);"><?= format_currency($totals['total_amount']) ?></span>
                    </div>
                </div>

                <div style="font-size:0.8rem; color:var(--text-dim); line-height:1.6; border-top:1px solid var(--border-subtle); padding-top:12px;">
                    &bull; Free cancellation within 24 hours.<br>
                    &bull; Taxes include standard international passenger tariffs and terminal security dues.
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
