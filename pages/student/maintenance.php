<?php
require_once "./database/db.php";
require_once "./app/models/Student.php";
require_once "./app/controllers/MaintenanceController.php";

// Check if user is authenticated
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Student') {
    header("Location: /login");
    exit();
}

$maintenanceModel = new MaintenanceRequest();
$controller = new MaintenanceController();

$user_id = $_SESSION['user']['user_id'];
$student_id = $_SESSION['user']['student_id'] ?? 0;
$db = new Database();
$conn = $db->connect();
$student = new Student($conn);

// Fetch student's first name
$first_name = $student->getFirstName($user_id);

// Fetch maintenance stats
$total_requests = count($maintenanceModel->getRequestsByStudent($student_id)['data']);
$pending_requests = $maintenanceModel->getPendingRequest($student_id);
$in_progress_requests = $maintenanceModel->getInProgressRequest($student_id);
$resolved_requests = $maintenanceModel->getResolvedRequest($student_id);

// Fetch users room
$room = $student->getRoomAllocation($student_id);
// echo "<pre>";
// print_r($room);
// echo "</pre>";
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

    <title>Kings Hostel - Maintenance</title>
    <meta name="description" content="Submit and manage your hostel maintenance requests" />

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

    <!-- Icons -->
    <link rel="stylesheet" href="../../assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="../../assets/vendor/fonts/fontawesome.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../../assets/vendor/libs/pickr/pickr-themes.css" />
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

    <!-- Custom CSS -->
    <style>
        .timeline-indicator {
            box-shadow: none !important;
            border: none !important;
        }

        .timeline-indicator i {
            color: #696cff;
        }

        .timeline-item .timeline-event {
            margin-left: 1rem;
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

                        <!-- Stats Cards -->
                        <div class="row g-6 mb-6">
                            <div class="col-sm-6 col-lg-3">
                                <div class="card card-border-shadow-primary h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar me-4">
                                                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-wrench icon-lg"></i></span>
                                            </div>
                                            <h4 class="mb-0"><?= $total_requests ?></h4>
                                        </div>
                                        <p class="mb-0">Total Requests</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="card card-border-shadow-warning h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar me-4">
                                                <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-time icon-lg"></i></span>
                                            </div>
                                            <h4 class="mb-0"><?= $pending_requests ?></h4>
                                        </div>
                                        <p class="mb-0">Pending</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="card card-border-shadow-info h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar me-4">
                                                <span class="avatar-initial rounded bg-label-info"><i class="bx bx-loader-circle icon-lg"></i></span>
                                            </div>
                                            <h4 class="mb-0"><?= $in_progress_requests ?></h4>
                                        </div>
                                        <p class="mb-0">In-Progress</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="card card-border-shadow-success h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar me-4">
                                                <span class="avatar-initial rounded bg-label-success"><i class="bx bx-check-circle icon-lg"></i></span>
                                            </div>
                                            <h4 class="mb-0"><?= $resolved_requests ?></h4>
                                        </div>
                                        <p class="mb-0">Completed</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Maintenance Table -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Your Maintenance Requests</h5>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newMaintenanceModal">
                                    <i class="bx bx-plus me-1"></i> New Request
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-3 mb-3 mb-md-0">
                                        <select id="typeFilter" class="form-select">
                                            <option value="">All Types</option>
                                            <option value="Plumbing">Plumbing</option>
                                            <option value="Electrical">Electrical</option>
                                            <option value="Furniture">Furniture</option>
                                            <option value="Appliance">Appliance</option>
                                            <option value="Structural">Structural</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3 mb-md-0">
                                        <select id="priorityFilter" class="form-select">
                                            <option value="">All Priorities</option>
                                            <option value="Low">Low</option>
                                            <option value="Medium">Medium</option>
                                            <option value="High">High</option>
                                            <option value="Emergency">Emergency</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select id="statusFilter" class="form-select">
                                            <option value="">All Statuses</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Assigned">Assigned</option>
                                            <option value="In-Progress">In-Progress</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Rejected">Rejected</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table datatables-maintenance">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Type</th>
                                                <th>Description</th>
                                                <th>Priority</th>
                                                <th>Status</th>
                                                <th>Submitted</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->

                    <!-- New Maintenance Request Modal -->
                    <div class="modal fade" id="newMaintenanceModal" tabindex="-1" aria-labelledby="newMaintenanceModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="newMaintenanceModalLabel">New Maintenance Request</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="newMaintenanceForm">
                                        <?php set_csrf() ?>
                                        <div class="mb-3">
                                            <label for="issueType" class="form-label">Issue Type</label>
                                            <select id="issueType" name="issue_type" class="form-select" required>
                                                <option value="">Select Type</option>
                                                <option value="Plumbing">Plumbing</option>
                                                <option value="Electrical">Electrical</option>
                                                <option value="Furniture">Furniture</option>
                                                <option value="Appliance">Appliance</option>
                                                <option value="Structural">Structural</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="roomId" class="form-label">Room</label>
                                            <select id="roomId" name="room_id" class="form-select" required>
                                                <?php if ($room): ?>
                                                    <option value="<?= $room['room_id'] ?>">
                                                        <?= htmlspecialchars($room['building'] . ' - Room ' . $room['room_number'] . ' (Floor ' . $room['floor'] . ')') ?>
                                                    </option>
                                                <?php else: ?>
                                                    <option value="">No room assigned</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea id="description" name="description" class="form-control" rows="4" required placeholder="Describe the issue..."></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="priority" class="form-label">Priority</label>
                                            <select id="priority" name="priority" class="form-select" required>
                                                <option value="Low">Low</option>
                                                <option value="Medium" selected>Medium</option>
                                                <option value="High">High</option>
                                                <option value="Emergency">Emergency</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Submit Request</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Maintenance Details Modal -->
                    <div class="modal fade" id="maintenanceDetailsModal" tabindex="-1" aria-labelledby="maintenanceDetailsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title mb-2 text-white" id="maintenanceDetailsModalLabel">Maintenance Request Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <!-- Status banner -->
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div>
                                            <h6>Request ID: <span id="modalRequestId"></span></h6>
                                            <div id="modalSubmittedTimeAgo"></div>
                                        </div>
                                        <div id="modalRequestStatus"></div>
                                    </div>

                                    <!-- Key information cards -->
                                    <div class="row mb-4">
                                        <div class="col-md-4 mb-3">
                                            <div class="card shadow-none border h-100">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="bx bx-category text-primary me-2 icon-md"></i>
                                                        <h6 class="mb-0">Type</h6>
                                                    </div>
                                                    <p class="mb-0" id="modalIssueType"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card shadow-none border h-100">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="bx bx-flag text-primary me-2 icon-md"></i>
                                                        <h6 class="mb-0">Priority</h6>
                                                    </div>
                                                    <div id="modalRequestPriority"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card shadow-none border h-100">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="bx bx-building text-primary me-2 icon-md"></i>
                                                        <h6 class="mb-0">Location</h6>
                                                    </div>
                                                    <p class="mb-0" id="modalRequestRoom"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Timeline section -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header d-flex align-items-center pb-2">
                                                    <i class="bx bx-message-square-detail text-primary me-2 icon-md"></i>
                                                    <h5 class="card-title mb-0">Details & Updates</h5>
                                                </div>
                                                <div class="card-body pt-3">
                                                    <div class="border rounded p-3 mb-3 bg-light-primary">
                                                        <h6 class="mb-2">Description</h6>
                                                        <p class="mb-0" id="modalRequestDescription"></p>
                                                    </div>

                                                    <div id="maintenanceTimeline" class="timeline mt-4">
                                                        <!-- Submission event -->
                                                        <div class="timeline-item">
                                                            <span class="timeline-indicator timeline-indicator-success">
                                                                <i class="icon-base bx bx-send"></i>
                                                            </span>
                                                            <div class="timeline-event">
                                                                <div class="timeline-header mb-1">
                                                                    <h6 class="mb-0">Request Submitted</h6>
                                                                    <small class="text-muted" id="modalRequestDate"></small>
                                                                </div>
                                                                <p class="mb-2">Your maintenance request has been registered in our system.</p>
                                                            </div>
                                                        </div>
                                                        <!-- Responses will be loaded here -->
                                                        <div id="responseSection"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="followUpBtn" style="display: none;">
                                        <i class="icon-base bx bx-comment-add icon-lg me-1"></i> Add Follow-up
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

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
    <script src="../../assets/vendor/libs/@algolia/autocomplete-js.js"></script>
    <script src="../../assets/vendor/libs/pickr/pickr.js"></script>
    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../../assets/vendor/libs/i18n/i18n.js"></script>
    <script src="../../assets/vendor/js/menu.js"></script>

    <!-- Vendors JS -->
    <script src="../../assets/vendor/libs/moment/moment.js"></script>
    <script src="../../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/select2/select2.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/auto-focus.js"></script>
    <script src="../../assets/vendor/libs/cleave-zen/cleave-zen.js"></script>
    <script src="../../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <!-- Main JS -->
    <script src="../../assets/js/main.js"></script>

    <script src="../../assets/js/extended-ui-timeline.js"></script>

    <!-- Page JS -->
    <script src="../../assets/js/app-maintenance-list.js"></script>
</body>

</html>