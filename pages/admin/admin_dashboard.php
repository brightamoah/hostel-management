<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ .  "/../../app/admin/dashboard_data.php";
require_once __DIR__ . "/../../app/controllers/Login.php";

?>

<!DOCTYPE html>
<html lang="en" class="layout-menu-fixed layout-navbar-fixed layout-navbar-sticky layout-compact" dir="ltr" data-skin="default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Kings Hostel - Admin Dashboard</title>
    <meta name="description" content="Admin dashboard for Kings Hostel management system" />

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="../../assets/img/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../assets/img/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/img/favicon_io/favicon-16x16.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons and Core CSS -->
    <link rel="stylesheet" href="../../assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="../../assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/sweetalert2/sweetalert2.css" />
    <!-- <link rel="stylesheet" href="../../assets/vendor/fonts/boxicons.css" /> -->
    <script src="../../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <link rel="stylesheet" href="../../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/@form-validation/form-validation.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Helpers -->
    <script src="../../assets/vendor/js/helpers.js"></script>
    <script src="../../assets/vendor/js/template-customizer.js"></script>
    <script src="../../assets/js/config.js"></script>

    <style>
        .stat-icon {
            font-size: 2rem;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .progress-card .progress {
            height: 8px;
        }

        .occupancy-chart {
            min-height: 320px;
        }

        .quick-actions .btn {
            margin: 0.5rem;
        }



        /* Better tooltips for truncated content */
        [data-bs-toggle="tooltip"] {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="layout-content-navbar layout-wrapper">
        <div class="layout-container">
            <!-- Menu -->
            <?php include_once __DIR__ . "/../../Components/sidebar.php"; ?>
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
                        <!-- Welcome Card -->
                        <?php include_once __DIR__ . "/../../Components/admin/dashboard_card.php" ?>

                        <!-- Statistics Cards -->
                        <div class="mb-4 row">
                            <div class="mb-4 col-sm-6 col-lg-3">
                                <div class="h-100 card card-hover">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div>
                                                <h5 class="mb-0 card-title">Total Students</h5>
                                                <h4 class="mt-2 mb-1 fw-bold"><?= number_format($total_students) ?></h4>
                                                <p class="card-text">Active residents</p>
                                            </div>
                                            <div class="bg-label-primary text-primary stat-icon">
                                                <i class="bx-group icon-base bx icon-xl"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 mt-2 progress progress-card">
                                            <div class="bg-primary progress-bar" style="width: 100%" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4 col-sm-6 col-lg-3">
                                <div class="h-100 card card-hover">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div>
                                                <h5 class="mb-0 card-title">Room Occupancy</h5>
                                                <h4 class="mt-2 mb-1 fw-bold"><?= number_format($occupancy_rate) ?>%</h4>
                                                <p class="card-text"><?= $occupied_rooms ?> of <?= $total_rooms ?> rooms</p>
                                            </div>
                                            <div class="bg-label-success text-success stat-icon">
                                                <i class="icon-base bx bx-home-circle icon-xl"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 mt-2 progress progress-card">
                                            <div class="bg-success progress-bar" style="width: <?= $occupancy_rate ?>%" role="progressbar" aria-valuenow="<?= $occupancy_rate ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4 col-sm-6 col-lg-3">
                                <div class="h-100 card card-hover">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div>
                                                <h5 class="mb-0 card-title">Available Rooms</h5>
                                                <h4 class="mt-2 mb-1 fw-bold"><?= number_format($available_rooms) ?></h4>
                                                <p class="card-text">Ready for allocation</p>
                                            </div>
                                            <div class="bg-label-info text-info stat-icon">
                                                <i class="icon-base bx bx-door-open icon-xl"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 mt-2 progress progress-card">
                                            <div class="bg-info progress-bar" style="width: <?= ($available_rooms / $total_rooms) * 100 ?>%" role="progressbar" aria-valuenow="<?= ($available_rooms / $total_rooms) * 100 ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4 col-sm-6 col-lg-3">
                                <div class="h-100 card card-hover">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div>
                                                <h5 class="mb-0 card-title">Monthly Revenue</h5>
                                                <h4 class="mt-2 mb-1 fw-bold">GH₵ <?= number_format($recent_payments_sum, 2) ?></h4>
                                                <p class="card-text">Last 30 days</p>
                                            </div>
                                            <div class="bg-label-warning text-warning stat-icon">
                                                <i class="icon-base bx bx-money icon-xl"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 mt-2 progress progress-card">
                                            <div class="bg-warning progress-bar" style="width: <?= min(($recent_payments_count / 30) * 100, 100) ?>%" role="progressbar" aria-valuenow="<?= min(($recent_payments_count / 30) * 100, 100) ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts & Status Row -->
                        <div class="mb-4 row">
                            <!-- Occupancy Chart -->
                            <div class="mb-4 col-lg-8">
                                <div class="h-100 card">
                                    <div class="d-flex align-items-center justify-content-between card-header">
                                        <h5 class="mb-0 card-title">Hostel Occupancy Overview</h5>
                                        <div class="dropdown">
                                            <button class="btn-outline-secondary btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Last 8 Months
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="javascript:void(0);">Last 3 Months</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">Last 6 Months</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">Last 12 Months</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="occupancyChart" class="occupancy-chart"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Maintenance Status -->
                            <div class="mb-4 col-lg-4">
                                <div class="shadow-sm border-0 h-100 card">
                                    <!-- Card Header -->
                                    <div class="d-flex align-items-center justify-content-between py-3 card-header">
                                        <h5 class="m-0 card-title">
                                            <i class='me-2 text-primary bx bx-clipboard'></i> Maintenance Status
                                        </h5>
                                        <span class="bg-primary rounded-pill badge">
                                            <?= $pending_maintenance + $in_progress_maintenance + $completed_maintenance ?> Total
                                        </span>
                                    </div>

                                    <!-- Card Body -->
                                    <div class="pt-4 card-body">
                                        <!-- Pending Status -->
                                        <div class="mb-4 maintenance-item">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2 rounded-circle avatar avatar-sm">
                                                        <i class="text-warning text-center icon-base bx bx-time icon-lg fs-4"></i>
                                                    </div>
                                                    <span class="fw-medium">Pending</span>
                                                </div>
                                                <span class="bg-label-warning badge fw-semibold fs-6">
                                                    <?= $pending_maintenance ?>
                                                </span>
                                            </div>
                                            <div class="rounded-pill progress" style="height: 8px;">
                                                <div class="bg-warning progress-bar"
                                                    style="width: <?= ($pending_maintenance / max(1, $pending_maintenance + $in_progress_maintenance + $completed_maintenance)) * 100 ?>%"
                                                    role="progressbar"
                                                    aria-valuenow="<?= $pending_maintenance ?>"
                                                    aria-valuemin="0"
                                                    aria-valuemax="<?= $pending_maintenance + $in_progress_maintenance + $completed_maintenance ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- In Progress Status -->
                                        <div class="mb-4 maintenance-item">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle avatar avatar-sm">
                                                        <i class="text-info text-center icon-base bx bx-trending-up icon-lg fs-4"></i>
                                                    </div>
                                                    <span class="fw-medium">In Progress</span>
                                                </div>
                                                <span class="bg-label-info badge fw-semibold fs-6">
                                                    <?= $in_progress_maintenance ?>
                                                </span>
                                            </div>
                                            <div class="rounded-pill progress" style="height: 8px;">
                                                <div class="bg-info progress-bar"
                                                    style="width: <?= ($in_progress_maintenance / max(1, $pending_maintenance + $in_progress_maintenance + $completed_maintenance)) * 100 ?>%"
                                                    role="progressbar"
                                                    aria-valuenow="<?= $in_progress_maintenance ?>"
                                                    aria-valuemin="0"
                                                    aria-valuemax="<?= $pending_maintenance + $in_progress_maintenance + $completed_maintenance ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Completed Status -->
                                        <div class="mb-4 maintenance-item">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2 rounded-circle avatar avatar-sm">
                                                        <i class="text-success icon-base bx bx-check-circle icon-lg"></i>
                                                    </div>
                                                    <span class="fw-medium">Completed</span>
                                                </div>
                                                <span class="bg-label-success badge fw-semibold fs-6">
                                                    <?= $completed_maintenance ?>
                                                </span>
                                            </div>
                                            <div class="rounded-pill progress" style="height: 8px;">
                                                <div class="bg-success progress-bar"
                                                    style="width: <?= ($completed_maintenance / max(1, $pending_maintenance + $in_progress_maintenance + $completed_maintenance)) * 100 ?>%"
                                                    role="progressbar"
                                                    aria-valuenow="<?= $completed_maintenance ?>"
                                                    aria-valuemin="0"
                                                    aria-valuemax="<?= $pending_maintenance + $in_progress_maintenance + $completed_maintenance ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Completion Rate -->
                                        <div class="bg-lighter mb-4 p-3 rounded-2">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="fw-medium">Completion Rate</span>
                                                <span class="text-success fw-bold">
                                                    <?= round(($completed_maintenance / max(1, $pending_maintenance + $in_progress_maintenance + $completed_maintenance)) * 100) ?>%
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Action Button -->
                                        <div class="text-center">
                                            <a href="/admin/maintenance" class="d-flex align-items-center justify-content-center mx-auto btn btn-primary" style="width: fit-content; margin-top: 2rem;">
                                                <i class="me-1 icon-base bx bx-wrench icon-lg"></i>
                                                <span>Manage Maintenance</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Activity Tables -->
                            <div class="mb-4 row">
                                <!-- Recent Bookings -->
                                <div class="mb-4 col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0 card-title">Recent Bookings</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-nowrap table-responsive">
                                                <table id="bookingsTable" class="table table-borderless">
                                                    <thead>
                                                        <tr>
                                                            <th>Student</th>
                                                            <th>Room</th>
                                                            <th>Start Date</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($recent_bookings as $booking): ?>
                                                            <tr>
                                                                <td data-bs-toggle="tooltip" title="<?= htmlspecialchars($booking['student_name']) ?>">
                                                                    <?= htmlspecialchars($booking['student_name']) ?>
                                                                </td>
                                                                <td>
                                                                    <?= htmlspecialchars($booking['room_number']) ?>
                                                                    <small class="text-muted">(<?= htmlspecialchars($booking['building']) ?>)</small>
                                                                </td>
                                                                <td><?= date('d M Y', strtotime($booking['start_date'])) ?></td>
                                                                <td class="text-center">
                                                                    <span class="bg-label-success badge">
                                                                        <?= htmlspecialchars($booking['status']) ?>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                        <?php if (empty($recent_bookings)): ?>
                                                            <tr>
                                                                <td colspan="4" class="text-center">No recent bookings</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <a href="/admin/rooms" class="mt-3 btn btn-sm btn-label-primary">View All Bookings</a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Maintenance Requests -->
                                <div class="mb-4 col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0 card-title">Recent Maintenance Requests</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-nowrap table-responsive">
                                                <table id="maintenanceTable" class="table table-borderless">
                                                    <thead>
                                                        <tr>
                                                            <th>Student</th>
                                                            <th>Room</th>
                                                            <th>Issue</th>
                                                            <th>Priority</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($recent_maintenance as $request): ?>
                                                            <tr>
                                                                <td data-bs-toggle="tooltip" title="<?= htmlspecialchars($request['student_name']) ?>">
                                                                    <?= htmlspecialchars($request['student_name']) ?>
                                                                </td>
                                                                <td>
                                                                    <?= htmlspecialchars($request['room_number']) ?>
                                                                    <small class="text-muted">(<?= htmlspecialchars($request['building']) ?>)</small>
                                                                </td>
                                                                <td data-bs-toggle="tooltip" title="<?= htmlspecialchars($request['issue_type']) ?>" class="text-truncate-custom">
                                                                    <?= htmlspecialchars($request['issue_type']) ?>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge bg-label-<?= $request['priority'] == 'Emergency' ? 'danger' : ($request['priority'] == 'High' ? 'warning' : ($request['priority'] == 'Medium' ? 'info' : 'success')) ?>">
                                                                        <?= htmlspecialchars($request['priority']) ?>
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge bg-label-<?= $request['status'] == 'Pending' ? 'warning' : ($request['status'] == 'Completed' ? 'success' : ($request['status'] == 'In-Progress' ? 'info' : 'danger')) ?>">
                                                                        <?= htmlspecialchars($request['status']) ?>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                        <?php if (empty($recent_maintenance)): ?>
                                                            <tr>
                                                                <td colspan="5" class="text-center">No recent requests</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <a href="/admin/maintenance" class="mt-3 btn btn-sm btn-label-primary">View All Requests</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Payments -->
                            <div class="mb-4 col-12">
                                <div class="card">
                                    <div class="border-bottom card-header">
                                        <h5 class="mb-0 card-title">Recent Payments</h5>
                                        <div class="d-flex align-items-center justify-content-between gap-md-0 pt-4 row g-6">
                                            <div class="col-md-4">
                                                <select id="paymentStatusFilter" class="form-select">
                                                    <option value="">All Statuses</option>
                                                    <option value="Completed">Completed</option>
                                                    <option value="Pending">Pending</option>
                                                    <option value="Failed">Failed</option>
                                                </select>
                                            </div>
                                            <div class="text-end col-md-4">
                                                <a href="/admin/billings" class="btn btn-primary">View All Payments</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-nowrap table-responsive">
                                            <table id="paymentsTable" class="table table-borderless">
                                                <thead>
                                                    <tr>
                                                        <th>Student</th>
                                                        <th>Amount</th>
                                                        <th>Purpose</th>
                                                        <th>Date</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($recent_payments as $payment): ?>
                                                        <tr data-payment-id="<?= $payment['payment_id'] ?>"
                                                            data-student-name="<?= htmlspecialchars($payment['student_name']) ?>"
                                                            data-student-id="<?= $payment['student_id'] ?>"
                                                            data-amount="<?= htmlspecialchars($payment['amount']) ?>"
                                                            data-purpose="<?= htmlspecialchars($payment['purpose']) ?>"
                                                            data-date="<?= htmlspecialchars($payment['payment_date']) ?>"
                                                            data-status="<?= htmlspecialchars($payment['status']) ?>">
                                                            <td data-bs-toggle="tooltip" title="<?= htmlspecialchars($payment['student_name']) ?>">
                                                                <?= htmlspecialchars($payment['student_name']) ?>
                                                            </td>
                                                            <td>GH₵ <?= number_format($payment['amount'], 2) ?></td>
                                                            <td><?= htmlspecialchars($payment['purpose']) ?></td>
                                                            <td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
                                                            <td>
                                                                <span class="badge bg-label-<?=
                                                                                            $payment['status'] == 'Completed' ? 'success' : ($payment['status'] == 'Pending' ? 'warning' : 'danger') ?>">
                                                                    <?= htmlspecialchars($payment['status']) ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex gap-2">
                                                                    <button type="button" class="btn btn-sm btn-icon view-payment-details"
                                                                        data-bs-toggle="modal" data-bs-target="#paymentModal">
                                                                        <i class="bx bx-show icon-md"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    <?php if (empty($recent_payments)): ?>
                                                        <tr>
                                                            <td colspan="6" class="text-center">No recent payments</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Details Modal -->
                            <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="paymentModalLabel">Payment Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="p-4 text-center modal-body">
                                            <div id="paymentAvatar" class="d-flex align-items-center justify-content-center bg-primary mx-auto mb-3 rounded-circle text-white avatar avatar-xl" style="width: 80px; height: 80px;">
                                                <span id="paymentInitials" style="font-size: 2rem;"></span>
                                            </div>
                                            <h5 id="paymentStudentName" class="mb-1"></h5>
                                            <p id="paymentId" class="mb-3 text-muted">
                                                <i class="me-1 bx bx-id-card"></i> ID: <span></span>
                                            </p>
                                            <div class="shadow-none mb-3 card">
                                                <div class="p-3 card-body">
                                                    <ul class="mb-0 list-unstyled">
                                                        <li class="mb-3">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <div>
                                                                    <i class="me-2 text-primary bx bx-money"></i>
                                                                    <span class="fw-semibold">Amount</span>
                                                                </div>
                                                                <span id="paymentAmount" class="text-end"></span>
                                                            </div>
                                                        </li>
                                                        <li class="mb-3">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <div>
                                                                    <i class="me-2 text-primary bx bx-briefcase"></i>
                                                                    <span class="fw-semibold">Purpose</span>
                                                                </div>
                                                                <span id="paymentPurpose" class="text-end"></span>
                                                            </div>
                                                        </li>
                                                        <li class="mb-3">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <div>
                                                                    <i class="me-2 text-primary bx bx-calendar"></i>
                                                                    <span class="fw-semibold">Payment Date</span>
                                                                </div>
                                                                <span id="paymentDate" class="text-end"></span>
                                                            </div>
                                                        </li>
                                                        <li class="mb-3">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <div>
                                                                    <i class="me-2 text-primary bx bx-info-circle"></i>
                                                                    <span class="fw-semibold">Status</span>
                                                                </div>
                                                                <span id="paymentStatus" class="badge"></span>
                                                            </div>
                                                        </li>
                                                        <li class="mb-0">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <div>
                                                                    <i class="me-2 text-primary bx bx-user"></i>
                                                                    <span class="fw-semibold">Student ID</span>
                                                                </div>
                                                                <span id="paymentStudentId" class="text-end"></span>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div id="paymentActions" class="d-flex justify-content-center">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
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
        <script src="../../assets/vendor/libs/moment/moment.js"></script>
        <script src="../../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
        <script src="../../assets/vendor/libs/select2/select2.js"></script>
        <script src="../../assets/vendor/libs/@form-validation/popular.js"></script>
        <script src="../../assets/vendor/libs/@form-validation/bootstrap5.js"></script>
        <script src="../../assets/vendor/libs/@form-validation/auto-focus.js"></script>
        <script src="../../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>
        <script src="../../assets/vendor/libs/apex-charts/apexcharts.js"></script>

        <!-- Main JS -->
        <script src="../../assets/js/main.js"></script>

        <!-- Page JS -->
        <script src="../../assets/js/app-dashboard-list.js"></script>

        <!-- Occupancy Chart -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let historicalOccupancy = <?php echo json_encode($historical_occupancy); ?>;
                let months = historicalOccupancy.map(item => item.month);
                let rates = historicalOccupancy.map(item => item.rate);

                let occupancyOptions = {
                    chart: {
                        height: 300,
                        type: 'area',
                        fontFamily: 'Public Sans',
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: 'Occupancy Rate',
                        data: rates
                    }],
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    colors: ['#696cff'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'dark',
                            type: 'vertical',
                            shadeIntensity: 0.3,
                            opacityFrom: 0.7,
                            opacityTo: 0.2,
                            stops: [0, 90, 100]
                        }
                    },
                    grid: {
                        borderColor: '#e2e8f0',
                        strokeDashArray: 5,
                        padding: {
                            top: -20,
                            right: 20,
                            bottom: 0,
                            left: 20
                        }
                    },
                    xaxis: {
                        categories: months,
                        axisBorder: {
                            show: false
                        },
                        labels: {
                            style: {
                                colors: '#7a7f9a',
                                fontSize: '13px'
                            },
                            rotate: -45,
                            rotateAlways: true
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(val) {
                                return val + '%';
                            },
                            style: {
                                colors: '#7a7f9a',
                                fontSize: '13px'
                            }
                        },
                        min: 0,
                        max: 100,
                        tickAmount: 5
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val + '% occupancy';
                            }
                        }
                    }
                };

                let occupancyChart = new ApexCharts(document.querySelector('#occupancyChart'), occupancyOptions);
                occupancyChart.render();
            });
        </script>
</body>

</html>