<?php
/**
 * SkyBook - Backend Bookings: Delete Draft API
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

$stmt = $db->prepare("DELETE FROM bookings WHERE booking_reference = ? AND user_id = ? AND status = 'pending'");
$stmt->execute([$bookingRef, $currentUser['id']]);

if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => true, 'message' => 'Draft deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Draft not found or already booked.']);
}
