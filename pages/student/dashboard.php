<?php

require_once "./database/db.php";
require_once "./app/models/Student.php";

// Check if user is authenticated
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Student') {
    header("Location: /login");
    exit();
}

$user_id = $_SESSION['user']['user_id']; // Get the logged-in user's ID
$db = new Database();
$conn = $db->connect();
$student = new Student($conn);

// Fetch student data
$first_name = $student->getFirstName($user_id);
$room_allocation = $student->getRoomAllocation($user_id);
$total_paid = $student->getTotalPaid($user_id);
$pending_balance = $student->getPendingBalance($user_id);
$open_requests = $student->getOpenMaintenanceRequests($user_id);
$payment_status = $student->getPaymentStatusSummary($user_id);
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

    <link rel="stylesheet" href="../../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/sweetalert2/sweetalert2.css" />

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




                        <div class=" mb-6 order-0">
                            <div class="card rounded-3">
                                <div class="d-flex align-items-start row">
                                    <div class="col-sm-7">
                                        <div class="card-body">
                                            <h5 class="card-title text-primary mb-3">Welcome back,
                                                <?= $first_name ?>! 🎉</h5>
                                            <p class="mb-6">
                                                Manage your hostel stay efficiently from here!
                                            </p>

                                            <a href="/student/profile" class="btn btn-md mt-5 btn-label-primary">View Your Profile</a>
                                        </div>
                                    </div>
                                    <div class="col-sm-5 text-center text-sm-left">
                                        <div class="card-body pb-0 px-0 px-md-6">
                                            <img
                                                src="../../assets/img/illustrations/man-with-laptop.png"
                                                height="175"
                                                class="scaleX-n1-rtl"
                                                alt="View Badge User" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        

                        <div class="row g-6 mb-6">
                            <!-- Room Allocation Card -->
                            <div class="col-sm-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <span class="text-heading">Room Allocation</span>
                                                <div class="d-flex align-items-center my-1">
                                                    <h5 class="mb-0 me-2">
                                                        <?= $room_allocation ? htmlspecialchars($room_allocation['room_number']) : "Not Assigned" ?>
                                                    </h5>
                                                    <p class="mb-0 <?= $room_allocation ? 'text-success' : 'text-warning' ?>">
                                                        (<?= $room_allocation ? "Assigned" : "Pending" ?>)
                                                    </p>
                                                </div>
                                                <small class="mb-0">
                                                    <?= $room_allocation ? "Cost: $" . number_format($room_allocation['amount'], 2) : "No room allocated" ?>
                                                </small>
                                            </div>
                                            <div class="avatar">
                                                <span class="avatar-initial rounded bg-label-primary">
                                                    <i class="icon-base bx bx-home icon-lg"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Paid Card (Updated) -->
                            <div class="col-sm-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <span class="text-heading">Total Paid</span>
                                                <div class="d-flex align-items-center my-1">
                                                    <h5 class="mb-0 me-2">GH₵<?= number_format($total_paid, 2) ?></h5>
                                                    <p class="mb-0 <?= htmlspecialchars($payment_status['class']) ?>">
                                                        (<?= htmlspecialchars($payment_status['status']) ?>)
                                                    </p>
                                                </div>
                                                <small class="mb-0">Payment status</small>
                                            </div>
                                            <div class="avatar">
                                                <span class="avatar-initial rounded bg-label-success">
                                                    <i class="icon-base bx bx-dollar icon-lg"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <!-- Pending Balance Card -->
                            <div class="col-sm-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <span class="text-heading">Pending Balance</span>
                                                <div class="d-flex align-items-center my-1">
                                                    <h5 class="mb-0 me-2">GH₵<?= number_format($pending_balance, 2) ?></h5>
                                                    <p class="mb-0 <?= $pending_balance > 0 ? 'text-danger' : 'text-success' ?>">
                                                        (<?= $pending_balance > 0 ? "Due" : "Cleared" ?>)
                                                    </p>
                                                </div>
                                                <small class="mb-0">Outstanding fees</small>
                                            </div>
                                            <div class="avatar">
                                                <span class="avatar-initial rounded bg-label-warning">
                                                    <i class="icon-base bx bx-wallet icon-lg"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Open Maintenance Requests Card -->
                            <div class="col-sm-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <span class="text-heading">Maintenance Requests</span>
                                                <div class="d-flex align-items-center my-1">
                                                    <h5 class="mb-0 me-2"><?= $open_requests ?></h5>
                                                    <p class="mb-0 <?= $open_requests > 0 ? 'text-warning' : 'text-success' ?>">
                                                        (<?= $open_requests > 0 ? "Pending" : "None" ?>)
                                                    </p>
                                                </div>
                                                <small class="mb-0">Open issues</small>
                                            </div>
                                            <div class="avatar">
                                                <span class="avatar-initial rounded bg-label-info">
                                                    <i class="icon-base bx bx-tools icon-lg"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Visitor List Table -->
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
                                            <option value="Cancelled">Cancelled</option> <!-- Added -->
                                            <option value="Denied">Denied</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <!-- Optional: Add a button for adding new visitors if needed -->
                                    </div>
                                </div>
                            </div>
                            <div class="card-datatable">
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
                                            <th>Actions</th> <!-- New Actions column -->
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <!-- Visitor Modal -->
                        <div class="modal fade" id="visitorModal" tabindex="-1" aria-labelledby="visitorModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="visitorModalLabel">Visitor Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center p-4">
                                        <!-- Profile Picture (Initials) -->
                                        <div id="visitorAvatar" class="avatar avatar-xl d-flex align-items-center justify-content-center rounded-circle bg-primary text-white mb-3 mx-auto" style="width: 80px; height: 80px;">
                                            <span id="visitorInitials" style="font-size: 2rem;"></span>
                                        </div>

                                        <!-- Visitor Name and Basic Info -->
                                        <h5 id="visitorName" class="mb-1"></h5>
                                        <p id="visitorRelation" class="text-muted mb-2"></p>
                                        <p id="visitorId" class="text-muted mb-3">
                                            <i class="bx bx-id-card me-1"></i> ID: <span></span>
                                        </p>

                                        <!-- Visitor Details with Icons -->
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

    <script src="../../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <!-- Main JS -->

    <script src="../../assets/js/main.js"></script>
    <!-- Page JS -->
    <script src="../../assets/js/app-visitor-list.js"></script>

</body>

</html>