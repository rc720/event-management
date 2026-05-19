<?php
$metaTitle = "Services | Luxury Event Management & Spatial Designs";
$metaDescription = "Explore the full suite of premier management solutions: Royal Weddings, high-end Corporate Summits, Live Concerts, and Art Galleries.";
$metaKeywords = "event services, wedding coordination, sound setups, corporate meetings";

require_once __DIR__ . '/includes/header.php';
?>

    <!-- Services Hero Banner -->
    <section class="detail-hero">
        <img src="https://images.unsplash.com/photo-1478812954026-9c750f0e89fc?q=80&w=1200&auto=format&fit=crop" class="detail-hero-img" alt="Our Services Banner">
        <div class="detail-hero-overlay"></div>
        <div class="container detail-hero-content">
            <span class="section-subtitle">What We Offer</span>
            <h1 class="display-3 fw-bold text-white mb-0" style="font-family: var(--font-heading);">Our Services</h1>
        </div>
    </section>

    <!-- Services Dynamic Listing Grid -->
    <section class="section-padding">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-subtitle">Exquisite Craftsmanship</span>
                <h2 class="section-title text-white">Full-Scale Premium Services</h2>
            </div>
            
            <div class="row g-4">
                
                <!-- Service 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="glass-panel feature-card">
                        <div class="feature-icon-wrapper"><i class="bi bi-hearts"></i></div>
                        <h4 class="text-white mb-3">Royal Weddings</h4>
                        <p class="text-muted">
                            Complete wedding styling and orchestration. We curate luxury venues, manage elite list coordination, floral designs, stage engineering, and catering.
                        </p>
                    </div>
                </div>
                
                <!-- Service 2 -->
                <div class="col-lg-4 col-md-6">
                    <div class="glass-panel feature-card">
                        <div class="feature-icon-wrapper"><i class="bi bi-award"></i></div>
                        <h4 class="text-white mb-3">Corporate Galas & Awards</h4>
                        <p class="text-muted">
                            Creating state-of-the-art experiences for global brands. Stage design, high-end AV management, visual projection mapping, and dinner coordination.
                        </p>
                    </div>
                </div>
                
                <!-- Service 3 -->
                <div class="col-lg-4 col-md-6">
                    <div class="glass-panel feature-card">
                        <div class="feature-icon-wrapper"><i class="bi bi-calendar3"></i></div>
                        <h4 class="text-white mb-3">Art Showcases & Galleries</h4>
                        <p class="text-muted">
                            Dynamic setups for luxury visual arts. We manage delicate layout parameters, lighting, and ambient styling to best showcase standard designs.
                        </p>
                    </div>
                </div>
                
                <!-- Service 4 -->
                <div class="col-lg-4 col-md-6">
                    <div class="glass-panel feature-card">
                        <div class="feature-icon-wrapper"><i class="bi bi-music-note-beamed"></i></div>
                        <h4 class="text-white mb-3">Concerts & Fashion Shows</h4>
                        <p class="text-muted">
                            Massive stadium acoustics, crowd control, and runway logistics. We build specialized stages, backstage systems, and advanced light fixtures.
                        </p>
                    </div>
                </div>
                
                <!-- Service 5 -->
                <div class="col-lg-4 col-md-6">
                    <div class="glass-panel feature-card">
                        <div class="feature-icon-wrapper"><i class="bi bi-people-fill"></i></div>
                        <h4 class="text-white mb-3">VIP Private Celebrations</h4>
                        <p class="text-muted">
                            Strictly confidential and highly customized private experiences. Bespoke themes, Michelin-star catering coordination, and security protocols.
                        </p>
                    </div>
                </div>
                
                <!-- Service 6 -->
                <div class="col-lg-4 col-md-6">
                    <div class="glass-panel feature-card">
                        <div class="feature-icon-wrapper"><i class="bi bi-display"></i></div>
                        <h4 class="text-white mb-3">Digital & Virtual Events</h4>
                        <p class="text-muted">
                            Hybrid meeting integrations. Global broadcast-quality streams, virtual green room settings, dynamic remote user setups, and interactive panels.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
