<?php
require_once __DIR__ . "/../../app/admin/billing_stats_data.php";
require_once __DIR__ . "/../../utils/format_currency.php";
require_once __DIR__ . "/../../utils/hostel_helpers.php";

// Variables for scope badge
$statsScope = isSuperAdmin() ? "All Hostels" : "Your Hostel";
$currentHostelDetails = getCurrentHostelDetails();
$scopeDetails = "";

if (!isSuperAdmin() && $currentHostelDetails) {
    $scopeDetails = " ({$currentHostelDetails['hostel_name']})";
}
?>

<!DOCTYPE html>
<html lang="en" class="layout-menu-fixed layout-navbar-fixed layout-navbar-sticky layout-compact" dir="ltr" data-skin="default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf'] ?? ''; ?>">

    <title>Kings Hostel - Admin Billing Dashboard</title>

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
    <!-- <link rel="stylesheet" href="../../assets/vendor/libs/flatpickr/flatpickr.css" /> -->


    <!-- Add this to your HTML head section if not already present -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/light.css"> -->
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">


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
                <?php include_once __DIR__ . "/../../Components/admin/header.php" ?>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="flex-grow-1 container-p-y container-xxl">

                        <!-- Billing Statistics -->
                        <?php include_once __DIR__ . "/../../Components/admin/billing/stats_card.php"; ?>

                        <!-- Invoice Actions and Filter -->
                        <?php include_once __DIR__ . "/../../Components/admin/billing/search_filter_actions.php"; ?>

                        <!-- Billing Records DataTable -->
                        <div class="card">
                            <div class="d-flex align-items-center justify-content-between card-header">
                                <h5 class="mb-0 card-title">Billing Records</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <div id="accessLevelIndicator" style="display: none;">
                                        <span class="bg-label-info badge" id="accessBadge">Loading...</span>
                                    </div>
                                    <div class="action-buttons">
                                        <button type="button" class="refresh-table btn-outline-secondary btn btn-sm">
                                            <i class="me-1 bx bx-refresh icon-lg"></i> Refresh
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive card-datatable">
                                <table class="table border-top datatables-billings">
                                    <thead>
                                        <tr>
                                            <th>Invoice ID</th>
                                            <th>Student</th>
                                            <th>Amount</th>
                                            <th>Date Issued</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Paid Amount</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>


                        <!-- Create Invoice Modal -->
                        <?php include_once __DIR__ . "/../../Components/admin/billing/create_invoice_modal.php"; ?>

                        <?php include_once __DIR__ . "/../../Components/admin/billing/edit_billing.php"; ?>

                        <!-- Record Payment Modal -->
                        <?php include_once __DIR__ . "/../../Components/admin/billing/record_payment_modal.php"; ?>

                        <!-- Invoice View Modal -->
                        <?php include_once __DIR__ . "/../../Components/admin/billing/view_invoice.php"; ?>

                        <!-- Payment History Modal -->
                        <?php include_once __DIR__ . "/../../Components/admin/billing/payment_history.php"; ?>

                        <!-- Send Reminder Modal -->
                        <?php include_once __DIR__ . "/../../Components/admin/billing/send_reminder.php"; ?>

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
    <!-- <script src="../../assets/vendor/libs/flatpickr/flatpickr.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.js"></script>
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
    <script src="../../assets/js/admin-billing-list.js"></script>

    <!-- Access Level Indicator Script -->
    <script>
        $(document).ready(function() {
            // Show access level indicator for non-super admins
            <?php if (!isSuperAdmin()): ?>
                const indicator = document.getElementById('accessLevelIndicator');
                const badge = document.getElementById('accessBadge');

                if (indicator && badge) {
                    badge.textContent = '<?= $statsScope ?><?= $scopeDetails ?>';
                    indicator.style.display = 'block';
                }
            <?php endif; ?>
        });
    </script>

</body>



</html>