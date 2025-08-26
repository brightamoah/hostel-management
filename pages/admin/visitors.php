<?php
require_once __DIR__ . "/../../app/admin/visitor-stats.php";
require_once __DIR__ . "/../../utils/hostel_helpers.php";

// Determine scope text for statistics
// $statsScope = isSuperAdmin() ? "All Hostels" : "Your Hostel";
?>

<!doctype html>
<html lang="en" class="layout-menu-fixed layout-navbar-fixed layout-navbar-sticky layout-compact"
    dir="ltr"
    data-skin="default"
    data-assets-path="../../assets/"
    data-template="vertical-menu-template"
    data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Kings Hostel - Admin Visitor Dashboard</title>
    <meta name="description" content="" />

    <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf'] ?? ''); ?>">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="../../assets/img/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../assets/img/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/img/favicon_io/favicon-16x16.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

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

    <!-- Helpers -->
    <script src="../../assets/vendor/js/helpers.js"></script>
    <script src="../../assets/vendor/js/template-customizer.js"></script>
    <script src="../../assets/js/config.js"></script>


    <style>
        /* Example styling for preview purposes */
        body {
            font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;
            /* background-color: #f5f5f9;
            color: #697a8d; */
        }

        .demo-wrapper {
            max-width: 900px;
            margin: 40px auto;
        }

        .demo-btn {
            background-color: #696cff;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-content-navbar layout-wrapper">
        <div class="layout-container">
            <!-- Menu -->
            <?php include_once "./Components/sidebar.php" ?>
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
                <?php include_once "./Components/admin/header.php" ?>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="flex-grow-1 container-p-y container-xxl">
                        <div class="mb-6 row g-6">
                            <div class="col-sm-6 col-xl-3">
                                <div class="card-border-shadow-primary card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <span class="text-heading">Total Visitors</span>
                                                <div class="d-flex align-items-center my-1">
                                                    <h4 class="me-2 mb-0"><?php echo $totalVisitors; ?></h4>
                                                    <p class="mb-0 text-success">(+<?php echo $totalVisitors > 0 ? round(($totalVisitors / max($totalVisitors, 20)) * 25, 1) : 0; ?>%)</p>
                                                </div>
                                                <!-- <small class="mb-0"><?php echo $statsScope; ?></small> -->
                                            </div>
                                            <div class="avatar">
                                                <span class="bg-label-primary rounded avatar-initial">
                                                    <i class="bx-group icon-base bx icon-lg"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-3">
                                <div class="card-border-shadow-info card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <span class="text-heading">Approved Visitors</span>
                                                <div class="d-flex align-items-center my-1">
                                                    <h4 class="me-2 mb-0"><?php echo $approvedVisitors; ?></h4>
                                                    <p class="mb-0 text-info">(<?php echo $totalVisitors > 0 ? round(($approvedVisitors / $totalVisitors) * 100, 1) : 0; ?>%)</p>
                                                </div>

                                            </div>
                                            <div class="avatar">
                                                <span class="bg-label-info rounded avatar-initial">
                                                    <i class="icon-base bx bx-check-circle icon-lg"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-3">
                                <div class="card-border-shadow-success card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <span class="text-heading">Checked-In Visitors</span>
                                                <div class="d-flex align-items-center my-1">
                                                    <h4 class="me-2 mb-0"><?php echo $checkedInVisitors; ?></h4>
                                                    <p class="mb-0 text-success">(<?php echo $approvedVisitors > 0 ? round(($checkedInVisitors / $approvedVisitors) * 100, 1) : 0; ?>%)</p>
                                                </div>

                                            </div>
                                            <div class="avatar">
                                                <span class="bg-label-success rounded avatar-initial">
                                                    <i class="icon-base bx bx-log-in-circle icon-lg"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-3">
                                <div class="card-border-shadow-warning card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <span class="text-heading">Pending Visitors</span>
                                                <div class="d-flex align-items-center my-1">
                                                    <h4 class="me-2 mb-0"><?php echo $pendingVisitors; ?></h4>
                                                    <p class="mb-0 text-warning">(<?php echo $totalVisitors > 0 ? round(($pendingVisitors / $totalVisitors) * 100, 1) : 0; ?>%)</p>
                                                </div>

                                            </div>
                                            <div class="avatar">
                                                <span class="bg-label-warning rounded avatar-initial">
                                                    <i class="icon-base bx bx-time icon-lg"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="border-bottom card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 class="mb-0 card-title">Visitor Log</h5>
                                    <div id="accessLevelIndicator"></div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between gap-md-0 pt-4 row g-6">
                                    <div class="col-md-4">
                                        <select id="statusFilter" class="form-select select2" data-placeholder="All Statuses">
                                            <option value="">All Statuses</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Approved">Approved</option>
                                            <option value="Checked-In">Checked-In</option>
                                            <option value="Checked-Out">Checked-Out</option>
                                            <option value="Cancelled">Cancelled</option>
                                            <option value="Denied">Denied</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select id="dateFilter" class="form-select select2" data-placeholder="All Dates">
                                            <option value="">All Dates</option>
                                            <option value="today">Today</option>
                                            <option value="tomorrow">Tomorrow</option>
                                            <option value="this_week">This Week</option>
                                            <option value="next_week">Next Week</option>
                                            <option value="this_month">This Month</option>
                                            <option value="next_month">Next Month</option>
                                            <option value="past">Past Visits</option>
                                            <option value="future">Future Visits</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" id="searchInput" class="form-control" placeholder="Search Visitor..." />
                                    </div>
                                    <div class="text-end col-md-4">
                                        <!-- CSRF token for admin actions -->
                                        <?php set_csrf(); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive card-datatable">
                                <table class="table border-top datatables-visitors">
                                    <thead>
                                        <tr>
                                            <th></th> <!-- Control column -->
                                            <th>Visitor Name</th>
                                            <th>Student</th>
                                            <th>Relationship</th>
                                            <th>Visit Date</th>
                                            <th>Last Check-In</th>
                                            <th>Last Check-Out</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <!-- Visitor Modal (Updated) -->
                        <!-- Modernized Visitor Modal -->
                        <div class="modal fade" id="visitorModal" tabindex="-1" aria-labelledby="visitorModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="shadow-lg border-0 modal-content">
                                    <!-- Header with gradient background -->
                                    <div class="bg-primary bg-gradient border-0 rounded-top text-white modal-header">
                                        <h5 class="modal-title fs-5 fw-semibold" id="visitorModalLabel">
                                            <i class="me-2 bx bx-user-circle"></i>Visitor Details
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="p-0 modal-body">
                                        <!-- Top Card Section with Avatar and Basic Info -->
                                        <div class="shadow-none mb-0 border-0 card">
                                            <div class="bg-lighter py-4 card-body">
                                                <div class="align-items-center row">
                                                    <div class="mb-3 mb-lg-0 text-center col-lg-3">
                                                        <div id="visitorAvatar" class="d-flex align-items-center justify-content-center bg-primary bg-opacity-25 shadow-sm mx-auto mb-3 rounded-circle avatar avatar-xl" style="width: 100px; height: 100px;">
                                                            <span id="visitorInitials" class="text-primary fw-bold" style="font-size: 2.5rem;"></span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 mb-lg-0 text-lg-start text-center col-lg-5">
                                                        <h4 id="visitorName" class="mb-1 fw-semibold"></h4>
                                                        <div class="d-flex justify-content-lg-start align-items-center justify-content-center">
                                                            <div id="visitorStatus" class="bg-label-success me-2 badge"></div>
                                                            <span id="visitorRelation" class="text-muted"></span>
                                                        </div>
                                                        <div class="mt-2">
                                                            <span class="text-muted">
                                                                <i class="me-1 bx bx-id-card"></i> ID: <span id="visitorId" class="fw-semibold"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="text-lg-end text-center col-lg-4">
                                                        <div id="visitorActions" class="d-flex flex-wrap justify-content-lg-end justify-content-center gap-2"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tabs Navigation -->
                                        <ul class="nav nav-tabs nav-fill" id="visitorModalTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="visitor-info-tab" data-bs-toggle="tab" data-bs-target="#visitor-info" type="button" role="tab" aria-controls="visitor-info" aria-selected="true">
                                                    <i class="me-1 bx bx-user"></i> Visitor Info
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="student-info-tab" data-bs-toggle="tab" data-bs-target="#student-info" type="button" role="tab" aria-controls="student-info" aria-selected="false">
                                                    <i class="me-1 bx bx-book-reader"></i> Student Info
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="visit-history-tab" data-bs-toggle="tab" data-bs-target="#visit-history" type="button" role="tab" aria-controls="visit-history" aria-selected="false">
                                                    <i class="me-1 bx bx-history"></i> Visit History
                                                </button>
                                            </li>
                                        </ul>

                                        <!-- Tab Content -->
                                        <div class="p-4 tab-content" id="visitorModalTabContent">
                                            <!-- Visitor Information Tab -->
                                            <div class="tab-pane fade show active" id="visitor-info" role="tabpanel" aria-labelledby="visitor-info-tab">
                                                <div class="row g-4">
                                                    <div class="col-md-6">
                                                        <div class="shadow-sm h-100 card">
                                                            <div class="card-body">
                                                                <h6 class="d-flex align-items-center mb-3 fw-semibold">
                                                                    <span class="bg-label-primary me-2 p-2 rounded badge"><i class="bx bx-phone"></i></span>
                                                                    Contact Information
                                                                </h6>
                                                                <div class="mb-3">
                                                                    <small class="d-block mb-1 text-muted">Phone Number</small>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="me-2 text-primary bx bx-phone"></i>
                                                                        <span id="visitorPhone" class="fw-semibold"></span>
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <small class="d-block mb-1 text-muted">Visit Date</small>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="me-2 text-primary bx bx-calendar"></i>
                                                                        <span id="visitorVisitDate" class="fw-semibold"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="shadow-sm h-100 card">
                                                            <div class="card-body">
                                                                <h6 class="d-flex align-items-center mb-3 fw-semibold">
                                                                    <span class="bg-label-info me-2 p-2 rounded badge"><i class="bx bx-briefcase"></i></span>
                                                                    Visit Purpose
                                                                </h6>
                                                                <div>
                                                                    <small class="d-block mb-1 text-muted">Reason for Visit</small>
                                                                    <div class="d-flex align-items-start">
                                                                        <i class="me-2 mt-1 text-info bx bx-message-square-detail"></i>
                                                                        <span id="visitorPurpose" class="fw-semibold"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Student Information Tab -->
                                            <div class="tab-pane fade" id="student-info" role="tabpanel" aria-labelledby="student-info-tab">
                                                <div class="shadow-sm card">
                                                    <div class="card-body">
                                                        <h6 class="d-flex align-items-center mb-4 fw-semibold">
                                                            <span class="bg-label-success me-2 p-2 rounded badge"><i class="bx bx-user"></i></span>
                                                            Student Details
                                                        </h6>

                                                        <div class="row g-4">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <small class="d-block mb-1 text-muted">Student Name</small>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="me-2 text-success bx bx-user"></i>
                                                                        <span id="visitorStudentName" class="fw-semibold"></span>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <small class="d-block mb-1 text-muted">Student ID</small>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="me-2 text-success bx bx-id-card"></i>
                                                                        <span id="visitorStudentId" class="fw-semibold"></span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <small class="d-block mb-1 text-muted">Student Email</small>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="me-2 text-success bx bx-envelope"></i>
                                                                        <span id="visitorStudentEmail" class="text-truncate fw-semibold"></span>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <small class="d-block mb-1 text-muted">Student Phone</small>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="me-2 text-success bx bx-phone"></i>
                                                                        <span id="visitorStudentPhone" class="fw-semibold"></span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-12">
                                                                <hr class="my-2">
                                                                <h6 class="d-flex align-items-center mb-3 fw-semibold">
                                                                    <span class="bg-label-warning me-2 p-2 rounded badge"><i class="bx bx-building"></i></span>
                                                                    Location Information
                                                                </h6>

                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3 mb-md-0">
                                                                            <small class="d-block mb-1 text-muted">Building</small>
                                                                            <div class="d-flex align-items-center">
                                                                                <i class="me-2 text-warning bx bx-building"></i>
                                                                                <span id="visitorBuilding" class="fw-semibold"></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div>
                                                                            <small class="d-block mb-1 text-muted">Room</small>
                                                                            <div class="d-flex align-items-center">
                                                                                <i class="me-2 text-warning bx bx-door-open"></i>
                                                                                <span id="visitorRoom" class="fw-semibold"></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Visit History Tab -->
                                            <div class="tab-pane fade" id="visit-history" role="tabpanel" aria-labelledby="visit-history-tab">
                                                <div class="shadow-sm card">
                                                    <div class="card-body">
                                                        <h6 class="d-flex align-items-center mb-3 fw-semibold">
                                                            <span class="bg-label-danger me-2 p-2 rounded badge"><i class="bx bx-time"></i></span>
                                                            Check-In/Check-Out History
                                                        </h6>

                                                        <div class="table-responsive">
                                                            <table class="table table-hover border-top">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th class="text-nowrap">
                                                                            <i class="me-1 text-success bx bx-log-in"></i> Check-In Time
                                                                        </th>
                                                                        <th class="text-nowrap">
                                                                            <i class="me-1 text-danger bx bx-log-out"></i> Check-Out Time
                                                                        </th>
                                                                        <th class="text-nowrap">
                                                                            <i class="me-1 text-primary bx bx-timer"></i> Duration
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="visitorLogs"></tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <?php include_once "./Components/footer.php" ?>
                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
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
    <script src="../../assets/js/extended-ui-sweetalert2.js"></script>
    <!-- Page JS -->
    <script src="../../assets/js/admin-visitor-list.js"></script>
</body>

</html>