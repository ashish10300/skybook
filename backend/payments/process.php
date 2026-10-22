<?php
/**
 * SkyBook - Backend Payments: Process Demo Payment
 */
header('Content-Type: application/json; charset=utf-8');
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Authentication required.']);
    exit;
}

$db = get_db();
$currentUser = current_user();

$bookingRef = trim($_POST['booking_reference'] ?? '');
$paymentMethod = sanitize($_POST['payment_method'] ?? 'Credit Card');

$bookingStmt = $db->prepare("SELECT * FROM bookings WHERE booking_reference = ? AND user_id = ? AND status = 'pending' LIMIT 1");
$bookingStmt->execute([$bookingRef, $currentUser['id']]);
$booking = $bookingStmt->fetch();

if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Valid pending booking not found.']);
    exit;
}

$txnId = generate_transaction_id();

$db->beginTransaction();
try {
    // 1. Insert payment record
    $payStmt = $db->prepare("
        INSERT INTO payments (booking_id, payment_method, transaction_id, amount, currency, payment_status, paid_at)
        VALUES (?, ?, ?, ?, 'USD', 'completed', NOW())
    ");
    $payStmt->execute([$booking['id'], $paymentMethod, $txnId, $booking['total_amount']]);

    // 2. Update booking status: pending -> booked
    $updateBooking = $db->prepare("UPDATE bookings SET status = 'booked', updated_at = NOW() WHERE id = ?");
    $updateBooking->execute([$booking['id']]);

    // 3. Decrement seats
    $seats = $db->prepare("UPDATE flights SET available_seats = GREATEST(0, available_seats - ?) WHERE id = ?");
    $seats->execute([$booking['passenger_count'], $booking['flight_id']]);

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Payment completed successfully.',
        'transaction_id' => $txnId,
        'booking_reference' => $bookingRef,
        'status' => 'booked'
    ]);
} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
