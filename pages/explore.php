<?php
/**
 * SkyBook - Explore Destinations
 */
$pageTitle = 'Explore World Destinations';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<section class="hero-section" style="min-height: auto; padding-top: 6rem; padding-bottom: 5rem; background-image: linear-gradient(to bottom, rgba(7, 10, 18, 0.4), rgba(7, 10, 18, 0.9)), url('../assets/img/hero-bg.jpg');">
    <div class="container">
        <!-- Page Header -->
        <div style="text-align: center; max-width: 760px; margin: 0 auto;">
            <span class="hero-tag">Global Horizons</span>
            <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Inspiring Destinations for Your Next Journey</h1>
            <p class="lead">
                Curated travel inspiration covering premier cultural capitals, architectural wonders, and tropical paradises accessible through SkyBook’s flight schedule.
            </p>
        </div>
    </div>
</section>

<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">

    <!-- Destinations Grid -->
    <div class="destinations-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 28px;">
        
        <!-- London -->
        <div class="destination-card">
            <div class="dest-img-wrap">
                <img class="dest-img" src="https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?auto=format&fit=crop&w=600&q=80" alt="London Big Ben">
                <span class="dest-badge">LHR &bull; London</span>
            </div>
            <div class="dest-content">
                <h3 class="dest-title">London, United Kingdom</h3>
                <p class="dest-desc">Immerse yourself in world-class theater in the West End, explore the British Museum, and experience timeless British charm along the Thames.</p>
                <div style="font-size: 0.8rem; color: var(--text-dim); margin-bottom: 12px;">
                    <strong>Best Time:</strong> May – September &bull; <strong>Flight Time:</strong> ~7h from JFK
                </div>
                <div class="dest-meta">
                    <div>Fares from <strong>₹42,000</strong></div>
                    <a href="<?= base_url('index.php?source=JFK&destination=LHR') ?>" class="btn btn-primary btn-sm book-route-btn">Book Route</a>
                </div>
            </div>
        </div>

        <!-- Paris -->
        <div class="destination-card">
            <div class="dest-img-wrap">
                <img class="dest-img" src="https://images.unsplash.com/photo-1502602898657-3e91760cbb34?auto=format&fit=crop&w=600&q=80" alt="Paris Eiffel Tower">
                <span class="dest-badge">CDG &bull; Paris</span>
            </div>
            <div class="dest-content">
                <h3 class="dest-title">Paris, France</h3>
                <p class="dest-desc">The City of Light captivates with haute cuisine, iconic architecture, romantic boulevards, and the masterpieces of the Louvre.</p>
                <div style="font-size: 0.8rem; color: var(--text-dim); margin-bottom: 12px;">
                    <strong>Best Time:</strong> June – August &bull; <strong>Flight Time:</strong> ~8h from JFK
                </div>
                <div class="dest-meta">
                    <div>Fares from <strong>₹47,500</strong></div>
                    <a href="<?= base_url('index.php?source=JFK&destination=CDG') ?>" class="btn btn-primary btn-sm book-route-btn">Book Route</a>
                </div>
            </div>
        </div>

        <!-- Dubai -->
        <div class="destination-card">
            <div class="dest-img-wrap">
                <img class="dest-img" src="https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=600&q=80" alt="Dubai Burj Khalifa">
                <span class="dest-badge">DXB &bull; Dubai</span>
            </div>
            <div class="dest-content">
                <h3 class="dest-title">Dubai, United Arab Emirates</h3>
                <p class="dest-desc">A desert metropolis defined by futuristic skyscrapers, lavish resorts, thrilling desert dunes, and global luxury shopping.</p>
                <div style="font-size: 0.8rem; color: var(--text-dim); margin-bottom: 12px;">
                    <strong>Best Time:</strong> November – March &bull; <strong>Flight Time:</strong> ~6.5h from LHR
                </div>
                <div class="dest-meta">
                    <div>Fares from <strong>₹18,000</strong></div>
                    <a href="<?= base_url('index.php?source=LHR&destination=DXB') ?>" class="btn btn-primary btn-sm book-route-btn">Book Route</a>
                </div>
            </div>
        </div>

        <!-- Tokyo -->
        <div class="destination-card">
            <div class="dest-img-wrap">
                <img class="dest-img" src="https://images.unsplash.com/photo-1503899036084-c55cdd92da26?auto=format&fit=crop&w=600&q=80" alt="Tokyo streets">
                <span class="dest-badge">HND &bull; Tokyo</span>
            </div>
            <div class="dest-content">
                <h3 class="dest-title">Tokyo, Japan</h3>
                <p class="dest-desc">A high-voltage metropolis combining Michelin-starred dining, traditional Shinto shrines, pop culture, and cherry blossoms.</p>
                <div style="font-size: 0.8rem; color: var(--text-dim); margin-bottom: 12px;">
                    <strong>Best Time:</strong> March – May &bull; <strong>Flight Time:</strong> ~7h from SIN
                </div>
                <div class="dest-meta">
                    <div>Fares from <strong>₹49,000</strong></div>
                    <a href="<?= base_url('index.php?source=SIN&destination=HND') ?>" class="btn btn-primary btn-sm book-route-btn">Book Route</a>
                </div>
            </div>
        </div>

        <!-- New York -->
        <div class="destination-card">
            <div class="dest-img-wrap">
                <img class="dest-img" src="https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?auto=format&fit=crop&w=600&q=80" alt="New York City skyline">
                <span class="dest-badge">JFK &bull; New York</span>
            </div>
            <div class="dest-content">
                <h3 class="dest-title">New York, United States</h3>
                <p class="dest-desc">The epicenter of commerce, culture, Broadway theater, Central Park serenity, and energetic round-the-clock streets.</p>
                <div style="font-size: 0.8rem; color: var(--text-dim); margin-bottom: 12px;">
                    <strong>Best Time:</strong> September – November &bull; <strong>Flight Time:</strong> ~8h from CDG
                </div>
                <div class="dest-meta">
                    <div>Fares from <strong>₹46,000</strong></div>
                    <a href="<?= base_url('index.php?source=LHR&destination=JFK') ?>" class="btn btn-primary btn-sm book-route-btn">Book Route</a>
                </div>
            </div>
        </div>

        <!-- Singapore -->
        <div class="destination-card">
            <div class="dest-img-wrap">
                <img class="dest-img" src="https://images.unsplash.com/photo-1525625293386-3f8f99389edd?auto=format&fit=crop&w=600&q=80" alt="Singapore Gardens by the Bay">
                <span class="dest-badge">SIN &bull; Singapore</span>
            </div>
            <div class="dest-content">
                <h3 class="dest-title">Singapore, Southeast Asia</h3>
                <p class="dest-desc">A futuristic Garden City with world-beating airport infrastructure, futuristic supertrees, and melting-pot hawker stalls.</p>
                <div style="font-size: 0.8rem; color: var(--text-dim); margin-bottom: 12px;">
                    <strong>Best Time:</strong> Year-Round &bull; <strong>Flight Time:</strong> ~7.5h from DXB
                </div>
                <div class="dest-meta">
                    <div>Fares from <strong>₹62,000</strong></div>
                    <a href="<?= base_url('index.php?source=DXB&destination=SIN') ?>" class="btn btn-primary btn-sm book-route-btn">Book Route</a>
                </div>
            </div>
        </div>

        <!-- Sydney -->
        <div class="destination-card">
            <div class="dest-img-wrap">
                <img class="dest-img" src="https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?auto=format&fit=crop&w=600&q=80" alt="Sydney Opera House">
                <span class="dest-badge">SYD &bull; Sydney</span>
            </div>
            <div class="dest-content">
                <h3 class="dest-title">Sydney, Australia</h3>
                <p class="dest-desc">Pristine coastal beaches, the architectural wonder of the Sydney Opera House, and breathtaking harbor cruise vistas.</p>
                <div style="font-size: 0.8rem; color: var(--text-dim); margin-bottom: 12px;">
                    <strong>Best Time:</strong> September – November &bull; <strong>Flight Time:</strong> ~9.5h from HND
                </div>
                <div class="dest-meta">
                    <div>Fares from <strong>₹68,000</strong></div>
                    <a href="<?= base_url('index.php?source=SYD&destination=HND') ?>" class="btn btn-primary btn-sm book-route-btn">Book Route</a>
                </div>
            </div>
        </div>

        <!-- Frankfurt -->
        <div class="destination-card">
            <div class="dest-img-wrap">
                <img class="dest-img" src="https://images.unsplash.com/photo-1541432901042-2d8bd64b4a9b?auto=format&fit=crop&w=600&q=80" alt="Frankfurt skyline">
                <span class="dest-badge">FRA &bull; Frankfurt</span>
            </div>
            <div class="dest-content">
                <h3 class="dest-title">Frankfurt, Germany</h3>
                <p class="dest-desc">The financial heartbeat of continental Europe, featuring charming historic Römerberg squares and central European connections.</p>
                <div style="font-size: 0.8rem; color: var(--text-dim); margin-bottom: 12px;">
                    <strong>Best Time:</strong> May – October &bull; <strong>Flight Time:</strong> ~12.5h from SIN
                </div>
                <div class="dest-meta">
                    <div>Fares from <strong>₹73,000</strong></div>
                    <a href="<?= base_url('index.php?source=FRA&destination=SIN') ?>" class="btn btn-primary btn-sm book-route-btn">Book Route</a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
