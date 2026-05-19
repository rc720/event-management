<?php
$metaTitle = "Blogs & Industry Insights | AuraEvents Planners";
$metaDescription = "Stay up to date with the latest visual design ideas, wedding styling trends, event management checklists, and professional portfolios.";
$metaKeywords = "event blog, design articles, wedding trends, corporate management ideas";

require_once __DIR__ . '/includes/header.php';

// Pagination setup
$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

try {
    // Total count of active blogs
    $totalCount = $pdo->query("SELECT COUNT(*) FROM `blogs` WHERE `status` = 'active'")->fetchColumn();
    $totalPages = ceil($totalCount / $limit);
    if ($totalPages < 1) $totalPages = 1;
    if ($page > $totalPages) $page = $totalPages;

    // Fetch paginated blogs
    $stmt = $pdo->prepare("SELECT * FROM `blogs` WHERE `status` = 'active' ORDER BY `created_at` DESC, `id` DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $blogsList = $stmt->fetchAll();
} catch (Exception $e) {
    $blogsList = [];
    $totalPages = 1;
}
?>

    <!-- Blogs List Hero -->
    <section class="detail-hero">
        <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=1200&auto=format&fit=crop" class="detail-hero-img" alt="Blogs Insights Banner">
        <div class="detail-hero-overlay"></div>
        <div class="container detail-hero-content">
            <span class="section-subtitle">Insights</span>
            <h1 class="display-3 fw-bold text-white mb-0" style="font-family: var(--font-heading);">Blogs & News</h1>
        </div>
    </section>

    <!-- Paginated Blogs Grid -->
    <section class="section-padding">
        <div class="container">
            <div class="row g-4">
                
                <?php if (!empty($blogsList)): ?>
                    <?php foreach ($blogsList as $bl): ?>
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
                                        <span><i class="bi bi-person-circle"></i> Admin</span>
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
                            <i class="bi bi-journal-x fs-1 text-muted"></i>
                            <p class="text-muted mt-3 mb-0">No active blogs available at the moment.</p>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Frontend Pagination Controls -->
            <?php if ($totalPages > 1): ?>
                <div class="row mt-5">
                    <div class="col-12 d-flex justify-content-center">
                        <nav aria-label="Blogs Pagination">
                            <ul class="pagination pagination-custom m-0">
                                
                                <!-- Prev Link -->
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                                
                                <!-- Numeric Links -->
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <!-- Next Link -->
                                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>

                            </ul>
                        </nav>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
