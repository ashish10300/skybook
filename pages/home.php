<?php
/**
 * SkyBook - Homepage
 */
$pageTitle = 'Book International Flights Seamlessly';
$extraJs = ['flight-search.js'];
$defaultSource = isset($_GET['source']) ? htmlspecialchars($_GET['source']) : '';
$defaultDest = isset($_GET['destination']) ? htmlspecialchars($_GET['destination']) : '';
require_once dirname(__DIR__) . '/includes/header.php';

?>

<!-- Hero Section with Flight Search Widget -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <span class="hero-tag">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                Next-Gen Aviation Reservation
            </span>
            <h1 class="hero-title">Experience Seamless Global Flight Booking</h1>
            <p class="hero-subtitle">
                Discover exceptional routes across premier international hubs. Search verified schedules, secure drafts with ease, and access virtual boarding passes instantly.
            </p>
        </div>

        <!-- Flight Search Widget Card -->
        <div class="search-widget-card">
            <form action="<?= base_url('pages/results.php') ?>" method="GET">
                <div class="search-trip-options">
                    <div class="trip-types">
                        <label class="trip-type-label">
                            <input type="radio" name="trip_type" value="one_way" checked>
                            <span>One Way</span>
                        </label>
                        <label class="trip-type-label">
                            <input type="radio" name="trip_type" value="round_trip">
                            <span>Round Trip (Flexible)</span>
                        </label>
                    </div>
                </div>

                <div class="search-grid">
                    <!-- Source Field -->
                    <div class="search-field">
                        <label for="search-source">From (Origin)</label>
                        <input type="text" id="search-source" name="source" list="airport-sources" placeholder="City or Airport (e.g. JFK)" value="<?= $defaultSource ?>" required autocomplete="off">
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
                        <label for="search-destination">To (Destination)</label>
                        <input type="text" id="search-destination" name="destination" list="airport-destinations" placeholder="City or Airport (e.g. LHR)" value="<?= $defaultDest ?>" required autocomplete="off">
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
                        <input type="date" id="search-date" name="date" required>
                    </div>

                    <!-- Passengers & Cabin Class -->
                    <div class="search-field">
                        <label for="search-passengers">Cabin &amp; Travelers</label>
                        <div style="display: flex; gap: 8px;">
                            <select id="search-passengers" name="passengers" style="width: 50%;">
                                <option value="1" selected>1 Adult</option>
                                <option value="2">2 Adults</option>
                                <option value="3">3 Adults</option>
                                <option value="4">4 Adults</option>
                                <option value="5">5 Adults</option>
                            </select>
                            <select id="search-class" name="travel_class" style="width: 50%;">
                                <option value="Economy" selected>Economy</option>
                                <option value="Premium Economy">Prem. Econ</option>
                                <option value="Business">Business</option>
                                <option value="First Class">First</option>
                            </select>
                        </div>
                    </div>

                    <!-- Search Submit Action -->
                    <button type="submit" class="btn btn-primary search-btn" id="home-search-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        Find Flights
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Popular International Destinations -->
<section style="padding: 4rem 0;">
    <div class="container">
        <div class="curated-header">
            <div>
                <span class="hero-tag" style="margin-bottom:8px;">Curated Getaways</span>
                <h2>Popular Flight Routes</h2>
                <p class="lead" style="margin:0; font-size:1rem;">Top global destinations with frequent flights and guaranteed lowest rates.</p>
            </div>
            <a href="<?= base_url('pages/explore.php') ?>" class="btn btn-outline btn-sm">Explore All &rarr;</a>
        </div>

        <div class="destinations-grid">
            <!-- Route Card 1: London -->
            <div class="destination-card">
                <div class="dest-img-wrap">
                    <img class="dest-img" src="https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?auto=format&fit=crop&w=600&q=80" alt="London skyline with Big Ben" loading="lazy">
                    <span class="dest-badge">LHR &bull; Heathrow</span>
                </div>
                <div class="dest-content">
                    <h3 class="dest-title">London, United Kingdom</h3>
                    <p class="dest-desc">Historic palaces, modern arts, and world-class culinary experiences in Britain's heart.</p>
                    <div class="dest-meta">
                        <div>From <strong>₹42,000</strong></div>
                        <a href="<?= base_url('pages/results.php?source=JFK&destination=LHR') ?>" class="btn btn-primary btn-sm book-route-btn">Book Route</a>
                    </div>
                </div>
            </div>

            <!-- Route Card 2: Dubai -->
            <div class="destination-card">
                <div class="dest-img-wrap">
                    <img class="dest-img" src="https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=600&q=80" alt="Dubai skyline" loading="lazy">
                    <span class="dest-badge">DXB &bull; Dubai</span>
                </div>
                <div class="dest-content">
                    <h3 class="dest-title">Dubai, United Arab Emirates</h3>
                    <p class="dest-desc">Ultra-modern architectural marvels, luxury shopping, and golden desert safaris.</p>
                    <div class="dest-meta">
                        <div>From <strong>₹18,000</strong></div>
                        <a href="<?= base_url('pages/results.php?source=LHR&destination=DXB') ?>" class="btn btn-primary btn-sm book-route-btn">Book Route</a>
                    </div>
                </div>
            </div>

            <!-- Route Card 3: Tokyo -->
            <div class="destination-card">
                <div class="dest-img-wrap">
                    <img class="dest-img" src="https://images.unsplash.com/photo-1503899036084-c55cdd92da26?auto=format&fit=crop&w=600&q=80" alt="Tokyo cityscape and lights" loading="lazy">
                    <span class="dest-badge">HND &bull; Haneda</span>
                </div>
                <div class="dest-content">
                    <h3 class="dest-title">Tokyo, Japan</h3>
                    <p class="dest-desc">A mesmerizing blend of ultramodern neon lights and timeless spiritual temples.</p>
                    <div class="dest-meta">
                        <div>From <strong>₹49,000</strong></div>
                        <a href="<?= base_url('pages/results.php?source=SIN&destination=HND') ?>" class="btn btn-primary btn-sm book-route-btn">Book Route</a>
                    </div>
                </div>
            </div>

            <!-- Route Card 4: Singapore -->
            <div class="destination-card">
                <div class="dest-img-wrap">
                    <img class="dest-img" src="https://images.unsplash.com/photo-1525625293386-3f8f99389edd?auto=format&fit=crop&w=600&q=80" alt="Singapore Marina Bay" loading="lazy">
                    <span class="dest-badge">SIN &bull; Changi</span>
                </div>
                <div class="dest-content">
                    <h3 class="dest-title">Singapore, Lion City</h3>
                    <p class="dest-desc">Vibrant Garden City, iconic Marina Bay skyline, and world-renowned street dining.</p>
                    <div class="dest-meta">
                        <div>From <strong>₹62,000</strong></div>
                        <a href="<?= base_url('pages/results.php?source=DXB&destination=SIN') ?>" class="btn btn-primary btn-sm book-route-btn">Book Route</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Airline Experience Section (Cabin) -->
<section style="padding: 6rem 0; background: var(--bg-main);">
    <div class="container cabin-grid">
        <div class="cabin-img-wrap" style="position: relative; border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-lg);">
            <img src="<?= base_url('assets/img/cabin.jpg') ?>" alt="Premium Cabin Interior" style="width: 100%; height: auto; display: block; transition: transform 0.5s;" class="hover-zoom">
            <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); padding: 2rem;">
                <h3 style="color: #fff; margin: 0;">Unparalleled Comfort</h3>
            </div>
        </div>
        <div>
            <span class="hero-tag" style="margin-bottom:1rem;">Premium Experience</span>
            <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem; line-height: 1.2;">Elevate Your Journey Above the Clouds</h2>
            <p class="lead" style="margin-bottom: 1.5rem;">Experience meticulously designed cabins that prioritize your comfort and privacy. Whether you're traveling for business or leisure, our fleet offers an oasis of calm at 35,000 feet.</p>
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 12px; margin-bottom: 2rem; color: var(--text-muted);">
                <li><strong style="color: var(--text-main);">✓</strong> Ergonomic seating with extra legroom</li>
                <li><strong style="color: var(--text-main);">✓</strong> Ambient mood lighting tailored to time zones</li>
                <li><strong style="color: var(--text-main);">✓</strong> State-of-the-art in-flight entertainment</li>
            </ul>
            <a href="<?= base_url('pages/explore.php') ?>" class="btn btn-outline">Discover Cabins</a>
        </div>
    </div>
</section>

<!-- Global Operations (Tarmac) -->
<section style="position: relative; padding: 8rem 0; background-image: linear-gradient(to right, rgba(12, 18, 30, 0.95), rgba(12, 18, 30, 0.4)), url('<?= base_url('assets/img/tarmac.jpg') ?>'); background-size: cover; background-position: center; background-attachment: fixed;">
    <div class="container">
        <div style="max-width: 600px;">
            <span class="hero-tag" style="margin-bottom:1rem; border-color: rgba(255,255,255,0.3); color: #fff;">Global Reach</span>
            <h2 style="font-size: 3rem; margin-bottom: 1.5rem; color: #fff;">Connecting the World's Major Hubs</h2>
            <p class="lead" style="color: rgba(255,255,255,0.8); margin-bottom: 2rem;">Our operations span across the busiest and most spectacular airports globally. From the twilight hours on the tarmac to the bustling morning gates, we ensure your flight is punctual, safe, and flawlessly executed.</p>
            <div class="stats-flex">
                <div>
                    <div style="font-size: 2.5rem; font-weight: 800; color: var(--accent);">50+</div>
                    <div style="font-size: 0.9rem; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 1px;">Destinations</div>
                </div>
                <div>
                    <div style="font-size: 2.5rem; font-weight: 800; color: var(--accent);">24/7</div>
                    <div style="font-size: 0.9rem; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 1px;">Operations</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SkyBook Core Advantages & Technology -->
<section style="background: var(--bg-surface); padding: 4.5rem 0; border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
    <div class="container">
        <div style="text-align:center; max-width:680px; margin: 0 auto 3rem;">
            <span class="hero-tag">Aviation Architecture</span>
            <h2>Why Reserve With SkyBook?</h2>
            <p class="lead" style="font-size:1rem;">Designed from scratch for uncompromising reliability, real-time ticket delivery, and resilient booking draft protection.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🛡️</div>
                <h3 class="feature-title">Reliable Local Database</h3>
                <p class="feature-desc">All airline routes and seat inventories are served directly from our native database without third-party rate limits or latency.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">💾</div>
                <h3 class="feature-title">Smart Draft Retention</h3>
                <p class="feature-desc">Never lose your unfinished flight bookings. Your pending reservations are securely persisted under your account until you decide to complete or remove them.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">🎟️</div>
                <h3 class="feature-title">Verified Virtual E-Tickets</h3>
                <p class="feature-desc">Receive print-ready virtual boarding passes equipped with unique SkyBook reference numbers, route specifications, passenger rosters, and barcode mockups.</p>
            </div>
        </div>
    </div>
</section>

<!-- Wing Sunset Parallax Break -->
<div style="height: 400px; background-image: url('<?= base_url('assets/img/wing-sunset.jpg') ?>'); background-size: cover; background-position: center; background-attachment: fixed; position: relative;">
    <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, transparent, var(--bg-main));"></div>
</div>

<!-- Call to Action Banner -->
<section style="padding: 0 0 6rem; text-align:center;">
    <div class="container">
        <div class="cta-banner" style="background-image: url('<?= base_url('assets/img/window-view.jpg') ?>');">
            <div style="position: absolute; inset: 0; background: rgba(7, 10, 18, 0.7); backdrop-filter: blur(2px);"></div>
            <div style="position: relative; z-index: 2;">
                <h2 style="font-size: 2.5rem; margin-bottom: 1rem; color: #fff;">Ready for Your Next International Voyage?</h2>
                <p class="lead" style="max-width: 600px; margin: 0 auto 2.5rem; color: rgba(255,255,255,0.9);">
                    Search schedules, compare Economy and Business travel classes, and enjoy a seamless booking workflow above the clouds.
                </p>
                <div style="display:flex; justify-content:center; gap: 16px; flex-wrap:wrap;">
                    <a href="<?= base_url('pages/search.php') ?>" class="btn btn-primary btn-lg" style="box-shadow: 0 8px 25px rgba(2, 132, 199, 0.5);">Search Flights Now</a>
                    <a href="<?= base_url('pages/explore.php') ?>" class="btn btn-secondary btn-lg" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: #fff;">Explore Travel Guides</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
