<?php
// Load admin header and sidebar
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// Fetch all enquiries from DB
try {
    $stmt = $pdo->query("SELECT * FROM `enquiries` ORDER BY `id` DESC");
    $allEnquiries = $stmt->fetchAll();
} catch (Exception $e) {
    $allEnquiries = [];
}
?>

    <!-- Header layout -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 text-white font-body fw-bold mb-1">User Enquiries</h1>
            <p class="text-muted mb-0">Manage and respond to secure contact requests from your clients.</p>
        </div>
    </div>

    <!-- DataTables Grid Panel -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title text-white">All Client Messages</h3>
        </div>
        
        <div class="table-responsive">
            <table class="table align-middle text-white" id="all-enquiries-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Subject</th>
                        <th>Date Received</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($allEnquiries)): ?>
                        <?php foreach ($allEnquiries as $enq): ?>
                            <tr id="enquiry-row-<?php echo $enq['id']; ?>">
                                <td>#<?php echo $enq['id']; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($enq['name']); ?></td>
                                <td><?php echo htmlspecialchars($enq['email']); ?></td>
                                <td><?php echo htmlspecialchars($enq['phone']); ?></td>
                                <td><?php echo htmlspecialchars($enq['subject']); ?></td>
                                <td><?php echo date('d M Y, h:i A', strtotime($enq['created_at'])); ?></td>
                                <td>
                                    <span class="badge <?php echo $enq['is_read'] ? 'bg-success' : 'bg-warning text-dark'; ?> rounded-pill" id="badge-read-<?php echo $enq['id']; ?>">
                                        <?php echo $enq['is_read'] ? 'Read' : 'Unread'; ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-info" onclick="viewEnquiryDetails(<?php echo $enq['id']; ?>)" title="View Message">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteEnquiryRow(<?php echo $enq['id']; ?>)" title="Delete Message">
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

    <!-- Enquiry Details Modal -->
    <div class="modal fade" id="viewEnquiryModal" tabindex="-1" aria-labelledby="viewEnquiryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-white font-body fw-bold" id="viewEnquiryModalLabel">Enquiry Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-white">
                    <p class="mb-2"><strong>Sender:</strong> <span id="enq-modal-name"></span></p>
                    <p class="mb-2"><strong>Email Address:</strong> <span id="enq-modal-email"></span></p>
                    <p class="mb-2"><strong>Phone Number:</strong> <span id="enq-modal-phone"></span></p>
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

    <!-- Core page script binding for DataTables and AJAX -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Instantiate jQuery DataTables with premium export configurations
            $('#all-enquiries-table').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'csv',
                        text: '<i class="bi bi-filetype-csv"></i> Export CSV',
                        className: 'dt-button',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-excel"></i> Export Excel',
                        className: 'dt-button',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bi bi-file-pdf"></i> Export PDF',
                        className: 'dt-button',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] }
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i> Print Table',
                        className: 'dt-button',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] }
                    }
                ],
                language: {
                    searchPlaceholder: "Search enquiries...",
                    search: ""
                }
            });

            // View Details trigger
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

            // Delete Enquiry Trigger
            window.deleteEnquiryRow = function(id) {
                Swal.fire({
                    title: 'Delete this message?',
                    text: "This process is irreversible!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#3f3f46',
                    confirmButtonText: 'Yes, delete!'
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
                                    // Remove row from DataTable
                                    const table = $('#all-enquiries-table').DataTable();
                                    table.row($(`#enquiry-row-${id}`)).remove().draw();
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
