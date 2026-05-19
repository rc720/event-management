<?php
// Retrieve system settings
$settings = getSystemSettings();
?>
    <!-- Reusable Premium Footer Section -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                
                <!-- Brand Profile -->
                <div class="col-lg-4 col-md-6">
                    <a class="d-flex align-items-center mb-4 text-decoration-none" href="index.php">
                        <?php if (!empty($settings['logo']) && file_exists(__DIR__ . '/../uploads/logo/' . $settings['logo'])): ?>
                            <img src="uploads/logo/<?php echo htmlspecialchars($settings['logo']); ?>" alt="AuraEvents Logo" style="height: 45px;">
                        <?php else: ?>
                            <span class="fs-3 fw-bold text-gradient" style="font-family: var(--font-heading);">AuraEvents</span>
                        <?php endif; ?>
                    </a>
                    <p class="footer-desc">
                        Designing visual spectacles and signature events. We craft high-profile corporate conferences, luxury weddings, and engaging custom exhibitions.
                    </p>
                    
                    <!-- Social Media Links -->
                    <div class="d-flex gap-2 mt-3">
                        <?php if ($settings['facebook_enable'] && !empty($settings['facebook_link'])): ?>
                            <a href="<?php echo htmlspecialchars($settings['facebook_link']); ?>" target="_blank" class="team-social-icon" aria-label="Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($settings['twitter_enable'] && !empty($settings['twitter_link'])): ?>
                            <a href="<?php echo htmlspecialchars($settings['twitter_link']); ?>" target="_blank" class="team-social-icon" aria-label="Twitter">
                                <i class="bi bi-twitter"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($settings['instagram_enable'] && !empty($settings['instagram_link'])): ?>
                            <a href="<?php echo htmlspecialchars($settings['instagram_link']); ?>" target="_blank" class="team-social-icon" aria-label="Instagram">
                                <i class="bi bi-instagram"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($settings['linkedin_enable'] && !empty($settings['linkedin_link'])): ?>
                            <a href="<?php echo htmlspecialchars($settings['linkedin_link']); ?>" target="_blank" class="team-social-icon" aria-label="LinkedIn">
                                <i class="bi bi-linkedin"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Quick Navigation Links -->
                <div class="col-lg-2 col-md-6">
                    <h5 class="text-white mb-4" style="font-family: var(--font-body); font-weight:600;">Navigation</h5>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="services.php">Our Services</a></li>
                        <li><a href="event.php">Recent Events</a></li>
                        <li><a href="blog.php">Blogs & News</a></li>
                    </ul>
                </div>
                
                <!-- Legal Links -->
                <div class="col-lg-2 col-md-6">
                    <h5 class="text-white mb-4" style="font-family: var(--font-body); font-weight:600;">Legal</h5>
                    <ul class="footer-links">
                        <li><a href="gallery.php">Gallery Portfolio</a></li>
                        <li><a href="contact.php">Contact Support</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="admin/login.php">Admin Login</a></li>
                    </ul>
                </div>
                
                <!-- Dynamic Studio Information -->
                <div class="col-lg-4 col-md-6">
                    <h5 class="text-white mb-4" style="font-family: var(--font-body); font-weight:600;">Contact Studio</h5>
                    <ul class="footer-links text-muted" style="padding-left: 0;">
                        <li class="d-flex align-items-start gap-2 mb-3">
                            <i class="bi bi-geo-alt text-gradient fs-5 mt-1"></i>
                            <span><?php echo nl2br(htmlspecialchars($settings['address'])); ?></span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-telephone text-gradient fs-5"></i>
                            <span><?php echo htmlspecialchars($settings['phone']); ?></span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-envelope text-gradient fs-5"></i>
                            <span><?php echo htmlspecialchars($settings['email']); ?></span>
                        </li>
                    </ul>
                </div>

            </div>
            
            <!-- Copyright Disclaimer -->
            <div class="footer-bottom text-center">
                <p class="m-0">&copy; <?php echo date('Y'); ?> AuraEvents. All rights reserved. Built with elegance and state-of-the-art Core PHP.</p>
            </div>
        </div>
    </footer>

    <!-- jQuery Library (Production ready CDN) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    
    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom Frontend Main JS -->
    <script src="assets/js/main.js"></script>
</body>
</html>
