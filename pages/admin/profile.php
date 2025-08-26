<?php
require_once "./database/db.php";
require_once "./app/models/User.php";

// Ensure session is started and set_csrf is available
if (!function_exists('set_csrf')) {
    require_once "./router.php";
}

// Check if user is authenticated and is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: /login");
    exit();
}

$admin_data = $_SESSION['user'];
$db = new Database();
$conn = $db->connect();
$user = new User($conn);

// Get admin details from the admins table
$admin_details = null;
try {
    $admin_details = $user->getAdminByUserId($admin_data['user_id']);
} catch (Exception $e) {
    error_log("Error fetching admin details: " . $e->getMessage());
}

// Get user stats (for admin dashboard-like stats)
// Initialize stats with default values
$stats = [
    'total_students' => 0,
    'total_rooms' => 0,
    'pending_payments' => 0,
    'pending_maintenance' => 0
];

try {
    // Check and get total students
    $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'Student'");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['total_students'] = $row['count'];
    }
} catch (Exception $e) {
    error_log("Error fetching students count: " . $e->getMessage());
}

try {
    // Check and get total rooms
    $result = $conn->query("SELECT COUNT(*) as count FROM rooms");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['total_rooms'] = $row['count'];
    }
} catch (Exception $e) {
    error_log("Error fetching rooms count: " . $e->getMessage());
}

try {
    // Check and get pending payments
    $result = $conn->query("SELECT COUNT(*) as count FROM billing WHERE status IN ('Unpaid', 'Overdue')");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['pending_payments'] = $row['count'];
    }
} catch (Exception $e) {
    error_log("Error fetching billing count: " . $e->getMessage());
}

try {
    // Check and get pending maintenance requests
    $result = $conn->query("SELECT COUNT(*) as count FROM maintenance_requests WHERE status = 'Pending'");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['pending_maintenance'] = $row['count'];
    }
} catch (Exception $e) {
    error_log("Error fetching maintenance count: " . $e->getMessage());
}
?>

<!doctype html>
<html lang="en" class="layout-menu-fixed layout-navbar-fixed layout-navbar-sticky layout-compact" dir="ltr" data-skin="default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Kings Hostel - Admin Profile</title>

    <meta name="description" content="Admin profile management for Kings Hostel" />

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
    <link rel="stylesheet" href="../../assets/vendor/libs/animate-css/animate.css" />
    <!-- Page CSS -->
    <style>
        .profile-header {
            background: linear-gradient(135deg, #28c76f, #48da89);
            border-radius: 0.5rem;
            padding: 2rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(40, 199, 111, 0.25);
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
        }

        .info-item {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s;
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
        }

        .tab-content {
            padding: 1.5rem;
        }



        .access-level-badge {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
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
                                <div class="mt-10 mb-4 card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-center mb-3 profile-avatar-wrapper">
                                            <?php
                                            $first_name = $admin_details['first_name'] ?? ($admin_data['name'] ? explode(' ', $admin_data['name'])[0] : 'Admin');
                                            $last_name = $admin_details['last_name'] ?? ($admin_data['name'] ? trim(str_replace(explode(' ', $admin_data['name'])[0], '', $admin_data['name'])) : 'User');
                                            if (empty($last_name)) $last_name = 'User';
                                            $initials = strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));
                                            $colors = ['#28c76f', '#7367f0', '#ea5455', '#ff9f43', '#00cfe8'];
                                            $bgColor = $colors[array_rand($colors)];
                                            ?>
                                            <div class="d-flex align-items-center justify-content-center rounded-circle text-white text-center profile-avatar"
                                                style="background-color: <?= $bgColor; ?>; height: 150px; width: 150px; font-size: 50px; color: #fff;">
                                                <?= $initials; ?>
                                            </div>
                                        </div>
                                        <div class="mb-4 text-center">
                                            <h3 class="mb-2">
                                                <?= htmlspecialchars($first_name . ' ' . $last_name); ?>
                                            </h3>
                                            <div class="mb-2">
                                                <span class="bg-label-success me-2 badge user-badge">
                                                    <?= htmlspecialchars($admin_data['role']); ?>
                                                </span>
                                                <?php if ($admin_details): ?>
                                                    <span class="bg-label-info badge access-level-badge">
                                                        <?= htmlspecialchars($admin_details['access_level']); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="mt-3 text-muted">
                                                <?= $admin_details ? 'Department: ' . htmlspecialchars($admin_details['department']) : 'System Administrator'; ?>
                                            </p>
                                        </div>

                                        <div class="d-flex justify-content-center mb-4">
                                            <button class="me-3 btn btn-success action-btn" data-bs-target="#editAdmin" data-bs-toggle="modal">
                                                <i class="me-1 icon-base bx bx-edit-alt"></i> Edit Profile
                                            </button>
                                            <button class="btn-outline-success btn action-btn">
                                                <i class="me-1 icon-lg bx bx-key"></i> Change Password
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="mb-4 row admin-stats">
                            <div class="col-md-3">
                                <div class="h-100 card profile-stats">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 avatar">
                                                <div class="bg-label-primary rounded avatar-initial">
                                                    <i class="bx bx-user fs-4"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0"><?= $stats['total_students'] ?? 0; ?></h4>
                                                <span>Total Students</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="h-100 card profile-stats">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 avatar">
                                                <div class="bg-label-success rounded avatar-initial">
                                                    <i class="bx bx-home fs-4"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0"><?= $stats['total_rooms'] ?? 0; ?></h4>
                                                <span>Total Rooms</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="h-100 card profile-stats">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 avatar">
                                                <div class="bg-label-warning rounded avatar-initial">
                                                    <i class="bx bx-credit-card fs-4"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0"><?= $stats['pending_payments'] ?? 0; ?></h4>
                                                <span>Pending Payments</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="h-100 card profile-stats">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 avatar">
                                                <div class="bg-label-danger rounded avatar-initial">
                                                    <i class="bx bx-wrench fs-4"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0"><?= $stats['pending_maintenance'] ?? 0; ?></h4>
                                                <span>Pending Maintenance</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <pre>
                            <?= print_r($admin_details, true) ?>
                        </pre>

                        <!-- Profile Tabs -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-4 card">
                                    <div class="pb-0 card-header">
                                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#admin-info" role="tab">
                                                    <i class="me-1 icon-sm bx bx-user"></i> Personal Info
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#admin-permissions" role="tab">
                                                    <i class="me-1 icon-sm bx bx-shield"></i> Permissions
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#admin-activities" role="tab">
                                                    <i class="me-1 icon-sm bx bx-history"></i> Activities
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <!-- Personal Info Tab -->
                                            <div class="tab-pane fade show active" id="admin-info" role="tabpanel">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3 info-item">
                                                            <label class="text-muted form-label">Full Name</label>
                                                            <p class="mb-0 fw-semibold"><?= htmlspecialchars($first_name . ' ' . $last_name); ?></p>
                                                        </div>
                                                        <div class="mb-3 info-item">
                                                            <label class="text-muted form-label">Email</label>
                                                            <p class="mb-0 fw-semibold"><?= htmlspecialchars($admin_data['email']); ?></p>
                                                        </div>
                                                        <div class="mb-3 info-item">
                                                            <label class="text-muted form-label">Role</label>
                                                            <p class="mb-0 fw-semibold"><?= htmlspecialchars($admin_data['role']); ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php if ($admin_details): ?>
                                                            <div class="mb-3 info-item">
                                                                <label class="text-muted form-label">Department</label>
                                                                <p class="mb-0 fw-semibold"><?= htmlspecialchars($admin_details['department']); ?></p>
                                                            </div>
                                                            <div class="mb-3 info-item">
                                                                <label class="text-muted form-label">Access Level</label>
                                                                <p class="mb-0 fw-semibold"><?= htmlspecialchars($admin_details['access_level']); ?></p>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="mb-3 info-item">
                                                            <label class="text-muted form-label">Last Login</label>
                                                            <p class="mb-0 fw-semibold"><?= $admin_data['last_login'] ?? 'N/A'; ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Permissions Tab -->
                                            <div class="tab-pane fade" id="admin-permissions" role="tabpanel">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h5 class="mb-3">Access Permissions</h5>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="list-group">
                                                                    <div class="list-group-item d-flex align-items-center justify-content-between">
                                                                        <span><i class="me-2 bx bx-user-check"></i>User Management</span>
                                                                        <span class="bg-success rounded-pill badge">Granted</span>
                                                                    </div>
                                                                    <div class="list-group-item d-flex align-items-center justify-content-between">
                                                                        <span><i class="me-2 bx bx-home"></i>Room Management</span>
                                                                        <span class="bg-success rounded-pill badge">Granted</span>
                                                                    </div>
                                                                    <div class="list-group-item d-flex align-items-center justify-content-between">
                                                                        <span><i class="me-2 bx bx-credit-card"></i>Billing Management</span>
                                                                        <span class="bg-success rounded-pill badge">Granted</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="list-group">
                                                                    <div class="list-group-item d-flex align-items-center justify-content-between">
                                                                        <span><i class="me-2 bx bx-wrench"></i>Maintenance Requests</span>
                                                                        <span class="bg-success rounded-pill badge">Granted</span>
                                                                    </div>
                                                                    <div class="list-group-item d-flex align-items-center justify-content-between">
                                                                        <span><i class="me-2 bx bx-shield-check"></i>Visitor Management</span>
                                                                        <span class="bg-success rounded-pill badge">Granted</span>
                                                                    </div>
                                                                    <div class="list-group-item d-flex align-items-center justify-content-between">
                                                                        <span><i class="me-2 bx bx-cog"></i>System Settings</span>
                                                                        <span class="badge bg-<?= ($admin_details['access_level'] ?? '') === 'Super Admin' ? 'success' : 'warning'; ?> rounded-pill">
                                                                            <?= ($admin_details['access_level'] ?? '') === 'Super Admin' ? 'Granted' : 'Limited'; ?>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Activities Tab -->
                                            <div class="tab-pane fade" id="admin-activities" role="tabpanel">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h5 class="mb-3">Recent Activities</h5>
                                                        <div class="activity-timeline">
                                                            <div class="d-flex align-items-start mb-3">
                                                                <div class="me-3 avatar avatar-sm">
                                                                    <div class="bg-label-primary rounded-circle avatar-initial">
                                                                        <i class="bx bx-user"></i>
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <p class="mb-1"><strong>Profile viewed</strong></p>
                                                                    <small class="text-muted">Today at <?= date('H:i'); ?></small>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-start mb-3">
                                                                <div class="me-3 avatar avatar-sm">
                                                                    <div class="bg-label-success rounded-circle avatar-initial">
                                                                        <i class="bx bx-log-in"></i>
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <p class="mb-1"><strong>Logged in</strong></p>
                                                                    <small class="text-muted"><?= $admin_data['last_login'] ?? 'N/A'; ?></small>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-start mb-3">
                                                                <div class="me-3 avatar avatar-sm">
                                                                    <div class="bg-label-info rounded-circle avatar-initial">
                                                                        <i class="bx bx-check"></i>
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <p class="mb-1"><strong>System access granted</strong></p>
                                                                    <small class="text-muted">Account created</small>
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

    <!-- Edit Admin Modal -->
    <div class="modal fade" id="editAdmin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-simple modal-edit-user">
            <div class="p-3 modal-content">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="mb-4 text-center">
                        <h3>Edit Admin Information</h3>
                        <p>Updating admin details will receive a privacy audit.</p>
                    </div>
                    <form id="editAdminForm" class="row g-3" method="POST" action="/admin/profile/update">
                        <?php set_csrf(); ?>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="modalEditAdminFirstName">First Name</label>
                            <input
                                type="text"
                                id="modalEditAdminFirstName"
                                name="first_name"
                                class="form-control"
                                value="<?= htmlspecialchars($admin_details['first_name'] ?? ($admin_data['name'] ? explode(' ', $admin_data['name'])[0] : '')); ?>"
                                required />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="modalEditAdminLastName">Last Name</label>
                            <input
                                type="text"
                                id="modalEditAdminLastName"
                                name="last_name"
                                class="form-control"
                                value="<?= htmlspecialchars($admin_details['last_name'] ?? ($admin_data['name'] ? trim(str_replace(explode(' ', $admin_data['name'])[0], '', $admin_data['name'])) : '')); ?>"
                                required />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="modalEditAdminEmail">Email</label>
                            <input
                                type="email"
                                id="modalEditAdminEmail"
                                name="email"
                                class="form-control"
                                value="<?= htmlspecialchars($admin_data['email']); ?>"
                                readonly />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="modalEditAdminDepartment">Department</label>
                            <select id="modalEditAdminDepartment" name="department" class="form-select" required>
                                <option value="">Select Department</option>
                                <option value="Administration" <?= ($admin_details['department'] ?? '') == 'Administration' ? 'selected' : ''; ?>>Administration</option>
                                <option value="IT Support" <?= ($admin_details['department'] ?? '') == 'IT Support' ? 'selected' : ''; ?>>IT Support</option>
                                <option value="Maintenance" <?= ($admin_details['department'] ?? '') == 'Maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                                <option value="Finance" <?= ($admin_details['department'] ?? '') == 'Finance' ? 'selected' : ''; ?>>Finance</option>
                                <option value="Student Affairs" <?= ($admin_details['department'] ?? '') == 'Student Affairs' ? 'selected' : ''; ?>>Student Affairs</option>
                                <option value="Security" <?= ($admin_details['department'] ?? '') == 'Security' ? 'selected' : ''; ?>>Security</option>
                            </select>
                        </div>
                        <?php if (($admin_details['access_level'] ?? 'Regular Admin') === 'Super Admin'): ?>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditAdminAccessLevel">Access Level</label>
                                <select id="modalEditAdminAccessLevel" name="access_level" class="form-select" required>
                                    <option value="Regular Admin" <?= ($admin_details['access_level'] ?? 'Regular Admin') == 'Regular Admin' ? 'selected' : ''; ?>>Regular Admin</option>
                                    <option value="Super Admin" <?= ($admin_details['access_level'] ?? '') == 'Super Admin' ? 'selected' : ''; ?>>Super Admin</option>
                                    <option value="Support Staff" <?= ($admin_details['access_level'] ?? '') == 'Support Staff' ? 'selected' : ''; ?>>Support Staff</option>
                                </select>
                            </div>
                        <?php else: ?>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditAdminAccessLevel">Access Level</label>
                                <input
                                    type="text"
                                    id="modalEditAdminAccessLevel"
                                    class="form-control"
                                    value="<?= htmlspecialchars($admin_details['access_level'] ?? 'Regular Admin'); ?>"
                                    readonly />
                                <small class="text-muted">Only Super Admins can modify access levels</small>
                            </div>
                        <?php endif; ?>
                        <div class="text-center col-12">
                            <button type="submit" class="me-1 me-sm-3 btn btn-success">Update Profile</button>
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
    <script src="../../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <!-- Main JS -->
    <script src="../../assets/js/main.js"></script>

    <!-- Page JS -->
    <script>
        $(function() {
            // Initialize Select2
            $('.select2').select2();

            // Form validation
            const editAdminForm = document.getElementById('editAdminForm');
            if (editAdminForm) {
                FormValidation.formValidation(editAdminForm, {
                    fields: {
                        first_name: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter first name'
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
                                    message: 'Please enter last name'
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
                                    message: 'Please enter email'
                                },
                                emailAddress: {
                                    message: 'Please enter a valid email address'
                                }
                            }
                        },
                        department: {
                            validators: {
                                notEmpty: {
                                    message: 'Please select a department'
                                }
                            }
                        },
                        access_level: {
                            validators: {
                                callback: {
                                    message: 'Please select an access level',
                                    callback: function(input) {
                                        // Only validate if the field is not readonly (i.e., user is Super Admin)
                                        const accessLevelField = document.getElementById('modalEditAdminAccessLevel');
                                        if (accessLevelField && accessLevelField.readOnly) {
                                            return true; // Skip validation for readonly field
                                        }
                                        return input.value !== '';
                                    }
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
                    editAdminForm.submit();
                });
            }

            // Auto-refresh stats every 30 seconds
            setInterval(function() {
                // You can add AJAX call here to refresh stats if needed
                console.log('Stats refreshed');
            }, 30000);
        });
    </script>
</body>

</html>