<?php
/**
 * SkyBook - Flight Search Results
 */
$pageTitle = 'Available Flight Results';
$extraJs = ['flight-search.js'];
require_once dirname(__DIR__) . '/includes/header.php';

$db = get_db();

// Extract and sanitize query parameters
$sourceParam = trim($_GET['source'] ?? '');
$destParam = trim($_GET['destination'] ?? '');
$dateParam = trim($_GET['date'] ?? '');
$passengersParam = max(1, (int)($_GET['passengers'] ?? 1));
$travelClassParam = trim($_GET['travel_class'] ?? 'all');
$sortParam = trim($_GET['sort'] ?? 'price'); // 'price', 'time', 'duration'

// Build dynamic SQL query
$sql = "SELECT * FROM flights WHERE status = 'active' AND available_seats >= :passengers";
$params = [':passengers' => $passengersParam];

if (!empty($sourceParam)) {
    $sql .= " AND (source LIKE :source OR source_code LIKE :source_code)";
    $params[':source'] = "%{$sourceParam}%";
    $params[':source_code'] = "%{$sourceParam}%";
}

if (!empty($destParam)) {
    $sql .= " AND (destination LIKE :dest OR destination_code LIKE :dest_code)";
    $params[':dest'] = "%{$destParam}%";
    $params[':dest_code'] = "%{$destParam}%";
}

if (!empty($travelClassParam) && $travelClassParam !== 'all') {
    $sql .= " AND travel_class = :travel_class";
    $params[':travel_class'] = $travelClassParam;
}

// Order by clause based on sort parameter
if ($sortParam === 'time') {
    $sql .= " ORDER BY departure_time ASC";
} elseif ($sortParam === 'duration') {
    $sql .= " ORDER BY duration ASC";
} else {
    $sql .= " ORDER BY price ASC";
}

$stmt = $db->prepare($sql);
$stmt->execute($params);
$flights = $stmt->fetchAll();

// If no flights found with strict filters, fallback to all active flights for demonstration
$isFallback = false;
if (empty($flights)) {
    $fallbackStmt = $db->prepare("SELECT * FROM flights WHERE status = 'active' ORDER BY price ASC LIMIT 10");
    $fallbackStmt->execute();
    $flights = $fallbackStmt->fetchAll();
    $isFallback = true;
}

/**
 * Helper to convert duration string "7h 15m" to total minutes for sorting
 */
function duration_to_minutes($durStr) {
    preg_match('/(?:(\d+)h)?\s*(?:(\d+)m)?/', $durStr, $matches);
    $hours = isset($matches[1]) ? (int)$matches[1] : 0;
    $mins = isset($matches[2]) ? (int)$matches[2] : 0;
    return ($hours * 60) + $mins;
}
?>

<div class="container" style="padding-top: 2rem; padding-bottom: 5rem;">
    <!-- Compact Search Revision Bar -->
    <div class="search-widget-card" style="padding: 1.25rem; margin-bottom: 2rem;">
        <form action="<?= base_url('pages/results.php') ?>" method="GET" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <div style="display:flex; align-items:center; gap: 12px; flex-wrap:wrap;">
                <div style="font-weight:700; color:#fff; font-size:1.1rem; display:flex; align-items:center; gap:8px;">
                    <span><?= htmlspecialchars($sourceParam ?: 'Any Origin') ?></span>
                    <span style="color:var(--accent);">&rarr;</span>
                    <span><?= htmlspecialchars($destParam ?: 'Any Destination') ?></span>
                </div>
                <span style="color:var(--border-strong);">|</span>
                <span style="font-size:0.875rem; color:var(--text-muted);">
                    <?= $passengersParam ?> <?= $passengersParam > 1 ? 'Travelers' : 'Traveler' ?> &bull; 
                    <?= htmlspecialchars($travelClassParam === 'all' ? 'All Classes' : $travelClassParam) ?>
                </span>
            </div>

            <a href="<?= base_url('pages/search.php') . '?' . http_build_query($_GET) ?>" class="btn btn-outline btn-sm">
                Modify Search
            </a>
        </form>
    </div>

    <!-- Results Header and Sorting Controls -->
    <div class="results-header">
        <div class="results-meta">
            <h2>
                <?= $isFallback ? 'Alternative Available Flights' : 'Available Flight Results' ?>
                <span style="color: var(--accent); font-size: 1.15rem; font-weight: 600;">(<?= count($flights) ?> found)</span>
            </h2>
            <p>
                <?php if ($isFallback): ?>
                    No direct schedules strictly matching "<?= htmlspecialchars($sourceParam) ?>" &rarr; "<?= htmlspecialchars($destParam) ?>". Showing top alternative international routes.
                <?php else: ?>
                    All flights listed are live in SkyBook's local database with real-time seat availability.
                <?php endif; ?>
            </p>
        </div>

        <!-- Sorting Buttons -->
        <div class="sort-group">
            <button type="button" class="sort-btn <?= ($sortParam === 'price') ? 'active' : '' ?>" data-sort="price">Cheapest</button>
            <button type="button" class="sort-btn <?= ($sortParam === 'time') ? 'active' : '' ?>" data-sort="time">Earliest</button>
            <button type="button" class="sort-btn <?= ($sortParam === 'duration') ? 'active' : '' ?>" data-sort="duration">Shortest</button>
        </div>
    </div>

    <!-- Flight Results List -->
    <div class="flights-list">
        <?php foreach ($flights as $flight): 
            $airlineMeta = get_airline_meta($flight['airline']);
            $unitPrice = (float)$flight['price'];
            $totalPrice = $unitPrice * $passengersParam;
            $durMinutes = duration_to_minutes($flight['duration']);
            
            // Build booking link
            $bookingQuery = http_build_query([
                'flight_id' => $flight['id'],
                'passengers' => $passengersParam,
                'travel_class' => $flight['travel_class']
            ]);
            $selectFlightUrl = base_url('pages/passenger.php?' . $bookingQuery);
        ?>
            <div class="flight-card" 
                 data-price="<?= $unitPrice ?>" 
                 data-departure="<?= htmlspecialchars($flight['departure_time']) ?>" 
                 data-duration-minutes="<?= $durMinutes ?>">
                 
                <!-- Airline Info -->
                <div class="airline-info">
                    <div class="airline-badge" style="background: <?= $airlineMeta['color'] ?>; color: #fff; border: 1px solid <?= $airlineMeta['accent'] ?>;">
                        <?= htmlspecialchars($airlineMeta['code']) ?>
                    </div>
                    <div>
                        <div class="airline-name"><?= htmlspecialchars($flight['airline']) ?></div>
                        <div class="flight-num">Flight <?= htmlspecialchars($flight['flight_number']) ?></div>
                    </div>
                </div>

                <!-- Flight Route & Times -->
                <div class="flight-route">
                    <!-- Origin -->
                    <div class="route-point">
                        <div class="route-time"><?= date('H:i', strtotime($flight['departure_time'])) ?></div>
                        <div class="route-code"><?= htmlspecialchars($flight['source_code']) ?></div>
                        <div class="route-city"><?= htmlspecialchars($flight['source']) ?></div>
                    </div>

                    <!-- Path & Duration -->
                    <div class="route-duration">
                        <div class="duration-text"><?= htmlspecialchars($flight['duration']) ?></div>
                        <div class="route-line">
                            <span class="route-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="transform: rotate(90deg); color: var(--text-dim);"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.3c.4-.2.6-.6.5-1.1z"/></svg>
                            </span>
                        </div>
                        <div style="font-size:0.7rem; color:var(--success); margin-top:4px;">Non-stop Direct</div>
                    </div>

                    <!-- Destination -->
                    <div class="route-point dest">
                        <div class="route-time"><?= date('H:i', strtotime($flight['arrival_time'])) ?></div>
                        <div class="route-code"><?= htmlspecialchars($flight['destination_code']) ?></div>
                        <div class="route-city"><?= htmlspecialchars($flight['destination']) ?></div>
                    </div>
                </div>

                <!-- Fare & Booking Action -->
                <div class="flight-pricing">
                    <div style="display:flex; gap:6px; align-items:center;">
                        <span class="fare-class-pill"><?= htmlspecialchars($flight['travel_class']) ?></span>
                        <span class="seat-status"><?= (int)$flight['available_seats'] ?> seats left</span>
                    </div>

                    <div>
                        <div class="flight-price"><?= format_currency($unitPrice) ?></div>
                        <div class="price-sub">
                            <?= $passengersParam > 1 ? format_currency($totalPrice) . ' total for ' . $passengersParam . ' pax' : 'per traveler' ?>
                        </div>
                    </div>

                    <a href="<?= $selectFlightUrl ?>" class="btn btn-primary btn-block btn-sm">
                        Select Flight &rarr;
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
