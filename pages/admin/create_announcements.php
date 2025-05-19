<?php
require_once __DIR__ . "/../../app/controllers/AnnouncementController.php";


$announcementController = new AnnouncementController();
$adminId = $_SESSION['user']['admin_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $priority = $_POST['priority'] ?? 'Medium';
    $target_mode = $_POST['target_mode'] ?? 'bulk';
    $target_audience = $target_mode === 'bulk' ? ($_POST['bulk_target_audience'] ?? 'All') : 'Specific';
    $specific_target_type = $target_mode === 'specific' ? ($_POST['specific_target_type'] ?? '') : null;
    $specific_target_id = $target_mode === 'specific' ? ($_POST['specific_target_id'] ?? null) : null;

    $result = $announcementController->createAnnouncement(
        $adminId,
        $title,
        $content,
        $priority,
        $target_audience,
        $specific_target_type,
        $specific_target_id
    );

    if ($result) {
        header("Location: /admin/announcements?status=success");
        exit;
    } else {
        $error = "Failed to create announcement.";
        error_log("Failed to create announcement: " . print_r(error_get_last(), true));
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="layout-navbar-fixed layout-navbar-sticky layout-menu-fixed layout-menu-collapsed layout-compact" dir="ltr" data-skin="default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Kings Hostel - Create Announcement</title>
    <meta name="description" content="Create a new hostel announcement." />
    <base href="/">
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
    <link rel="stylesheet" href="../../assets/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/@form-validation/form-validation.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/summernote/summernote-bs5.css" />

    <!-- Custom CSS for Summernote -->
    <style>
        .note-editor.note-frame {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .note-editable {
            min-height: 200px;
            background: #fff;
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
                <?php include_once __DIR__ . "/../../Components/admin/header.php" ?>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h5 class="card-title mb-0">Create New Announcement</h5>
                            </div>
                            <div class="card-body">
                                <form id="createAnnouncementForm" method="POST" action="/admin/announcements/create">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Content</label>
                                        <textarea class="form-control summernote" id="content" name="content" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="priority" class="form-label">Priority</label>
                                        <select class="form-select" id="priority" name="priority" required>
                                            <option value="Low">Low</option>
                                            <option value="Medium" selected>Medium</option>
                                            <option value="High">High</option>
                                            <option value="Urgent">Urgent</option>
                                        </select>
                                    </div>
                                    <!-- Tabs for Bulk and Specific Targets -->
                                    <ul class="nav nav-tabs" id="announcementTabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="bulk-tab" data-bs-toggle="tab" href="#bulk" role="tab">Bulk</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="specific-tab" data-bs-toggle="tab" href="#specific" role="tab">Specific</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content mt-3">
                                        <!-- Bulk Tab -->
                                        <div class="tab-pane fade show active" id="bulk" role="tabpanel">
                                            <div class="mb-3">
                                                <label for="bulk_target_audience" class="form-label">Target Audience</label>
                                                <select class="form-select" id="bulk_target_audience" name="bulk_target_audience" required>
                                                    <option value="" disabled selected>Select target audience</option>
                                                    <option value="All" selected>All</option>
                                                    <option value="Students">Students</option>
                                                    <option value="Admins">Admins</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- Specific Tab -->
                                        <div class="tab-pane fade" id="specific" role="tabpanel">
                                            <div class="mb-3">
                                                <label for="specific_target_type" class="form-label">Target Type</label>
                                                <select class="form-select" id="specific_target_type" name="specific_target_type" required>
                                                    <option value="" disabled selected>Select target type</option>
                                                    <option value="student">Student</option>
                                                    <option value="admin">Admin</option>
                                                    <option value="building">Building</option>
                                                    <option value="room">Room</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="specific_target_id" class="form-label">Select Target</label>
                                                <select class="form-select" id="specific_target_id" name="specific_target_id" required></select>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="action" value="create">
                                    <input type="hidden" name="target_mode" id="target_mode" value="bulk">
                                    <div class="mt-3">
                                        <a href="/admin/announcements" class="btn btn-label-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Post Announcement</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->

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

    <!-- Vendors JS -->
    <script src="../../assets/vendor/libs/summernote/summernote-bs5.js"></script>
    <script src="../../assets/vendor/libs/select2/select2.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/auto-focus.js"></script>
    <script src="../../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <!-- Main JS -->
    <script src="../../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../../assets/js/admin-create-announcements.js"></script>

</body>

</html>