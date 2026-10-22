<?php
/**
 * SkyBook - User Authentication: Registration
 */
$pageTitle = 'Register SkyBook Account';
require_once dirname(__DIR__) . '/includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: ' . base_url('index.php'));
    exit;
}

$error = '';
$redirectUrl = $_GET['redirect'] ?? $_SESSION['return_to'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $redirectUrl = trim($_POST['redirect'] ?? $redirectUrl);

    // Form validations
    if (empty($fullName) || empty($email) || empty($phone) || empty($password)) {
        $error = 'All fields marked with an asterisk are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters in length.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match. Please re-enter carefully.';
    } else {
        $db = get_db();

        // Check for existing duplicate email
        $checkStmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetch()) {
            $error = 'An account with this email address already exists. Please sign in instead.';
        } else {
            // Hash password securely with bcrypt
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $insertStmt = $db->prepare("INSERT INTO users (full_name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, 'user')");
            $success = $insertStmt->execute([$fullName, $email, $phone, $passwordHash]);

            if ($success) {
                unset($_SESSION['return_to']);
                set_flash('success', "Registration complete! Please login to continue.");

                if (!empty($redirectUrl)) {
                    header('Location: ' . base_url('pages/login.php?redirect=' . urlencode($redirectUrl)));
                } else {
                    header('Location: ' . base_url('pages/login.php'));
                }
                exit;
            } else {
                $error = 'Could not create account due to a database error. Please try again.';
            }
        }
    }
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="container" style="padding-top: 3.5rem; padding-bottom: 5rem;">
    <div class="auth-wrapper">
        
        <div style="text-align: center; margin-bottom: 2rem;">
            <div class="brand-icon" style="margin: 0 auto 1rem; width: 48px; height: 48px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.3c.4-.2.6-.6.5-1.1z"/>
                </svg>
            </div>
            <h1 style="font-size: 1.85rem; margin-bottom: 6px;">Create Your SkyBook Account</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Join SkyBook to manage reservations, track live flight status, and receive virtual boarding passes.
            </p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form action="" method="POST">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectUrl) ?>">

                <div class="form-group">
                    <label class="form-label" for="reg-fullname">Full Legal Name *</label>
                    <input type="text" id="reg-fullname" name="full_name" class="form-control" placeholder="e.g. Captain Alexander Pierce" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="reg-email">Email Address *</label>
                    <input type="email" id="reg-email" name="email" class="form-control" placeholder="name@domain.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="reg-phone">Phone / Mobile Number *</label>
                    <input type="tel" id="reg-phone" name="phone" class="form-control" placeholder="+1 (555) 000-0000" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="reg-password">Password *</label>
                        <input type="password" id="reg-password" name="password" class="form-control" placeholder="Min. 6 chars" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="reg-confirm">Confirm Password *</label>
                        <input type="password" id="reg-confirm" name="confirm_password" class="form-control" placeholder="Repeat password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">
                    Complete Registration &rarr;
                </button>
            </form>
        </div>

        <div style="text-align: center; font-size: 0.9rem; color: var(--text-muted);">
            Already registered? 
            <a href="<?= base_url('pages/login.php' . (!empty($redirectUrl) ? '?redirect=' . urlencode($redirectUrl) : '')) ?>" style="font-weight: 600;">
               Login
            </a>
        </div>

    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
