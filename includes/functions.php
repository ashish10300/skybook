<?php
/**
 * SkyBook - Core Application Functions & Helpers
 */

require_once __DIR__ . '/../config/database.php';

// Safe session startup
function start_session_if_none() {
    if (session_status() === PHP_SESSION_NONE) {
        // Configure secure cookie parameters if possible
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        session_start();
    }
}

start_session_if_none();

if (!defined('APP_URL')) define('APP_URL', getenv('APP_URL') ?: '');

/**
 * Return dynamic base URL for the application regardless of directory name
 */
function base_url($path = '') {
    if (defined('APP_URL') && APP_URL !== '') {
        $base = rtrim(APP_URL, '/');
    } else {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        // Find document root and project root
        $docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
        $projectDir = str_replace('\\', '/', realpath(dirname(__DIR__)));
        
        // Compute relative path
        if ($docRoot !== '' && strpos($projectDir, $docRoot) === 0) {
            $rel = substr($projectDir, strlen($docRoot));
        } else {
            // Fallback: examine SCRIPT_NAME
            $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
            // If script is inside pages/ or admin/ or backend/
            $parts = explode('/', trim($scriptDir, '/'));
            $rel = '';
            if (!empty($parts)) {
                // If top folder is skyBook_2.0
                $rel = '/' . $parts[0];
            }
        }
        $rel = '/' . trim($rel, '/');
        if ($rel === '/') $rel = '';
        
        $base = $protocol . $host . $rel;
    }
    
    $cleanPath = ltrim($path, '/');
    return $cleanPath ? $base . '/' . $cleanPath : $base . '/';
}

/**
 * Helper to get asset URL
 */
function asset_url($path = '') {
    return base_url($path);
}

/**
 * Check if current visitor is authenticated
 */
function is_logged_in() {
    return !empty($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

/**
 * Obtain current authenticated user details from session or database
 */
function current_user() {
    return $_SESSION['user'] ?? null;
}

/**
 * Check if the current user has the 'admin' role
 */
function is_admin() {
    return is_logged_in() && (current_user()['role'] ?? '') === 'admin';
}

/**
 * Guard route: require user to be logged in
 */
function require_login($redirectUrl = null) {
    if (!is_logged_in()) {
        $currentUri = $_SERVER['REQUEST_URI'] ?? base_url();
        $_SESSION['return_to'] = $redirectUrl ?: $currentUri;
        set_flash('info', 'Please sign in to continue with your booking.');
        header('Location: ' . base_url('pages/login.php'));
        exit;
    }
}

/**
 * Guard route: require admin privileges
 */
function require_admin() {
    if (!is_admin()) {
        if (!is_logged_in()) {
            $_SESSION['return_to'] = $_SERVER['REQUEST_URI'] ?? base_url('admin/index.php');
            set_flash('error', 'Administrator login required.');
            header('Location: ' . base_url('admin/login.php'));
            exit;
        } else {
            set_flash('error', 'Access denied. You do not have administrator permissions.');
            header('Location: ' . base_url('index.php'));
            exit;
        }
    }
}

/**
 * Set a session flash message
 */
function set_flash($type, $message) {
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }
    $_SESSION['flash'][] = [
        'type' => $type, // 'success', 'error', 'warning', 'info'
        'message' => $message
    ];
}

/**
 * Retrieve and clear all pending flash messages
 */
function get_flashes() {
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

/**
 * Sanitize user input string
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency with international symbol
 */
function format_currency($amount, $currency = 'INR') {
    $num = (float)$amount;
    switch (strtoupper($currency)) {
        case 'USD':
            return '$' . number_format($num, 2);
        case 'EUR':
            return '€' . number_format($num, 2);
        case 'GBP':
            return '£' . number_format($num, 2);
        case 'INR':
            return '₹' . number_format($num, 2);
        case 'AED':
            return 'AED ' . number_format($num, 2);
        default:
            return $currency . ' ' . number_format($num, 2);
    }
}

/**
 * Format readable date
 */
function format_date($dateStr, $format = 'D, d M Y') {
    if (!$dateStr) return 'N/A';
    $time = strtotime($dateStr);
    return $time ? date($format, $time) : $dateStr;
}

/**
 * Format readable time
 */
function format_time($timeStr, $format = 'H:i') {
    if (!$timeStr) return 'N/A';
    $time = strtotime($timeStr);
    return $time ? date($format, $time) : $timeStr;
}

/**
 * Generate a unique SkyBook booking reference code (e.g., SB-8K4F29)
 */
function generate_booking_reference() {
    $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return 'SB-' . $code;
}

/**
 * Generate unique demo payment transaction ID
 */
function generate_transaction_id() {
    return 'TXN-' . strtoupper(bin2hex(random_bytes(5))) . mt_rand(1000, 9999);
}

/**
 * Calculate financial totals: base fare, taxes (12%), total
 */
function calculate_booking_totals($flightPrice, $passengerCount) {
    $passengers = max(1, (int)$passengerCount);
    $basePrice = (float)$flightPrice;
    $baseAmount = round($basePrice * $passengers, 2);
    $taxes = round($baseAmount * 0.12, 2); // 12% standard aviation taxes & airport fees
    $totalAmount = round($baseAmount + $taxes, 2);

    return [
        'unit_price' => $basePrice,
        'passenger_count' => $passengers,
        'base_amount' => $baseAmount,
        'taxes' => $taxes,
        'total_amount' => $totalAmount,
        'currency' => 'INR'
    ];
}

/**
 * Retrieve count of pending drafts for current user
 */
function get_user_draft_count($userId = null) {
    if (!$userId) {
        $userId = current_user()['id'] ?? null;
    }
    if (!$userId) return 0;

    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ? AND status = 'pending'");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * CSRF Protection Helpers
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

/**
 * Get visual airline color or short code for card styling
 */
function get_airline_meta($airline) {
    $map = [
        'British Airways' => ['code' => 'BA', 'color' => '#1b365d', 'accent' => '#eb2227'],
        'Emirates' => ['code' => 'EK', 'color' => '#d71921', 'accent' => '#c2a265'],
        'Singapore Airlines' => ['code' => 'SQ', 'color' => '#002244', 'accent' => '#d4af37'],
        'Virgin Atlantic' => ['code' => 'VS', 'color' => '#b30838', 'accent' => '#e60000'],
        'American Airlines' => ['code' => 'AA', 'color' => '#0078d2', 'accent' => '#c8102e'],
        'Delta Air Lines' => ['code' => 'DL', 'color' => '#002244', 'accent' => '#c8102e'],
        'Air France' => ['code' => 'AF', 'color' => '#002157', 'accent' => '#e1000f'],
        'Lufthansa' => ['code' => 'LH', 'color' => '#05164d', 'accent' => '#ffab00'],
        'Japan Airlines' => ['code' => 'JL', 'color' => '#cc0000', 'accent' => '#000000'],
        'Qatar Airways' => ['code' => 'QR', 'color' => '#5c0632', 'accent' => '#8a1538'],
        'Air India' => ['code' => 'AI', 'color' => '#ed1c24', 'accent' => '#f58220'],
        'Qantas' => ['code' => 'QF', 'color' => '#e0001b', 'accent' => '#ffffff'],
        'JetBlue' => ['code' => 'B6', 'color' => '#00205b', 'accent' => '#0074d9'],
        'flydubai' => ['code' => 'FZ', 'color' => '#0055b8', 'accent' => '#f58220'],
    ];

    return $map[$airline] ?? ['code' => substr($airline, 0, 2), 'color' => '#1e293b', 'accent' => '#38bdf8'];
}
