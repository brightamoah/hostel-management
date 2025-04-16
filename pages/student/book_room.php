<?php


?>


<!DOCTYPE html>
<html
    lang="en"
    class="layout-navbar-sticky layout-menu-fixed layout-compact"
    dir="ltr"
    data-skin="default"
    data-assets-path="../../assets/"
    data-template="vertical-menu-template"
    data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Kings Hostel - Student Dashboard</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="../../assets/img/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../assets/img/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/img/favicon_io/favicon-16x16.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="../../assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="../../assets/vendor/fonts/fontawesome.css" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="../../assets/vendor/libs/pickr/pickr-themes.css" />

    <link rel="stylesheet" href="../../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- endbuild -->

    <link rel="stylesheet" href="../../assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/@form-validation/form-validation.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="../../assets/vendor/js/template-customizer.js"></script>

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="../../assets/js/config.js"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            <?php include_once "./Components/sidebar.php" ?>

            <div class="menu-mobile-toggler d-xl-none rounded-1">
                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
                    <i class="bx bx-menu icon-base"></i>
                    <i class="bx bx-chevron-right icon-base"></i>
                </a>
            </div>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <?php include_once "./Components/header.php" ?>

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">



                        <div class="col-xl">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Basic with Icons</h5>
                                    <small class="text-body float-end">Merged input group</small>
                                </div>
                                <div class="card-body">
                                    <form>
                                        <div class="mb-6">
                                            <label class="form-label" for="basic-icon-default-fullname">Full Name</label>
                                            <div class="input-group input-group-merge">
                                                <span id="basic-icon-default-fullname2" class="input-group-text"><i class="icon-base bx bx-user"></i></span>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="basic-icon-default-fullname"
                                                    placeholder="John Doe"
                                                    aria-label="John Doe"
                                                    aria-describedby="basic-icon-default-fullname2" />
                                            </div>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="basic-icon-default-company">Company</label>
                                            <div class="input-group input-group-merge">
                                                <span id="basic-icon-default-company2" class="input-group-text"><i class="icon-base bx bx-buildings"></i></span>
                                                <input
                                                    type="text"
                                                    id="basic-icon-default-company"
                                                    class="form-control"
                                                    placeholder="ACME Inc."
                                                    aria-label="ACME Inc."
                                                    aria-describedby="basic-icon-default-company2" />
                                            </div>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="basic-icon-default-email">Email</label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text"><i class="icon-base bx bx-envelope"></i></span>
                                                <input
                                                    type="text"
                                                    id="basic-icon-default-email"
                                                    class="form-control"
                                                    placeholder="john.doe"
                                                    aria-label="john.doe"
                                                    aria-describedby="basic-icon-default-email2" />
                                                <span id="basic-icon-default-email2" class="input-group-text">@example.com</span>
                                            </div>
                                            <div class="form-text">You can use letters, numbers & periods</div>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="basic-icon-default-phone">Phone No</label>
                                            <div class="input-group input-group-merge">
                                                <span id="basic-icon-default-phone2" class="input-group-text"><i class="icon-base bx bx-phone"></i></span>
                                                <input
                                                    type="text"
                                                    id="basic-icon-default-phone"
                                                    class="form-control phone-mask"
                                                    placeholder="658 799 8941"
                                                    aria-label="658 799 8941"
                                                    aria-describedby="basic-icon-default-phone2" />
                                            </div>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="basic-icon-default-message">Message</label>
                                            <div class="input-group input-group-merge">
                                                <span id="basic-icon-default-message2" class="input-group-text"><i class="icon-base bx bx-comment"></i></span>
                                                <textarea
                                                    id="basic-icon-default-message"
                                                    class="form-control"
                                                    placeholder="Hi, Do you have a moment to talk Joe?"
                                                    aria-label="Hi, Do you have a moment to talk Joe?"
                                                    aria-describedby="basic-icon-default-message2"></textarea>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Send</button>
                                    </form>
                                </div>
                            </div>
                        </div>



                    </div>
                </div>
                <!-- / Content -->

                <!-- Footer -->
                <?php include_once "./Components/footer.php" ?>
                <!-- / Footer -->

                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/theme.js  -->

    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>

    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/@algolia/autocomplete-js.js"></script>

    <script src="../../assets/vendor/libs/pickr/pickr.js"></script>

    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../../assets/vendor/libs/hammer/hammer.js"></script>

    <script src="../../assets/vendor/libs/i18n/i18n.js"></script>

    <script src="../../assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../../assets/vendor/libs/moment/moment.js"></script>
    <script src="../../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/select2/select2.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/auto-focus.js"></script>
    <script src="../../assets/vendor/libs/cleave-zen/cleave-zen.js"></script>

    <!-- Main JS -->

    <script src="../../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../../assets/js/app-visitor-list.js"></script>

</body>

</html>