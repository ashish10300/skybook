<?php
/**
 * SkyBook - Backend Bookings: Draft Status Check & Sync
 */
header('Content-Type: application/json; charset=utf-8');
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Authentication required.']);
    exit;
}

$db = get_db();
$currentUser = current_user();
$bookingRef = trim($_GET['booking_reference'] ?? '');

$stmt = $db->prepare("
    SELECT b.*, f.price, f.available_seats, f.status AS flight_status
    FROM bookings b
    JOIN flights f ON b.flight_id = f.id
    WHERE b.booking_reference = ? AND b.user_id = ?
    LIMIT 1
");
$stmt->execute([$bookingRef, $currentUser['id']]);
$draft = $stmt->fetch();

if (!$draft) {
    echo json_encode(['success' => false, 'message' => 'Draft not found.']);
    exit;
}

echo json_encode([
    'success' => true,
    'draft' => $draft,
    'is_available' => ($draft['flight_status'] === 'active' && $draft['available_seats'] >= $draft['passenger_count'])
]);
