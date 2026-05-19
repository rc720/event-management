<?php
// Load admin header and sidebar
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// Fetch statistics counts securely
try {
    $countBlogs = $pdo->query("SELECT COUNT(*) FROM `blogs`")->fetchColumn();
    $countEvents = $pdo->query("SELECT COUNT(*) FROM `events`")->fetchColumn();
    $countGallery = $pdo->query("SELECT COUNT(*) FROM `gallery`")->fetchColumn();
    $countTeam = $pdo->query("SELECT COUNT(*) FROM `team_members`")->fetchColumn();
    $countEnquiries = $pdo->query("SELECT COUNT(*) FROM `enquiries`")->fetchColumn();
} catch (Exception $e) {
    $countBlogs = $countEvents = $countGallery = $countTeam = $countEnquiries = 0;
}

// Fetch 5 most recent enquiries
try {
    $stmtEnq = $pdo->query("SELECT * FROM `enquiries` ORDER BY `id` DESC LIMIT 5");
    $recentEnquiries = $stmtEnq->fetchAll();
} catch (Exception $e) {
    $recentEnquiries = [];
}
?>

    <!-- Dashboard Main Heading -->
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h1 class="h3 text-white font-body fw-bold mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Welcome back, <b><?php echo htmlspecialchars($_SESSION['admin_fullname']); ?></b>! Here is your studio's overview.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <span class="badge bg-secondary p-2 font-body"><i class="bi bi-clock"></i> Local: <?php echo date('d M Y, h:i A'); ?></span>
        </div>
    </div>

    <!-- Stats Count Dashboard Grid -->
    <div class="row g-4 mb-5">
        
        <!-- Total Blogs -->
        <div class="col-lg-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-details">
                    <h5>Total Blogs</h5>
                    <h2><?php echo number_format($countBlogs); ?></h2>
                </div>
                <div class="stat-icon blogs"><i class="bi bi-newspaper"></i></div>
            </div>
        </div>
        
        <!-- Total Events -->
        <div class="col-lg-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-details">
                    <h5>Total Events</h5>
                    <h2><?php echo number_format($countEvents); ?></h2>
                </div>
                <div class="stat-icon events"><i class="bi bi-calendar-event"></i></div>
            </div>
        </div>
        
        <!-- Total Gallery -->
        <div class="col-lg-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-details">
                    <h5>Gallery Images</h5>
                    <h2><?php echo number_format($countGallery); ?></h2>
                </div>
                <div class="stat-icon gallery"><i class="bi bi-images"></i></div>
            </div>
        </div>
        
        <!-- Total Team -->
        <div class="col-lg-3 col-sm-6">
            <div class="stat-card font-body">
                <div class="stat-details">
                    <h5>Team Members</h5>
                    <h2><?php echo number_format($countTeam); ?></h2>
                </div>
                <div class="stat-icon team"><i class="bi bi-people"></i></div>
            </div>
        </div>

    </div>

    <!-- Second Row containing Latest Enquiries Table -->
    <div class="row g-4">
        
        <div class="col-xl-8 col-lg-12">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title text-white">Recent Contact Enquiries</h3>
                    <a href="enquiries.php" class="btn btn-sm btn-outline-secondary font-body">Manage All</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table align-middle text-white" id="recent-enquiries-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Submitted At</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentEnquiries)): ?>
                                <?php foreach ($recentEnquiries as $enq): ?>
                                    <tr id="enquiry-row-<?php echo $enq['id']; ?>">
                                        <td><?php echo htmlspecialchars($enq['name']); ?></td>
                                        <td><?php echo htmlspecialchars($enq['email']); ?></td>
                                        <td><?php echo htmlspecialchars($enq['subject']); ?></td>
                                        <td><?php echo date('d M, h:i A', strtotime($enq['created_at'])); ?></td>
                                        <td>
                                            <span class="badge <?php echo $enq['is_read'] ? 'bg-success' : 'bg-warning text-dark'; ?> rounded-pill" id="badge-read-<?php echo $enq['id']; ?>">
                                                <?php echo $enq['is_read'] ? 'Read' : 'Unread'; ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-info" onclick="viewEnquiryDetails(<?php echo $enq['id']; ?>)" title="View Enquiry">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteEnquiryRow(<?php echo $enq['id']; ?>)" title="Delete Enquiry">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No recent queries found. Everything is quiet!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-lg-12">
            <div class="admin-card text-center py-5">
                <i class="bi bi-gear-wide-connected text-gradient fs-1 mb-3 d-block"></i>
                <h4 class="text-white font-body fw-bold">Quick Configurations</h4>
                <p class="text-muted small">Update your global phone numbers, email address, address details, and logos in the systems manager.</p>
                <a href="settings.php" class="btn-accent text-decoration-none d-inline-block mt-3">Configure Studio</a>
            </div>
        </div>

    </div>

    <!-- Enquiry Viewer Modal -->
    <div class="modal fade" id="viewEnquiryModal" tabindex="-1" aria-labelledby="viewEnquiryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-white font-body fw-bold" id="viewEnquiryModalLabel">Enquiry Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-white">
                    <p class="mb-2"><strong>From:</strong> <span id="enq-modal-name"></span></p>
                    <p class="mb-2"><strong>Email:</strong> <span id="enq-modal-email"></span></p>
                    <p class="mb-2"><strong>Phone:</strong> <span id="enq-modal-phone"></span></p>
                    <p class="mb-2"><strong>Subject:</strong> <span id="enq-modal-subject"></span></p>
                    <hr class="border-secondary">
                    <p class="mb-0"><strong>Message:</strong></p>
                    <p class="text-muted mt-2 mb-0" id="enq-modal-message" style="white-space: pre-line;"></p>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary font-body" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Core Page Inline Scripts -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // View Enquiry Details Modal
            window.viewEnquiryDetails = function(id) {
                $.ajax({
                    url: 'ajax/enquiries_handler.php?action=view',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#enq-modal-name').text(response.data.name);
                            $('#enq-modal-email').text(response.data.email);
                            $('#enq-modal-phone').text(response.data.phone);
                            $('#enq-modal-subject').text(response.data.subject);
                            $('#enq-modal-message').text(response.data.message);
                            
                            // Visual update to read state
                            $(`#badge-read-${id}`).removeClass('bg-warning text-dark').addClass('bg-success').text('Read');
                            
                            $('#viewEnquiryModal').modal('show');
                        } else {
                            Swal.fire({ icon: 'error', title: 'Failed', text: response.message });
                        }
                    }
                });
            };

            // Delete Enquiry AJAX
            window.deleteEnquiryRow = function(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'ajax/enquiries_handler.php?action=delete',
                            type: 'POST',
                            data: { id: id },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    $(`#enquiry-row-${id}`).fadeOut(400, function() { $(this).remove(); });
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            }
                        });
                    }
                });
            };
        });
    </script>

<?php
// Load admin footer
require_once __DIR__ . '/includes/footer.php';
?>
