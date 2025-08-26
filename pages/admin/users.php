<?php
require_once __DIR__ . "/../../app/admin/users_stats.php";
require_once __DIR__ . "/../../utils/hostel_helpers.php";

// Determine scope text for statistics
$statsScope = isSuperAdmin() ? "All Hostels" : "Your Hostel";
?>

<!DOCTYPE html>
<html lang="en" class="layout-menu-fixed layout-navbar-fixed layout-navbar-sticky layout-compact"
    dir="ltr" data-skin="default" data-assets-path="../../assets/" data-template="vertical-menu-template"
    data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Kings Hostel - Admin Users</title>
    <meta name="description" content="Admin dashboard for managing users in Kings Hostel" />
    <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf'] ?? ''); ?>">

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

    <!-- Icons -->
    <link rel="stylesheet" href="../../assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="../../assets/vendor/fonts/fontawesome.css" />

    <link rel="stylesheet" href="../../assets/vendor/libs/pickr/pickr-themes.css" />
    <!-- Core CSS -->
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

    <link rel="stylesheet" href="../../assets/vendor/libs/apex-charts/apex-charts.css" />

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

                        <!-- Statistic Cards -->
                        <?php include_once __DIR__ . "/../../Components/admin/user/stat_cards.php"; ?>

                        <!-- Users DataTable -->
                        <div class="card">
                            <div class="d-flex align-items-center justify-content-between card-header">
                                <h5 class="mb-0 card-title">Users List</h5>
                                <div id="accessLevelIndicator" style="display: none;">
                                    <span class="bg-label-info badge" id="accessBadge">Loading...</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-4 row">
                                    <div class="mb-3 col-md-4 user_role"></div>
                                    <div class="col-md-4 user_status"></div>

                                </div>
                                <table class="table datatables-users">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <!-- <th><input type="checkbox" class="form-check-input"></th> -->
                                            <th>User</th>
                                            <th>Role</th>
                                            <th>Resident Status</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <!-- /Users DataTable -->

                        <!-- Add User Offcanvas -->
                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser"
                            aria-labelledby="offcanvasAddUserLabel">
                            <div class="offcanvas-header">
                                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add New User</h5>
                                <button type="button" class="text-reset btn-close" data-bs-dismiss="offcanvas"
                                    aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form id="addNewUserForm" class="form-control-validation">
                                    <?php set_csrf() ?>
                                    <div class="mb-3">
                                        <label for="userFullname" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="userFullname" name="userFullname"
                                            required />
                                    </div>
                                    <div class="mb-3">
                                        <label for="userEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="userEmail" name="userEmail"
                                            required />
                                    </div>
                                    <div class="mb-3">
                                        <label for="userPassword" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="userPassword"
                                            name="userPassword" required minlength="8" />
                                    </div>
                                    <div class="mb-3">
                                        <label for="userRole" class="form-label">Role</label>
                                        <select class="form-select" id="userRole" name="userRole" required>
                                            <option value="" disabled selected>Select Role</option>
                                            <option value="Student">Student</option>
                                            <option value="Admin">Admin</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Add User</button>
                                </form>
                            </div>
                        </div>
                        <!-- /Add User Offcanvas -->

                        <!-- Student Details Modal -->
                        <?php include_once __DIR__ . "/../../Components/admin/user/student_details.php"; ?>

                        <!-- Admin Details Modal -->
                        <?php include_once __DIR__ . "/../../Components/admin/user/admin-details.php"; ?>

                    </div>
                    <!-- /Content -->

                    <!-- Footer -->
                    <?php include_once __DIR__ . "/../../Components/footer.php" ?>
                    <!-- /Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- /Content wrapper -->
            </div>
            <!-- /Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area -->
        <div class="drag-target"></div>
    </div>
    <!-- /Layout wrapper -->

    <!-- Core JS -->
    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../assets/vendor/js/menu.js"></script>


    <script src="../../assets/vendor/libs/pickr/pickr.js"></script>
    <!-- Vendors JS -->
    <script src="../../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/select2/select2.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/auto-focus.js"></script>
    <script src="../../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <!-- Main JS -->
    <script src="../../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../../assets/js/app-user-list.js"></script>
</body>

</html>