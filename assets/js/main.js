/**
 * AuraEvents - Frontend Main Interaction Scripts
 */

$(document).ready(function () {
    // --------------------------------------------------------
    // 1. Navigation Scroll Effect
    // --------------------------------------------------------
    $(window).on('scroll', function () {
        if ($(window).scrollTop() > 50) {
            $('.navbar-custom').addClass('scrolled');
        } else {
            $('.navbar-custom').removeClass('scrolled');
        }
    });

    // --------------------------------------------------------
    // 2. Interactive AJAX Contact Form Submission
    // --------------------------------------------------------
    $('#frontend-contact-form').on('submit', function (e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalBtnText = submitBtn.html();

        // Front-end validations
        const name = $('#name').val().trim();
        const email = $('#email').val().trim();
        const phone = $('#phone').val().trim();
        const subject = $('#subject').val().trim();
        const message = $('#message').val().trim();

        if (!name || !email || !phone || !subject || !message) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'All fields are mandatory. Please fill in the details.',
                confirmButtonColor: '#a855f7'
            });
            return;
        }

        // Email validation regex
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Email',
                text: 'Please enter a valid email address.',
                confirmButtonColor: '#a855f7'
            });
            return;
        }

        // Disable button & show spinner
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');

        $.ajax({
            url: 'admin/ajax/enquiries_handler.php?action=create',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function (response) {
                submitBtn.prop('disabled', false).html(originalBtnText);
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Enquiry Sent!',
                        text: response.message,
                        confirmButtonColor: '#a855f7'
                    });
                    form[0].reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        text: response.message,
                        confirmButtonColor: '#a855f7'
                    });
                }
            },
            error: function () {
                submitBtn.prop('disabled', false).html(originalBtnText);
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: 'Unable to deliver your enquiry right now. Please try again later.',
                    confirmButtonColor: '#a855f7'
                });
            }
        });
    });

    // --------------------------------------------------------
    // 3. Simple Dynamic Gallery Filters (Frontend portfolio)
    // --------------------------------------------------------
    $('.gallery-filter-btn').on('click', function () {
        $('.gallery-filter-btn').removeClass('active');
        $(this).addClass('active');

        const filterValue = $(this).attr('data-filter');
        if (filterValue === 'all') {
            $('.gallery-grid-item').show(400);
        } else {
            $('.gallery-grid-item').hide();
            $(`.gallery-grid-item[data-category="${filterValue}"]`).show(400);
        }
    });
});
