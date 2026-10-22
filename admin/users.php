<?php
/**
 * SkyBook - Admin User Directory (View-Only)
 */
$pageTitle = 'Registered Users';
require_once dirname(__DIR__) . '/includes/admin_header.php';

$db = get_db();

// Retrieve all registered users with booking counts
$usersStmt = $db->query("
    SELECT u.id, u.full_name, u.email, u.phone, u.role, u.created_at,
           COUNT(b.id) AS total_bookings,
           SUM(CASE WHEN b.status = 'booked' THEN 1 ELSE 0 END) AS confirmed_bookings,
           SUM(CASE WHEN b.status = 'pending' THEN 1 ELSE 0 END) AS pending_drafts
    FROM users u
    LEFT JOIN bookings b ON u.id = b.user_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
$users = $usersStmt->fetchAll();
?>

<!-- Page Header -->
<div class="admin-page-header">
    <div class="admin-page-title">
        <h1>Registered Users Directory</h1>
        <p>Complete directory of traveler accounts and administrators. Passwords are securely hashed and never exposed.</p>
    </div>
</div>

<!-- Toolbar -->
<div class="admin-toolbar">
    <div>
        <span style="font-weight:700; color:#fff;">Total Users: <?= count($users) ?></span>
    </div>
    <div>
        <input type="text" id="admin-table-search" class="admin-search-input" placeholder="Search by name, email, or role...">
    </div>
</div>

<!-- Users Table -->
<div class="form-card" style="padding:1.5rem;">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Full Name</th>
                    <th>Email Address</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Confirmed Bookings</th>
                    <th>Pending Drafts</th>
                    <th>Registration Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><strong style="color:var(--text-dim);">#<?= $u['id'] ?></strong></td>
                        <td>
                            <strong style="color:#fff;"><?= htmlspecialchars($u['full_name']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['phone']) ?></td>
                        <td>
                            <span class="status-pill status-<?= $u['role'] === 'admin' ? 'active' : 'booked' ?>" style="<?= $u['role'] === 'admin' ? 'background:rgba(245,158,11,0.15); color:var(--warning);' : '' ?>">
                                <?= ucfirst($u['role']) ?>
                            </span>
                        </td>
                        <td>
                            <span style="font-weight:700; color:var(--success);"><?= (int)$u['confirmed_bookings'] ?></span>
                        </td>
                        <td>
                            <span style="font-weight:700; color:var(--warning);"><?= (int)$u['pending_drafts'] ?></span>
                        </td>
                        <td><?= format_date($u['created_at'], 'd M Y, H:i') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
