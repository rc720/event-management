<?php
$metaTitle = "Aesthetic Gallery & Design Portfolio | AuraEvents Studio";
$metaDescription = "Step inside our visual event archives. Browse high-resolution WebP showcases of royal weddings, luxury corporate galas, and concert designs.";
$metaKeywords = "event gallery, event portfolio, design photos, luxury event archives";

require_once __DIR__ . '/includes/header.php';

// Fetch active gallery items
try {
    $stmt = $pdo->query("SELECT * FROM `gallery` WHERE `status` = 'active' ORDER BY `id` DESC");
    $galleryList = $stmt->fetchAll();
} catch (Exception $e) {
    $galleryList = [];
}
?>

    <!-- Gallery List Hero Banner -->
    <section class="detail-hero">
        <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=1200&auto=format&fit=crop" class="detail-hero-img" alt="Gallery Showcase Banner">
        <div class="detail-hero-overlay"></div>
        <div class="container detail-hero-content">
            <span class="section-subtitle">Portfolio</span>
            <h1 class="display-3 fw-bold text-white mb-0" style="font-family: var(--font-heading);">Exquisite Portfolio</h1>
        </div>
    </section>

    <!-- Gallery Portfolio Grid -->
    <section class="section-padding">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-subtitle">Our Work Speak For Itself</span>
                <h2 class="section-title text-white">Visual Design Showcase</h2>
            </div>
            
            <div class="row g-4">
                
                <?php if (!empty($galleryList)): ?>
                    <?php foreach ($galleryList as $gal): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="gallery-item">
                                <?php if (!empty($gal['featured_image']) && file_exists(__DIR__ . '/uploads/gallery/' . $gal['featured_image'])): ?>
                                    <img src="uploads/gallery/<?php echo htmlspecialchars($gal['featured_image']); ?>" class="gallery-img" alt="<?php echo htmlspecialchars($gal['title']); ?>">
                                <?php else: ?>
                                    <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=600&auto=format&fit=crop" class="gallery-img" alt="<?php echo htmlspecialchars($gal['title']); ?>">
                                <?php endif; ?>
                                <div class="gallery-overlay">
                                    <h4 class="text-white fw-bold m-0"><?php echo htmlspecialchars($gal['title']); ?></h4>
                                    <p class="text-muted small m-0 mt-1">Bespoke Design Portfolio</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="glass-panel p-5">
                            <i class="bi bi-images fs-1 text-muted"></i>
                            <p class="text-muted mt-3 mb-0">No active gallery portfolio items uploaded yet. Check back soon!</p>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
