<?php
// Load admin header and sidebar navigation
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// Fetch current blog articles
try {
    $stmt = $pdo->query("SELECT * FROM `blogs` ORDER BY `id` DESC");
    $blogsList = $stmt->fetchAll();
} catch (Exception $e) {
    $blogsList = [];
}
?>

    <!-- Page Header and Actions Bar -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="h3 text-white font-body fw-bold mb-1">Blogs Module</h1>
            <p class="text-muted mb-0">Publish dynamic stories, news alerts, and spatial planning guides.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0 d-flex flex-wrap gap-2 justify-content-md-end">
            <button type="button" class="btn btn-outline-info font-body" data-bs-toggle="modal" data-bs-target="#csvImportModal">
                <i class="bi bi-file-earmark-arrow-up"></i> Import CSV
            </button>
            <button type="button" class="btn-accent font-body" onclick="openAddBlogModal()">
                <i class="bi bi-plus-circle"></i> Add New Blog
            </button>
        </div>
    </div>

    <!-- DataTables Card -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title text-white">Articles Directory</h3>
        </div>
        
        <div class="table-responsive">
            <table class="table align-middle text-white" id="blogs-datatables">
                <thead>
                    <tr>
                        <th>Thumbnail</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Published Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($blogsList)): ?>
                        <?php foreach ($blogsList as $bl): ?>
                            <tr id="blog-row-<?php echo $bl['id']; ?>">
                                <td>
                                    <div style="width: 80px; height: 50px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color);">
                                        <?php if (!empty($bl['featured_image']) && file_exists(__DIR__ . '/../uploads/blogs/' . $bl['featured_image'])): ?>
                                            <img src="../uploads/blogs/<?php echo htmlspecialchars($bl['featured_image']); ?>" style="width:100%; height:100%; object-fit:cover;" alt="Thumb">
                                        <?php else: ?>
                                            <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=150&auto=format&fit=crop" style="width:100%; height:100%; object-fit:cover;" alt="Fallback">
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="fw-bold"><?php echo htmlspecialchars($bl['title']); ?></td>
                                <td class="text-muted small"><?php echo htmlspecialchars($bl['slug']); ?></td>
                                <td>
                                    <span class="<?php echo $bl['status'] == 'active' ? 'badge-active' : 'badge-inactive'; ?>">
                                        <?php echo ucfirst($bl['status']); ?>
                                    </span>
                                </td>
                                <td class="small"><?php echo date('d M Y, h:i A', strtotime($bl['created_at'])); ?></td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-warning" onclick="editBlog(<?php echo $bl['id']; ?>)" title="Edit Article">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteBlog(<?php echo $bl['id']; ?>)" title="Delete Article">
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

    <!-- AJAX Add/Edit Blog Modal -->
    <div class="modal fade" id="blogModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="blogModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form id="blog-form" enctype="multipart/form-data">
                    
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title text-white font-body fw-bold" id="blogModalLabel">Add Blog Post</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body text-white">
                        
                        <input type="hidden" id="blog_id" name="blog_id" value="0">
                        
                        <div class="row g-3">
                            <!-- Title & Slug -->
                            <div class="col-md-6">
                                <label for="title" class="form-label">Article Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required placeholder="Enter spectacular title">
                            </div>
                            <div class="col-md-6">
                                <label for="slug" class="form-label">SEO URL Slug (Auto Generated)</label>
                                <input type="text" class="form-control" id="slug" name="slug" placeholder="e.g. spectaculer-gala-night">
                            </div>
                            
                            <!-- Strict Dimension Upload WebP Image -->
                            <div class="col-12">
                                <label class="form-label d-block">Featured Image (Required: WebP format, 1200x630 px) <span class="text-danger">*</span></label>
                                <input type="file" id="featured_image" name="featured_image" accept=".webp" class="d-none">
                                <div class="image-preview-box" id="blog-image-preview">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <p>Click here to choose WebP file (Exactly 1200x630 px)</p>
                                </div>
                            </div>
                            
                            <!-- Short description -->
                            <div class="col-12">
                                <label for="short_description" class="form-label">Short Summary Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="short_description" name="short_description" rows="3" required placeholder="Draft a dynamic 2-sentence hook for lists..."></textarea>
                            </div>
                            
                            <!-- Full Description (CKEditor) -->
                            <div class="col-12">
                                <label for="full_description" class="form-label">Full Rich Content Description <span class="text-danger">*</span></label>
                                <textarea id="full_description" name="full_description"></textarea>
                            </div>
                            
                            <!-- SEO Meta Fields -->
                            <div class="col-12 mt-4 pt-3 border-top border-secondary">
                                <h6 class="text-gradient fw-bold font-body">SEO Metadata Settings (Optional)</h6>
                            </div>
                            <div class="col-md-6">
                                <label for="meta_title" class="form-label">Meta Title Tag</label>
                                <input type="text" class="form-control" id="meta_title" name="meta_title" placeholder="AuraEvents | Spectacular Post">
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="meta_keywords" class="form-label">Meta Keywords Tag (Comma Separated)</label>
                                <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" placeholder="wedding, dynamic, trends, planners">
                            </div>
                            <div class="col-12">
                                <label for="meta_description" class="form-label">Meta Description Tag</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="2" placeholder="Draft specialized SEO search summaries..."></textarea>
                            </div>
                        </div>

                    </div>
                    
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary font-body" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn-accent font-body" id="save-btn">Publish Article</button>
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
                        <p class="small text-muted">Ingest multiple blog articles instantly using a standard CSV file structure.</p>
                        <div class="alert alert-secondary text-white-50 border-secondary small" style="background: rgba(255,255,255,0.02)">
                            <strong>Required Column Mapping Order:</strong><br>
                            1. Title, 2. Short Description, 3. Full Description, 4. Meta Title, 5. Meta Keywords, 6. Meta Description, 7. Status
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

    <!-- Core page script binding for DataTables and AJAX -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Instantiate dynamic DataTable with export settings
            $('#blogs-datatables').DataTable({
                responsive: true,
                order: [[4, 'desc']],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'csv',
                        text: '<i class="bi bi-filetype-csv"></i> Export CSV',
                        className: 'dt-button',
                        exportOptions: { columns: [1, 2, 3, 4] }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-excel"></i> Export Excel',
                        className: 'dt-button',
                        exportOptions: { columns: [1, 2, 3, 4] }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bi bi-file-pdf"></i> Export PDF',
                        className: 'dt-button',
                        exportOptions: { columns: [1, 2, 3, 4] }
                    }
                ],
                language: {
                    searchPlaceholder: "Search articles...",
                    search: ""
                }
            });

            // Auto Generate Slug
            autoGenerateSlug('#title', '#slug');

            // Image Preview & Strict Dimensions (1200x630 WebP ONLY!)
            initImageValidation('#featured_image', '#blog-image-preview', 1200, 630);

            // Instantiate CKEditor 5
            let descEditor;
            ClassicEditor
                .create(document.querySelector('#full_description'), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo']
                })
                .then(editor => {
                    descEditor = editor;
                })
                .catch(error => {
                    console.error(error);
                });

            // Open Add Modal
            window.openAddBlogModal = function() {
                $('#blog_id').val(0);
                $('#blog-form')[0].reset();
                $('#blogModalLabel').text('Add Blog Post');
                $('#save-btn').text('Publish Article');
                $('#blog-image-preview').html(`
                    <i class="bi bi-cloud-arrow-up"></i>
                    <p>Click here to choose WebP file (Exactly 1200x630 px)</p>
                `);
                
                if(descEditor) descEditor.setData('');
                
                $('#blogModal').modal('show');
            };

            // Edit Blog AJAX fetch
            window.editBlog = function(id) {
                $.ajax({
                    url: 'ajax/blogs_handler.php?action=get',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#blog_id').val(response.data.id);
                            $('#title').val(response.data.title);
                            $('#slug').val(response.data.slug);
                            $('#short_description').val(response.data.short_description);
                            $('#meta_title').val(response.data.meta_title);
                            $('#meta_keywords').val(response.data.meta_keywords);
                            $('#meta_description').val(response.data.meta_description);
                            $('#status').val(response.data.status);
                            
                            // Load CKEditor data
                            if(descEditor) descEditor.setData(response.data.full_description);
                            
                            // Update preview box
                            if(response.data.featured_image) {
                                $('#blog-image-preview').html(`<img src="../uploads/blogs/${response.data.featured_image}" alt="Preview" />`);
                            }

                            $('#blogModalLabel').text('Edit Blog Post');
                            $('#save-btn').text('Save Changes');
                            $('#blogModal').modal('show');
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: response.message });
                        }
                    }
                });
            };

            // Delete Blog Post AJAX
            window.deleteBlog = function(id) {
                Swal.fire({
                    title: 'Delete this blog article?',
                    text: "You won't be able to recover this post!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#3f3f46',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'ajax/blogs_handler.php?action=delete',
                            type: 'POST',
                            data: { id: id },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    // Remove row from DataTable
                                    const table = $('#blogs-datatables').DataTable();
                                    table.row($(`#blog-row-${id}`)).remove().draw();
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            }
                        });
                    }
                });
            };

            // Add/Edit Form submit via AJAX
            $('#blog-form').on('submit', function(e) {
                e.preventDefault();
                
                // Copy editor data to standard textarea before sending
                if(descEditor) {
                    $('#full_description').val(descEditor.getData());
                }

                // Double check description validation
                if($('#full_description').val().trim() === '') {
                    Swal.fire({ icon: 'warning', title: 'Validation Warning', text: 'Full Content description is required.' });
                    return;
                }

                const formData = new FormData(this);
                $('#save-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

                $.ajax({
                    url: 'ajax/blogs_handler.php?action=save',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        $('#save-btn').prop('disabled', false).text($('#blog_id').val() > 0 ? 'Save Changes' : 'Publish Article');
                        if (response.success) {
                            $('#blogModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                confirmButtonColor: '#a855f7'
                            }).then(() => {
                                window.location.reload(); // Reload to fetch fresh DataTables state
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Publication Failed', text: response.message });
                        }
                    },
                    error: function() {
                        $('#save-btn').prop('disabled', false).text($('#blog_id').val() > 0 ? 'Save Changes' : 'Publish Article');
                    }
                });
            });

            // CSV Import form submission via AJAX
            $('#csv-import-form').on('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                $('#import-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing CSV...');

                $.ajax({
                    url: 'ajax/blogs_handler.php?action=import',
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
// Load admin footer script inclusions
require_once __DIR__ . '/includes/footer.php';
?>
