<?php
/**
 * SkyBook - Backend Auth: Register Handler
 */
header('Content-Type: application/json; charset=utf-8');
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($fullName) || empty($email) || empty($phone) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

$db = get_db();
$check = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$check->execute([$email]);
if ($check->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Email address is already registered.']);
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$insert = $db->prepare("INSERT INTO users (full_name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, 'user')");
if ($insert->execute([$fullName, $email, $phone, $hash])) {
    $userId = $db->lastInsertId();
    $_SESSION['user'] = [
        'id' => $userId,
        'full_name' => $fullName,
        'email' => $email,
        'phone' => $phone,
        'role' => 'user',
    ];
    echo json_encode(['success' => true, 'message' => 'Registration successful.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to register account.']);
}
