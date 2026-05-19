<?php
$metaTitle = "Contact Us | Book Your Elite Consultation Session";
$metaDescription = "Reach out to our creative planners. Submit your enquiries for luxury weddings, corporate events, or custom spatial designs.";
$metaKeywords = "contact us, book wedding, event planner email, address";

require_once __DIR__ . '/includes/header.php';
?>

    <!-- Contact Hero Banner -->
    <section class="detail-hero">
        <img src="https://images.unsplash.com/photo-1534536281715-e28d76689b4d?q=80&w=1200&auto=format&fit=crop" class="detail-hero-img" alt="Contact Us Banner">
        <div class="detail-hero-overlay"></div>
        <div class="container detail-hero-content">
            <span class="section-subtitle">Get In Touch</span>
            <h1 class="display-3 fw-bold text-white mb-0" style="font-family: var(--font-heading);">Contact Us</h1>
        </div>
    </section>

    <!-- Interactive Contact Channels and Forms -->
    <section class="section-padding">
        <div class="container">
            <div class="row g-5">
                
                <!-- Contact Information Column -->
                <div class="col-lg-5">
                    <span class="section-subtitle">Reach Out</span>
                    <h2 class="text-white mb-4" style="font-family: var(--font-heading);">Let's Plan Your Next Showcase</h2>
                    <p class="text-muted mb-5">
                        Have a grand project or customized private celebration in mind? Contact our planning studio directly. Our design experts will revert within 24 business hours.
                    </p>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <h5 class="text-white font-body fw-bold mb-1">Our Studio Address</h5>
                            <span class="text-muted"><?php echo nl2br(htmlspecialchars($settings['address'])); ?></span>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="bi bi-telephone"></i></div>
                        <div>
                            <h5 class="text-white font-body fw-bold mb-1">Direct Phone Lines</h5>
                            <a href="tel:<?php echo htmlspecialchars($settings['phone']); ?>" class="text-muted text-decoration-none"><?php echo htmlspecialchars($settings['phone']); ?></a>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="bi bi-envelope"></i></div>
                        <div>
                            <h5 class="text-white font-body fw-bold mb-1">General Inquiries</h5>
                            <a href="mailto:<?php echo htmlspecialchars($settings['email']); ?>" class="text-muted text-decoration-none"><?php echo htmlspecialchars($settings['email']); ?></a>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Form Column -->
                <div class="col-lg-7">
                    <div class="glass-panel p-4 p-md-5">
                        <h3 class="text-white mb-4" style="font-family: var(--font-heading);">Submit Your Enquiry</h3>
                        
                        <form id="frontend-contact-form" class="custom-form">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label text-white">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label text-white">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="john@example.com" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label text-white">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="+1 (555) 000-0000" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="subject" class="form-label text-white">Subject</label>
                                    <input type="text" class="form-control" id="subject" name="subject" placeholder="Bespoke Wedding Booking" required>
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label text-white">Your Project Vision / Message</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" placeholder="Share specific date parameters, location preferences, guest counts, and styling requirements..." required></textarea>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn-premium w-100">Send Secure Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
