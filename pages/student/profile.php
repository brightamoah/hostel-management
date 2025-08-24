<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../../app/models/Student.php";
require_once __DIR__ . "/../../utils/avatar.php";


// Check if user is authenticated
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Student') {
    header("Location: /login");
    exit();
}


$student_data = $_SESSION['user'];
$conn = getDb();
$student = new Student($conn);


// Fetch room allocation for room information
$room_allocation = $student->getRoomAllocation($student_data['user_id']);

// Fetch additional relevant student stats
$open_maintenance_requests = $student->getOpenMaintenanceRequests($student_data['user_id']);
$pending_visitors = $student->getPendingVisitors($student_data['user_id']);

$recent_activities = $student->getRecentActivities($student_data['user_id']);

$avatar = Avatar::generateUserAvatar($student_data);
$initials = $avatar['initials'];
$bg_color = $avatar['bg_color'];

?>

<!doctype html>
<html lang="en" class="layout-menu-collapsed layout-menu-fixed layout-navbar-fixed layout-navbar-sticky layout-compact" dir="ltr" data-skin="default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-bs-theme="light">

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

        /* Large profile avatar styling */
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

        .profile-stats {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-radius: 0.5rem;
            padding: 1.5rem;
            transition: all 0.3s;
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
                <?php include_once __DIR__ . "/../../Components/header.php" ?>
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
                                                <?= htmlspecialchars($student_data['first_name'] . ' ' . $student_data['last_name']); ?>
                                            </h3>
                                            <span class="bg-label-primary badge user-badge">
                                                <?= htmlspecialchars($student_data['role']); ?>
                                            </span>
                                            <p class="mt-3 text-muted">
                                                <?php
                                                echo $room_allocation
                                                    ? 'Room ' . htmlspecialchars($room_allocation['room_number']) . ' | ' . htmlspecialchars($room_allocation['room_type'])
                                                    : 'No room allocated';
                                                ?>
                                            </p>
                                        </div>

                                        <div class="justify-content-center mb-4 row g-2">
                                            <div class="d-md-inline d-grid col-12 col-md-auto">
                                                <button class="me-md-3 mb-2 mb-md-0 w-100 w-md-auto btn btn-primary action-btn" data-bs-target="#editUser" data-bs-toggle="modal">
                                                    <i class="me-1 icon-base bx bx-edit-alt"></i> Edit Profile
                                                </button>
                                            </div>
                                            <div class="d-md-inline d-grid col-12 col-md-auto">
                                                <button class="btn-outline-primary w-100 w-md-auto btn action-btn">
                                                    <i class="me-1 icon-lg bx bx-key"></i> Change Password
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php include_once __DIR__ . "/../../Components/student/profile/stats_card.php" ?>

                        <!-- Profile Tabs -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-4 card">
                                    <div class="pb-0 card-header">
                                        <ul class="flex-nowrap overflow-auto nav nav-tabs card-header-tabs" role="tablist">
                                            <li class="nav-item">
                                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-info" role="tab">Personal Info</button>
                                            </li>
                                            <li class="nav-item">
                                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-room" role="tab">Room Details</button>
                                            </li>
                                            <li class="nav-item">
                                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-activities" role="tab">Activities</button>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <!-- Personal Info Tab -->
                                            <?php include_once __DIR__ . "/../../Components/student/profile/personal_info.php" ?>


                                            <?php include_once __DIR__ . "/../../Components/student/profile/room_tab.php" ?>

                                            <!-- Activities Tab -->
                                            <?php include_once __DIR__ . "/../../Components/student/profile/activities_tab.php" ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php include_once __DIR__ . "/../../Components/footer.php" ?>

                    <div class="content-backdrop fade"></div>
                </div>

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
    <?= include_once __DIR__ . "/../../Components/student/profile/edit_profile.php" ?>

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

            // Fix Select2 in Bootstrap modal for gender select
            if ($.fn.select2) {
                $('#editUser').on('shown.bs.modal', function() {
                    $("#modalEditUserGender").select2({
                        dropdownParent: $('#editUser'),
                        placeholder: "Select Gender",
                        allowClear: true,
                        width: "100%"
                    });
                });
            }

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