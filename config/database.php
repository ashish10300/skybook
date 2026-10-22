<?php
/**
 * SkyBook - Database Configuration & Connection
 * Compatible with XAMPP MySQL / MariaDB and phpMyAdmin
 */

if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
if (!defined('DB_PORT')) define('DB_PORT', getenv('DB_PORT') ?: '3306');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'skybook_db');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');

/**
 * Obtain a shared PDO database instance.
 * Automatically handles database creation & schema seeding if database has not yet been initialized.
 *
 * @return PDO
 */
function get_db() {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // If the database does not exist, try to connect to MySQL server and auto-create & seed
            if ($e->getCode() == 1049 || strpos($e->getMessage(), 'Unknown database') !== false) {
                try {
                    $serverDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
                    $serverPdo = new PDO($serverDsn, DB_USER, DB_PASS, $options);
                    $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

                    // Now connect to the newly created database
                    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

                    // Auto-execute seed script if present
                    $sqlFile = dirname(__DIR__) . '/database/skybook_db.sql';
                    if (file_exists($sqlFile)) {
                        $sql = file_get_contents($sqlFile);
                        // Strip CREATE DATABASE / USE statements if needed, or run the script
                        $pdo->exec($sql);
                    }
                    return $pdo;
                } catch (Exception $initEx) {
                    die(render_db_error_page("Database Initialization Notice", "Could not connect to MySQL server or auto-create database: " . htmlspecialchars($initEx->getMessage())));
                }
            } else {
                die(render_db_error_page("Database Connection Error", "SkyBook could not connect to MariaDB/MySQL via XAMPP. Make sure MySQL is started in your XAMPP Control Panel.<br><br><strong>Error Details:</strong> " . htmlspecialchars($e->getMessage())));
            }
        }
    }

    return $pdo;
}

/**
 * Render a professional fallback screen if DB connection fails
 */
function render_db_error_page($title, $message) {
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title} - SkyBook</title>
    <style>
        body {
            background-color: #0b0f19;
            color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .error-card {
            background: #111827;
            border: 1px solid #1f293d;
            border-radius: 12px;
            padding: 32px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
        }
        .badge {
            display: inline-block;
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
        }
        h1 { margin: 0 0 12px 0; font-size: 22px; color: #fff; }
        p { color: #9ca3af; line-height: 1.6; margin-bottom: 20px; font-size: 14px; }
        .steps {
            background: #0d121f;
            border: 1px solid #1e293b;
            padding: 16px;
            border-radius: 8px;
            color: #cbd5e1;
            font-size: 13px;
            line-height: 1.7;
        }
        .steps ol { margin: 0; padding-left: 20px; }
        .btn {
            display: inline-block;
            margin-top: 20px;
            background: #0ea5e9;
            color: #fff;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <span class="badge">Database Setup</span>
        <h1>{$title}</h1>
        <p>{$message}</p>
        <div class="steps">
            <strong>How to resolve:</strong>
            <ol>
                <li>Open the <strong>XAMPP Control Panel</strong>.</li>
                <li>Ensure both <strong>Apache</strong> and <strong>MySQL</strong> are started (Status: Running).</li>
                <li>Open phpMyAdmin: <a href="http://localhost/phpmyadmin/" target="_blank" style="color:#38bdf8;">http://localhost/phpmyadmin/</a></li>
                <li>Import <code>database/skybook_db.sql</code> into phpMyAdmin, or refresh this page once MySQL is running.</li>
            </ol>
        </div>
        <a href="" class="btn">Retry Connection</a>
    </div>
</body>
</html>
HTML;
}
