<?php
/**
 * SkyBook - Admin Flight Management (Full CRUD)
 */
$pageTitle = 'Flight Management';
require_once dirname(__DIR__) . '/includes/functions.php';
require_admin();

$db = get_db();

// Handle CRUD POST Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_flight') {
        $flightNumber = strtoupper(trim($_POST['flight_number'] ?? ''));
        $airline = trim($_POST['airline'] ?? '');
        $source = trim($_POST['source'] ?? '');
        $sourceCode = strtoupper(trim($_POST['source_code'] ?? ''));
        $destination = trim($_POST['destination'] ?? '');
        $destCode = strtoupper(trim($_POST['destination_code'] ?? ''));
        $deptDate = $_POST['departure_date'] ?? date('Y-m-d');
        $deptTime = $_POST['departure_time'] ?? '10:00:00';
        $arrTime = $_POST['arrival_time'] ?? '14:00:00';
        $duration = trim($_POST['duration'] ?? '4h 00m');
        $travelClass = $_POST['travel_class'] ?? 'Economy';
        $price = (float)($_POST['price'] ?? 100);
        $seats = (int)($_POST['available_seats'] ?? 150);
        $status = $_POST['status'] ?? 'active';

        if (!empty($flightNumber) && !empty($airline) && !empty($sourceCode) && !empty($destCode)) {
            $stmt = $db->prepare("
                INSERT INTO flights (flight_number, airline, source, source_code, destination, destination_code, departure_date, departure_time, arrival_time, duration, travel_class, price, available_seats, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$flightNumber, $airline, $source, $sourceCode, $destination, $destCode, $deptDate, $deptTime, $arrTime, $duration, $travelClass, $price, $seats, $status]);
            set_flash('success', "Flight {$flightNumber} added successfully to active schedules.");
        } else {
            set_flash('error', "Please fill in all required flight parameters.");
        }
        header('Location: ' . base_url('admin/flights.php'));
        exit;

    } elseif ($action === 'edit_flight') {
        $flightId = (int)($_POST['flight_id'] ?? 0);
        $flightNumber = strtoupper(trim($_POST['flight_number'] ?? ''));
        $airline = trim($_POST['airline'] ?? '');
        $source = trim($_POST['source'] ?? '');
        $sourceCode = strtoupper(trim($_POST['source_code'] ?? ''));
        $destination = trim($_POST['destination'] ?? '');
        $destCode = strtoupper(trim($_POST['destination_code'] ?? ''));
        $deptDate = $_POST['departure_date'] ?? date('Y-m-d');
        $deptTime = $_POST['departure_time'] ?? '10:00:00';
        $arrTime = $_POST['arrival_time'] ?? '14:00:00';
        $duration = trim($_POST['duration'] ?? '4h 00m');
        $travelClass = $_POST['travel_class'] ?? 'Economy';
        $price = (float)($_POST['price'] ?? 100);
        $seats = (int)($_POST['available_seats'] ?? 150);
        $status = $_POST['status'] ?? 'active';

        if ($flightId > 0) {
            $stmt = $db->prepare("
                UPDATE flights 
                SET flight_number = ?, airline = ?, source = ?, source_code = ?, destination = ?, destination_code = ?,
                    departure_date = ?, departure_time = ?, arrival_time = ?, duration = ?, travel_class = ?, price = ?,
                    available_seats = ?, status = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$flightNumber, $airline, $source, $sourceCode, $destination, $destCode, $deptDate, $deptTime, $arrTime, $duration, $travelClass, $price, $seats, $status, $flightId]);
            set_flash('success', "Flight {$flightNumber} updated successfully.");
        }
        header('Location: ' . base_url('admin/flights.php'));
        exit;

    } elseif ($action === 'delete_flight') {
        $flightId = (int)($_POST['flight_id'] ?? 0);

        if ($flightId > 0) {
            // Check if flight is referenced by existing bookings
            $checkStmt = $db->prepare("SELECT COUNT(*) FROM bookings WHERE flight_id = ?");
            $checkStmt->execute([$flightId]);
            $hasBookings = (int)$checkStmt->fetchColumn() > 0;

            if ($hasBookings) {
                // Safely mark as cancelled to preserve historical booking records without breaking FK
                $cancelStmt = $db->prepare("UPDATE flights SET status = 'cancelled' WHERE id = ?");
                $cancelStmt->execute([$flightId]);
                set_flash('warning', "Flight #{$flightId} has linked booking records. Status was safely updated to 'cancelled' to preserve data integrity.");
            } else {
                // Hard delete is safe
                $delStmt = $db->prepare("DELETE FROM flights WHERE id = ?");
                $delStmt->execute([$flightId]);
                set_flash('success', "Flight #{$flightId} deleted from database.");
            }
        }
        header('Location: ' . base_url('admin/flights.php'));
        exit;
    }
}

// Fetch all flights
$flightsStmt = $db->query("SELECT * FROM flights ORDER BY departure_date DESC, departure_time ASC");
$flights = $flightsStmt->fetchAll();

require_once dirname(__DIR__) . '/includes/admin_header.php';
?>

<!-- Page Header -->
<div class="admin-page-header">
    <div class="admin-page-title">
        <h1>Flight Schedule Management</h1>
        <p>Direct CRUD inventory management for all international and domestic routes.</p>
    </div>

    <div>
        <button type="button" class="btn btn-primary" id="open-add-flight-btn">
            + Add New Flight
        </button>
    </div>
</div>

<!-- Toolbar & Search -->
<div class="admin-toolbar">
    <div>
        <span style="font-weight:700; color:#fff;">Total Flights: <?= count($flights) ?></span>
    </div>

    <div>
        <input type="text" id="admin-table-search" class="admin-search-input" placeholder="Search airline, route, or flight no...">
    </div>
</div>

<!-- Flights Table -->
<div class="form-card" style="padding: 1.5rem;">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Flight No</th>
                    <th>Airline</th>
                    <th>Route</th>
                    <th>Date</th>
                    <th>Schedule</th>
                    <th>Class</th>
                    <th>Price</th>
                    <th>Seats</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($flights)): ?>
                    <tr>
                        <td colspan="10" style="text-align:center; padding:2rem; color:var(--text-dim);">
                            No flights available in database. Click "+ Add New Flight" to schedule.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($flights as $f): ?>
                        <tr>
                            <td>
                                <strong style="font-family:monospace; color:var(--accent);">
                                    <?= htmlspecialchars($f['flight_number']) ?>
                                </strong>
                            </td>
                            <td><?= htmlspecialchars($f['airline']) ?></td>
                            <td>
                                <strong><?= htmlspecialchars($f['source_code']) ?></strong> &rarr; <strong><?= htmlspecialchars($f['destination_code']) ?></strong>
                                <div style="font-size:0.75rem; color:var(--text-dim);">
                                    <?= htmlspecialchars($f['source']) ?> to <?= htmlspecialchars($f['destination']) ?>
                                </div>
                            </td>
                            <td><?= format_date($f['departure_date'], 'd M Y') ?></td>
                            <td>
                                <?= date('H:i', strtotime($f['departure_time'])) ?> &ndash; <?= date('H:i', strtotime($f['arrival_time'])) ?>
                                <span style="font-size:0.75rem; color:var(--text-dim); display:block;"><?= htmlspecialchars($f['duration']) ?></span>
                            </td>
                            <td><span class="fare-class-pill"><?= htmlspecialchars($f['travel_class']) ?></span></td>
                            <td><strong><?= format_currency($f['price']) ?></strong></td>
                            <td>
                                <span style="font-weight:600; color:<?= $f['available_seats'] < 20 ? 'var(--warning)' : 'var(--text-main)' ?>;">
                                    <?= $f['available_seats'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-pill status-<?= $f['status'] === 'active' ? 'booked' : ($f['status'] === 'delayed' ? 'pending' : 'active') ?>" style="<?= $f['status'] === 'cancelled' ? 'background:var(--danger-bg); color:var(--danger); border-color:rgba(239,68,68,0.3);' : '' ?>">
                                    <?= ucfirst($f['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <button type="button" class="btn btn-secondary btn-sm open-edit-flight-btn" data-flight='<?= htmlspecialchars(json_encode($f), ENT_QUOTES, 'UTF-8') ?>' style="padding:4px 8px; font-size:0.75rem;">
                                        Edit
                                    </button>
                                    
                                    <form action="" method="POST" onsubmit="return confirm('Confirm removal or cancellation of flight <?= htmlspecialchars($f['flight_number']) ?>?');" style="display:inline;">
                                        <input type="hidden" name="action" value="delete_flight">
                                        <input type="hidden" name="flight_id" value="<?= $f['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding:4px 8px; font-size:0.75rem;">
                                            Del
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal 1: Add New Flight Modal -->
<div class="modal-backdrop" id="add-flight-modal">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Schedule New Flight</h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <form action="" method="POST">
            <input type="hidden" name="action" value="add_flight">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Flight Number *</label>
                        <input type="text" name="flight_number" class="form-control" placeholder="e.g. BA-205" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Airline Name *</label>
                        <input type="text" name="airline" class="form-control" placeholder="e.g. British Airways" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Origin City *</label>
                        <input type="text" name="source" class="form-control" placeholder="e.g. New York" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Origin Airport Code *</label>
                        <input type="text" name="source_code" class="form-control" placeholder="e.g. JFK" maxlength="5" required style="text-transform:uppercase;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Destination City *</label>
                        <input type="text" name="destination" class="form-control" placeholder="e.g. London" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Destination Airport Code *</label>
                        <input type="text" name="destination_code" class="form-control" placeholder="e.g. LHR" maxlength="5" required style="text-transform:uppercase;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Departure Date *</label>
                        <input type="date" name="departure_date" class="form-control" value="<?= date('Y-m-d', strtotime('+2 days')) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Duration *</label>
                        <input type="text" name="duration" class="form-control" placeholder="e.g. 7h 15m" required value="7h 15m">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Departure Time *</label>
                        <input type="time" name="departure_time" class="form-control" value="09:00" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Arrival Time *</label>
                        <input type="time" name="arrival_time" class="form-control" value="16:15" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Travel Class *</label>
                        <select name="travel_class" class="form-control">
                            <option value="Economy">Economy</option>
                            <option value="Premium Economy">Premium Economy</option>
                            <option value="Business">Business</option>
                            <option value="First Class">First Class</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price (USD) *</label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="e.g. 450.00" value="450.00" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Available Seats *</label>
                        <input type="number" name="available_seats" class="form-control" value="150" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Operational Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="delayed">Delayed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-cancel-btn">Cancel</button>
                <button type="submit" class="btn btn-primary">Save to Schedule</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Edit Flight Modal -->
<div class="modal-backdrop" id="edit-flight-modal">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Edit Flight Schedule</h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <form action="" method="POST">
            <input type="hidden" name="action" value="edit_flight">
            <input type="hidden" name="flight_id" id="edit-flight-id">

            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Flight Number *</label>
                        <input type="text" name="flight_number" id="edit-flight-number" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Airline Name *</label>
                        <input type="text" name="airline" id="edit-airline" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Origin City *</label>
                        <input type="text" name="source" id="edit-source" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Origin Airport Code *</label>
                        <input type="text" name="source_code" id="edit-source-code" class="form-control" required style="text-transform:uppercase;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Destination City *</label>
                        <input type="text" name="destination" id="edit-destination" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Destination Airport Code *</label>
                        <input type="text" name="destination_code" id="edit-destination-code" class="form-control" required style="text-transform:uppercase;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Departure Date *</label>
                        <input type="date" name="departure_date" id="edit-departure-date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Duration *</label>
                        <input type="text" name="duration" id="edit-duration" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Departure Time *</label>
                        <input type="time" name="departure_time" id="edit-departure-time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Arrival Time *</label>
                        <input type="time" name="arrival_time" id="edit-arrival-time" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Travel Class *</label>
                        <select name="travel_class" id="edit-travel-class" class="form-control">
                            <option value="Economy">Economy</option>
                            <option value="Premium Economy">Premium Economy</option>
                            <option value="Business">Business</option>
                            <option value="First Class">First Class</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price (USD) *</label>
                        <input type="number" step="0.01" name="price" id="edit-price" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Available Seats *</label>
                        <input type="number" name="available_seats" id="edit-available-seats" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Operational Status</label>
                        <select name="status" id="edit-status" class="form-control">
                            <option value="active">Active</option>
                            <option value="delayed">Delayed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-cancel-btn">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Flight Details</button>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
