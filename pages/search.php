<?php
/**
 * SkyBook - Dedicated Flight Search Page
 */
$pageTitle = 'Search International Flights';
$extraJs = ['flight-search.js'];
require_once dirname(__DIR__) . '/includes/header.php';
?>

<!-- Hero Section with Flight Search Widget -->
<section class="hero-section" style="min-height: auto; padding-top: 6rem; padding-bottom: 4rem; background-image: linear-gradient(to bottom, rgba(7, 10, 18, 0.4), rgba(7, 10, 18, 0.9)), url('../assets/img/wing-sunset.jpg');">
    <div class="container">
        <!-- Headline -->
        <div class="hero-content">
            <span class="hero-tag">Direct Database Search</span>
            <h1 class="hero-title">Find &amp; Compare Global Flight Schedules</h1>
            <p class="hero-subtitle">
                Query our real-time flight inventory across international airports. Compare Economy, Premium, and Business cabins with guaranteed transparent pricing.
            </p>
        </div>

        <!-- Search Form Card -->
        <div class="search-widget-card" style="margin-bottom: 3.5rem;">
        <form action="<?= base_url('pages/results.php') ?>" method="GET">
            <div class="search-trip-options">
                <div class="trip-types">
                    <label class="trip-type-label">
                        <input type="radio" name="trip_type" value="one_way" checked>
                        <span>One Way</span>
                    </label>
                    <label class="trip-type-label">
                        <input type="radio" name="trip_type" value="round_trip">
                        <span>Round Trip</span>
                    </label>
                </div>

                <div style="font-size:0.85rem; color: var(--text-dim);">
                    Zero third-party API dependencies &bull; Live local database inventory
                </div>
            </div>

            <div class="search-grid">
                <!-- Source Field -->
                <div class="search-field">
                    <label for="search-source">Origin Airport / City</label>
                    <input type="text" id="search-source" name="source" list="airport-sources" placeholder="e.g. JFK or New York" value="<?= htmlspecialchars($_GET['source'] ?? '') ?>" required autocomplete="off">
                    <datalist id="airport-sources">
                        <option value="JFK">New York (JFK) - John F. Kennedy</option>
                        <option value="LHR">London (LHR) - Heathrow</option>
                        <option value="DXB">Dubai (DXB) - International</option>
                        <option value="SIN">Singapore (SIN) - Changi</option>
                        <option value="HND">Tokyo (HND) - Haneda</option>
                        <option value="CDG">Paris (CDG) - Charles de Gaulle</option>
                        <option value="BOM">Mumbai (BOM) - Chhatrapati Shivaji</option>
                        <option value="DEL">Delhi (DEL) - Indira Gandhi</option>
                        <option value="LAX">Los Angeles (LAX) - International</option>
                        <option value="FRA">Frankfurt (FRA) - Frankfurt Airport</option>
                        <option value="SYD">Sydney (SYD) - Kingsford Smith</option>
                    </datalist>
                </div>

                <!-- Swap Button -->
                <button type="button" class="swap-btn" title="Swap origin and destination" aria-label="Swap airports">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M7 16V4M7 4L3 8M7 4L11 8M17 8V20M17 20L21 16M17 20L13 16"/>
                    </svg>
                </button>

                <!-- Destination Field -->
                <div class="search-field">
                    <label for="search-destination">Destination Airport / City</label>
                    <input type="text" id="search-destination" name="destination" list="airport-destinations" placeholder="e.g. LHR or London" value="<?= htmlspecialchars($_GET['destination'] ?? '') ?>" required autocomplete="off">
                    <datalist id="airport-destinations">
                        <option value="LHR">London (LHR) - Heathrow</option>
                        <option value="DXB">Dubai (DXB) - International</option>
                        <option value="SIN">Singapore (SIN) - Changi</option>
                        <option value="HND">Tokyo (HND) - Haneda</option>
                        <option value="CDG">Paris (CDG) - Charles de Gaulle</option>
                        <option value="JFK">New York (JFK) - John F. Kennedy</option>
                        <option value="BOM">Mumbai (BOM) - Chhatrapati Shivaji</option>
                        <option value="DEL">Delhi (DEL) - Indira Gandhi</option>
                        <option value="LAX">Los Angeles (LAX) - International</option>
                        <option value="FRA">Frankfurt (FRA) - Frankfurt Airport</option>
                        <option value="SYD">Sydney (SYD) - Kingsford Smith</option>
                    </datalist>
                </div>

                <!-- Departure Date -->
                <div class="search-field">
                    <label for="search-date">Departure Date</label>
                    <input type="date" id="search-date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>" required>
                </div>

                <!-- Travelers & Class -->
                <div class="search-field">
                    <label for="search-passengers">Cabin &amp; Travelers</label>
                    <div style="display: flex; gap: 8px;">
                        <select id="search-passengers" name="passengers" style="width: 50%;">
                            <option value="1" <?= (($_GET['passengers'] ?? '1') == '1') ? 'selected' : '' ?>>1 Adult</option>
                            <option value="2" <?= (($_GET['passengers'] ?? '') == '2') ? 'selected' : '' ?>>2 Adults</option>
                            <option value="3" <?= (($_GET['passengers'] ?? '') == '3') ? 'selected' : '' ?>>3 Adults</option>
                            <option value="4" <?= (($_GET['passengers'] ?? '') == '4') ? 'selected' : '' ?>>4 Adults</option>
                            <option value="5" <?= (($_GET['passengers'] ?? '') == '5') ? 'selected' : '' ?>>5 Adults</option>
                        </select>
                        <select id="search-class" name="travel_class" style="width: 50%;">
                            <option value="all">All Cabins</option>
                            <option value="Economy" <?= (($_GET['travel_class'] ?? '') == 'Economy') ? 'selected' : '' ?>>Economy</option>
                            <option value="Premium Economy" <?= (($_GET['travel_class'] ?? '') == 'Premium Economy') ? 'selected' : '' ?>>Premium</option>
                            <option value="Business" <?= (($_GET['travel_class'] ?? '') == 'Business') ? 'selected' : '' ?>>Business</option>
                            <option value="First Class" <?= (($_GET['travel_class'] ?? '') == 'First Class') ? 'selected' : '' ?>>First</option>
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary search-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    Search Flights
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Route Shortcuts -->
    <div>
        <h3 style="margin-bottom: 1.25rem;">Popular Global Corridors</h3>
        <div class="destinations-grid">
            <!-- JFK to LHR -->
            <div class="destination-card">
                <div class="dest-img-wrap">
                    <img class="dest-img" src="https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?auto=format&fit=crop&w=600&q=80" alt="London">
                    <span class="dest-badge">JFK &rarr; LHR</span>
                </div>
                <div class="dest-content">
                    <h3 class="dest-title">London, United Kingdom</h3>
                    <p class="dest-desc">New York to London &bull; Daily Flights across the Atlantic.</p>
                    <div class="dest-meta">
                        <div>From <strong>₹42,000</strong></div>
                        <a href="<?= base_url('pages/results.php?source=JFK&destination=LHR') ?>" class="btn btn-outline btn-sm">Search</a>
                    </div>
                </div>
            </div>

            <!-- LHR to DXB -->
            <div class="destination-card">
                <div class="dest-img-wrap">
                    <img class="dest-img" src="https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=600&q=80" alt="Dubai">
                    <span class="dest-badge">LHR &rarr; DXB</span>
                </div>
                <div class="dest-content">
                    <h3 class="dest-title">Dubai, UAE</h3>
                    <p class="dest-desc">London to Dubai &bull; Premium direct service on Emirates / BA.</p>
                    <div class="dest-meta">
                        <div>From <strong>₹54,000</strong></div>
                        <a href="<?= base_url('pages/results.php?source=LHR&destination=DXB') ?>" class="btn btn-outline btn-sm">Search</a>
                    </div>
                </div>
            </div>

            <!-- DXB to SIN -->
            <div class="destination-card">
                <div class="dest-img-wrap">
                    <img class="dest-img" src="https://images.unsplash.com/photo-1525625293386-3f8f99389edd?auto=format&fit=crop&w=600&q=80" alt="Singapore">
                    <span class="dest-badge">DXB &rarr; SIN</span>
                </div>
                <div class="dest-content">
                    <h3 class="dest-title">Singapore</h3>
                    <p class="dest-desc">Dubai to Singapore &bull; Daily non-stop flights to Changi.</p>
                    <div class="dest-meta">
                        <div>From <strong>₹62,000</strong></div>
                        <a href="<?= base_url('pages/results.php?source=DXB&destination=SIN') ?>" class="btn btn-outline btn-sm">Search</a>
                    </div>
                </div>
            </div>

            <!-- SIN to HND -->
            <div class="destination-card">
                <div class="dest-img-wrap">
                    <img class="dest-img" src="https://images.unsplash.com/photo-1503899036084-c55cdd92da26?auto=format&fit=crop&w=600&q=80" alt="Tokyo">
                    <span class="dest-badge">SIN &rarr; HND</span>
                </div>
                <div class="dest-content">
                    <h3 class="dest-title">Tokyo, Japan</h3>
                    <p class="dest-desc">Singapore to Tokyo &bull; Experience ANA / JAL direct service.</p>
                    <div class="dest-meta">
                        <div>From <strong>₹49,000</strong></div>
                        <a href="<?= base_url('pages/results.php?source=SIN&destination=HND') ?>" class="btn btn-outline btn-sm">Search</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
