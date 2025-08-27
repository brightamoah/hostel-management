<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../../app/models/User.php";
require_once __DIR__ . "/../../utils/avatar.php";


if (!function_exists('set_csrf')) {
    require_once __DIR__ . "/../../router.php";
}

// Check if user is authenticated and is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: /login");
    exit();
}

$admin_data = $_SESSION['user'];
$conn = getDb();
$user = new User($conn);

// Get admin details from the admins table
$admin_details = null;
try {
    $admin_details = $user->getAdminByUserId($admin_data['user_id']);
} catch (Exception $e) {
    error_log("Error fetching admin details: " . $e->getMessage());
}


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

$avatar = Avatar::generateUserAvatar($admin_data);
$initials = $avatar['initials'];
$bg_color = $avatar['bg_color'];


$first_name = '';
$last_name = '';

if ($admin_details && isset($admin_details['first_name']) && isset($admin_details['last_name'])) {
    $first_name = $admin_details['first_name'];
    $last_name = $admin_details['last_name'];
} elseif (isset($admin_data['name']) && !empty($admin_data['name'])) {
    // Fallback to splitting the name from session
    $name_parts = explode(' ', trim($admin_data['name']), 2);
    $first_name = $name_parts[0] ?? '';
    $last_name = $name_parts[1] ?? '';
}

$recent_activities = [];
$recent_activities = [];
try {
    $recent_activities = $user->getAdminRecentActivities($admin_data['user_id']);
} catch (Exception $e) {
    error_log("Error fetching admin activities: " . $e->getMessage());
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

        .profile-avatar-large {
            width: 150px;
            height: 150px;
            font-size: 50px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 5px solid #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 576px) {
            .profile-avatar-large {
                width: 90px;
                height: 90px;
                font-size: 32px;
            }

            .profile-header {
                padding: 1rem 0;
            }

            .profile-stats {
                padding: 1rem;
            }

            .action-btn {
                min-width: auto;
            }
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
            <?php include_once __DIR__ . "/../../Components/sidebar.php" ?>

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
                                            <div class="avatar profile-avatar-large" style="border: none; box-shadow: none;">
                                                <span class="bg-label-<?= $bg_color; ?> rounded-circle avatar-initial profile-avatar-large"><?= $initials; ?></span>
                                            </div>
                                        </div>
                                        <div class="mb-4 text-center">
                                            <h3 class="mb-2">
                                                <?= htmlspecialchars("$first_name $last_name"); ?>
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
                        <?php include_once __DIR__ . "/../../Components/admin/profile/stat_cards.php" ?>

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
                                            <?php include_once __DIR__ . "/../../Components/admin/profile/personal_info.php" ?>

                                            <!-- Permissions Tab -->
                                            <?php include_once __DIR__ . "/../../Components/admin/profile/permissions_tab.php" ?>

                                            <!-- Activities Tab -->
                                            <?php include_once __DIR__ . "/../../Components/admin/profile/activities_tab.php" ?>
                                        </div>
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

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Edit Admin Modal -->
    <?php include_once __DIR__ . "/../../Components/admin/profile/edit_modal.php" ?>

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

            if ($.fn.select2) {
                $('#modalEditAdminDepartment').select2({
                    dropdownParent: $('#editAdmin'),
                    placeholder: "Select Department",
                });

                $('#modalEditAdminAccessLevel').select2({
                    dropdownParent: $('#editAdmin'),
                    placeholder: "Select Access Level",
                });
            }

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