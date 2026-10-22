<?php
/**
 * SkyBook - Admin Logout Handler
 */
require_once dirname(__DIR__) . '/includes/functions.php';

if (session_status() === PHP_SESSION_ACTIVE) {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

start_session_if_none();
set_flash('info', 'Administrator session terminated.');
header('Location: ' . base_url('admin/login.php'));
exit;
