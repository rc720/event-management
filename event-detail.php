<?php
require_once __DIR__ . '/config/db.php';

// Safe query based on slug parameter
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$event = null;

if (!empty($slug)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM `events` WHERE `slug` = :slug AND `status` = 'active' LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $event = $stmt->fetch();
    } catch (Exception $e) {
        $event = null;
    }
}

// Redirect if event is not found
if (!$event) {
    header("Location: event.php");
    exit;
}

// Set SEO metadata parameters dynamically before header is included
$metaTitle = !empty($event['meta_title']) ? $event['meta_title'] : $event['title'] . " | AuraEvents Dynamic Showcase";
$metaKeywords = !empty($event['meta_keywords']) ? $event['meta_keywords'] : "event, luxury, design, portfolio, " . $event['title'];
$metaDescription = !empty($event['meta_description']) ? $event['meta_description'] : $event['short_description'];

require_once __DIR__ . '/includes/header.php';
?>

    <!-- Event Detail Hero Banner -->
    <section class="detail-hero">
        <?php if (!empty($event['featured_image']) && file_exists(__DIR__ . '/uploads/events/' . $event['featured_image'])): ?>
            <img src="uploads/events/<?php echo htmlspecialchars($event['featured_image']); ?>" class="detail-hero-img" alt="<?php echo htmlspecialchars($event['title']); ?>">
        <?php else: ?>
            <img src="https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=1200&auto=format&fit=crop" class="detail-hero-img" alt="<?php echo htmlspecialchars($event['title']); ?>">
        <?php endif; ?>
        
        <div class="detail-hero-overlay"></div>
        <div class="container detail-hero-content">
            <span class="section-subtitle">Exquisite Showcase</span>
            <h1 class="display-3 fw-bold text-white mb-0" style="font-family: var(--font-heading);"><?php echo htmlspecialchars($event['title']); ?></h1>
        </div>
    </section>

    <!-- Detailed Content View -->
    <section class="section-padding">
        <div class="container">
            <div class="row g-5">
                
                <!-- Main Description Column -->
                <div class="col-lg-8">
                    <div class="glass-panel p-4 p-md-5 mb-4">
                        <h2 class="text-white mb-4" style="font-family: var(--font-heading);">Event Overview</h2>
                        
                        <!-- Rich CKEditor HTML description rendering -->
                        <div class="text-muted leading-relaxed" style="font-size: 1.05rem;">
                            <?php echo $event['full_description']; ?>
                        </div>
                    </div>
                    
                    <a href="event.php" class="btn-premium-outline"><i class="bi bi-arrow-left"></i> Back to Events</a>
                </div>
                
                <!-- Event Meta Sidebar -->
                <div class="col-lg-4">
                    <div class="glass-panel p-4 position-sticky" style="top: 100px;">
                        <h4 class="text-white mb-4" style="font-family: var(--font-heading);">Event Highlights</h4>
                        
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="feature-icon-wrapper m-0 bg-primary"><i class="bi bi-calendar-event"></i></div>
                            <div>
                                <h6 class="text-white m-0 font-body fw-bold">Date</h6>
                                <small class="text-muted"><?php echo date('F d, Y', strtotime($event['event_date'])); ?></small>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="feature-icon-wrapper m-0 bg-primary"><i class="bi bi-clock"></i></div>
                            <div>
                                <h6 class="text-white m-0 font-body fw-bold">Time</h6>
                                <small class="text-muted"><?php echo date('h:i A', strtotime($event['event_time'])); ?></small>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="feature-icon-wrapper m-0 bg-primary"><i class="bi bi-geo-alt"></i></div>
                            <div>
                                <h6 class="text-white m-0 font-body fw-bold">Location Venue</h6>
                                <small class="text-muted"><?php echo htmlspecialchars($event['location']); ?></small>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-3 border-top border-secondary">
                            <h6 class="text-white mb-3 font-body fw-bold">Share Showcase</h6>
                            <div class="d-flex gap-2">
                                <a href="https://facebook.com" target="_blank" class="team-social-icon"><i class="bi bi-facebook"></i></a>
                                <a href="https://twitter.com" target="_blank" class="team-social-icon"><i class="bi bi-twitter"></i></a>
                                <a href="https://linkedin.com" target="_blank" class="team-social-icon"><i class="bi bi-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
