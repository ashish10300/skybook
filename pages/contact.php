<?php
/**
 * SkyBook - Contact & Global Support
 */
$pageTitle = 'Contact SkyBook Flight Support';
require_once dirname(__DIR__) . '/includes/header.php';

$formSubmitted = false;
$feedbackMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senderName = sanitize($_POST['sender_name'] ?? '');
    $senderEmail = sanitize($_POST['sender_email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (!empty($senderName) && !empty($senderEmail) && !empty($message)) {
        // Simulate processing without a database insertion
        $formSubmitted = true;
        $feedbackMsg = "Thank you, {$senderName}. Your message regarding '{$subject}' has been received and saved. Our flight operations team will respond to {$senderEmail} within 2 hours.";
    }
}
?>

<section class="hero-section" style="min-height: auto; padding-top: 6rem; padding-bottom: 5rem; background-image: linear-gradient(to bottom, rgba(7, 10, 18, 0.4), rgba(7, 10, 18, 1)), url('../assets/img/tarmac.jpg');">
    <div class="container">
        <!-- Headline -->
        <div style="text-align: center; max-width: 720px; margin: 0 auto;">
            <span class="hero-tag">24/7 Operations Desk</span>
            <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">We Are Here to Assist Your Journey</h1>
            <p class="lead">
                Whether you need assistance with booking amendments, draft inquiries, or corporate aviation reservations, our international support desk is available around the clock.
            </p>
        </div>
    </div>
</section>

<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">

    <!-- Contact Grid -->
    <div class="contact-grid">
        
        <!-- Left: Contact Details Cards -->
        <div>
            <div class="form-card" style="margin-bottom: 1.5rem;">
                <h3 style="margin-bottom: 1.25rem;">Global Headquarters</h3>
                
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div style="display: flex; gap: 14px; align-items: flex-start;">
                        <div class="feature-icon" style="width: 40px; height: 40px; margin: 0; font-size: 1.1rem;">📍</div>
                        <div>
                            <strong style="display:block; color:#fff; font-size: 0.95rem;">Operations Center</strong>
                            <span style="color: var(--text-muted); font-size: 0.875rem; line-height: 1.6;">
                                SkyBook Aviation Tower, Terminal Boulevard<br>
                                Suite 1200, Queens, New York 11430, USA
                            </span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 14px; align-items: flex-start;">
                        <div class="feature-icon" style="width: 40px; height: 40px; margin: 0; font-size: 1.1rem;">📞</div>
                        <div>
                            <strong style="display:block; color:#fff; font-size: 0.95rem;">Direct Telephone</strong>
                            <span style="color: var(--text-muted); font-size: 0.875rem;">
                                +1 (800) 555-SKYBOOK &bull; +44 20 7946 0912
                            </span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 14px; align-items: flex-start;">
                        <div class="feature-icon" style="width: 40px; height: 40px; margin: 0; font-size: 1.1rem;">✉️</div>
                        <div>
                            <strong style="display:block; color:#fff; font-size: 0.95rem;">Email Inquiries</strong>
                            <span style="color: var(--text-muted); font-size: 0.875rem;">
                                support@skybook.com &bull; concierge@skybook.com
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Operating Hours & SLA -->
            <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.5rem;">
                <h4 style="margin-bottom: 10px; font-size: 1rem; color: var(--accent);">Flight Operations SLA</h4>
                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin: 0;">
                    Passenger support is maintained 24 hours a day, 365 days a year. E-tickets and booking confirmations are generated immediately upon database payment confirmation.
                </p>
            </div>
        </div>

        <!-- Right: Interactive Contact Form UI -->
        <div class="form-card">
            <h3 style="margin-bottom: 0.5rem;">Send an Inquiry</h3>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1.5rem;">
                Complete the dispatch form below and an aviation specialist will respond promptly.
            </p>

            <?php if ($formSubmitted): ?>
                <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                    <span><?= $feedbackMsg ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="contact-name">Full Name *</label>
                        <input type="text" id="contact-name" name="sender_name" class="form-control" placeholder="e.g. Captain James Kirk" required value="<?= htmlspecialchars($currentUser['full_name'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact-email">Email Address *</label>
                        <input type="email" id="contact-email" name="sender_email" class="form-control" placeholder="e.g. james@starfleet.org" required value="<?= htmlspecialchars($currentUser['email'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="contact-subject">Topic / Subject</label>
                    <select id="contact-subject" name="subject" class="form-control">
                        <option value="General Flight Inquiry">General Flight Inquiry</option>
                        <option value="Booking & Draft Assistance">Booking &amp; Draft Assistance</option>
                        <option value="E-Ticket Reissuance">E-Ticket Reissuance</option>
                        <option value="Group & Corporate Travel">Group &amp; Corporate Travel</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="contact-message">Your Message *</label>
                    <textarea id="contact-message" name="message" class="form-control" rows="5" placeholder="Detail your travel query or booking reference..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Transmit Message &rarr;
                </button>
            </form>
        </div>

    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
