<?php
require_once "./app/controllers/visitors/visitor_stats.php";

?>

<!doctype html>
<html lang="en" class="layout-navbar-sticky layout-menu-fixed layout-compact" dir="ltr" data-skin="default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Kings Hostel - Visitor</title>
    <meta name="description" content="" />

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
                        <div class="row g-6 mb-6">
                            <div class="col-sm-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <span class="text-heading">Total Visitors</span>
                                                <div class="d-flex align-items-center my-1">
                                                    <h4 class="mb-0 me-2"><?php echo $totalVisitors; ?></h4>
                                                    <p class="text-success mb-0">(+<?php echo $totalVisitors > 0 ? round(($totalVisitors - ($totalVisitors * 0.71)) / $totalVisitors * 100) : 0; ?>%)</p>
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
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <span class="text-heading">Approved Visitors</span>
                                                <div class="d-flex align-items-center my-1">
                                                    <h4 class="mb-0 me-2"><?php echo $approvedVisitors; ?></h4>
                                                    <p class="text-success mb-0">(+<?php echo $approvedVisitors > 0 ? round(($approvedVisitors - ($approvedVisitors * 0.82)) / $approvedVisitors * 100) : 0; ?>%)</p>
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
                                <div class="card">
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
                                <div class="card">
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
                                    <div class="col-md-4 text-end">
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerVisitorModal">
                                            <i class="bx bx-plus icon-base me-1"></i> Register Visitor
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-datatable table-responsive">
                                <table class="datatables-visitors table border-top">
                                    <thead>
                                        <tr>
                                            <th></th> <!-- Control column -->
                                            <th></th> <!-- Checkbox column -->
                                            <th>Visitor Name</th>
                                            <th>Relationship</th>
                                            <th>Visit Date</th>
                                            <th>Check-In</th>
                                            <th>Check-Out</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <!-- Register Visitor Modal -->
                        <div class="modal fade" id="registerVisitorModal" tabindex="-1" aria-labelledby="registerVisitorModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="registerVisitorModalLabel">Register New Visitor</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="registerVisitorForm">
                                            <div class="mb-3">
                                                <label for="visitorName" class="form-label">Visitor Name</label>
                                                <input type="text" class="form-control" id="visitorName" name="visitor_name" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="relation" class="form-label">Relationship</label>
                                                <input type="text" class="form-control" id="relation" name="relation" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="phoneNumber" class="form-label">Phone Number</label>
                                                <input type="text" class="form-control" id="phoneNumber" name="phone_number" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="visitDate" class="form-label">Visit Date</label>
                                                <input type="date" class="form-control" id="visitDate" name="visit_date" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="purpose" class="form-label">Purpose of Visit</label>
                                                <textarea class="form-control" id="purpose" name="purpose" rows="3" required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Register Visitor</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Visitor Modal (Existing) -->
                        <div class="modal fade" id="visitorModal" tabindex="-1" aria-labelledby="visitorModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="visitorModalLabel">Visitor Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center p-4">
                                        <div id="visitorAvatar" class="avatar avatar-xl d-flex align-items-center justify-content-center rounded-circle bg-primary text-white mb-3 mx-auto" style="width: 80px; height: 80px;">
                                            <span id="visitorInitials" style="font-size: 2rem;"></span>
                                        </div>
                                        <h5 id="visitorName" class="mb-1"></h5>
                                        <p id="visitorRelation" class="text-muted mb-2"></p>
                                        <p id="visitorId" class="text-muted mb-3">
                                            <i class="bx bx-id-card me-1"></i> ID: <span></span>
                                        </p>
                                        <div class="card shadow-none mb-3">
                                            <div class="card-body p-3">
                                                <ul class="list-unstyled mb-0">
                                                    <li class="mb-3">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <i class="bx bx-phone me-2 text-primary"></i>
                                                                <span class="fw-semibold">Phone</span>
                                                            </div>
                                                            <span id="visitorPhone" class="text-end"></span>
                                                        </div>
                                                    </li>
                                                    <li class="mb-3">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <i class="bx bx-calendar me-2 text-primary"></i>
                                                                <span class="fw-semibold">Visit Date</span>
                                                            </div>
                                                            <span id="visitorVisitDate" class="text-end"></span>
                                                        </div>
                                                    </li>
                                                    <li class="mb-3">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <i class="bx bx-log-in-circle me-2 text-primary"></i>
                                                                <span class="fw-semibold">Check-In</span>
                                                            </div>
                                                            <span id="visitorCheckIn" class="text-end"></span>
                                                        </div>
                                                    </li>
                                                    <li class="mb-3">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <i class="bx bx-log-out-circle me-2 text-primary"></i>
                                                                <span class="fw-semibold">Check-Out</span>
                                                            </div>
                                                            <span id="visitorCheckOut" class="text-end"></span>
                                                        </div>
                                                    </li>
                                                    <li class="mb-3">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <i class="bx bx-info-circle me-2 text-primary"></i>
                                                                <span class="fw-semibold">Status</span>
                                                            </div>
                                                            <span id="visitorStatus" class="badge"></span>
                                                        </div>
                                                    </li>
                                                    <li class="mb-3">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <i class="bx bx-briefcase me-2 text-primary"></i>
                                                                <span class="fw-semibold">Purpose</span>
                                                            </div>
                                                            <span id="visitorPurpose" class="text-end"></span>
                                                        </div>
                                                    </li>
                                                    <li class="mb-0">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <i class="bx bx-user me-2 text-primary"></i>
                                                                <span class="fw-semibold">Student ID</span>
                                                            </div>
                                                            <span id="visitorStudentId" class="text-end"></span>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div id="visitorActions" class="d-flex justify-content-center"></div>
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
    <script src="../../assets/js/app-visitor-list.js"></script>
</body>

</html>