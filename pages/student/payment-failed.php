<?php

$reference = $_GET['reference'] ?? '';

if (isset($_SESSION['payment_error']) && $_SESSION['payment_error']['reference'] === $reference) {
    $error_details = $_SESSION['payment_error'];
    unset($_SESSION['payment_error']);
} else {
    // If session data is not available, redirect to billing page
    header("Location: /student/billing?error=invalid_access");
    exit;
}

$error_message = $error_details['message'] ?? 'An unknown error occurred.';


ob_start();
?>

<!doctype html>
<html lang="en" class="layout-menu-fixed layout-navbar-fixed layout-navbar-sticky layout-compact" dir="ltr" data-skin="default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Payment Failed - Kings Hostel</title>

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

    <!-- Helpers -->
    <script src="../../assets/vendor/js/helpers.js"></script>
    <script src="../../assets/vendor/js/template-customizer.js"></script>
    <script src="../../assets/js/config.js"></script>
</head>

<body>
    <div class="layout-content-navbar layout-wrapper">
        <div class="layout-container">
            <?php include_once "./Components/sidebar.php" ?>
            <div class="layout-page">
                <?php include_once "./Components/header.php" ?>
                <div class="content-wrapper">
                    <div class="flex-grow-1 container-p-y container-xxl">
                        <div class="justify-content-center row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="text-center card-body">
                                        <div class="mb-4">
                                            <i class="text-danger bx bx-x-circle" style="font-size: 4rem;"></i>
                                        </div>
                                        <h3 class="text-danger">Payment Failed</h3>
                                        <p class="mb-4"><?= htmlspecialchars($error_message) ?></p>

                                        <?php if ($reference): ?>
                                            <div class="bg-light card">
                                                <div class="card-body">
                                                    <p><strong>Reference:</strong> <?= htmlspecialchars($reference) ?></p>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <div class="d-flex align-items-center justify-content-center gap-3 me-4 mt-4">
                                            <a href="/student/billing" class="btn btn-primary">
                                                <i class='me-1 bx bx-refresh' style="font-size: 1.5rem;"></i>
                                                Try Again</a>
                                            <a href="/student/dashboard" class="btn btn-secondary">
                                                <i class="me-1 bx bx-home" style="font-size: 1.5rem;"></i>
                                                Dashboard</a>
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
    <script src="../../assets/js/notifications.js"></script>
</body>

</html>