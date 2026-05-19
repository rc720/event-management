<?php
// Include admin header and sidebar
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// Fetch current team profiles from DB
try {
    $stmt = $pdo->query("SELECT * FROM `team_members` ORDER BY `id` DESC");
    $teamList = $stmt->fetchAll();
} catch (Exception $e) {
    $teamList = [];
}
?>

    <!-- Page Header and Actions Bar -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="h3 text-white font-body fw-bold mb-1">Team Members Module</h1>
            <p class="text-muted mb-0">Publish professional profiles of creative directors, spatial designers, and coordinators.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0 d-flex flex-wrap gap-2 justify-content-md-end">
            <button type="button" class="btn btn-outline-info font-body" data-bs-toggle="modal" data-bs-target="#csvImportModal">
                <i class="bi bi-file-earmark-arrow-up"></i> Import CSV
            </button>
            <button type="button" class="btn-accent font-body" onclick="openAddTeamModal()">
                <i class="bi bi-plus-circle"></i> Add Specialist
            </button>
        </div>
    </div>

    <!-- DataTables Panel -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title text-white">Creative Specialists</h3>
        </div>
        
        <div class="table-responsive">
            <table class="table align-middle text-white" id="team-datatables">
                <thead>
                    <tr>
                        <th>Avatar</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Status</th>
                        <th>Social Links</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($teamList)): ?>
                        <?php foreach ($teamList as $tm): ?>
                            <tr id="team-row-<?php echo $tm['id']; ?>">
                                <td>
                                    <div style="width: 50px; height: 50px; border-radius: 50%; overflow: hidden; border: 2px solid var(--border-color);">
                                        <?php if (!empty($tm['featured_image']) && file_exists(__DIR__ . '/../uploads/team/' . $tm['featured_image'])): ?>
                                            <img src="../uploads/team/<?php echo htmlspecialchars($tm['featured_image']); ?>" style="width:100%; height:100%; object-fit:cover;" alt="Avatar">
                                        <?php else: ?>
                                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=150&auto=format&fit=crop" style="width:100%; height:100%; object-fit:cover;" alt="Fallback">
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="fw-bold"><?php echo htmlspecialchars($tm['name']); ?></td>
                                <td class="text-gradient fw-bold font-body" style="font-size:0.9rem;"><?php echo htmlspecialchars($tm['designation']); ?></td>
                                <td>
                                    <span class="<?php echo $tm['status'] == 'active' ? 'badge-active' : 'badge-inactive'; ?>">
                                        <?php echo ucfirst($tm['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <?php if (!empty($tm['facebook'])): ?>
                                            <a href="<?php echo htmlspecialchars($tm['facebook']); ?>" target="_blank" class="text-white small" title="Facebook"><i class="bi bi-facebook"></i></a>
                                        <?php endif; ?>
                                        <?php if (!empty($tm['twitter'])): ?>
                                            <a href="<?php echo htmlspecialchars($tm['twitter']); ?>" target="_blank" class="text-white small" title="Twitter"><i class="bi bi-twitter"></i></a>
                                        <?php endif; ?>
                                        <?php if (!empty($tm['instagram'])): ?>
                                            <a href="<?php echo htmlspecialchars($tm['instagram']); ?>" target="_blank" class="text-white small" title="Instagram"><i class="bi bi-instagram"></i></a>
                                        <?php endif; ?>
                                        <?php if (!empty($tm['linkedin'])): ?>
                                            <a href="<?php echo htmlspecialchars($tm['linkedin']); ?>" target="_blank" class="text-white small" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-warning" onclick="editTeam(<?php echo $tm['id']; ?>)" title="Edit Profile">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteTeam(<?php echo $tm['id']; ?>)" title="Delete Profile">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- AJAX Add/Edit Team Modal -->
    <div class="modal fade" id="teamModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="teamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form id="team-form" enctype="multipart/form-data">
                    
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title text-white font-body fw-bold" id="teamModalLabel">Add Specialist</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body text-white">
                        
                        <input type="hidden" id="member_id" name="member_id" value="0">
                        
                        <div class="row g-3">
                            <!-- Name & Designation -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required placeholder="Sarah Jenkins">
                            </div>
                            <div class="col-md-6">
                                <label for="designation" class="form-label">Designation Designation <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="designation" name="designation" required placeholder="Staging Director">
                            </div>
                            
                            <!-- Strict Dimension Upload WebP Image -->
                            <div class="col-12">
                                <label class="form-label d-block">Profile Image (Required: WebP format, 500x500 px) <span class="text-danger">*</span></label>
                                <input type="file" id="featured_image" name="featured_image" accept=".webp" class="d-none">
                                <div class="image-preview-box" id="team-image-preview">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <p>Click here to choose WebP file (Exactly 500x500 px)</p>
                                </div>
                            </div>
                            
                            <!-- Description (CKEditor) -->
                            <div class="col-12">
                                <label for="description" class="form-label">Specialist Biography <span class="text-danger">*</span></label>
                                <textarea id="description" name="description"></textarea>
                            </div>
                            
                            <!-- Social Profiles -->
                            <div class="col-12 mt-4 pt-3 border-top border-secondary">
                                <h6 class="text-gradient fw-bold font-body">Social Handles Settings</h6>
                            </div>
                            <div class="col-md-6">
                                <label for="facebook" class="form-label">Facebook Profile Link</label>
                                <input type="url" class="form-control" id="facebook" name="facebook" placeholder="https://facebook.com/username">
                            </div>
                            <div class="col-md-6">
                                <label for="twitter" class="form-label">Twitter Profile Link</label>
                                <input type="url" class="form-control" id="twitter" name="twitter" placeholder="https://twitter.com/username">
                            </div>
                            <div class="col-md-6">
                                <label for="instagram" class="form-label">Instagram Profile Link</label>
                                <input type="url" class="form-control" id="instagram" name="instagram" placeholder="https://instagram.com/username">
                            </div>
                            <div class="col-md-6">
                                <label for="linkedin" class="form-label">LinkedIn Profile Link</label>
                                <input type="url" class="form-control" id="linkedin" name="linkedin" placeholder="https://linkedin.com/in/username">
                            </div>
                            
                            <div class="col-12">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary font-body" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn-accent font-body" id="save-btn">Publish Profile</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- CSV Bulk Import Modal -->
    <div class="modal fade" id="csvImportModal" tabindex="-1" aria-labelledby="csvImportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="csv-import-form">
                    
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title text-white font-body fw-bold" id="csvImportModalLabel">CSV Bulk Importer</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body text-white">
                        <p class="small text-muted">Ingest multiple team profiles instantly using a standard CSV file structure.</p>
                        <div class="alert alert-secondary text-white-50 border-secondary small" style="background: rgba(255,255,255,0.02)">
                            <strong>Required Column Mapping Order:</strong><br>
                            1. Name, 2. Designation, 3. Biography Description, 4. Facebook, 5. Twitter, 6. Instagram, 7. LinkedIn, 8. Status
                        </div>
                        
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">Choose CSV File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control text-white" id="csv_file" name="csv_file" accept=".csv" required>
                        </div>
                    </div>
                    
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary font-body" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-accent font-body" id="import-btn">Process CSV</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Page Scripts -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Instantiate dynamic DataTable with export settings
            $('#team-datatables').DataTable({
                responsive: true,
                order: [[1, 'asc']],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'csv',
                        text: '<i class="bi bi-filetype-csv"></i> Export CSV',
                        className: 'dt-button',
                        exportOptions: { columns: [1, 2, 3] }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-excel"></i> Export Excel',
                        className: 'dt-button',
                        exportOptions: { columns: [1, 2, 3] }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bi bi-file-pdf"></i> Export PDF',
                        className: 'dt-button',
                        exportOptions: { columns: [1, 2, 3] }
                    }
                ],
                language: {
                    searchPlaceholder: "Search profiles...",
                    search: ""
                }
            });

            // Image Preview & Strict Dimensions (500x500 WebP ONLY!)
            initImageValidation('#featured_image', '#team-image-preview', 500, 500);

            // Instantiate CKEditor 5
            let bioEditor;
            ClassicEditor
                .create(document.querySelector('#description'), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo']
                })
                .then(editor => {
                    bioEditor = editor;
                })
                .catch(error => {
                    console.error(error);
                });

            // Open Add Modal
            window.openAddTeamModal = function() {
                $('#member_id').val(0);
                $('#team-form')[0].reset();
                $('#teamModalLabel').text('Add Specialist');
                $('#save-btn').text('Publish Profile');
                $('#team-image-preview').html(`
                    <i class="bi bi-cloud-arrow-up"></i>
                    <p>Click here to choose WebP file (Exactly 500x500 px)</p>
                `);
                
                if(bioEditor) bioEditor.setData('');
                
                $('#teamModal').modal('show');
            };

            // Edit Profile AJAX fetch
            window.editTeam = function(id) {
                $.ajax({
                    url: 'ajax/team_handler.php?action=get',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#member_id').val(response.data.id);
                            $('#name').val(response.data.name);
                            $('#designation').val(response.data.designation);
                            
                            $('#facebook').val(response.data.facebook);
                            $('#twitter').val(response.data.twitter);
                            $('#instagram').val(response.data.instagram);
                            $('#linkedin').val(response.data.linkedin);
                            $('#status').val(response.data.status);
                            
                            // Load CKEditor data
                            if(bioEditor) bioEditor.setData(response.data.description);
                            
                            // Update preview box
                            if(response.data.featured_image) {
                                $('#team-image-preview').html(`<img src="../uploads/team/${response.data.featured_image}" alt="Preview" />`);
                            }

                            $('#teamModalLabel').text('Edit Specialist Profile');
                            $('#save-btn').text('Save Changes');
                            $('#teamModal').modal('show');
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: response.message });
                        }
                    }
                });
            };

            // Delete Team AJAX
            window.deleteTeam = function(id) {
                Swal.fire({
                    title: 'Delete this profile?',
                    text: "You won't be able to recover this profile!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#3f3f46',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'ajax/team_handler.php?action=delete',
                            type: 'POST',
                            data: { id: id },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    // Remove row from DataTable
                                    const table = $('#team-datatables').DataTable();
                                    table.row($(`#team-row-${id}`)).remove().draw();
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            }
                        });
                    }
                });
            };

            // Form Submit via AJAX
            $('#team-form').on('submit', function(e) {
                e.preventDefault();
                
                // Copy editor data to standard textarea before sending
                if(bioEditor) {
                    $('#description').val(bioEditor.getData());
                }

                // Check description validation
                if($('#description').val().trim() === '') {
                    Swal.fire({ icon: 'warning', title: 'Validation Warning', text: 'Biography description is required.' });
                    return;
                }

                const formData = new FormData(this);
                $('#save-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

                $.ajax({
                    url: 'ajax/team_handler.php?action=save',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        $('#save-btn').prop('disabled', false).text($('#member_id').val() > 0 ? 'Save Changes' : 'Publish Profile');
                        if (response.success) {
                            $('#teamModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                confirmButtonColor: '#a855f7'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Action Failed', text: response.message });
                        }
                    },
                    error: function() {
                        $('#save-btn').prop('disabled', false).text($('#member_id').val() > 0 ? 'Save Changes' : 'Publish Profile');
                    }
                });
            });

            // CSV Import form submission via AJAX
            $('#csv-import-form').on('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                $('#import-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing CSV...');

                $.ajax({
                    url: 'ajax/team_handler.php?action=import',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        $('#import-btn').prop('disabled', false).text('Process CSV');
                        if (response.success) {
                            $('#csvImportModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Import Complete',
                                text: response.message,
                                confirmButtonColor: '#a855f7'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Import Failed', text: response.message });
                        }
                    },
                    error: function() {
                        $('#import-btn').prop('disabled', false).text('Process CSV');
                    }
                });
            });

        });
    </script>

<?php
// Load admin footer
require_once __DIR__ . '/includes/footer.php';
?>
