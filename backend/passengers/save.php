<?php
/**
 * SkyBook - Backend Passengers: Save Passenger Roster
 */
header('Content-Type: application/json; charset=utf-8');
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Authentication required.']);
    exit;
}

$db = get_db();
$currentUser = current_user();

$bookingId = (int)($_POST['booking_id'] ?? 0);
$passengers = $_POST['passengers'] ?? [];

// Verify booking ownership
$verify = $db->prepare("SELECT id FROM bookings WHERE id = ? AND user_id = ? AND status = 'pending' LIMIT 1");
$verify->execute([$bookingId, $currentUser['id']]);
if (!$verify->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Pending booking not found.']);
    exit;
}

$db->beginTransaction();
try {
    $del = $db->prepare("DELETE FROM passengers WHERE booking_id = ?");
    $del->execute([$bookingId]);

    $insert = $db->prepare("
        INSERT INTO passengers (booking_id, passenger_type, title, first_name, last_name, gender, date_of_birth, nationality, passport_number, passport_expiry)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($passengers as $p) {
        $insert->execute([
            $bookingId,
            $p['type'] ?? 'adult',
            $p['title'] ?? 'Mr',
            sanitize($p['first_name'] ?? ''),
            sanitize($p['last_name'] ?? ''),
            $p['gender'] ?? 'Male',
            $p['date_of_birth'] ?? '1995-01-01',
            sanitize($p['nationality'] ?? 'United States'),
            strtoupper(sanitize($p['passport_number'] ?? '')),
            $p['passport_expiry'] ?? '2030-12-31'
        ]);
    }

    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Passengers saved successfully.']);
} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
