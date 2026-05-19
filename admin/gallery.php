<?php
// Include admin header and sidebar
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// Fetch current gallery items from DB
try {
    $stmt = $pdo->query("SELECT * FROM `gallery` ORDER BY `id` DESC");
    $galleryList = $stmt->fetchAll();
} catch (Exception $e) {
    $galleryList = [];
}
?>

    <!-- Page Header and Actions Bar -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="h3 text-white font-body fw-bold mb-1">Gallery Module</h1>
            <p class="text-muted mb-0">Manage your elegant portfolio designs, staging showcases, and visual renders.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0 d-flex flex-wrap gap-2 justify-content-md-end">
            <button type="button" class="btn btn-outline-info font-body" data-bs-toggle="modal" data-bs-target="#csvImportModal">
                <i class="bi bi-file-earmark-arrow-up"></i> Import CSV
            </button>
            <button type="button" class="btn-accent font-body" onclick="openAddGalleryModal()">
                <i class="bi bi-plus-circle"></i> Add New Image
            </button>
        </div>
    </div>

    <!-- DataTables Panel -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title text-white">Visual Showcase</h3>
        </div>
        
        <div class="table-responsive">
            <table class="table align-middle text-white" id="gallery-datatables">
                <thead>
                    <tr>
                        <th>Image Preview</th>
                        <th>Title / Caption</th>
                        <th>Status</th>
                        <th>Uploaded At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($galleryList)): ?>
                        <?php foreach ($galleryList as $gal): ?>
                            <tr id="gallery-row-<?php echo $gal['id']; ?>">
                                <td>
                                    <div style="width: 80px; height: 60px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color);">
                                        <?php if (!empty($gal['featured_image']) && file_exists(__DIR__ . '/../uploads/gallery/' . $gal['featured_image'])): ?>
                                            <img src="../uploads/gallery/<?php echo htmlspecialchars($gal['featured_image']); ?>" style="width:100%; height:100%; object-fit:cover;" alt="Thumb">
                                        <?php else: ?>
                                            <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=150&auto=format&fit=crop" style="width:100%; height:100%; object-fit:cover;" alt="Fallback">
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="fw-bold"><?php echo htmlspecialchars($gal['title']); ?></td>
                                <td>
                                    <span class="<?php echo $gal['status'] == 'active' ? 'badge-active' : 'badge-inactive'; ?>">
                                        <?php echo ucfirst($gal['status']); ?>
                                    </span>
                                </td>
                                <td class="small"><?php echo date('d M Y, h:i A', strtotime($gal['created_at'])); ?></td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-warning" onclick="editGallery(<?php echo $gal['id']; ?>)" title="Edit Image">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteGallery(<?php echo $gal['id']; ?>)" title="Delete Image">
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

    <!-- AJAX Add/Edit Gallery Modal -->
    <div class="modal fade" id="galleryModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="gallery-form" enctype="multipart/form-data">
                    
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title text-white font-body fw-bold" id="galleryModalLabel">Add Gallery Image</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body text-white">
                        
                        <input type="hidden" id="gallery_id" name="gallery_id" value="0">
                        
                        <div class="row g-3">
                            <!-- Title / Caption -->
                            <div class="col-12">
                                <label for="title" class="form-label">Image Title / Caption <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required placeholder="e.g. Royal Wedding Main Stage">
                            </div>
                            
                            <!-- Strict Dimension Upload WebP Image -->
                            <div class="col-12">
                                <label class="form-label d-block">Featured Image (Required: WebP format, 800x600 px) <span class="text-danger">*</span></label>
                                <input type="file" id="featured_image" name="featured_image" accept=".webp" class="d-none">
                                <div class="image-preview-box" id="gallery-image-preview">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <p>Click here to choose WebP file (Exactly 800x600 px)</p>
                                </div>
                            </div>
                            
                            <!-- Status -->
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
                        <button type="submit" class="btn-accent font-body" id="save-btn">Publish Image</button>
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
                        <p class="small text-muted">Ingest multiple gallery entries instantly using a standard CSV file structure.</p>
                        <div class="alert alert-secondary text-white-50 border-secondary small" style="background: rgba(255,255,255,0.02)">
                            <strong>Required Column Mapping Order:</strong><br>
                            1. Title, 2. Status
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
            $('#gallery-datatables').DataTable({
                responsive: true,
                order: [[3, 'desc']],
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
                    searchPlaceholder: "Search images...",
                    search: ""
                }
            });

            // Image Preview & Strict Dimensions (800x600 WebP ONLY!)
            initImageValidation('#featured_image', '#gallery-image-preview', 800, 600);

            // Open Add Modal
            window.openAddGalleryModal = function() {
                $('#gallery_id').val(0);
                $('#gallery-form')[0].reset();
                $('#galleryModalLabel').text('Add Gallery Image');
                $('#save-btn').text('Publish Image');
                $('#gallery-image-preview').html(`
                    <i class="bi bi-cloud-arrow-up"></i>
                    <p>Click here to choose WebP file (Exactly 800x600 px)</p>
                `);
                
                $('#galleryModal').modal('show');
            };

            // Edit Gallery AJAX fetch
            window.editGallery = function(id) {
                $.ajax({
                    url: 'ajax/gallery_handler.php?action=get',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#gallery_id').val(response.data.id);
                            $('#title').val(response.data.title);
                            $('#status').val(response.data.status);
                            
                            // Update preview box
                            if(response.data.featured_image) {
                                $('#gallery-image-preview').html(`<img src="../uploads/gallery/${response.data.featured_image}" alt="Preview" />`);
                            }

                            $('#galleryModalLabel').text('Edit Gallery Image');
                            $('#save-btn').text('Save Changes');
                            $('#galleryModal').modal('show');
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: response.message });
                        }
                    }
                });
            };

            // Delete Gallery AJAX
            window.deleteGallery = function(id) {
                Swal.fire({
                    title: 'Delete this showcase image?',
                    text: "This image will be permanently lost!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#3f3f46',
                    confirmButtonText: 'Yes, delete!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'ajax/gallery_handler.php?action=delete',
                            type: 'POST',
                            data: { id: id },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    // Remove row from DataTable
                                    const table = $('#gallery-datatables').DataTable();
                                    table.row($(`#gallery-row-${id}`)).remove().draw();
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            }
                        });
                    }
                });
            };

            // Form Submit via AJAX
            $('#gallery-form').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                $('#save-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

                $.ajax({
                    url: 'ajax/gallery_handler.php?action=save',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        $('#save-btn').prop('disabled', false).text($('#gallery_id').val() > 0 ? 'Save Changes' : 'Publish Image');
                        if (response.success) {
                            $('#galleryModal').modal('hide');
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
                        $('#save-btn').prop('disabled', false).text($('#gallery_id').val() > 0 ? 'Save Changes' : 'Publish Image');
                    }
                });
            });

            // CSV Import AJAX
            $('#csv-import-form').on('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                $('#import-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing CSV...');

                $.ajax({
                    url: 'ajax/gallery_handler.php?action=import',
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
