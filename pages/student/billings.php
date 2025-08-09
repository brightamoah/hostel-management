<?php
// session_start();
?>

<!doctype html>
<html lang="en" class="layout-menu-collapsed layout-menu-fixed layout-navbar-fixed layout-navbar-sticky layout-compact" dir="ltr" data-skin="default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Kings Hostel - Billings</title>
    <meta name="description" content="" />

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

    <link rel="stylesheet" href="../../assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="../../assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/sweetalert2/sweetalert2.css" />
    <script src="../../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <!-- Core CSS -->
    <link rel="stylesheet" href="../../assets/vendor/libs/pickr/pickr-themes.css" />
    <link rel="stylesheet" href="../../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/@form-validation/form-validation.css" />

    <!-- Helpers -->
    <script src="../../assets/vendor/js/helpers.js"></script>
    <script src="../../assets/vendor/js/template-customizer.js"></script>
    <script src="../../assets/js/config.js"></script>
</head>

<body>
    <div class="layout-content-navbar layout-wrapper">
        <div class="layout-container">
            <?php include_once "./Components/sidebar.php" ?>
            <div class="rounded-1 menu-mobile-toggler d-xl-none">
                <a href="javascript:void(0);" class="p-2 rounded-1 text-bg-secondary text-large layout-menu-toggle menu-link">
                    <i class="bx bx-menu icon-base"></i>
                    <i class="bx-chevron-right bx icon-base"></i>
                </a>
            </div>

            <div class="layout-page">
                <?php include_once "./Components/header.php" ?>

                <div class="content-wrapper">
                    <div class="flex-grow-1 container-p-y container-xxl">
                        <!-- Billings Table -->
                        <div class="card" id="billingsTable">
                            <div class="border-bottom card-header">
                                <h5 class="mb-0 card-title">Your Billings</h5>
                                <div class="d-flex align-items-center justify-content-between gap-md-0 pt-4 row g-6">
                                    <div class="col-md-3">
                                        <input type="text" id="billingSearch" class="form-control" placeholder="Search billings..." />
                                    </div>
                                    <div class="col-md-3">
                                        <select id="statusFilter" class="form-select">
                                            <option value="">All Statuses</option>
                                            <option value="Unpaid">Unpaid</option>
                                            <option value="Partially Paid">Partially Paid</option>
                                            <option value="Fully Paid">Fully Paid</option>
                                            <option value="Overdue">Overdue</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6"></div>
                                </div>
                            </div>
                            <div class="table-responsive card-datatable">
                                <table class="table border-top datatables-billings">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Billing ID</th>
                                            <th>Description</th>
                                            <th>Amount (GH₵)</th>
                                            <th>Date Due</th>
                                            <th>Status</th>
                                            <th>Outstanding (GH₵)</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <!-- Payment Confirmation Modal -->
                        <div class="modal fade" id="paymentConfirmationModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Payment Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="paymentForm">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <h6 class="mb-3">Billing Information</h6>
                                                    <div class="dark:bg-light card">
                                                        <div class="card-body">
                                                            <p class="mb-1"><strong>Billing ID:</strong> <span id="confirmBillingId"></span></p>
                                                            <p class="mb-1"><strong>Description:</strong> <span id="confirmDescription"></span></p>
                                                            <p class="mb-1"><strong>Purpose:</strong> <span id="confirmPurpose"></span></p>
                                                            <p class="mb-0"><strong>Outstanding Amount:</strong> <span id="confirmMaxAmount" class="text-danger"></span></p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <h6 class="mb-3">Payment Amount</h6>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="paymentType" id="fullPayment" value="full" checked>
                                                        <label class="form-check-label" for="fullPayment">
                                                            <strong>Pay Full Amount:</strong> <span id="fullAmountDisplay"></span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="paymentType" id="partialPayment" value="partial">
                                                        <label class="form-check-label" for="partialPayment">
                                                            <strong>Partial Payment</strong>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12" id="partialAmountSection" style="display: none;">
                                                    <label for="paymentAmount" class="form-label">Enter Amount (GH₵)</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">GH₵</span>
                                                        <input type="number" class="form-control" id="paymentAmount"
                                                            step="0.01" min="1" placeholder="Enter amount">
                                                    </div>
                                                    <div class="form-text">
                                                        Minimum: GH₵1.00 | Maximum: <span id="maxAmountText"></span>
                                                    </div>
                                                    <div id="amountError" class="text-danger small" style="display: none;"></div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary confirm-pay-btn">
                                            <i class="me-1 bx bx-credit-card"></i>
                                            Proceed to Pay
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php include_once __DIR__ . "/../../Components/admin/billing/view_invoice.php"; ?>

                        <?php include_once "./Components/footer.php" ?>
                        <div class="content-backdrop fade"></div>
                    </div>
                </div>
            </div>

            <div class="layout-overlay layout-menu-toggle"></div>
            <div class="drag-target"></div>
        </div>

        <!-- Core JS -->
        <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
        <script src="../../assets/vendor/libs/popper/popper.js"></script>
        <script src="../../assets/vendor/js/bootstrap.js"></script>
        <script src="../../assets/vendor/libs/@algolia/autocomplete-js.js"></script>
        <script src="../../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>
        <script src="../../assets/vendor/libs/pickr/pickr.js"></script>
        <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
        <script src="../../assets/vendor/libs/hammer/hammer.js"></script>
        <script src="../../assets/vendor/libs/i18n/i18n.js"></script>
        <script src="../../assets/vendor/js/menu.js"></script>
        <script src="../../assets/vendor/libs/moment/moment.js"></script>
        <script src="../../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
        <script src="../../assets/vendor/libs/select2/select2.js"></script>
        <script src="../../assets/vendor/libs/@form-validation/popular.js"></script>
        <script src="../../assets/vendor/libs/@form-validation/bootstrap5.js"></script>
        <script src="../../assets/vendor/libs/@form-validation/auto-focus.js"></script>
        <script src="../../assets/vendor/libs/cleave-zen/cleave-zen.js"></script>
        <script src="../../assets/js/main.js"></script>
        <script src="../../assets/js/ui-modals.js"></script>
        <script src="../../assets/js/app-billing-list.js"></script>
</body>

</html>