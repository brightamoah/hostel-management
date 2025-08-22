<section id="Contact" class="section-py landing-contact">
    <div class="container">
        <div class="mb-4 text-center">
            <span class="bg-label-primary badge">Contact US</span>
        </div>
        <h4 class="mb-1 text-center">
            <span class="z-1 position-relative fw-extrabold">Get in touch
                <img
                    src="../../assets/img/front-pages/icons/section-title-icon.png"
                    alt="hostel management"
                    class="bottom-0 z-n1 position-absolute object-fit-contain section-title-img" />
            </span>
            with us
        </h4>


        <p class="mb-12 pb-md-4 text-center">Have questions about accommodation, payments, or maintenance? We're here to help.</p>


        <div class="row g-6">
            <div class="col-lg-5">
                <div class="position-relative p-2 border h-100 contact-img-box">
                    <img
                        src="../../assets/img/front-pages/icons/contact-border.png"
                        alt="contact border"
                        class="d-lg-block position-absolute contact-border-img d-none scaleX-n1-rtl" />
                    <!-- <img
                        src="../../assets/img/front-pages/landing-page/contact-customer-service.png"
                        alt="hostel management support team"
                        class="w-100 contact-img scaleX-n1-rtl" /> -->

                    <img src="../../assets/img/admin-2.png" alt="hostel management support team"
                        class="w-100 contact-img scaleX-n1-rtl">


                    <div class="p-4 pb-2">
                        <div class="row g-4">
                            <div class="col-md-6 col-lg-12 col-xl-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-label-primary me-3 p-1_5 rounded badge">
                                        <i class="icon-base bx bx-envelope icon-lg"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0">Email</p>
                                        <h6 class="mb-0">
                                            <a href="mailto:kingshostelmgt@gmail.com" class="text-heading">kingshostelmgt@gmail.com</a>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-12 col-xl-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-label-success me-3 p-1_5 rounded badge">
                                        <i class="icon-base bx bx-phone-call icon-lg"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0">Hostel Office</p>
                                        <h6 class="mb-0"><a href="tel:+233549684848" class="text-heading">+233 54 968 4848</a></h6>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 col-12">
                                <div class="d-flex align-items-center">
                                    <div class="bg-label-warning me-3 p-1_5 rounded badge">
                                        <i class="icon-base bx bx-time icon-lg"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0">Office Hours</p>
                                        <h6 class="mb-0">Monday - Friday, 8:00AM - 6:00PM</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 col-12">
                                <div class="d-flex align-items-center">
                                    <div class="bg-label-info me-3 p-1_5 rounded">
                                        <i class="icon-base bx bx-map icon-lg"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0">Location</p>
                                        <h6 class="mb-0">Kings Hostel, University Campus</h6>
                                    </div>
                                </div>
                            </div>
                            <!-- <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3970.4313868432873!2d-0.19879932493184974!3d5.650561994330763!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfdf9c7ebaeabe93%3A0xd78257e67498c1a0!2sUniversity%20of%20Ghana!5e0!3m2!1sen!2sgh!4v1755427755909!5m2!1sen!2sgh" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe> -->
                        </div>
                    </div>
                </div>
            </div>



            <div class="col-lg-7">
                <div class="h-100 card">
                    <div class="card-body">
                        <h4 class="mb-2 text-center">Send us a message</h4>
                        <p class="mb-6 text-center">
                            Need assistance with room bookings, maintenance requests, or payment inquiries?<br
                                class="d-lg-block d-none" />
                            Our hostel management team is ready to assist you.
                        </p>
                        <form id="hostelContactForm" method="post" action="send-contact-form">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label" for="contact-form-fullname">Full Name</label>
                                    <input type="text" class="form-control" id="contact-form-fullname" name="fullName" placeholder="Your full name" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="contact-form-email">Email</label>
                                    <input
                                        type="email"
                                        id="contact-form-email"
                                        name="email"
                                        class="form-control"
                                        placeholder="your.email@example.com"
                                        required />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="contact-form-phone">Phone Number</label>
                                    <input
                                        type="tel"
                                        name="phoneNumber"
                                        maxlength="13"
                                        id="contact-form-phone"
                                        class="form-control"
                                        placeholder="Your phone number" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="contact-form-subject">Subject</label>
                                    <select class="form-select" id="contact-form-subject" name="subject" required>
                                        <option value="" selected disabled>Select an option</option>
                                        <option value="Room Booking">Room Booking</option>
                                        <option value="Maintenance Request">Maintenance Request</option>
                                        <option value="Payment Inquiry">Payment Inquiry</option>
                                        <option value="Room Change Request">Room Change Request</option>
                                        <option value="General Inquiry">General Inquiry</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="contact-form-message">Message</label>
                                    <textarea
                                        id="contact-form-message"
                                        class="form-control"
                                        rows="12"
                                        name="message"
                                        placeholder="Please provide details about your inquiry"
                                        required></textarea>
                                </div>
                                <div class="col-12">
                                    <input type="submit" value="Send Message" class="btn btn-primary" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Contact Us: End -->

<script>
    document.addEventListener("DOMContentLoaded", function() {

        $('#contact-form-subject').select2({
            placeholder: 'Select an option',
            allowClear: true
        });

        const contactForm = document.getElementById('hostelContactForm');

        if (contactForm) {
            contactForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(this);
                const submitButton = this.querySelector('input[type="submit"]');
                const originalText = submitButton.value;

                // Disable button and show loading state
                submitButton.disabled = true;
                submitButton.value = 'Sending...';

                Swal.fire({
                    title: 'Sending Message...',
                    text: 'Please wait while we send your message.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                fetch('/send-contact-form', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Message Sent Successfully!',
                                text: data.message,
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false,
                                timer: 5000,
                                timerProgressBar: true,
                                showCloseButton: true
                            }).then(() => {

                                contactForm.reset();

                                $('#contact-form-subject').val(null).trigger('change');

                                const formControls = contactForm.querySelectorAll('.form-control, .form-select');
                                formControls.forEach(control => {
                                    control.classList.remove('is-valid', 'is-invalid');
                                });

                                // Clear any validation feedback
                                const feedbacks = contactForm.querySelectorAll('.valid-feedback, .invalid-feedback');
                                feedbacks.forEach(feedback => {
                                    feedback.style.display = 'none';
                                });

                                $('.select2-selection').removeClass('is-valid is-invalid');
                            })


                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed to Send Message',
                                text: data.error || 'An error occurred while sending your message. Please try again.',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false,
                                showCloseButton: true
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.close();

                        Swal.fire({
                            icon: 'error',
                            title: 'Network Error',
                            text: 'A network error occurred. Please check your connection and try again.',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false,
                            showCloseButton: true
                        });
                    })
                    .finally(() => {
                        // Re-enable button
                        submitButton.disabled = false;
                        submitButton.value = originalText;
                    });
            });
        }

    })
</script>