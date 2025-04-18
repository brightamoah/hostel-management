<?php
require_once "./database/db.php";
require_once "./app/models/Student.php";

// Check if user is authenticated
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Student') {
    header("Location: /login");
    exit();
}

$student_data = $_SESSION['user'];
$db = new Database();
$conn = $db->connect();
$student = new Student($conn);

// Fetch room allocation for room information
$room_allocation = $student->getRoomAllocation($student_data['user_id']);
?>

<!doctype html>
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

    <title>Kings Hostel - Student Profile</title>

    <meta name="description" content="Student profile management for Kings Hostel" />

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

    <!-- Page CSS -->
    <style>
        .profile-header {
            background: linear-gradient(135deg, #7367f0, #9e95f5);
            border-radius: 0.5rem;
            padding: 2rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(115, 103, 240, 0.25);
        }

        .profile-cover {
            background-image: url('../../assets/img/backgrounds/profile-banner.jpg');
            background-size: cover;
            background-position: center;
            height: 250px;
            border-radius: 0.5rem 0.5rem 0 0;
            position: relative;
        }

        .profile-avatar-wrapper {
            position: relative;
            margin-top: -75px;
        }

        .profile-avatar {
            border: 5px solid #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-stats {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-radius: 0.5rem;
            padding: 1.5rem;
            transition: all 0.3s;
        }

        .profile-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(115, 103, 240, 0.15);
        }

        .info-item {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }

        .info-item:hover {
            background-color: rgba(115, 103, 240, 0.05);
        }

        .user-badge {
            padding: 0.4rem 1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .action-btn {
            min-width: 120px;
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(115, 103, 240, 0.25);
        }

        .tab-content {
            padding: 1.5rem;
        }

        /* Center the modal */
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
        }

        @media (min-width: 576px) {
            .modal-dialog-centered {
                min-height: calc(100% - 3.5rem);
            }
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
                        <!-- Display messages -->
                        <?php if (isset($_SESSION['message-update'])): ?>
                            <div class="alert alert-<?= $_SESSION['message_type']; ?> alert-dismissible" role="alert">
                                <?= htmlspecialchars($_SESSION['message-update']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['message-update'], $_SESSION['message_type']); ?>
                        <?php endif; ?>

                        <!-- Header -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card mt-10 mb-4">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-center profile-avatar-wrapper mb-3">
                                            <?php
                                            $initials = strtoupper(substr($student_data['first_name'], 0, 1) . substr($student_data['last_name'], 0, 1));
                                            $colors = ['#7367f0', '#28c76f', '#ea5455', '#ff9f43', '#00cfe8'];
                                            $bgColor = $colors[array_rand($colors)];
                                            ?>
                                            <div class="rounded-circle profile-avatar d-flex align-items-center justify-content-center text-center text-white"
                                                style="background-color: <?= $bgColor; ?>; height: 150px; width: 150px; font-size: 50px; color: #fff;">
                                                <?= $initials; ?>
                                            </div>
                                        </div>
                                        <div class="text-center mb-4">
                                            <h3 class="mb-2">
                                                <?= htmlspecialchars($student_data['first_name'] . ' ' . $student_data['last_name']); ?>
                                            </h3>
                                            <span class="badge bg-label-primary user-badge">
                                                <?= htmlspecialchars($student_data['role']); ?>
                                            </span>
                                            <p class="text-muted mt-3">
                                                <?php
                                                echo $room_allocation
                                                    ? 'Room ' . htmlspecialchars($room_allocation['room_number']) . ' | ' . htmlspecialchars($room_allocation['room_type'])
                                                    : 'No room allocated';
                                                ?>
                                            </p>
                                        </div>

                                        <div class="d-flex justify-content-center mb-4">
                                            <button class="btn btn-primary action-btn me-3" data-bs-target="#editUser" data-bs-toggle="modal">
                                                <i class="icon-base bx bx-edit-alt me-1"></i> Edit Profile
                                            </button>
                                            <button class="btn btn-outline-primary action-btn">
                                                <i class="icon-lg bx bx-key me-1"></i> Change Password
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card profile-stats h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3">
                                                <div class="avatar-initial bg-label-primary rounded">
                                                    <i class="icon-base bx bx-calendar icon-lg"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0">
                                                    <?php
                                                    $enrollment_date = new DateTime($student_data['enrollment_date']);
                                                    $today = new DateTime();
                                                    $days = $today->diff($enrollment_date)->days;
                                                    echo $days;
                                                    ?>
                                                </h4>
                                                <span>Days at Hostel</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card profile-stats h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3">
                                                <div class="avatar-initial bg-label-success rounded">
                                                    <i class="icon-base bx bx-check-circle icon-lg"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0">
                                                    <?php
                                                    $payment_status = $student->getPaymentStatusSummary($student_data['user_id']);
                                                    echo $payment_status['status'] === 'Cleared' ? '100%' : 'Pending';
                                                    ?>
                                                </h4>
                                                <span>Payment Status</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card profile-stats h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3">
                                                <div class="avatar-initial bg-label-warning rounded">
                                                    <i class="icon-base bx bx-star icon-lg"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0">4.8</h4>
                                                <span>Room Rating</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Tabs -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-4">
                                    <div class="card-header pb-0">
                                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                            <li class="nav-item">
                                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-info" role="tab">Personal Info</button>
                                            </li>
                                            <li class="nav-item">
                                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-billing" role="tab">Billing</button>
                                            </li>
                                            <li class="nav-item">
                                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-activities" role="tab">Activities</button>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <!-- Personal Info Tab -->
                                            <div class="tab-pane fade show active" id="profile-info" role="tabpanel">
                                                <div class="row">
                                                    <div class="col-md-6 mt-5">
                                                        <div class="info-item mb-3">
                                                            <div class="d-flex align-items-center">
                                                                <i class="bx bx-user icon-xl text-primary me-2"></i>
                                                                <div>
                                                                    <span class="fw-medium d-block">Full Name</span>
                                                                    <span class="text-muted">
                                                                        <?= htmlspecialchars($student_data['first_name'] . ' ' . $student_data['last_name']); ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="info-item mb-3">
                                                            <div class="d-flex align-items-center">
                                                                <i class="bx bx-envelope icon-xl text-primary me-2"></i>
                                                                <div>
                                                                    <span class="fw-medium d-block">Email</span>
                                                                    <span class="text-muted">
                                                                        <?= htmlspecialchars($student_data['email']); ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="info-item mb-3">
                                                            <div class="d-flex align-items-center">
                                                                <i class="bx bx-phone icon-xl text-primary me-2"></i>
                                                                <div>
                                                                    <span class="fw-medium d-block">Contact</span>
                                                                    <span class="text-muted">
                                                                        <?= htmlspecialchars($student_data['phone_number']); ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="info-item mb-3">
                                                            <div class="d-flex align-items-center">
                                                                <i class="bx bx-user-check icon-xl text-primary me-2"></i>
                                                                <div>
                                                                    <span class="fw-medium d-block">Status</span>
                                                                    <span class="badge bg-label-success">Active</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mt-3">
                                                        <div class="info-item mb-3">
                                                            <div class="d-flex align-items-center">
                                                                <i class="bx bx-calendar icon-xl text-primary me-2"></i>
                                                                <div>
                                                                    <span class="fw-medium d-block">Date of Birth</span>
                                                                    <span class="text-muted">
                                                                        <?= htmlspecialchars($student_data['date_of_birth']); ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="info-item mb-3">
                                                            <div class="d-flex align-items-center">
                                                                <i class="bx bx-home icon-xl text-primary me-2"></i>
                                                                <div>
                                                                    <span class="fw-medium d-block">Address</span>
                                                                    <span class="text-muted">
                                                                        <?= htmlspecialchars($student_data['address'] ?: 'Not provided'); ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="info-item mb-3">
                                                            <div class="d-flex align-items-center">
                                                                <i class="bx bx-user-voice icon-xl text-primary me-2"></i>
                                                                <div>
                                                                    <span class="fw-medium d-block">Emergency Contact</span>
                                                                    <span class="text-muted">
                                                                        <?= htmlspecialchars($student_data['emergency_contact_name'] . ': ' . $student_data['emergency_contact_number']); ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="info-item mb-3">
                                                            <div class="d-flex align-items-center">
                                                                <i class="bx bx-heart icon-xl text-primary me-2"></i>
                                                                <div>
                                                                    <span class="fw-medium d-block">Health Condition</span>
                                                                    <span class="text-muted">
                                                                        <?= htmlspecialchars($student_data['health_condition'] ?: 'None'); ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Billing Tab -->
                                            <div class="tab-pane fade" id="profile-billing" role="tabpanel">
                                                <h5 class="mb-4">Payment History</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-borderless">
                                                        <thead>
                                                            <tr>
                                                                <th>Invoice</th>
                                                                <th>Date</th>
                                                                <th>Amount</th>
                                                                <th>Status</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $billings = $student->getBillings($student_data['user_id']);
                                                            foreach ($billings as $billing) {
                                                                echo '<tr>';
                                                                echo '<td>#' . htmlspecialchars($billing['billing_id']) . '</td>';
                                                                echo '<td>' . htmlspecialchars(date('M d, Y', strtotime($billing['date_due']))) . '</td>';
                                                                echo '<td>$' . number_format($billing['amount'], 2) . '</td>';
                                                                echo '<td><span class="badge bg-label-' . ($billing['status'] === 'Fully Paid' ? 'success' : 'warning') . '">' . htmlspecialchars($billing['status']) . '</span></td>';
                                                                echo '<td><button class="btn btn-sm btn-outline-primary">View</button></td>';
                                                                echo '</tr>';
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Activities Tab -->
                                            <div class="tab-pane fade" id="profile-activities" role="tabpanel">
                                                <h5 class="mb-4">Recent Activities</h5>
                                                <ul class="timeline">
                                                    <li class="timeline-item">
                                                        <span class="timeline-point timeline-point-primary">
                                                            <i class="bx bx-home-circle"></i>
                                                        </span>
                                                        <div class="timeline-event">
                                                            <div class="timeline-header">
                                                                <h6 class="mb-0">Room Allocation</h6>
                                                                <small class="text-muted"><?= date('M d, Y H:i A', strtotime($student_data['enrollment_date'])); ?></small>
                                                            </div>
                                                            <p class="mb-0">
                                                                <?php
                                                                echo $room_allocation
                                                                    ? 'Allocated to Room ' . htmlspecialchars($room_allocation['room_number'])
                                                                    : 'No room allocated';
                                                                ?>
                                                            </p>
                                                        </div>
                                                    </li>
                                                </ul>
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

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-simple modal-edit-user">
            <div class="modal-content p-3">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3>Edit User Information</h3>
                        <p>Updating user details will receive a privacy audit.</p>
                    </div>
                    <form id="editUserForm" class="row g-3" method="POST" action="/student/profile/update">
                        <?php set_csrf(); ?>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="modalEditUserFirstName">First Name</label>
                            <input
                                type="text"
                                id="modalEditUserFirstName"
                                name="first_name"
                                class="form-control"
                                value="<?= htmlspecialchars($student_data['first_name']); ?>"
                                required />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="modalEditUserLastName">Last Name</label>
                            <input
                                type="text"
                                id="modalEditUserLastName"
                                name="last_name"
                                class="form-control"
                                value="<?= htmlspecialchars($student_data['last_name']); ?>"
                                required />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="modalEditUserEmail">Email</label>
                            <input
                                type="email"
                                id="modalEditUserEmail"
                                name="email"
                                class="form-control"
                                value="<?= htmlspecialchars($student_data['email']); ?>"
                                required />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="modalEditUserPhone">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text">GH (+233)</span>
                                <input
                                    type="text"
                                    id="modalEditUserPhone"
                                    name="phone_number"
                                    class="form-control phone-number-mask"
                                    value="<?= htmlspecialchars($student_data['phone_number']); ?>"
                                    required />
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="modalEditUserGender">Gender</label>
                            <select id="modalEditUserGender" name="gender" class="form-select" required>
                                <option value="Male" <?= $student_data['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?= $student_data['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?= $student_data['gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="modalEditUserAddress">Address</label>
                            <input
                                type="text"
                                id="modalEditUserAddress"
                                name="address"
                                class="form-control"
                                value="<?= htmlspecialchars($student_data['address'] ?: ''); ?>"
                                required />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="modalEditEmergencyContactName">Emergency Contact Name</label>
                            <input
                                type="text"
                                id="modalEditEmergencyContactName"
                                name="emergency_contact_name"
                                class="form-control"
                                value="<?= htmlspecialchars($student_data['emergency_contact_name']); ?>"
                                required />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="modalEditEmergencyContactNumber">Emergency Contact Number</label>
                            <div class="input-group">
                                <span class="input-group-text">GH (+233)</span>
                                <input
                                    type="text"
                                    id="modalEditEmergencyContactNumber"
                                    name="emergency_contact_number"
                                    class="form-control phone-number-mask"
                                    value="<?= htmlspecialchars($student_data['emergency_contact_number']); ?>"
                                    required />
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="modalEditHealthCondition">Health Condition</label>
                            <textarea
                                id="modalEditHealthCondition"
                                name="health_condition"
                                class="form-control"
                                rows="4"><?= htmlspecialchars($student_data['health_condition'] ?: ''); ?></textarea>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Main JS -->
    <script src="../../assets/js/main.js"></script>

    <!-- Page JS -->
    <script>
        $(function() {
            // Initialize Select2
            $('.select2').select2();

            // Form validation
            const editUserForm = document.getElementById('editUserForm');
            if (editUserForm) {
                FormValidation.formValidation(editUserForm, {
                    fields: {
                        first_name: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter your first name'
                                },
                                stringLength: {
                                    min: 2,
                                    max: 50,
                                    message: 'First name must be between 2 and 50 characters'
                                }
                            }
                        },
                        last_name: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter your last name'
                                },
                                stringLength: {
                                    min: 2,
                                    max: 50,
                                    message: 'Last name must be between 2 and 50 characters'
                                }
                            }
                        },
                        email: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter your email'
                                },
                                emailAddress: {
                                    message: 'Please enter a valid email address'
                                }
                            }
                        },
                        phone_number: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter your phone number'
                                },
                                regexp: {
                                    regexp: /^(\+233|0)\d{9}$/,
                                    message: 'Phone number must be in +233XXXXXXXXX or 0XXXXXXXXX format'
                                }
                            }
                        },
                        gender: {
                            validators: {
                                notEmpty: {
                                    message: 'Please select your gender'
                                }
                            }
                        },
                        address: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter your address'
                                }
                            }
                        },
                        emergency_contact_name: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter emergency contact name'
                                }
                            }
                        },
                        emergency_contact_number: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter emergency contact number'
                                },
                                regexp: {
                                    regexp: /^(\+233|0)\d{9}$/,
                                    message: 'Emergency contact number must be in +233XXXXXXXXX or 0XXXXXXXXX format'
                                }
                            }
                        }
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5({
                            eleValidClass: '',
                            rowSelector: '.col-12'
                        }),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        autoFocus: new FormValidation.plugins.AutoFocus()
                    }
                }).on('core.form.valid', function() {
                    // Ensure form submits
                    editUserForm.submit();
                });
            }
        });
    </script>
</body>

</html>