<?php


require_once "./app/models/Room.php";
?>

<!doctype html>
<html lang="en" class="layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-skin="default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Student Dashboard - Hostel Management</title>
    
    
    <link rel="apple-touch-icon" sizes="180x180" href="../../assets/img/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../assets/img/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/img/favicon_io/favicon-16x16.png">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/pickr/pickr-themes.css" />
    <link rel="stylesheet" href="../../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/apex-charts/apex-charts.css" />



    <script src="../../assets/vendor/js/helpers.js"></script>
    <script src="../../assets/js/config.js"></script>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include './components/sidebar.php'; ?>
            <div class="layout-page">
                <?php include './components/header.php'; ?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">


                        <div class="card-body row p-0 pb-6 g-6 p-xl-5">

                            <div class="col-xxl-8 mb-6 order-0">
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


                            <div class="col-12 col-lg-4 ps-md-4 ps-lg-6">
                                <div class="d-flex justify-content-between align-items-center bg-white rounded-3 mb-4 p-5">
                                    <div>
                                        <div>
                                            <h5 class="mb-1">Time in Hostel</h5>
                                            <p class="mb-9">Since Enrollment</p>
                                        </div>
                                        <div class="time-spending-chart">
                                            <h4 class="mb-2">10<span class="text-body">d</span> 12<span class="text-body">h</span></h4>
                                            <span class="badge bg-label-success">+2%</span>
                                        </div>
                                    </div>
                                    <div id="leadsReportChart"></div>
                                </div>
                            </div>

                        </div>


                        <div style="margin-top: -2rem;">

                            <?php include_once './components/dashboard_card.php'; ?>
                        </div>


                        <div>
                            <?php include './components/visitors.php'; ?>
                        </div>





                    </div>
                    <?php include './components/footer.php'; ?>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
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
    <script src="../../assets/vendor/libs/moment/moment.js"></script>
    <script src="../../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/app-academy-dashboard.js"></script>
    <!-- Page JS -->
    <script src="../../assets/js/tables-datatables-extensions.js"></script>
</body>
</body>

</html>