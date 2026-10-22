<?php
/**
 * SkyBook - Backend Flights: Search API
 */
header('Content-Type: application/json; charset=utf-8');
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';

$source = trim($_GET['source'] ?? '');
$destination = trim($_GET['destination'] ?? '');
$passengers = max(1, (int)($_GET['passengers'] ?? 1));
$travelClass = trim($_GET['travel_class'] ?? 'all');

$db = get_db();

$sql = "SELECT * FROM flights WHERE status = 'active' AND available_seats >= :passengers";
$params = [':passengers' => $passengers];

if (!empty($source)) {
    $sql .= " AND (source LIKE :source OR source_code LIKE :source_code)";
    $params[':source'] = "%{$source}%";
    $params[':source_code'] = "%{$source}%";
}

if (!empty($destination)) {
    $sql .= " AND (destination LIKE :dest OR destination_code LIKE :dest_code)";
    $params[':dest'] = "%{$destination}%";
    $params[':dest_code'] = "%{$destination}%";
}

if (!empty($travelClass) && $travelClass !== 'all') {
    $sql .= " AND travel_class = :travel_class";
    $params[':travel_class'] = $travelClass;
}

$sql .= " ORDER BY price ASC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$flights = $stmt->fetchAll();

echo json_encode([
    'success' => true,
    'count' => count($flights),
    'flights' => $flights
]);
