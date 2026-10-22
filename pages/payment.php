<?php
/**
 * SkyBook - Demo Payment Gateway
 */
$pageTitle = 'Secure Demo Payment';
$extraJs = ['booking.js'];
require_once dirname(__DIR__) . '/includes/functions.php';

// Must be logged in
require_login();

$db = get_db();
$currentUser = current_user();
$bookingRef = trim($_GET['booking_ref'] ?? '');

if (empty($bookingRef)) {
    set_flash('error', 'No booking reference provided.');
    header('Location: ' . base_url('pages/drafts.php'));
    exit;
}

// Retrieve pending booking with ownership check
$bookingStmt = $db->prepare("
    SELECT b.*, f.flight_number, f.airline, f.source, f.source_code, f.destination, f.destination_code, 
           f.departure_time, f.arrival_time, f.available_seats, f.price
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

// If already paid and booked, redirect to confirmation
if ($booking['status'] === 'booked') {
    set_flash('info', 'This booking has already been paid and confirmed.');
    header('Location: ' . base_url('pages/confirmation.php?booking_ref=' . urlencode($booking['booking_reference'])));
    exit;
}

$error = '';

// Handle Payment Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = sanitize($_POST['payment_method'] ?? 'Credit/Debit Card');
    $simulateOutcome = $_POST['simulation_outcome'] ?? 'success'; // Allows testing failure scenario

    if ($simulateOutcome === 'fail') {
        // Payment failed scenario - booking remains pending
        $error = 'Demo Transaction Declined: Simulated payment failure. Your booking remains securely preserved in Drafts.';
    } else {
        // Successful payment processing
        $transactionId = generate_transaction_id();
        $totalAmount = (float)$booking['total_amount'];

        $db->beginTransaction();
        try {
            // 1. Insert Payment Record
            $payStmt = $db->prepare("
                INSERT INTO payments (booking_id, payment_method, transaction_id, amount, currency, payment_status, paid_at)
                VALUES (?, ?, ?, ?, 'INR', 'completed', NOW())
            ");
            $payStmt->execute([$booking['id'], $paymentMethod, $transactionId, $totalAmount]);

            // 2. Update Booking Status from 'pending' to 'booked'
            $updateBooking = $db->prepare("UPDATE bookings SET status = 'booked', updated_at = NOW() WHERE id = ?");
            $updateBooking->execute([$booking['id']]);

            // 3. Decrement flight available seats
            $seatDeduct = $db->prepare("UPDATE flights SET available_seats = GREATEST(0, available_seats - ?) WHERE id = ?");
            $seatDeduct->execute([$booking['passenger_count'], $booking['flight_id']]);

            $db->commit();

            set_flash('success', 'Payment successful! Your reservation has been confirmed and boarding pass issued.');
            header('Location: ' . base_url('pages/confirmation.php?booking_ref=' . urlencode($booking['booking_reference'])));
            exit;

        } catch (Exception $e) {
            $db->rollBack();
            $error = 'Payment error: ' . $e->getMessage();
        }
    }
}
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
        <div class="step-item done">
            <span class="step-num">&check;</span>
            <span>Review Booking</span>
        </div>
        <div class="step-divider"></div>
        <div class="step-item active">
            <span class="step-num">4</span>
            <span>Payment</span>
        </div>
        <div class="step-divider"></div>
        <div class="step-item">
            <span class="step-num">5</span>
            <span>E-Ticket</span>
        </div>
    </div>

    <div class="booking-layout">
        <!-- Left: Payment Form & Methods -->
        <div>
            <div class="form-card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; border-bottom:1px solid var(--border-subtle); padding-bottom:12px;">
                    <div>
                        <h2 style="font-size: 1.4rem; margin:0 0 4px 0;">Select Payment Channel</h2>
                        <span style="font-size:0.85rem; color:var(--text-dim);">Secure Payment Processing</span>
                    </div>
                    <span class="status-pill status-active">256-Bit SSL Secured</span>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <!-- Payment Method Tabs -->
                <div class="payment-tabs">
                    <button type="button" class="payment-tab-btn active" data-method="card" data-method-label="Credit/Debit Card">
                        💳 Card
                    </button>
                    <button type="button" class="payment-tab-btn" data-method="upi" data-method-label="UPI Instant">
                        📱 UPI
                    </button>
                    <button type="button" class="payment-tab-btn" data-method="netbanking" data-method-label="Net Banking">
                        🏦 Net Banking
                    </button>
                </div>

                <form action="" method="POST" id="payment-form">
                    <input type="hidden" name="payment_method" id="selected-payment-method" value="Credit/Debit Card">

                    <!-- Tab 1: Credit / Debit Card -->
                    <div class="payment-pane active" id="pane-card">
                        <div class="form-group">
                            <label class="form-label" for="card-holder">Cardholder Full Name</label>
                            <input type="text" id="card-holder" class="form-control" placeholder="e.g. Alexander Pierce" value="">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="card-number">Card Number</label>
                            <input type="text" id="card-number" class="form-control" placeholder="4532 &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; 8920" maxlength="19" value="">
                        </div>

                        <div class="form-row compact-row">
                            <div class="form-group">
                                <label class="form-label" for="card-expiry">Expiry (MM/YY)</label>
                                <input type="text" id="card-expiry" class="form-control" placeholder="12/28" maxlength="5" value="">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="card-cvv">Security Code (CVV)</label>
                                <input type="password" id="card-cvv" class="form-control" placeholder="&bull;&bull;&bull;" maxlength="4" value="">
                            </div>
                        </div>

                        <div style="font-size:0.78rem; color:var(--text-dim); margin-top:4px;">
                            * Card details are processed locally for demonstration and are NEVER stored in the database.
                        </div>
                    </div>

                    <!-- Tab 2: UPI Instant Payment -->
                    <div class="payment-pane" id="pane-upi">
                        <div class="form-group">
                            <label class="form-label" for="upi-id">Virtual Payment Address (VPA / UPI ID)</label>
                            <input type="text" id="upi-id" class="form-control" placeholder="traveler@okbank" value="">
                        </div>
                        <div style="background:var(--bg-input); padding:16px; border-radius:var(--radius-md); text-align:center; border:1px solid var(--border-subtle); margin-top:12px;">
                            <div class="stub-qr" style="width:110px; height:110px; margin: 0 auto 10px;">
                                <div class="qr-matrix"></div>
                            </div>
                            <span style="font-size:0.8rem; color:var(--text-muted);">Scan QR using any UPI app to authorize</span>
                        </div>
                    </div>

                    <!-- Tab 3: Net Banking -->
                    <div class="payment-pane" id="pane-netbanking">
                        <div class="form-group">
                            <label class="form-label" for="bank-selector">Select Financial Institution</label>
                            <select id="bank-selector" class="form-control">
                                <option value="Chase Bank">JPMorgan Chase &bull; International</option>
                                <option value="Barclays">Barclays Bank UK</option>
                                <option value="HSBC">HSBC Global Banking</option>
                                <option value="Citibank">Citibank Aviation Services</option>
                                <option value="Standard Chartered">Standard Chartered</option>
                                <option value="HDFC Bank">HDFC International Banking</option>
                            </select>
                        </div>
                        <p style="font-size:0.85rem; color:var(--text-muted);">
                            You will be redirected to the institution's secure gateway authorization mockup.
                        </p>
                    </div>

                    <input type="hidden" name="simulation_outcome" value="success">

                    <div class="mobile-stack-actions" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-top:1.5rem;">
                        <a href="<?= base_url('pages/review.php?booking_ref=' . urlencode($booking['booking_reference'])) ?>" class="btn btn-outline">
                            &larr; Back to Review
                        </a>

                        <button type="submit" class="btn btn-primary btn-lg" id="pay-submit-btn">
                            Pay <?= format_currency($booking['total_amount']) ?> &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Order Summary -->
        <div>
            <div class="summary-card">
                <h3 class="summary-title">Payment Summary</h3>

                <div style="margin-bottom:1.25rem;">
                    <div style="font-size:0.8rem; color:var(--text-dim); text-transform:uppercase; font-weight:700;">Flight</div>
                    <div style="font-size:1.05rem; font-weight:700; color:#fff;">
                        <?= htmlspecialchars($booking['airline']) ?> &bull; <?= htmlspecialchars($booking['flight_number']) ?>
                    </div>
                    <div style="font-size:0.85rem; color:var(--accent);">
                        <?= htmlspecialchars($booking['source_code']) ?> &rarr; <?= htmlspecialchars($booking['destination_code']) ?>
                    </div>
                </div>

                <div class="fare-breakdown">
                    <div class="fare-row">
                        <span>Travelers</span>
                        <span><?= $booking['passenger_count'] ?> Passenger(s)</span>
                    </div>

                    <div class="fare-row">
                        <span>Base Fare</span>
                        <span><?= format_currency($booking['base_amount']) ?></span>
                    </div>

                    <div class="fare-row">
                        <span>Aviation Taxes &amp; Fees</span>
                        <span><?= format_currency($booking['taxes']) ?></span>
                    </div>

                    <div class="fare-row total">
                        <span>Amount to Pay</span>
                        <span style="color:var(--accent);"><?= format_currency($booking['total_amount']) ?></span>
                    </div>
                </div>

                <div style="font-size:0.78rem; color:var(--text-dim); line-height:1.6; border-top:1px solid var(--border-subtle); padding-top:12px;">
                    🛡️ <strong>Encrypted Checkout:</strong> Once paid, the booking is permanently confirmed, seats are locked, and your printable boarding pass is ready.
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
