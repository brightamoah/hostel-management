<?php
require_once __DIR__ . "/../../app/admin/visitor-stats.php";
?>

<!doctype html>
<html lang="en" class="layout-navbar-fixed layout-navbar-sticky layout-menu-fixed layout-menu-collapsed layout-compact"
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
            background-color: #f5f5f9;
            color: #697a8d;
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
                <?php include_once "./Components/admin/header.php" ?>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row g-6 mb-6">
                            <div class="col-sm-6 col-xl-3">
                                <div class="card card-border-shadow-primary">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <span class="text-heading">Total Visitors</span>
                                                <div class="d-flex align-items-center my-1">
                                                    <h4 class="mb-0 me-2"><?php echo $totalVisitors; ?></h4>
                                                    <p class="text-success mb-0">(+<?php echo $totalVisitors > 0 ? round(($totalVisitors - $totalVisitors * 0.71) / $totalVisitors * 100) : 0; ?>%)</p>
                                                </div>
                                                <small class="mb-0">All Registered Visitors</small>
                                            </div>
                                            <div class="avatar">
                                                <span class="avatar-initial rounded bg-label-primary">
                                                    <i class="icon-base bx bx-group icon-lg"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-3">
                                <div class="card card-border-shadow-info">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <span class="text-heading">Approved Visitors</span>
                                                <div class="d-flex align-items-center my-1">
                                                    <h4 class="mb-0 me-2"><?php echo $approvedVisitors; ?></h4>
                                                    <p class="text-success mb-0">(+<?php echo $approvedVisitors > 0 ? round(($approvedVisitors - $approvedVisitors * 0.82) / $approvedVisitors * 100) : 0; ?>%)</p>
                                                </div>
                                                <small class="mb-0">Approved by Admin</small>
                                            </div>
                                            <div class="avatar">
                                                <span class="avatar-initial rounded bg-label-info">
                                                    <i class="icon-base bx bx-check-circle icon-lg"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-3">
                                <div class="card card-border-shadow-success">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <span class="text-heading">Checked-In Visitors</span>
                                                <div class="d-flex align-items-center my-1">
                                                    <h4 class="mb-0 me-2"><?php echo $checkedInVisitors; ?></h4>
                                                    <p class="text-danger mb-0">(<?php echo $checkedInVisitors > 0 ? round(($checkedInVisitors * 0.86 - $checkedInVisitors) / $checkedInVisitors * 100) : 0; ?>%)</p>
                                                </div>
                                                <small class="mb-0">Currently Checked-In</small>
                                            </div>
                                            <div class="avatar">
                                                <span class="avatar-initial rounded bg-label-success">
                                                    <i class="icon-base bx bx-log-in-circle icon-lg"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-3">
                                <div class="card card-border-shadow-warning">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <span class="text-heading">Pending Visitors</span>
                                                <div class="d-flex align-items-center my-1">
                                                    <h4 class="mb-0 me-2"><?php echo $pendingVisitors; ?></h4>
                                                    <p class="text-success mb-0">(+<?php echo $pendingVisitors > 0 ? round(($pendingVisitors - $pendingVisitors * 0.58) / $pendingVisitors * 100) : 0; ?>%)</p>
                                                </div>
                                                <small class="mb-0">Awaiting Approval</small>
                                            </div>
                                            <div class="avatar">
                                                <span class="avatar-initial rounded bg-label-warning">
                                                    <i class="icon-base bx bx-time icon-lg"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header border-bottom">
                                <h5 class="card-title mb-0">Visitor Log</h5>
                                <div class="d-flex justify-content-between align-items-center row pt-4 gap-md-0 g-6">
                                    <div class="col-md-4">
                                        <select id="statusFilter" class="form-select">
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
                                        <input type="text" id="searchInput" class="form-control" placeholder="Search Visitor..." />
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <!-- CSRF token for admin actions -->
                                        <?php set_csrf(); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-datatable table-responsive">
                                <table class="datatables-visitors table border-top">
                                    <thead>
                                        <tr>
                                            <th></th> <!-- Control column -->
                                            <th>Visitor Name</th>
                                            <th>Student</th>
                                            <th>Relationship</th>
                                            <th>Visit Date</th>
                                            <th>Latest Check-In</th>
                                            <th>Latest Check-Out</th>
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
                                <div class="modal-content shadow-lg border-0">
                                    <!-- Header with gradient background -->
                                    <div class="modal-header bg-primary bg-gradient text-white border-0 rounded-top">
                                        <h5 class="modal-title fs-5 fw-semibold" id="visitorModalLabel">
                                            <i class="bx bx-user-circle me-2"></i>Visitor Details
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body p-0">
                                        <!-- Top Card Section with Avatar and Basic Info -->
                                        <div class="card shadow-none border-0 mb-0">
                                            <div class="card-body bg-lighter py-4">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-3 text-center mb-3 mb-lg-0">
                                                        <div id="visitorAvatar" class="avatar avatar-xl d-flex align-items-center justify-content-center rounded-circle mb-3 mx-auto bg-primary bg-opacity-25 shadow-sm" style="width: 100px; height: 100px;">
                                                            <span id="visitorInitials" class="text-primary fw-bold" style="font-size: 2.5rem;"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-5 text-center text-lg-start mb-3 mb-lg-0">
                                                        <h4 id="visitorName" class="fw-semibold mb-1"></h4>
                                                        <div class="d-flex align-items-center justify-content-center justify-content-lg-start">
                                                            <div id="visitorStatus" class="badge bg-label-success me-2"></div>
                                                            <span id="visitorRelation" class="text-muted"></span>
                                                        </div>
                                                        <div class="mt-2">
                                                            <span class="text-muted">
                                                                <i class="bx bx-id-card me-1"></i> ID: <span id="visitorId" class="fw-semibold"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 text-center text-lg-end">
                                                        <div id="visitorActions" class="d-flex flex-wrap justify-content-center justify-content-lg-end gap-2"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tabs Navigation -->
                                        <ul class="nav nav-tabs nav-fill" id="visitorModalTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="visitor-info-tab" data-bs-toggle="tab" data-bs-target="#visitor-info" type="button" role="tab" aria-controls="visitor-info" aria-selected="true">
                                                    <i class="bx bx-user me-1"></i> Visitor Info
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="student-info-tab" data-bs-toggle="tab" data-bs-target="#student-info" type="button" role="tab" aria-controls="student-info" aria-selected="false">
                                                    <i class="bx bx-book-reader me-1"></i> Student Info
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="visit-history-tab" data-bs-toggle="tab" data-bs-target="#visit-history" type="button" role="tab" aria-controls="visit-history" aria-selected="false">
                                                    <i class="bx bx-history me-1"></i> Visit History
                                                </button>
                                            </li>
                                        </ul>

                                        <!-- Tab Content -->
                                        <div class="tab-content p-4" id="visitorModalTabContent">
                                            <!-- Visitor Information Tab -->
                                            <div class="tab-pane fade show active" id="visitor-info" role="tabpanel" aria-labelledby="visitor-info-tab">
                                                <div class="row g-4">
                                                    <div class="col-md-6">
                                                        <div class="card shadow-sm h-100">
                                                            <div class="card-body">
                                                                <h6 class="fw-semibold mb-3 d-flex align-items-center">
                                                                    <span class="badge bg-label-primary rounded p-2 me-2"><i class="bx bx-phone"></i></span>
                                                                    Contact Information
                                                                </h6>
                                                                <div class="mb-3">
                                                                    <small class="text-muted d-block mb-1">Phone Number</small>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="bx bx-phone text-primary me-2"></i>
                                                                        <span id="visitorPhone" class="fw-semibold"></span>
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <small class="text-muted d-block mb-1">Visit Date</small>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="bx bx-calendar text-primary me-2"></i>
                                                                        <span id="visitorVisitDate" class="fw-semibold"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="card shadow-sm h-100">
                                                            <div class="card-body">
                                                                <h6 class="fw-semibold mb-3 d-flex align-items-center">
                                                                    <span class="badge bg-label-info rounded p-2 me-2"><i class="bx bx-briefcase"></i></span>
                                                                    Visit Purpose
                                                                </h6>
                                                                <div>
                                                                    <small class="text-muted d-block mb-1">Reason for Visit</small>
                                                                    <div class="d-flex align-items-start">
                                                                        <i class="bx bx-message-square-detail text-info me-2 mt-1"></i>
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
                                                <div class="card shadow-sm">
                                                    <div class="card-body">
                                                        <h6 class="fw-semibold mb-4 d-flex align-items-center">
                                                            <span class="badge bg-label-success rounded p-2 me-2"><i class="bx bx-user"></i></span>
                                                            Student Details
                                                        </h6>

                                                        <div class="row g-4">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <small class="text-muted d-block mb-1">Student Name</small>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="bx bx-user text-success me-2"></i>
                                                                        <span id="visitorStudentName" class="fw-semibold"></span>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <small class="text-muted d-block mb-1">Student ID</small>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="bx bx-id-card text-success me-2"></i>
                                                                        <span id="visitorStudentId" class="fw-semibold"></span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <small class="text-muted d-block mb-1">Student Email</small>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="bx bx-envelope text-success me-2"></i>
                                                                        <span id="visitorStudentEmail" class="fw-semibold text-truncate"></span>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <small class="text-muted d-block mb-1">Student Phone</small>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="bx bx-phone text-success me-2"></i>
                                                                        <span id="visitorStudentPhone" class="fw-semibold"></span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-12">
                                                                <hr class="my-2">
                                                                <h6 class="fw-semibold mb-3 d-flex align-items-center">
                                                                    <span class="badge bg-label-warning rounded p-2 me-2"><i class="bx bx-building"></i></span>
                                                                    Location Information
                                                                </h6>

                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3 mb-md-0">
                                                                            <small class="text-muted d-block mb-1">Building</small>
                                                                            <div class="d-flex align-items-center">
                                                                                <i class="bx bx-building text-warning me-2"></i>
                                                                                <span id="visitorBuilding" class="fw-semibold"></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div>
                                                                            <small class="text-muted d-block mb-1">Room</small>
                                                                            <div class="d-flex align-items-center">
                                                                                <i class="bx bx-door-open text-warning me-2"></i>
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
                                                <div class="card shadow-sm">
                                                    <div class="card-body">
                                                        <h6 class="fw-semibold mb-3 d-flex align-items-center">
                                                            <span class="badge bg-label-danger rounded p-2 me-2"><i class="bx bx-time"></i></span>
                                                            Check-In/Check-Out History
                                                        </h6>

                                                        <div class="table-responsive">
                                                            <table class="table table-hover border-top">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th class="text-nowrap">
                                                                            <i class="bx bx-log-in me-1 text-success"></i> Check-In Time
                                                                        </th>
                                                                        <th class="text-nowrap">
                                                                            <i class="bx bx-log-out me-1 text-danger"></i> Check-Out Time
                                                                        </th>
                                                                        <th class="text-nowrap">
                                                                            <i class="bx bx-timer me-1 text-primary"></i> Duration
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