<?php
require_once __DIR__ . "/../../app/controllers/AnnouncementController.php";

$announcementController = new AnnouncementController();
$announcements = $announcementController->getAllAnnouncements();
?>

<!DOCTYPE html>
<html lang="en" class="layout-navbar-fixed layout-navbar-sticky layout-menu-fixed layout-menu-collapsed layout-compact" dir="ltr" data-skin="default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Kings Hostel - Admin Announcements</title>
    <meta name="description" content="Manage hostel announcements efficiently." />

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
    <link rel="stylesheet" href="../../assets/vendor/fonts/fontawesome.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
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
        .announcement-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .announcement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .announcement-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .announcement-content {
            padding: 1.5rem;
            max-height: 150px;
            overflow: hidden;
            position: relative;
        }

        .announcement-content:after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            height: 50px;
            width: 100%;

        }

        .announcement-footer {
            padding: 1rem 1.5rem;
            background-color: rgba(0, 0, 0, 0.02);
        }

        .priority-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .priority-urgent {
            background-color: #ff3e1d;
        }

        .priority-high {
            background-color: #ff9f43;
        }

        .priority-medium {
            background-color: #3a97f9;
        }

        .priority-low {
            background-color: #28c76f;
        }

        /* .search-bar {
            border-radius: 2rem;
            padding-left: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        } */

        .filter-badge {
            font-size: 0.75rem;
            padding: 0.7rem 0.5rem;
            margin-right: 0.5rem;
            border-radius: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            display: inline-flex;
        }

        /* .filter-badge.active {
            background-color: #5a8dee;
            color: #fff;
        }

        .filter-badge:hover:not(.active) {
            background-color: #eaecef;
        } */

        .announcement-title {
            display: -webkit-box;
            line-clamp: 1;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .announcement-list-view .announcement-item {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .announcement-list-view .announcement-item:hover {
            background-color: rgba(90, 141, 238, 0.05);
            border-left-color: var(--bs-primary);
        }

        .view-toggle-btn.active {
            color: var(--bs-primary);
            background-color: rgba(var(--bs-primary-rgb, 90, 141, 238), 0.2);
        }
    </style>



    <!-- Helpers -->
    <script src="../../assets/vendor/js/helpers.js"></script>
    <script src="../../assets/vendor/js/template-customizer.js"></script>
    <script src="../../assets/js/config.js"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <?php include_once __DIR__ . "/../../Components/sidebar.php" ?>

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
                <?php include_once "./Components/admin/header.php" ?>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="card">
                            <div class="card-header pb-0">
                                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                    <div>

                                        <p class="mb-3">Manage and communicate important information to the hostel community</p>
                                    </div>
                                    <div class="d-flex">
                                        <div class="btn-group me-3 view-toggle" role="group" aria-label="View toggle">
                                            <button type="button" class="btn btn-icon rounded-pill view-toggle-btn active" data-view="grid">
                                                <i class="bx bx-grid-alt"></i>
                                            </button>
                                            <button type="button" class="btn btn-icon rounded-pill view-toggle-btn" data-view="list">
                                                <i class="bx bx-list-ul"></i>
                                            </button>
                                        </div>
                                        <a href="/admin/announcements/create" class="btn btn-primary">
                                            <i class="bx bx-plus me-md-1"></i>
                                            <span class="d-none d-md-inline-block">New Announcement</span>
                                        </a>
                                    </div>
                                </div>

                                <!-- Search And Filters -->
                                <?php include_once __DIR__ . "/../../Components/admin/announcements/search-filters.php" ?>


                                <div class="filter-tags d-flex flex-wrap mb-3">
                                    <!-- Active filters will be dynamically added here via JS -->
                                </div>
                            </div>

                            <div class="card-body">
                                <!-- Grid View (Default) -->
                                <?php include_once __DIR__ . "/../../Components/admin/announcements/grid-view.php" ?>


                                <!-- List View (Hidden by default) -->
                                <?php include_once __DIR__ . "/../../Components/admin/announcements/list-view.php" ?>

                                <!-- No Results Message -->
                                <div id="noResults" class="text-center py-5 d-none">
                                    <img src="../../assets/img/illustrations/page-misc-error-light.png" data-app-light-img="illustrations/page-misc-error-light.png" data-app-dark-img="illustrations/page-misc-error-dark.png" alt="No results" class="mb-4" height="180">
                                    <h4 class="mb-2">No Announcements Found</h4>
                                    <p class="text-muted">We couldn't find any announcements matching your criteria. Try adjusting your search or filters.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / Content -->

                <!-- View Announcement Modal -->
                <?php include_once __DIR__ . "/../../Components/admin/announcements/view-modal.php" ?>


                <!-- / Modals -->

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
    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../../assets/vendor/js/menu.js"></script>

    <!-- Vendors JS -->
    <script src="../../assets/vendor/libs/summernote/summernote-bs5.js"></script>
    <script src="../../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/select2/select2.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/auto-focus.js"></script>
    <script src="../../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <!-- Main JS -->
    <script src="../../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../../assets/js/admin-announcement-new.js"></script>

    
    </script>

    <!-- <script>
       

        $(document).ready(function() {
            $('#viewAnnouncementModal').on('show.bs.modal', function() {
                // Add animation class
                $(this).find('.modal-content').addClass('animate__animated animate__fadeIn');

                // Format content elements for better styling
                setTimeout(function() {
                    const content = $('#view_content');

                    // Add Bootstrap classes to tables
                    content.find('table').addClass('table table-bordered');

                    // Add Bootstrap classes to images
                    content.find('img').addClass('img-fluid');

                    // Add shadow to code blocks
                    content.find('pre, code').addClass('shadow-sm');
                }, 100);
            });

            // Remove the classes when hiding the modal
            $('#viewAnnouncementModal').on('hide.bs.modal', function() {
                $(this).find('.modal-content').removeClass('animate__animated animate__fadeIn');
            });
        });
    </script> -->
</body>

</html>