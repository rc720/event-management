<?php
// Set specific SEO meta fields for the Home Page
$metaTitle = "AuraEvents | Creative Event Design & Portfolio Showcase";
$metaDescription = "Step into a world of curated luxury event design, premium corporate planning, signature weddings, and professional portfolio management.";
$metaKeywords = "luxury weddings, event planning, corporate events, portfolio, creative events, auraevents";

require_once __DIR__ . '/includes/header.php';

// Prepare database statistics counts dynamically
try {
    $statBlogs = $pdo->query("SELECT COUNT(*) FROM `blogs` WHERE `status` = 'active'")->fetchColumn();
    $statEvents = $pdo->query("SELECT COUNT(*) FROM `events` WHERE `status` = 'active'")->fetchColumn();
    $statGallery = $pdo->query("SELECT COUNT(*) FROM `gallery` WHERE `status` = 'active'")->fetchColumn();
    $statTeam = $pdo->query("SELECT COUNT(*) FROM `team_members` WHERE `status` = 'active'")->fetchColumn();
} catch (Exception $e) {
    // Fail-safe default variables if tables are not initialized
    $statBlogs = 0; $statEvents = 0; $statGallery = 0; $statTeam = 0;
}

// Fetch 3 most recent active events
try {
    $stmtEvents = $pdo->query("SELECT * FROM `events` WHERE `status` = 'active' ORDER BY `event_date` DESC, `id` DESC LIMIT 3");
    $recentEvents = $stmtEvents->fetchAll();
} catch (Exception $e) {
    $recentEvents = [];
}

// Fetch 3 most recent active blogs
try {
    $stmtBlogs = $pdo->query("SELECT * FROM `blogs` WHERE `status` = 'active' ORDER BY `created_at` DESC, `id` DESC LIMIT 3");
    $recentBlogs = $stmtBlogs->fetchAll();
} catch (Exception $e) {
    $recentBlogs = [];
}

// Fetch 6 active gallery portfolio items
try {
    $stmtGallery = $pdo->query("SELECT * FROM `gallery` WHERE `status` = 'active' ORDER BY `id` DESC LIMIT 6");
    $recentGallery = $stmtGallery->fetchAll();
} catch (Exception $e) {
    $recentGallery = [];
}
?>

    <!-- 1. PREMIUM HERO SECTION -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 hero-content">
                    <span class="section-subtitle">Visual Spectacles & Experiences</span>
                    <h1 class="hero-title text-white">We Design <span class="text-gradient">Unforgettable</span> Memories</h1>
                    <p class="hero-subtitle">
                        From luxury weddings and high-profile corporate summits to signature private events and grand exhibitions, we bring your grand vision to life with precision and style.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="event.php" class="btn-premium">Explore Events</a>
                        <a href="contact.php" class="btn-premium-outline">Book Consultation</a>
                    </div>
                </div>
                <div class="col-lg-6 position-relative text-center">
                    <!-- Premium Glass Illustration Box -->
                    <div class="glass-panel p-4 d-inline-block position-relative" style="max-width: 480px; transform: rotate(2deg);">
                        <img src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=600&auto=format&fit=crop" class="img-fluid rounded-4 shadow-lg" alt="Creative Event Design" style="aspect-ratio: 4/3; object-fit: cover;">
                        <div class="position-absolute bottom-0 start-0 m-4 p-3 glass-panel d-flex align-items-center gap-3" style="transform: translate(-20px, 10px);">
                            <div class="feature-icon-wrapper m-0 bg-primary"><i class="bi bi-award-fill"></i></div>
                            <div class="text-start">
                                <h6 class="m-0 text-white fw-bold">Award-Winning</h6>
                                <small class="text-muted">Event Agency</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. INTERACTIVE DATABASE STATISTICS COUNT -->
    <section class="section-padding py-5" style="background: rgba(255,255,255,0.01); border-bottom: 1px solid var(--glass-border);">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-counter-item">
                        <div class="stat-number text-gradient"><?php echo number_format($statEvents); ?>+</div>
                        <div class="text-muted fw-bold font-body">Dynamic Events</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-counter-item">
                        <div class="stat-number text-gradient"><?php echo number_format($statBlogs); ?>+</div>
                        <div class="text-muted fw-bold font-body">Blogs & Articles</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-counter-item">
                        <div class="stat-number text-gradient"><?php echo number_format($statGallery); ?>+</div>
                        <div class="text-muted fw-bold font-body">Exquisite Designs</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-counter-item">
                        <div class="stat-number text-gradient"><?php echo number_format($statTeam); ?>+</div>
                        <div class="text-muted fw-bold font-body">Creative Specialists</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. SERVICES PREVIEW SECTION -->
    <section class="section-padding">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-subtitle">What We Do Best</span>
                <h2 class="section-title text-white">Curated Luxury Management Services</h2>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="glass-panel feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-hearts"></i>
                        </div>
                        <h4 class="text-white mb-3">Royal & Luxury Weddings</h4>
                        <p class="text-muted mb-0">
                            We transform your fairy tale vision into a grand reality, handling royal decors, celebrity performers, and premium catering.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="glass-panel feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                        <h4 class="text-white mb-3">Corporate Summits</h4>
                        <p class="text-muted mb-0">
                            Fostering elegant brand positioning. We craft high-profile corporate galas, visual product launches, and interactive international conferences.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="glass-panel feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <h4 class="text-white mb-3">Signature Art Exhibitions</h4>
                        <p class="text-muted mb-0">
                            Perfect balance of aesthetic storytelling and spatial design, managing high-profile galleries, and curated custom showcases.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. DYNAMIC EVENTS SECTION -->
    <section class="section-padding" style="background: rgba(9,9,11,0.5);">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-end mb-5">
                <div>
                    <span class="section-subtitle">Showcase & Calendars</span>
                    <h2 class="section-title text-white m-0">Recent Dynamic Events</h2>
                </div>
                <a href="event.php" class="btn-premium-outline mt-3 mt-sm-0">View All Events</a>
            </div>

            <div class="row g-4">
                <?php if (!empty($recentEvents)): ?>
                    <?php foreach ($recentEvents as $ev): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="dynamic-card">
                                <div class="dynamic-card-img-wrapper">
                                    <div class="event-date-badge">
                                        <?php echo date('d M, Y', strtotime($ev['event_date'])); ?>
                                    </div>
                                    <?php if (!empty($ev['featured_image']) && file_exists(__DIR__ . '/uploads/events/' . $ev['featured_image'])): ?>
                                        <img src="uploads/events/<?php echo htmlspecialchars($ev['featured_image']); ?>" class="dynamic-card-img" alt="<?php echo htmlspecialchars($ev['title']); ?>">
                                    <?php else: ?>
                                        <img src="https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?q=80&w=600&auto=format&fit=crop" class="dynamic-card-img" alt="<?php echo htmlspecialchars($ev['title']); ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="dynamic-card-body">
                                    <div class="card-meta">
                                        <span><i class="bi bi-clock"></i> <?php echo date('h:i A', strtotime($ev['event_time'])); ?></span>
                                        <span><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($ev['location']); ?></span>
                                    </div>
                                    <h4 class="dynamic-card-title text-white">
                                        <a href="event-detail.php?slug=<?php echo htmlspecialchars($ev['slug']); ?>" class="text-white text-decoration-none hover-accent">
                                            <?php echo htmlspecialchars($ev['title']); ?>
                                        </a>
                                    </h4>
                                    <p class="dynamic-card-desc"><?php echo htmlspecialchars($ev['short_description']); ?></p>
                                    <a href="event-detail.php?slug=<?php echo htmlspecialchars($ev['slug']); ?>" class="card-readmore">
                                        Explore Event <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="glass-panel p-5">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <p class="text-muted mt-3 mb-0">No active events scheduled at the moment. Check back soon!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- 5. DYNAMIC GALLERY PORTFOLIO -->
    <section class="section-padding">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-end mb-5">
                <div>
                    <span class="section-subtitle">Visual Portfolio</span>
                    <h2 class="section-title text-white m-0">Exquisite Gallery Portfolio</h2>
                </div>
                <a href="gallery.php" class="btn-premium-outline mt-3 mt-sm-0">Open Grid Gallery</a>
            </div>

            <div class="row g-4">
                <?php if (!empty($recentGallery)): ?>
                    <?php foreach ($recentGallery as $gal): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="gallery-item">
                                <?php if (!empty($gal['featured_image']) && file_exists(__DIR__ . '/uploads/gallery/' . $gal['featured_image'])): ?>
                                    <img src="uploads/gallery/<?php echo htmlspecialchars($gal['featured_image']); ?>" class="gallery-img" alt="<?php echo htmlspecialchars($gal['title']); ?>">
                                <?php else: ?>
                                    <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=600&auto=format&fit=crop" class="gallery-img" alt="<?php echo htmlspecialchars($gal['title']); ?>">
                                <?php endif; ?>
                                <div class="gallery-overlay">
                                    <h4 class="text-white fw-bold m-0"><?php echo htmlspecialchars($gal['title']); ?></h4>
                                    <p class="text-muted small m-0 mt-1">Creative Showcase</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="glass-panel p-5">
                            <i class="bi bi-images fs-1 text-muted"></i>
                            <p class="text-muted mt-3 mb-0">Our creative design team is building a beautiful portfolio. Stay tuned!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- 6. DYNAMIC BLOGS & ARTICLES SECTION -->
    <section class="section-padding" style="background: rgba(9,9,11,0.5);">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-end mb-5">
                <div>
                    <span class="section-subtitle">Blogs & Media</span>
                    <h2 class="section-title text-white m-0">Recent Dynamic Insights</h2>
                </div>
                <a href="blog.php" class="btn-premium-outline mt-3 mt-sm-0">Read All Articles</a>
            </div>

            <div class="row g-4">
                <?php if (!empty($recentBlogs)): ?>
                    <?php foreach ($recentBlogs as $bl): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="dynamic-card">
                                <div class="dynamic-card-img-wrapper">
                                    <?php if (!empty($bl['featured_image']) && file_exists(__DIR__ . '/uploads/blogs/' . $bl['featured_image'])): ?>
                                        <img src="uploads/blogs/<?php echo htmlspecialchars($bl['featured_image']); ?>" class="dynamic-card-img" alt="<?php echo htmlspecialchars($bl['title']); ?>">
                                    <?php else: ?>
                                        <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=600&auto=format&fit=crop" class="dynamic-card-img" alt="<?php echo htmlspecialchars($bl['title']); ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="dynamic-card-body">
                                    <div class="card-meta">
                                        <span><i class="bi bi-calendar3"></i> <?php echo date('d M, Y', strtotime($bl['created_at'])); ?></span>
                                        <span><i class="bi bi-tag-fill"></i> Insight</span>
                                    </div>
                                    <h4 class="dynamic-card-title text-white">
                                        <a href="blog-detail.php?slug=<?php echo htmlspecialchars($bl['slug']); ?>" class="text-white text-decoration-none hover-accent">
                                            <?php echo htmlspecialchars($bl['title']); ?>
                                        </a>
                                    </h4>
                                    <p class="dynamic-card-desc"><?php echo htmlspecialchars($bl['short_description']); ?></p>
                                    <a href="blog-detail.php?slug=<?php echo htmlspecialchars($bl['slug']); ?>" class="card-readmore">
                                        Read Article <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="glass-panel p-5">
                            <i class="bi bi-newspaper fs-1 text-muted"></i>
                            <p class="text-muted mt-3 mb-0">No active blog posts available. We are drafting beautiful insights soon.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
