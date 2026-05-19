<?php
// Include admin header and sidebar
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// Fetch current scheduled events from DB
try {
    $stmt = $pdo->query("SELECT * FROM `events` ORDER BY `id` DESC");
    $eventsList = $stmt->fetchAll();
} catch (Exception $e) {
    $eventsList = [];
}
?>

    <!-- Page Header and Actions Bar -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="h3 text-white font-body fw-bold mb-1">Events Module</h1>
            <p class="text-muted mb-0">Schedule luxury weddings, corporate congresses, and dynamic visual exhibitions.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0 d-flex flex-wrap gap-2 justify-content-md-end">
            <button type="button" class="btn btn-outline-info font-body" data-bs-toggle="modal" data-bs-target="#csvImportModal">
                <i class="bi bi-file-earmark-arrow-up"></i> Import CSV
            </button>
            <button type="button" class="btn-accent font-body" onclick="openAddEventModal()">
                <i class="bi bi-plus-circle"></i> Add New Event
            </button>
        </div>
    </div>

    <!-- DataTables Panel -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title text-white">Events Schedule</h3>
        </div>
        
        <div class="table-responsive">
            <table class="table align-middle text-white" id="events-datatables">
                <thead>
                    <tr>
                        <th>Thumbnail</th>
                        <th>Title</th>
                        <th>Date & Time</th>
                        <th>Venue / Location</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($eventsList)): ?>
                        <?php foreach ($eventsList as $ev): ?>
                            <tr id="event-row-<?php echo $ev['id']; ?>">
                                <td>
                                    <div style="width: 80px; height: 50px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color);">
                                        <?php if (!empty($ev['featured_image']) && file_exists(__DIR__ . '/../uploads/events/' . $ev['featured_image'])): ?>
                                            <img src="../uploads/events/<?php echo htmlspecialchars($ev['featured_image']); ?>" style="width:100%; height:100%; object-fit:cover;" alt="Thumb">
                                        <?php else: ?>
                                            <img src="https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?q=80&w=150&auto=format&fit=crop" style="width:100%; height:100%; object-fit:cover;" alt="Fallback">
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="fw-bold"><?php echo htmlspecialchars($ev['title']); ?></td>
                                <td>
                                    <span class="text-gradient fw-bold"><?php echo date('d M Y', strtotime($ev['event_date'])); ?></span><br>
                                    <small class="text-muted"><?php echo date('h:i A', strtotime($ev['event_time'])); ?></small>
                                </td>
                                <td><i class="bi bi-geo-alt-fill text-muted me-1"></i> <?php echo htmlspecialchars($ev['location']); ?></td>
                                <td>
                                    <span class="<?php echo $ev['status'] == 'active' ? 'badge-active' : 'badge-inactive'; ?>">
                                        <?php echo ucfirst($ev['status']); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-warning" onclick="editEvent(<?php echo $ev['id']; ?>)" title="Edit Event">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteEvent(<?php echo $ev['id']; ?>)" title="Delete Event">
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

    <!-- AJAX Add/Edit Event Modal -->
    <div class="modal fade" id="eventModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form id="event-form" enctype="multipart/form-data">
                    
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title text-white font-body fw-bold" id="eventModalLabel">Add Event Post</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body text-white">
                        
                        <input type="hidden" id="event_id" name="event_id" value="0">
                        
                        <div class="row g-3">
                            <!-- Title & Slug -->
                            <div class="col-md-6">
                                <label for="title" class="form-label">Event Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required placeholder="Enter spectacular title">
                            </div>
                            <div class="col-md-6">
                                <label for="slug" class="form-label">SEO URL Slug (Auto Generated)</label>
                                <input type="text" class="form-control" id="slug" name="slug" placeholder="e.g. majestic-wedding-summit">
                            </div>
                            
                            <!-- Date, Time, Location -->
                            <div class="col-md-4">
                                <label for="event_date" class="form-label">Event Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="event_date" name="event_date" required>
                            </div>
                            <div class="col-md-4">
                                <label for="event_time" class="form-label">Event Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="event_time" name="event_time" required>
                            </div>
                            <div class="col-md-4">
                                <label for="location" class="form-label">Venue Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="location" name="location" required placeholder="Biltmore Hotel, Miami">
                            </div>
                            
                            <!-- Strict Dimension Upload WebP Image -->
                            <div class="col-12">
                                <label class="form-label d-block">Featured Image (Required: WebP format, 1200x700 px) <span class="text-danger">*</span></label>
                                <input type="file" id="featured_image" name="featured_image" accept=".webp" class="d-none">
                                <div class="image-preview-box" id="event-image-preview">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <p>Click here to choose WebP file (Exactly 1200x700 px)</p>
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
                        <button type="submit" class="btn-accent font-body" id="save-btn">Schedule Event</button>
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
                        <p class="small text-muted">Ingest multiple scheduled events instantly using a standard CSV file structure.</p>
                        <div class="alert alert-secondary text-white-50 border-secondary small" style="background: rgba(255,255,255,0.02)">
                            <strong>Required Column Mapping Order:</strong><br>
                            1. Title, 2. Short Description, 3. Full Description, 4. Date (YYYY-MM-DD), 5. Time (HH:MM:SS), 6. Location/Venue, 7. Meta Title, 8. Meta Keywords, 9. Meta Description, 10. Status
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
            $('#events-datatables').DataTable({
                responsive: true,
                order: [[2, 'desc']],
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
                    searchPlaceholder: "Search events...",
                    search: ""
                }
            });

            // Auto Generate Slug
            autoGenerateSlug('#title', '#slug');

            // Image Preview & Strict Dimensions (1200x700 WebP ONLY!)
            initImageValidation('#featured_image', '#event-image-preview', 1200, 700);

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
            window.openAddEventModal = function() {
                $('#event_id').val(0);
                $('#event-form')[0].reset();
                $('#eventModalLabel').text('Add Event Post');
                $('#save-btn').text('Schedule Event');
                $('#event-image-preview').html(`
                    <i class="bi bi-cloud-arrow-up"></i>
                    <p>Click here to choose WebP file (Exactly 1200x700 px)</p>
                `);
                
                if(descEditor) descEditor.setData('');
                
                $('#eventModal').modal('show');
            };

            // Edit Event AJAX fetch
            window.editEvent = function(id) {
                $.ajax({
                    url: 'ajax/events_handler.php?action=get',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#event_id').val(response.data.id);
                            $('#title').val(response.data.title);
                            $('#slug').val(response.data.slug);
                            $('#event_date').val(response.data.event_date);
                            $('#event_time').val(response.data.event_time);
                            $('#location').val(response.data.location);
                            $('#short_description').val(response.data.short_description);
                            
                            $('#meta_title').val(response.data.meta_title);
                            $('#meta_keywords').val(response.data.meta_keywords);
                            $('#meta_description').val(response.data.meta_description);
                            $('#status').val(response.data.status);
                            
                            // Load CKEditor data
                            if(descEditor) descEditor.setData(response.data.full_description);
                            
                            // Update preview box
                            if(response.data.featured_image) {
                                $('#event-image-preview').html(`<img src="../uploads/events/${response.data.featured_image}" alt="Preview" />`);
                            }

                            $('#eventModalLabel').text('Edit Event Post');
                            $('#save-btn').text('Save Changes');
                            $('#eventModal').modal('show');
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: response.message });
                        }
                    }
                });
            };

            // Delete Event Post AJAX
            window.deleteEvent = function(id) {
                Swal.fire({
                    title: 'Delete this scheduled event?',
                    text: "This action is permanent!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#3f3f46',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'ajax/events_handler.php?action=delete',
                            type: 'POST',
                            data: { id: id },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    // Remove row from DataTable
                                    const table = $('#events-datatables').DataTable();
                                    table.row($(`#event-row-${id}`)).remove().draw();
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            }
                        });
                    }
                });
            };

            // Form Submit via AJAX
            $('#event-form').on('submit', function(e) {
                e.preventDefault();
                
                // Copy editor data to standard textarea before sending
                if(descEditor) {
                    $('#full_description').val(descEditor.getData());
                }

                // Check description validation
                if($('#full_description').val().trim() === '') {
                    Swal.fire({ icon: 'warning', title: 'Validation Warning', text: 'Full Content description is required.' });
                    return;
                }

                const formData = new FormData(this);
                $('#save-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

                $.ajax({
                    url: 'ajax/events_handler.php?action=save',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        $('#save-btn').prop('disabled', false).text($('#event_id').val() > 0 ? 'Save Changes' : 'Schedule Event');
                        if (response.success) {
                            $('#eventModal').modal('hide');
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
                        $('#save-btn').prop('disabled', false).text($('#event_id').val() > 0 ? 'Save Changes' : 'Schedule Event');
                    }
                });
            });

            // CSV Import form submission via AJAX
            $('#csv-import-form').on('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                $('#import-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing CSV...');

                $.ajax({
                    url: 'ajax/events_handler.php?action=import',
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
