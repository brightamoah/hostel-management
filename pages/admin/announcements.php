<?php

require_once __DIR__ . "/../../app/controllers/AnnouncementController.php";

$announcementController = new AnnouncementController();
?>

<!DOCTYPE html>
<html lang="en" class="layout-menu-fixed layout-navbar-fixed layout-navbar-sticky layout-compact" dir="ltr" data-skin="default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Kings Hostel - Admin Announcements</title>
    <meta name="description" content="Manage hostel announcements efficiently." />
    <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf'] ?? ''); ?>">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="../../assets/img/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../assets/img/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/img/favicon_io/favicon-16x16.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="../../assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/@form-validation/form-validation.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/summernote/summernote-bs5.css" />

    <!-- Custom CSS -->
    <style>
        .priority-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }



        .dataTables_wrapper .dataTables_filter {
            display: none;
        }

        /* Hide default DataTables search */
        .filter-container {
            margin-bottom: 1.5rem;
        }

        .filter-container select {
            min-width: 150px;
        }
    </style>

    <!-- Helpers -->
    <script src="../../assets/vendor/js/helpers.js"></script>
    <script src="../../assets/vendor/js/template-customizer.js"></script>
    <script src="../../assets/js/config.js"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-content-navbar layout-wrapper">
        <div class="layout-container">
            <!-- Menu -->
            <?php include_once __DIR__ . "/../../Components/sidebar.php" ?>
            <div class="rounded-1 menu-mobile-toggler d-xl-none">
                <a href="javascript:void(0);" class="p-2 rounded-1 text-bg-secondary text-large layout-menu-toggle menu-link">
                    <i class="bx bx-menu icon-base"></i>
                    <i class="bx-chevron-right bx icon-base"></i>
                </a>
            </div>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <?php include_once __DIR__ . "/../../Components/admin/header.php" ?>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="flex-grow-1 container-p-y container-xxl">
                        <div class="card">
                            <div class="d-flex align-items-center justify-content-between card-header">
                                <h5 class="mb-0">Announcements</h5>
                                <div>
                                    <button class="refresh-table me-2 btn btn-primary">
                                        <i class="me-1 bx bx-refresh"></i> Refresh
                                    </button>
                                    <a href="/admin/announcements/create" class="btn btn-primary">
                                        <i class="me-md-1 bx bx-plus"></i>
                                        <span class="d-md-inline-block d-none">New Announcement</span>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Search And Filters -->
                                <?php include_once __DIR__ . "/../../Components/admin/announcements/search-filters.php" ?>
                                <div class="table-responsive">
                                    <table class="table datatables-announcements">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Posted By</th>
                                                <th>Priority</th>
                                                <th>Target Audience</th>
                                                <th>Date Posted</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->

                    <!-- View Announcement Modal -->
                    <?php include_once __DIR__ . "/../../Components/admin/announcements/view-modal.php" ?>

                    <!-- Footer -->
                    <?php include_once __DIR__ . "/../../Components/footer.php" ?>
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
    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../../assets/vendor/js/menu.js"></script>
    <script src="../../assets/vendor/libs/jszip/jszip.js"></script>
    <script src="../../assets/vendor/libs/pdfmake/pdfmake.js"></script>
    <script src="../../assets/vendor/libs/pdfmake/vfs_fonts.js"></script>
    <script src="../../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/datatables-buttons/datatables-buttons.js"></script>
    <script src="../../assets/vendor/libs/datatables-buttons-bs5/buttons-bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/select2/select2.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/auto-focus.js"></script>
    <script src="../../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="../../assets/vendor/libs/moment/moment.js"></script>
    <script src="../../assets/vendor/libs/summernote/summernote-bs5.js"></script>

    <!-- Main JS -->
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/extended-ui-sweetalert2.js"></script>

    <!-- Page JS -->
    <script src="../../assets/js/admin-announcement-new.js"></script>
</body>

</html>