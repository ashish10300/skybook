<?php
/**
 * SkyBook - 1-Click Database Setup & Seeder Utility
 * For XAMPP / MariaDB / Local Development
 */

$host = 'localhost';
$port = '3306';
$user = 'root';
$pass = '';
$dbName = 'skybook_db';

$status = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        // 1. Create database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        $pdo->exec("USE `{$dbName}`;");

        // 2. Read SQL file
        $sqlFile = __DIR__ . '/skybook_db.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("SQL seed file not found at: {$sqlFile}");
        }

        $sqlContent = file_get_contents($sqlFile);
        $pdo->exec($sqlContent);

        $status = "Database `{$dbName}` successfully configured and seeded with international flights, admin user, and sample reservations!";

    } catch (Exception $e) {
        $error = "Database setup failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup &bull; SkyBook</title>
    <link rel="stylesheet" href="../css/main.css">
    <style>
        body {
            background-color: #070a12;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .setup-card {
            background: #111827;
            border: 1px solid #1f293d;
            border-radius: 16px;
            padding: 32px;
            max-width: 580px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        }
    </style>
</head>
<body>
    <div class="setup-card">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:1.5rem;">
            <div class="brand-icon" style="width:40px; height:40px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.3c.4-.2.6-.6.5-1.1z"/></svg>
            </div>
            <div>
                <h2 style="font-size:1.35rem; margin:0;">SkyBook Database Installer</h2>
                <span style="font-size:0.8rem; color:var(--accent);">MariaDB / MySQL Automated Seeder</span>
            </div>
        </div>

        <?php if (!empty($status)): ?>
            <div class="alert alert-success" style="margin-bottom:1.5rem;">
                <span><?= htmlspecialchars($status) ?></span>
            </div>
            <div style="display:flex; gap:12px;">
                <a href="../index.php" class="btn btn-primary btn-block">Go to SkyBook Homepage &rarr;</a>
                <a href="../admin/login.php" class="btn btn-secondary btn-block">Go to Admin Login &rarr;</a>
            </div>
        <?php else: ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-error" style="margin-bottom:1.5rem;">
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <p style="color:var(--text-muted); font-size:0.9rem; line-height:1.6; margin-bottom:1.25rem;">
                This utility initializes the <code>skybook_db</code> database in MariaDB, creates the 5 core relational tables (<code>users</code>, <code>flights</code>, <code>bookings</code>, <code>passengers</code>, <code>payments</code>), and injects realistic sample data.
            </p>

            <div style="background:var(--bg-input); padding:14px; border-radius:var(--radius-md); border:1px solid var(--border-subtle); margin-bottom:1.5rem; font-size:0.85rem;">
                <strong>Configuration Target:</strong><br>
                Host: <code><?= $host ?>:<?= $port ?></code> &bull; User: <code><?= $user ?></code> &bull; DB: <code><?= $dbName ?></code>
            </div>

            <form action="" method="POST">
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    ⚡ Initialize &amp; Seed Database Now
                </button>
            </form>

            <div style="text-align:center; margin-top:1.25rem;">
                <span style="font-size:0.8rem; color:var(--text-dim);">
                    Alternatively, import <code>database/skybook_db.sql</code> manually via phpMyAdmin.
                </span>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
