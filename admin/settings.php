<?php
// Include admin header and sidebar navigation
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// System settings is already loaded inside header.php!
?>

    <!-- Page Header and Actions Bar -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 text-white font-body fw-bold mb-1">Global settings</h1>
            <p class="text-muted mb-0">Configure branding parameters, contact studio coordinates, and social visibility configurations.</p>
        </div>
    </div>

    <!-- Main settings Config Form Card -->
    <div class="row">
        <div class="col-lg-8">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title text-white">Branding & Contact coordinates</h3>
                </div>
                
                <form id="settings-form" enctype="multipart/form-data">
                    <div class="row g-4">
                        
                        <!-- Site Title -->
                        <div class="col-12">
                            <label for="site_title" class="form-label text-white-50">Website Main Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="site_title" name="site_title" required value="<?php echo htmlspecialchars($settings['site_title']); ?>" placeholder="AuraEvents - Luxury Event Planner">
                        </div>
                        
                        <!-- Logo Upload and Preview -->
                        <div class="col-md-6">
                            <label class="form-label text-white-50 d-block">Website Brand Logo (.webp ONLY)</label>
                            <input type="file" id="logo" name="logo" accept=".webp" class="form-control mb-3 text-white">
                            <small class="text-muted d-block mb-3">Enforcing clean standard WebP format logo for high-speed loads.</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-white-50 d-block">Active Logo State</label>
                            <div id="logo-preview-container" class="glass-panel p-3 text-center d-flex align-items-center justify-content-center flex-column" style="min-height: 110px;">
                                <?php if (!empty($settings['logo']) && file_exists(__DIR__ . '/../uploads/logo/' . $settings['logo'])): ?>
                                    <img src="../uploads/logo/<?php echo htmlspecialchars($settings['logo']); ?>" alt="Brand Logo" style="max-height: 50px;" class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-1 font-body" onclick="deleteBrandLogo()"><i class="bi bi-trash"></i> Remove Logo</button>
                                <?php else: ?>
                                    <span class="text-muted small">No dynamic logo uploaded. Using text brand instead.</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Contact Details -->
                        <div class="col-md-6">
                            <label for="phone" class="form-label text-white-50">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" required value="<?php echo htmlspecialchars($settings['phone']); ?>" placeholder="+1 (555) 123-4567">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label text-white-50">Contact Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($settings['email']); ?>" placeholder="info@auraevents.com">
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label text-white-50">Physical Studio Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="address" name="address" rows="3" required placeholder="Enter street, suite and zip details..."><?php echo htmlspecialchars($settings['address']); ?></textarea>
                        </div>

                        <!-- Social Media Integrations -->
                        <div class="col-12 mt-5 pt-3 border-top border-secondary">
                            <h5 class="text-gradient font-body fw-bold">Social Media Channels</h5>
                            <p class="text-muted small">Configure external links and dynamically toggle their display settings in the website's footer columns.</p>
                        </div>
                        
                        <!-- Facebook Config -->
                        <div class="col-md-8">
                            <label for="facebook_link" class="form-label text-white-50">Facebook Profile Link</label>
                            <input type="url" class="form-control" id="facebook_link" name="facebook_link" value="<?php echo htmlspecialchars($settings['facebook_link']); ?>" placeholder="https://facebook.com/auraevents">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="theme-switch-wrapper mb-2">
                                <label class="theme-switch" for="facebook_enable">
                                    <input type="checkbox" id="facebook_enable" name="facebook_enable" <?php echo $settings['facebook_enable'] ? 'checked' : ''; ?> />
                                    <div class="slider">
                                        <i class="bi bi-x-circle-fill"></i>
                                        <i class="bi bi-check-circle-fill"></i>
                                    </div>
                                </label>
                                <span class="small text-muted font-body ms-2">Visible</span>
                            </div>
                        </div>

                        <!-- Twitter Config -->
                        <div class="col-md-8">
                            <label for="twitter_link" class="form-label text-white-50">Twitter Profile Link</label>
                            <input type="url" class="form-control" id="twitter_link" name="twitter_link" value="<?php echo htmlspecialchars($settings['twitter_link']); ?>" placeholder="https://twitter.com/auraevents">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="theme-switch-wrapper mb-2">
                                <label class="theme-switch" for="twitter_enable">
                                    <input type="checkbox" id="twitter_enable" name="twitter_enable" <?php echo $settings['twitter_enable'] ? 'checked' : ''; ?> />
                                    <div class="slider">
                                        <i class="bi bi-x-circle-fill"></i>
                                        <i class="bi bi-check-circle-fill"></i>
                                    </div>
                                </label>
                                <span class="small text-muted font-body ms-2">Visible</span>
                            </div>
                        </div>

                        <!-- Instagram Config -->
                        <div class="col-md-8">
                            <label for="instagram_link" class="form-label text-white-50">Instagram Profile Link</label>
                            <input type="url" class="form-control" id="instagram_link" name="instagram_link" value="<?php echo htmlspecialchars($settings['instagram_link']); ?>" placeholder="https://instagram.com/auraevents">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="theme-switch-wrapper mb-2">
                                <label class="theme-switch" for="instagram_enable">
                                    <input type="checkbox" id="instagram_enable" name="instagram_enable" <?php echo $settings['instagram_enable'] ? 'checked' : ''; ?> />
                                    <div class="slider">
                                        <i class="bi bi-x-circle-fill"></i>
                                        <i class="bi bi-check-circle-fill"></i>
                                    </div>
                                </label>
                                <span class="small text-muted font-body ms-2">Visible</span>
                            </div>
                        </div>

                        <!-- LinkedIn Config -->
                        <div class="col-md-8">
                            <label for="linkedin_link" class="form-label text-white-50">LinkedIn Studio Link</label>
                            <input type="url" class="form-control" id="linkedin_link" name="linkedin_link" value="<?php echo htmlspecialchars($settings['linkedin_link']); ?>" placeholder="https://linkedin.com/company/auraevents">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="theme-switch-wrapper mb-2">
                                <label class="theme-switch" for="linkedin_enable">
                                    <input type="checkbox" id="linkedin_enable" name="linkedin_enable" <?php echo $settings['linkedin_enable'] ? 'checked' : ''; ?> />
                                    <div class="slider">
                                        <i class="bi bi-x-circle-fill"></i>
                                        <i class="bi bi-check-circle-fill"></i>
                                    </div>
                                </label>
                                <span class="small text-muted font-body ms-2">Visible</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 mt-4 pt-3 border-top border-secondary text-end">
                            <button type="submit" class="btn-accent font-body" id="save-settings-btn">Save settings Configurations</button>
                        </div>

                    </div>
                </form>

            </div>
        </div>
        
        <!-- Sidebar Helper panel -->
        <div class="col-lg-4">
            <div class="admin-card text-center py-5">
                <i class="bi bi-info-circle text-gradient fs-1 mb-3 d-block"></i>
                <h5 class="text-white font-body fw-bold">Dynamic Configurations</h5>
                <p class="text-muted small px-3">These configurations affect global structural elements in real time. Modifications here update navbar brands, email forms, footer columns, and the contact panels instantly across the entire platform.</p>
            </div>
        </div>
    </div>

    <!-- Core page script binding settings edits -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            
            // Delete brand logo file via AJAX
            window.deleteBrandLogo = function() {
                Swal.fire({
                    title: 'Remove website logo?',
                    text: 'This deletes the file on the server. Brand logo will fall back to dynamic text brand.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#3f3f46',
                    confirmButtonText: 'Yes, delete logo!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'ajax/settings_handler.php?action=delete_logo',
                            type: 'POST',
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Deleted!', response.message, 'success').then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire('Failed', response.message, 'error');
                                }
                            }
                        });
                    }
                });
            };

            // Form Submit via AJAX
            $('#settings-form').on('submit', function(e) {
                e.preventDefault();

                // Logo file type check
                const logoInput = document.getElementById('logo');
                if (logoInput.files.length > 0) {
                    const file = logoInput.files[0];
                    const extension = file.name.split('.').pop().toLowerCase();
                    if (extension !== 'webp') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Logo Format',
                            text: 'Global Policy: Logo must strictly be in WebP (.webp) format!',
                            confirmButtonColor: '#a855f7'
                        });
                        return;
                    }
                }

                const formData = new FormData(this);
                $('#save-settings-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving configurations...');

                $.ajax({
                    url: 'ajax/settings_handler.php?action=save',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        $('#save-settings-btn').prop('disabled', false).text('Save settings Configurations');
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Saved!',
                                text: response.message,
                                confirmButtonColor: '#a855f7'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Update Failed', text: response.message });
                        }
                    },
                    error: function() {
                        $('#save-settings-btn').prop('disabled', false).text('Save settings Configurations');
                    }
                });
            });

        });
    </script>

<?php
// Load admin footer
require_once __DIR__ . '/includes/footer.php';
?>
