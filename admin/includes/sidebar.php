<?php
// Active page navigation logic
$activePage = basename($_SERVER['PHP_SELF']);
?>
        <!-- Admin Left Sidebar Navigation Panel -->
        <aside id="admin-sidebar">
            
            <div class="sidebar-logo">
                <a href="index.php" class="text-decoration-none">
                    <?php if (!empty($settings['logo']) && file_exists(__DIR__ . '/../../uploads/logo/' . $settings['logo'])): ?>
                        <img src="../uploads/logo/<?php echo htmlspecialchars($settings['logo']); ?>" alt="AuraEvents Admin">
                    <?php else: ?>
                        <span class="fs-4 fw-bold text-gradient" style="font-family: 'Outfit', sans-serif;">AuraAdmin</span>
                    <?php endif; ?>
                </a>
            </div>
            
            <ul class="sidebar-nav">
                
                <li class="sidebar-nav-item">
                    <a href="index.php" class="sidebar-nav-link <?php echo $activePage == 'index.php' ? 'active' : ''; ?>">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="sidebar-nav-item">
                    <a href="blogs.php" class="sidebar-nav-link <?php echo $activePage == 'blogs.php' ? 'active' : ''; ?>">
                        <i class="bi bi-newspaper"></i>
                        <span>Blogs Module</span>
                    </a>
                </li>
                
                <li class="sidebar-nav-item">
                    <a href="events.php" class="sidebar-nav-link <?php echo $activePage == 'events.php' ? 'active' : ''; ?>">
                        <i class="bi bi-calendar-event"></i>
                        <span>Events Module</span>
                    </a>
                </li>
                
                <li class="sidebar-nav-item">
                    <a href="gallery.php" class="sidebar-nav-link <?php echo $activePage == 'gallery.php' ? 'active' : ''; ?>">
                        <i class="bi bi-images"></i>
                        <span>Gallery Module</span>
                    </a>
                </li>
                
                <li class="sidebar-nav-item">
                    <a href="team.php" class="sidebar-nav-link <?php echo $activePage == 'team.php' ? 'active' : ''; ?>">
                        <i class="bi bi-people"></i>
                        <span>Team Members</span>
                    </a>
                </li>
                
                <li class="sidebar-nav-item">
                    <a href="enquiries.php" class="sidebar-nav-link <?php echo $activePage == 'enquiries.php' ? 'active' : ''; ?>">
                        <i class="bi bi-envelope-paper"></i>
                        <span>Enquiries</span>
                    </a>
                </li>
                
                <li class="sidebar-nav-item">
                    <a href="settings.php" class="sidebar-nav-link <?php echo $activePage == 'settings.php' ? 'active' : ''; ?>">
                        <i class="bi bi-sliders2"></i>
                        <span>Global Settings</span>
                    </a>
                </li>
                
                <li class="sidebar-nav-item mt-5 pt-3 border-top border-secondary">
                    <a href="logout.php" class="sidebar-nav-link text-danger">
                        <i class="bi bi-box-arrow-left text-danger"></i>
                        <span>Logout Portal</span>
                    </a>
                </li>
                
            </ul>
            
        </aside>

        <!-- Right Hand Main Content Area (Sidebar closes this div) -->
        <main id="admin-content">
            
            <!-- Dynamic Top Header Bar -->
            <header id="admin-header">
                
                <button class="sidebar-toggle-btn">
                    <i class="bi bi-list"></i>
                </button>
                
                <div class="theme-switch-wrapper ms-auto d-flex align-items-center gap-3">
                    <span class="small text-muted font-body">Dark Mode Toggle</span>
                    <label class="theme-switch" for="theme-checkbox">
                        <input type="checkbox" id="theme-checkbox" />
                        <div class="slider">
                            <i class="bi bi-sun-fill"></i>
                            <i class="bi bi-moon-stars-fill"></i>
                        </div>
                    </label>
                </div>
                
            </header>
            
            <!-- Inner Content wrapper -->
            <div class="p-4 p-md-5">
