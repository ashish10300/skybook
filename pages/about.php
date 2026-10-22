<?php
/**
 * SkyBook - About Platform
 * Modern Commercial Aviation Experience with Blended Atmospheric Imagery
 */
$pageTitle = 'About SkyBook Platform';
$extraCss = ['about.css'];
require_once dirname(__DIR__) . '/includes/header.php';
?>

<!-- Atmospheric Blended Hero Section -->
<section class="about-hero">
    <div class="container">
        <!-- Hero Headline -->
        <div class="about-hero-content">
            <span class="hero-tag">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.3c.4-.2.6-.6.5-1.1z"/>
                </svg>
                Next-Gen Aviation Platform
            </span>
            <h1 class="about-hero-title">
                Reinventing the <span class="title-gradient">Global Flight</span> Reservation Experience
            </h1>
            <p class="about-hero-lead">
                SkyBook is an international flight booking and ticketing platform built from first principles. Engineered with zero middleware bottlenecks, transparent fares, and instant virtual boarding pass issuance.
            </p>
        </div>

        <!-- Glassmorphic Key Metrics Strip -->
        <div class="stats-strip">
            <div class="stat-card">
                <div class="stat-number">120+</div>
                <div class="stat-label">Global Direct Routes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">&lt; 1.2s</div>
                <div class="stat-label">Real-time Search Latency</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">100%</div>
                <div class="stat-label">Live Seat Accuracy</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">99.98%</div>
                <div class="stat-label">System Uptime SLA</div>
            </div>
        </div>
    </div>
</section>

<!-- Blended Section: What is SkyBook? -->
<section class="about-section-blended">
    <div class="container">
        <div class="about-split-grid">
            <div>
                <span class="hero-tag" style="margin-bottom: 1rem;">Autonomous Inventory</span>
                <h2 style="font-size: clamp(1.8rem, 3.5vw, 2.5rem); margin-bottom: 1.25rem; color: #fff; line-height: 1.2;">
                    Engineered Without Intermediaries
                </h2>
                <p style="color: var(--text-muted); line-height: 1.8; font-size: 1.05rem; margin-bottom: 1.25rem;">
                    SkyBook is a modern flight booking platform designed for maximum velocity, transparency, and absolute transactional reliability. Rather than relying on sluggish third-party metasearch scrapers, SkyBook manages an autonomous high-performance flight schedule repository.
                </p>
                <p style="color: var(--text-muted); line-height: 1.8; font-size: 1.05rem;">
                    Travelers benefit from real-time seat decrementing, persistent drafts that protect your reservation, and cryptographic virtual boarding passes issued in seconds.
                </p>

                <!-- Architectural Highlights -->
                <ul class="about-check-list">
                    <li class="about-check-item">
                        <span class="about-check-icon">&check;</span>
                        <span><strong>Atomic Inventory Decrementing:</strong> Seat allocations are locked with ACID transaction guarantees.</span>
                    </li>
                    <li class="about-check-item">
                        <span class="about-check-icon">&check;</span>
                        <span><strong>Unified Booking Drafts:</strong> Uncompleted journeys persist cleanly under <code>pending</code> status.</span>
                    </li>
                    <li class="about-check-item">
                        <span class="about-check-icon">&check;</span>
                        <span><strong>Instant E-Ticket Generation:</strong> Official boarding passes with machine-readable barcodes &amp; QR verification.</span>
                    </li>
                </ul>
            </div>

            <!-- High-Resolution Jet Visual Card -->
            <div class="about-image-card">
                <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1200&q=85" alt="Commercial Airliner In Flight">
                <div class="about-card-badge">
                    <div>
                        <div class="badge-text-title">SkyBook Fleet Engine</div>
                        <div class="badge-text-sub">International Civil Aviation Standard</div>
                    </div>
                    <span class="status-pill status-booked" style="font-size: 0.7rem;">VERIFIED</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section: The Seamless Journey (Interactive Workflow) -->
<section style="padding: 5rem 0; background: var(--bg-main);">
    <div class="container">
        <div style="text-align: center; max-width: 680px; margin: 0 auto 3rem;">
            <span class="hero-tag">System Architecture</span>
            <h2 style="font-size: clamp(1.8rem, 3.5vw, 2.5rem); margin-bottom: 1rem;">The Four-Stage Seamless Journey</h2>
            <p class="lead" style="font-size: 1rem;">
                Every interaction in SkyBook is calibrated for ease, speed, and absolute peace of mind.
            </p>
        </div>

        <div class="steps-timeline-grid">
            <div class="step-card">
                <div class="step-badge-num">1</div>
                <h3 class="step-card-title">Precision Search</h3>
                <p class="step-card-desc">
                    Search commercial international routes across hubs in New York, London, Dubai, Paris, and Tokyo with instant multi-parameter filtering.
                </p>
            </div>

            <div class="step-card">
                <div class="step-badge-num">2</div>
                <h3 class="step-card-title">Traveler Roster</h3>
                <p class="step-card-desc">
                    Register lead passenger and traveling companions with real-time passport validation and cabin seat allocation preview.
                </p>
            </div>

            <div class="step-card">
                <div class="step-badge-num">3</div>
                <h3 class="step-card-title">Persistent Drafts</h3>
                <p class="step-card-desc">
                    Review fare summaries safely. Even if you exit or close your browser, your booking remains secured in Drafts with <code>pending</code> status.
                </p>
            </div>

            <div class="step-card">
                <div class="step-badge-num">4</div>
                <h3 class="step-card-title">Instant E-Ticket</h3>
                <p class="step-card-desc">
                    Upon payment confirmation, seats are atomically reserved, the booking becomes <code>booked</code>, and an official printable boarding pass is ready.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Section: Fleet & Cabin Classes Showcase -->
<section style="padding: 5rem 0; background: var(--bg-surface); border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
    <div class="container">
        <div style="text-align: center; max-width: 720px; margin: 0 auto 2.5rem;">
            <span class="hero-tag">Elevated Travel</span>
            <h2 style="font-size: clamp(1.8rem, 3.5vw, 2.5rem); margin-bottom: 1rem;">World-Class Fleet &amp; Cabin Standards</h2>
            <p class="lead" style="font-size: 1rem;">
                From ultra-luxurious private suites to ergonomic long-haul seating, explore how SkyBook delivers excellence across every travel tier.
            </p>
        </div>

        <div class="cabins-showcase-grid">
            <!-- First / Business Class -->
            <div class="cabin-showcase-card">
                <div class="cabin-img-wrap">
                    <img src="https://images.unsplash.com/photo-1569154941061-e231b4725ef1?auto=format&fit=crop&w=800&q=80" alt="First & Business Class Suite">
                    <span class="cabin-tag">First &bull; Business</span>
                </div>
                <div class="cabin-content">
                    <h3 class="cabin-title">Private Lie-Flat Suites</h3>
                    <p class="cabin-desc">
                        Enjoy bespoke privacy doors, 180° lie-flat beds, Michelin-curated menus, and priority fast-track terminal privileges worldwide.
                    </p>
                    <ul class="cabin-perks">
                        <li><span>&check;</span> 180° Fully Reclining Bed</li>
                        <li><span>&check;</span> Chauffeur &amp; Lounge Access</li>
                        <li><span>&check;</span> 60kg Baggage Allowance</li>
                    </ul>
                </div>
            </div>

            <!-- Premium Economy -->
            <div class="cabin-showcase-card">
                <div class="cabin-img-wrap">
                    <img src="https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?auto=format&fit=crop&w=800&q=80" alt="Cockpit Avionics & Aviation Comfort">
                    <span class="cabin-tag">Premium Economy</span>
                </div>
                <div class="cabin-content">
                    <h3 class="cabin-title">Ergonomic Long-Haul</h3>
                    <p class="cabin-desc">
                        Enhanced 38-inch pitch, dedicated cabin section, premium noise-cancelling entertainment, and priority overhead stowage.
                    </p>
                    <ul class="cabin-perks">
                        <li><span>&check;</span> Extra Legroom &amp; Footrests</li>
                        <li><span>&check;</span> Complimentary High-Speed Wi-Fi</li>
                        <li><span>&check;</span> Dedicated Cabin Crew</li>
                    </ul>
                </div>
            </div>

            <!-- Modern Fleet -->
            <div class="cabin-showcase-card">
                <div class="cabin-img-wrap">
                    <img src="https://images.unsplash.com/photo-1464037866556-6812c9d1c72e?auto=format&fit=crop&w=800&q=80" alt="Modern Commercial Jet Airliner">
                    <span class="cabin-tag">Modern Aircraft Fleet</span>
                </div>
                <div class="cabin-content">
                    <h3 class="cabin-title">Next-Gen Airframes</h3>
                    <p class="cabin-desc">
                        State-of-the-art Boeing 787 Dreamliners and Airbus A350 widebody aircraft equipped with whisper-quiet engines and advanced cabin filtration.
                    </p>
                    <ul class="cabin-perks">
                        <li><span>&check;</span> 25% Lower Carbon Footprint</li>
                        <li><span>&check;</span> Advanced HEPA Air Circulation</li>
                        <li><span>&check;</span> High-Altitude Cabin Pressurization</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Atmospheric Blended Call to Action -->
        <div class="about-cta-banner">
            <h2 class="cta-title">Ready to Experience the Sky?</h2>
            <p class="cta-lead">
                Explore premier international flight schedules, lock your seats instantly, and enjoy seamless boarding pass issuance.
            </p>
            <div class="cta-buttons">
                <a href="<?= base_url('pages/explore.php') ?>" class="btn btn-primary btn-lg">
                    Explore Destinations &rarr;
                </a>
                <a href="<?= base_url('index.php') ?>" class="btn btn-outline btn-lg" style="background: rgba(11, 17, 32, 0.6); backdrop-filter: blur(8px);">
                    Search Flights
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
