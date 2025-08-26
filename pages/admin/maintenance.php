<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../../app/models/MaintenanceRequest.php";

// Check if user is authenticated and is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: /login");
    exit();
}

$maintenanceModel = new MaintenanceRequest();

$conn = getDb();

// Fetch maintenance stats
$total_requests = count($maintenanceModel->getAllRequests()['data']);
$pending_requests = $maintenanceModel->getPendingRequest();
$in_progress_requests = $maintenanceModel->getInProgressRequest();
$resolved_requests = $maintenanceModel->getResolvedRequest();
$rejected_requests = $maintenanceModel->getRejectedRequest();
?>

<!DOCTYPE html>
<html lang="en" class="layout-menu-fixed layout-navbar-fixed layout-navbar-sticky layout-compact" dir="ltr" data-skin="default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Kings Hostel - Admin Maintenance Dashboard</title>
    <meta name="description" content="Manage hostel maintenance requests" />
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
    <div class="layout-content-navbar layout-wrapper">
        <div class="layout-container">
            <!-- Menu -->
            <?php include_once "./Components/sidebar.php" ?>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <?php include_once "./Components/admin/header.php" ?>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="flex-grow-1 container-p-y container-xxl">
                        <!-- Stats Cards -->
                        <div class="mb-6 row g-6">
                            <div class="col-sm-6 col-lg-3">
                                <div class="card-border-shadow-primary h-100 card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-4 avatar">
                                                <span class="bg-label-primary rounded avatar-initial"><i class="bx bx-wrench icon-lg"></i></span>
                                            </div>
                                            <h4 class="mb-0"><?php echo $total_requests; ?></h4>
                                        </div>
                                        <p class="mb-0">Total Requests</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="card-border-shadow-warning h-100 card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-4 avatar">
                                                <span class="bg-label-warning rounded avatar-initial"><i class="bx bx-time icon-lg"></i></span>
                                            </div>
                                            <h4 class="mb-0"><?php echo $pending_requests; ?></h4>
                                        </div>
                                        <p class="mb-0">Pending</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="card-border-shadow-info h-100 card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-4 avatar">
                                                <span class="bg-label-info rounded avatar-initial"><i class="bx bx-loader-circle icon-lg"></i></span>
                                            </div>
                                            <h4 class="mb-0"><?php echo $in_progress_requests; ?></h4>
                                        </div>
                                        <p class="mb-0">In-Progress</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="card-border-shadow-success h-100 card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-4 avatar">
                                                <span class="bg-label-success rounded avatar-initial"><i class="bx bx-check-circle icon-lg"></i></span>
                                            </div>
                                            <h4 class="mb-0"><?php echo $resolved_requests; ?></h4>
                                        </div>
                                        <p class="mb-0">Completed</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="card-border-shadow-danger h-100 card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-4 avatar">
                                                <span class="bg-label-danger rounded avatar-initial"><i class="bx bx-x-circle icon-lg"></i></span>
                                            </div>
                                            <h4 class="mb-0"><?php echo $rejected_requests; ?></h4>
                                        </div>
                                        <p class="mb-0">Rejected</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Maintenance Table -->
                        <div class="card">
                            <div class="d-flex align-items-center justify-content-between card-header">
                                <h5 class="mb-0">Maintenance Requests</h5>
                                <div>
                                    <button class="refresh-table me-2 btn btn-primary">
                                        <i class="me-1 bx bx-refresh"></i> Refresh
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-4 row">
                                    <div class="mb-3 mb-md-0 col-md-3">
                                        <select id="typeFilter" class="form-select">
                                            <option value="">All Types</option>
                                            <option value="Plumbing">Plumbing</option>
                                            <option value="Electrical">Electrical</option>
                                            <option value="Furniture">Furniture</option>
                                            <option value="Appliance">Appliance</option>
                                            <option value="Structural">Structural</option>
                                            <option value="Pest Control">Pest Control</option>
                                            <option value="Internet/Wi-Fi">Internet/Wi-Fi</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 mb-md-0 col-md-3">
                                        <select id="priorityFilter" class="form-select">
                                            <option value="">All Priorities</option>
                                            <option value="Low">Low</option>
                                            <option value="Medium">Medium</option>
                                            <option value="High">High</option>
                                            <option value="Emergency">Emergency</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 mb-md-0 col-md-3">
                                        <select id="statusFilter" class="form-select">
                                            <option value="">All Statuses</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Assigned">Assigned</option>
                                            <option value="In-Progress">In-Progress</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Rejected">Rejected</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" id="maintenanceSearch" class="form-control" placeholder="Search Request">
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table datatables-maintenance">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Student</th>
                                                <th>Room</th>
                                                <th>Type</th>
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

                    <!-- Maintenance Details Modal -->
                    <div class="modal fade" id="maintenanceDetailsModal" tabindex="-1" aria-labelledby="maintenanceDetailsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="bg-primary modal-header">
                                    <h5 class="mb-2 text-white modal-title" id="maintenanceDetailsModalLabel">Maintenance Request Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="p-4 modal-body">
                                    <!-- Status banner -->
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div>
                                            <h6>Request ID: <span id="modalRequestId"></span></h6>
                                            <div id="modalSubmittedTimeAgo"></div>
                                        </div>
                                        <div id="modalRequestStatus"></div>
                                    </div>

                                    <!-- Key information cards -->
                                    <div class="mb-4 row">
                                        <div class="mb-3 col-md-4">
                                            <div class="shadow-none border h-100 card">
                                                <div class="p-3 card-body">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="me-2 text-primary bx bx-user icon-md"></i>
                                                        <h6 class="mb-0">Student</h6>
                                                    </div>
                                                    <p class="mb-0" id="modalStudentName"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4">
                                            <div class="shadow-none border h-100 card">
                                                <div class="p-3 card-body">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="me-2 text-primary bx bx-category icon-md"></i>
                                                        <h6 class="mb-0">Type</h6>
                                                    </div>
                                                    <p class="mb-0" id="modalIssueType"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4">
                                            <div class="shadow-none border h-100 card">
                                                <div class="p-3 card-body">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="me-2 text-primary bx bx-flag icon-md"></i>
                                                        <h6 class="mb-0">Priority</h6>
                                                    </div>
                                                    <div id="modalRequestPriority"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4">
                                            <div class="shadow-none border h-100 card">
                                                <div class="p-3 card-body">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="me-2 text-primary bx bx-building icon-md"></i>
                                                        <h6 class="mb-0">Room</h6>
                                                    </div>
                                                    <p class="mb-0" id="modalRequestRoom"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4">
                                            <div class="shadow-none border h-100 card">
                                                <div class="p-3 card-body">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="me-2 text-primary bx bx-calendar icon-md"></i>
                                                        <h6 class="mb-0">Submitted</h6>
                                                    </div>
                                                    <p class="mb-0" id="modalRequestDate"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Timeline section -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="d-flex align-items-center pb-2 card-header">
                                                    <i class="me-2 text-primary bx bx-message-square-detail icon-md"></i>
                                                    <h5 class="mb-0 card-title">Details & Updates</h5>
                                                </div>
                                                <div class="pt-3 card-body">
                                                    <div class="bg-light-primary mb-3 p-3 border rounded">
                                                        <h6 class="mb-2">Description</h6>
                                                        <p class="mb-0" id="modalRequestDescription"></p>
                                                    </div>
                                                    <div id="maintenanceTimeline" class="mt-4 timeline">
                                                        <!-- Submission event -->
                                                        <div class="timeline-item">
                                                            <span class="timeline-indicator timeline-indicator-success">
                                                                <i class="icon-base bx bx-send"></i>
                                                            </span>
                                                            <div class="timeline-event">
                                                                <div class="mb-1 timeline-header">
                                                                    <h6 class="mb-0">Request Submitted</h6>
                                                                    <small class="text-muted" id="modalRequestDateTimeline"></small>
                                                                </div>
                                                                <p class="mb-2">Maintenance request registered in the system.</p>
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
                                <div class="border-top-0 modal-footer">
                                    <button type="button" class="btn-outline-secondary btn" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary add-response-btn" data-bs-toggle="modal" data-bs-target="#addResponseModal">Add Response</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Response Modal -->
                    <div class="modal fade" id="addResponseModal" tabindex="-1" aria-labelledby="addResponseModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addResponseModalLabel">Add Response</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="addResponseForm">
                                        <?php set_csrf() ?>
                                        <input type="hidden" id="responseRequestId" name="request_id">
                                        <div class="mb-3">
                                            <label for="responseText" class="form-label">Response</label>
                                            <textarea id="responseText" name="response_text" class="form-control" rows="4" required placeholder="Enter your response..."></textarea>
                                        </div>
                                        <button type="submit" class="w-100 btn btn-primary">Submit Response</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <?php include_once "./Components/footer.php" ?>
                    <!-- / Footer -->
                </div>
                <!-- / Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/pickr/pickr.js"></script>
    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
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

    <!-- Main JS -->
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/extended-ui-sweetalert2.js"></script>
    <script src="../../assets/js/extended-ui-timeline.js"></script>

    <!-- Page JS -->
    <script src="../../assets/js/admin-maintenance-list.js"></script>
</body>

</html>