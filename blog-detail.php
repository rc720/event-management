<?php
require_once __DIR__ . '/config/db.php';

// Safe query based on slug parameter
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$blog = null;

if (!empty($slug)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM `blogs` WHERE `slug` = :slug AND `status` = 'active' LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $blog = $stmt->fetch();
    } catch (Exception $e) {
        $blog = null;
    }
}

// Redirect if blog is not found
if (!$blog) {
    header("Location: blog.php");
    exit;
}

// Map SEO details dynamically before header inclusion
$metaTitle = !empty($blog['meta_title']) ? $blog['meta_title'] : $blog['title'] . " | AuraEvents Insights";
$metaKeywords = !empty($blog['meta_keywords']) ? $blog['meta_keywords'] : "blog, visual design, wedding trend, " . $blog['title'];
$metaDescription = !empty($blog['meta_description']) ? $blog['meta_description'] : $blog['short_description'];

require_once __DIR__ . '/includes/header.php';
?>

    <!-- Blog Detail Hero Banner -->
    <section class="detail-hero">
        <?php if (!empty($blog['featured_image']) && file_exists(__DIR__ . '/uploads/blogs/' . $blog['featured_image'])): ?>
            <img src="uploads/blogs/<?php echo htmlspecialchars($blog['featured_image']); ?>" class="detail-hero-img" alt="<?php echo htmlspecialchars($blog['title']); ?>">
        <?php else: ?>
            <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=1200&auto=format&fit=crop" class="detail-hero-img" alt="<?php echo htmlspecialchars($blog['title']); ?>">
        <?php endif; ?>
        
        <div class="detail-hero-overlay"></div>
        <div class="container detail-hero-content">
            <span class="section-subtitle">Insights</span>
            <h1 class="display-3 fw-bold text-white mb-0" style="font-family: var(--font-heading);"><?php echo htmlspecialchars($blog['title']); ?></h1>
        </div>
    </section>

    <!-- Main Content Reader -->
    <section class="section-padding">
        <div class="container">
            <div class="row g-5">
                
                <!-- Blog Description Column -->
                <div class="col-lg-8">
                    <div class="glass-panel p-4 p-md-5 mb-4">
                        <div class="card-meta mb-4 pb-3 border-bottom border-secondary">
                            <span><i class="bi bi-calendar3"></i> Published: <?php echo date('M d, Y', strtotime($blog['created_at'])); ?></span>
                            <span><i class="bi bi-person-circle"></i> Authored by: Admin</span>
                        </div>
                        
                        <!-- Rich CKEditor HTML description rendering -->
                        <div class="text-muted leading-relaxed" style="font-size: 1.05rem;">
                            <?php echo $blog['full_description']; ?>
                        </div>
                    </div>
                    
                    <a href="blog.php" class="btn-premium-outline"><i class="bi bi-arrow-left"></i> Back to Blogs</a>
                </div>
                
                <!-- Side Panel (Recent Posts) -->
                <div class="col-lg-4">
                    <div class="glass-panel p-4 position-sticky" style="top: 100px;">
                        <h4 class="text-white mb-4" style="font-family: var(--font-heading);">Recent Articles</h4>
                        
                        <?php
                        try {
                            $stmtRec = $pdo->prepare("SELECT * FROM `blogs` WHERE `status` = 'active' AND `id` != :curr_id ORDER BY `id` DESC LIMIT 3");
                            $stmtRec->execute(['curr_id' => $blog['id']]);
                            $recentBlogsList = $stmtRec->fetchAll();
                        } catch (Exception $e) {
                            $recentBlogsList = [];
                        }
                        ?>
                        
                        <?php if (!empty($recentBlogsList)): ?>
                            <?php foreach ($recentBlogsList as $rb): ?>
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div style="width: 70px; height: 50px; flex-shrink: 0; border-radius: 8px; overflow: hidden;">
                                        <?php if (!empty($rb['featured_image']) && file_exists(__DIR__ . '/uploads/blogs/' . $rb['featured_image'])): ?>
                                            <img src="uploads/blogs/<?php echo htmlspecialchars($rb['featured_image']); ?>" style="width:100%; height:100%; object-fit:cover;" alt="">
                                        <?php else: ?>
                                            <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=150&auto=format&fit=crop" style="width:100%; height:100%; object-fit:cover;" alt="">
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <h6 class="m-0 font-body fw-bold" style="font-size:0.9rem; line-height: 1.2;">
                                            <a href="blog-detail.php?slug=<?php echo htmlspecialchars($rb['slug']); ?>" class="text-white text-decoration-none hover-accent">
                                                <?php echo htmlspecialchars($rb['title']); ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted"><?php echo date('M d, Y', strtotime($rb['created_at'])); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted small m-0">No other recent articles available.</p>
                        <?php endif; ?>

                        <div class="mt-4 pt-3 border-top border-secondary">
                            <h6 class="text-white mb-3 font-body fw-bold">Share Article</h6>
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
