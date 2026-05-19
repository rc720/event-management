<?php
$metaTitle = "About Us | Creative Specialist Event Planners";
$metaDescription = "Discover the team of visual artists, logistic masterminds, and creative managers behind AuraEvents.";
$metaKeywords = "about us, creative team, event managers, wedding planners";

require_once __DIR__ . '/includes/header.php';

// Fetch active team members
try {
    $stmtTeam = $pdo->query("SELECT * FROM `team_members` WHERE `status` = 'active' ORDER BY `id` ASC");
    $teamMembers = $stmtTeam->fetchAll();
} catch (Exception $e) {
    $teamMembers = [];
}
?>

    <!-- About Detail Hero Banner -->
    <section class="detail-hero">
        <img src="https://images.unsplash.com/photo-1469371670807-013ccf25f16a?q=80&w=1200&auto=format&fit=crop" class="detail-hero-img" alt="About AuraEvents Banner">
        <div class="detail-hero-overlay"></div>
        <div class="container detail-hero-content">
            <span class="section-subtitle">Our Journey</span>
            <h1 class="display-3 fw-bold text-white mb-0" style="font-family: var(--font-heading);">About AuraEvents</h1>
        </div>
    </section>

    <!-- Detailed Company Story -->
    <section class="section-padding">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <span class="section-subtitle">The Visionaries</span>
                    <h2 class="text-white mb-4" style="font-size:2.5rem;">We craft visual stories and immersive atmospheres.</h2>
                    <p class="text-muted mb-4">
                        Founded in 2018, AuraEvents has risen to become one of the premier event planning and signature portfolio management studios in the region. We believe that an event is not just a gathering; it is a canvas of storytelling, lighting, music, and spatial choreography.
                    </p>
                    <p class="text-muted mb-4">
                        Our master design team manages everything, starting from initial blueprints, visual rendering, location scouting, global vendor contracts, and physical build, ensuring that your dreams are executed down to the smallest detail.
                    </p>
                    
                    <div class="row g-4 mt-2">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="feature-icon-wrapper m-0 bg-primary"><i class="bi bi-star-fill"></i></div>
                                <div>
                                    <h6 class="text-white fw-bold m-0 font-body">Elite Portfolio</h6>
                                    <small class="text-muted">Over 250+ events</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="feature-icon-wrapper m-0 bg-primary"><i class="bi bi-shield-check"></i></div>
                                <div>
                                    <h6 class="text-white fw-bold m-0 font-body">100% Reliable</h6>
                                    <small class="text-muted">Certified planners</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="glass-panel p-2 rounded-4">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=800&auto=format&fit=crop" class="img-fluid rounded-4 shadow-lg" alt="Team meeting">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dynamic Team Section -->
    <section class="section-padding" style="background: rgba(9,9,11,0.5);">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-subtitle">Creative Minds</span>
                <h2 class="section-title text-white">Our Creative Specialists</h2>
            </div>
            
            <div class="row g-4 justify-content-center">
                <?php if (!empty($teamMembers)): ?>
                    <?php foreach ($teamMembers as $tm): ?>
                        <div class="col-lg-3 col-md-6">
                            <div class="glass-panel team-card">
                                <div class="team-img-wrapper">
                                    <?php if (!empty($tm['featured_image']) && file_exists(__DIR__ . '/uploads/team/' . $tm['featured_image'])): ?>
                                        <img src="uploads/team/<?php echo htmlspecialchars($tm['featured_image']); ?>" class="team-img" alt="<?php echo htmlspecialchars($tm['name']); ?>">
                                    <?php else: ?>
                                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=300&auto=format&fit=crop" class="team-img" alt="<?php echo htmlspecialchars($tm['name']); ?>">
                                    <?php endif; ?>
                                </div>
                                <h4 class="text-white mb-1"><?php echo htmlspecialchars($tm['name']); ?></h4>
                                <div class="team-designation"><?php echo htmlspecialchars($tm['designation']); ?></div>
                                <div class="text-muted small mb-3"><?php echo strip_tags($tm['description']); ?></div>
                                
                                <!-- Social Media Links -->
                                <div class="team-socials">
                                    <?php if (!empty($tm['facebook'])): ?>
                                        <a href="<?php echo htmlspecialchars($tm['facebook']); ?>" target="_blank" class="team-social-icon"><i class="bi bi-facebook"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($tm['twitter'])): ?>
                                        <a href="<?php echo htmlspecialchars($tm['twitter']); ?>" target="_blank" class="team-social-icon"><i class="bi bi-twitter"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($tm['instagram'])): ?>
                                        <a href="<?php echo htmlspecialchars($tm['instagram']); ?>" target="_blank" class="team-social-icon"><i class="bi bi-instagram"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($tm['linkedin'])): ?>
                                        <a href="<?php echo htmlspecialchars($tm['linkedin']); ?>" target="_blank" class="team-social-icon"><i class="bi bi-linkedin"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback default team if database is empty -->
                    <div class="col-lg-3 col-md-6">
                        <div class="glass-panel team-card">
                            <div class="team-img-wrapper">
                                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=300&auto=format&fit=crop" class="team-img" alt="Sarah Jenkins">
                            </div>
                            <h4 class="text-white mb-1">Sarah Jenkins</h4>
                            <div class="team-designation">Founder & Director</div>
                            <p class="text-muted small">Passionate creator with 10+ years in design and spatial art management.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="glass-panel team-card">
                            <div class="team-img-wrapper">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=300&auto=format&fit=crop" class="team-img" alt="Marcus Sterling">
                            </div>
                            <h4 class="text-white mb-1">Marcus Sterling</h4>
                            <div class="team-designation">Operations Chief</div>
                            <p class="text-muted small">Logistical expert with mastery in massive crowds and event flow setups.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
