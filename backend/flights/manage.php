<?php
/**
 * SkyBook - Backend Flights: Management API
 */
header('Content-Type: application/json; charset=utf-8');
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';

if (!is_admin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized administrator access.']);
    exit;
}

$db = get_db();
$action = $_POST['action'] ?? '';

if ($action === 'status_toggle') {
    $flightId = (int)($_POST['flight_id'] ?? 0);
    $status = $_POST['status'] ?? 'active';

    $stmt = $db->prepare("UPDATE flights SET status = ? WHERE id = ?");
    $stmt->execute([$status, $flightId]);

    echo json_encode(['success' => true, 'message' => "Flight status updated to {$status}."]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
