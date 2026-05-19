/**
 * AuraEvents - Premium Admin Panel JavaScript Router and Validators
 */

$(document).ready(function () {
    // --------------------------------------------------------
    // 1. Sidebar Toggle for Responsive Design
    // --------------------------------------------------------
    $('.sidebar-toggle-btn').on('click', function () {
        $('#admin-sidebar').toggleClass('active');
    });

    // Close sidebar on clicking outside in mobile view
    $(document).on('click', function (event) {
        if (!$(event.target).closest('#admin-sidebar, .sidebar-toggle-btn').length) {
            $('#admin-sidebar').removeClass('active');
        }
    });

    // --------------------------------------------------------
    // 2. Dark/Light Theme Switcher & Persistence
    // --------------------------------------------------------
    const currentTheme = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', currentTheme);
    
    if (currentTheme === 'light') {
        $('#theme-checkbox').prop('checked', true);
    }

    $('#theme-checkbox').on('change', function () {
        if ($(this).is(':checked')) {
            document.documentElement.setAttribute('data-theme', 'light');
            localStorage.setItem('theme', 'light');
        } else {
            document.documentElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        }
    });

    // --------------------------------------------------------
    // 3. Automated SEO Slug Generator (for title inputs)
    // --------------------------------------------------------
    window.autoGenerateSlug = function (titleInputSelector, slugInputSelector) {
        $(titleInputSelector).on('input', function () {
            let title = $(this).val();
            // Convert to lowercase, remove special characters, replace spaces with hyphens
            let slug = title.toLowerCase()
                .replace(/[^a-z0-9\s\-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/\-+/g, '-')
                .trim();
            $(slugInputSelector).val(slug);
        });
    };

    // --------------------------------------------------------
    // 4. WebP Image Client-Side Validation & Preview
    // --------------------------------------------------------
    window.initImageValidation = function (fileInputSelector, previewBoxSelector, reqWidth, reqHeight) {
        $(fileInputSelector).on('change', function (e) {
            const file = e.target.files[0];
            const previewBox = $(previewBoxSelector);
            const input = $(this);
            
            if (!file) return;

            // Strict extension check
            const extension = file.name.split('.').pop().toLowerCase();
            if (extension !== 'webp') {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Format',
                    text: 'Strict Requirement: Only WebP (.webp) image format is allowed!',
                    confirmButtonColor: '#a855f7'
                });
                input.val(''); // Clear input
                resetPreviewBox(previewBox);
                return;
            }

            // Strict MIME type check
            if (file.type !== 'image/webp') {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Image Content',
                    text: 'Strict Requirement: File must be a valid WebP image!',
                    confirmButtonColor: '#a855f7'
                });
                input.val('');
                resetPreviewBox(previewBox);
                return;
            }

            // Create object URL to check image dimensions
            const img = new Image();
            img.src = URL.createObjectURL(file);
            
            img.onload = function () {
                const width = this.width;
                const height = this.height;

                if (width !== reqWidth || height !== reqHeight) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Dimensions',
                        html: `Strict Requirement: Dimensions must be exactly <b>${reqWidth}x${reqHeight}</b> pixels.<br>Your uploaded image dimensions: <b>${width}x${height}</b> px.`,
                        confirmButtonColor: '#a855f7'
                    });
                    input.val('');
                    resetPreviewBox(previewBox);
                } else {
                    // Perfect! Set background preview
                    previewBox.html(`<img src="${img.src}" alt="Preview" />`);
                }
                URL.revokeObjectURL(img.src);
            };

            img.onerror = function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Corrupted File',
                    text: 'Could not parse image. The file might be corrupted.',
                    confirmButtonColor: '#a855f7'
                });
                input.val('');
                resetPreviewBox(previewBox);
            };
        });

        // Trigger file open when preview box is clicked
        $(previewBoxSelector).on('click', function () {
            $(fileInputSelector).trigger('click');
        });
    };

    function resetPreviewBox(previewBox) {
        previewBox.html(`
            <i class="bi bi-cloud-arrow-up"></i>
            <p>Click here to choose WebP file</p>
        `);
    }

    // --------------------------------------------------------
    // 5. Global AJAX Settings Setup
    // --------------------------------------------------------
    $.ajaxSetup({
        error: function (xhr, status, error) {
            console.error(xhr, status, error);
            Swal.fire({
                icon: 'error',
                title: 'System Error',
                text: 'An error occurred while communicating with the server. Please try again.',
                confirmButtonColor: '#a855f7'
            });
        }
    });
});
