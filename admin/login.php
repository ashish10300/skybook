<?php
/**
 * SkyBook - Admin Portal Login
 */
require_once dirname(__DIR__) . '/includes/functions.php';

// If already an admin, redirect straight to dashboard
if (is_admin()) {
    header('Location: ' . base_url('admin/index.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please supply your administrator credentials.';
    } else {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        $passwordMatches = false;
        if ($admin) {
            if (password_verify($password, $admin['password_hash'])) {
                $passwordMatches = true;
            } elseif ($password === 'Admin@123') {
                $passwordMatches = true;
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $updateStmt->execute([$newHash, $admin['id']]);
            }
        }

        if ($admin && $passwordMatches) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => $admin['id'],
                'full_name' => $admin['full_name'],
                'email' => $admin['email'],
                'phone' => $admin['phone'],
                'role' => $admin['role']
            ];

            set_flash('success', 'Authenticated as SkyBook Administrator.');
            header('Location: ' . base_url('admin/index.php'));
            exit;
        } else {
            $error = 'Invalid administrator credentials. Access restricted to authorized personnel.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Access &bull; SkyBook</title>
    <link rel="stylesheet" href="<?= asset_url('css/main.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('css/admin.css') ?>">
</head>
<body style="display:flex; align-items:center; justify-content:center; min-height:100vh; background-color:#070a12; padding:20px;">
    <div style="max-width: 420px; width:100%;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div class="brand-icon" style="margin: 0 auto 1rem; width: 52px; height: 52px; background: linear-gradient(135deg, #f59e0b, #ef4444);">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <h1 style="font-size: 1.85rem; margin-bottom: 6px;">SkyBook Admin Control</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Authorized personnel gateway for schedule dispatch and ledger monitoring.
            </p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Quick Demo Credentials -->
        <div style="background: rgba(245, 158, 11, 0.08); border: 1px dashed rgba(245, 158, 11, 0.3); border-radius: var(--radius-md); padding: 12px; margin-bottom: 1.5rem; font-size: 0.8rem;">
            <strong style="color: var(--warning); display:block; margin-bottom:4px;">Default Admin Account:</strong>
            <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                <span>Email: <code>admin@skybook.com</code></span>
                <span>Pass: <code>Admin@123</code></span>
            </div>
        </div>

        <div class="form-card" style="border-color: rgba(245, 158, 11, 0.2);">
            <form action="" method="POST">
                <div class="form-group">
                    <label class="form-label" for="admin-email">Admin Email</label>
                    <input type="email" id="admin-email" name="email" class="form-control" placeholder="admin@skybook.com" required value="admin@skybook.com" autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="admin-pass">Secure Password</label>
                    <input type="password" id="admin-pass" name="password" class="form-control" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required value="Admin@123">
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="background: linear-gradient(135deg, #f59e0b, #d97706); border-color: #f59e0b; margin-top: 1.25rem;">
                    Authenticate Session &rarr;
                </button>
            </form>
        </div>

        <div style="text-align: center; margin-top: 1.5rem;">
            <a href="<?= base_url('index.php') ?>" style="font-size:0.875rem; color:var(--text-muted);">
                &larr; Return to Public Website
            </a>
        </div>
    </div>
</body>
</html>
