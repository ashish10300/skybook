<?php
/**
 * SkyBook - Backend Bookings: Create Pending Booking Draft
 */
header('Content-Type: application/json; charset=utf-8');
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Authentication required.']);
    exit;
}

$db = get_db();
$currentUser = current_user();

$flightId = (int)($_POST['flight_id'] ?? 0);
$passengerCount = max(1, (int)($_POST['passenger_count'] ?? 1));
$travelClass = $_POST['travel_class'] ?? 'Economy';

$flightStmt = $db->prepare("SELECT * FROM flights WHERE id = ? AND status = 'active' LIMIT 1");
$flightStmt->execute([$flightId]);
$flight = $flightStmt->fetch();

if (!$flight) {
    echo json_encode(['success' => false, 'message' => 'Flight not available.']);
    exit;
}

$totals = calculate_booking_totals($flight['price'], $passengerCount);
$ref = generate_booking_reference();

$insert = $db->prepare("
    INSERT INTO bookings (booking_reference, user_id, flight_id, travel_date, travel_class, passenger_count, base_amount, taxes, total_amount, currency, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'USD', 'pending')
");
$insert->execute([$ref, $currentUser['id'], $flightId, $flight['departure_date'], $travelClass, $passengerCount, $totals['base_amount'], $totals['taxes'], $totals['total_amount']]);

echo json_encode([
    'success' => true,
    'booking_id' => $db->lastInsertId(),
    'booking_reference' => $ref,
    'status' => 'pending'
]);
