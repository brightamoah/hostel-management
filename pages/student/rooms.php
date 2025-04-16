<?php
// require_once "./app/controllers/announcement.php";
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

    <title>Kings Hostel - Student Dashboard</title>
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

    <!-- Custom Styles -->
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include_once "./Components/sidebar.php" ?>
            <div class="menu-mobile-toggler d-xl-none rounded-1">
                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
                    <i class="bx bx-menu icon-base"></i>
                    <i class="bx bx-chevron-right icon-base"></i>
                </a>
            </div>

            <div class="layout-page">
                <?php include_once "./Components/header.php" ?>

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Rooms Table -->
                        <div class="card" id="roomsTable">
                            <div class="card-header border-bottom">
                                <h5 class="card-title mb-0">Available Rooms</h5>
                                <div class="d-flex justify-content-between align-items-center row pt-4 gap-md-0 g-6">
                                    <div class="col-md-3">
                                        <input type="text" id="roomSearch" class="form-control" placeholder="Search rooms..." />
                                    </div>
                                    <div class="col-md-3">
                                        <select id="buildingFilter" class="form-select">
                                            <option value="">All Buildings</option>
                                            <option value="Hostel A">Hostel A</option>
                                            <option value="Hostel B">Hostel B</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select id="roomTypeFilter" class="form-select">
                                            <option value="">All Room Types</option>
                                            <option value="Single">Single</option>
                                            <option value="Double">Double</option>
                                            <option value="Triple">Triple</option>
                                            <option value="Quad">Quad</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select id="floorFilter" class="form-select">
                                            <option value="">All Floors</option>
                                            <option value="1">1st Floor</option>
                                            <option value="2">2nd Floor</option>
                                            <option value="3">3rd Floor</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-datatable table-responsive">
                                <table class="datatables-rooms table border-top">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>RM#</th>
                                            <th>Building</th>
                                            <th>Floor</th>
                                            <th>Type</th>
                                            <th>Availability</th>
                                            <th>Status</th>
                                            <th>Amount</th> <!-- Changed from Features -->
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <!-- Room Details Modal -->
                        <div class="modal fade room-details-modal" id="roomDetailsModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title text-white" id="roomModalTitle">Room Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <!-- Room Overview Card -->
                                            <div class="col-12 mb-4">
                                                <div class="card shadow-none border">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <h5 class="mb-0" id="modalRoomNumberHeader"></h5>
                                                            <span class="badge bg-label-primary" id="modalRoomTypeHeader"></span>
                                                        </div>
                                                        <p class="mb-0"><i class="bx bx-map-pin me-1"></i> <span id="modalBuildingHeader"></span>, Floor <span id="modalFloorHeader"></span></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Room Details -->
                                            <div class="col-md-6">
                                                <div class="card shadow-none border mb-4">
                                                    <div class="card-header bg-transparent">
                                                        <h6 class="mb-0"><i class="bx bx-info-circle me-2"></i>Room Information</h6>
                                                    </div>
                                                    <div class="card-body pt-0">
                                                        <div class="row">
                                                            <div class="col-6 mb-3">
                                                                <small class="text-muted d-block">Room Number</small>
                                                                <span id="modalRoomNumber"></span>
                                                            </div>
                                                            <div class="col-6 mb-3">
                                                                <small class="text-muted d-block">Building</small>
                                                                <span id="modalBuilding"></span>
                                                            </div>
                                                            <div class="col-6 mb-3">
                                                                <small class="text-muted d-block">Floor</small>
                                                                <span id="modalFloor"></span>
                                                            </div>
                                                            <div class="col-6 mb-3">
                                                                <small class="text-muted d-block">Room Type</small>
                                                                <span id="modalRoomType"></span>
                                                            </div>
                                                            <div class="col-6 mb-3">
                                                                <small class="text-muted d-block">Amount: </small>
                                                                <strong id="modalAmount"></strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Occupancy Details -->
                                            <div class="col-md-6">
                                                <div class="card shadow-none border mb-4">
                                                    <div class="card-header bg-transparent">
                                                        <h6 class="mb-0"><i class="bx bx-user me-2"></i>Occupancy</h6>
                                                    </div>
                                                    <div class="card-body pt-0">
                                                        <div class="row">
                                                            <div class="col-12 mb-3">
                                                                <small class="text-muted d-block">Status</small>
                                                                <span id="modalStatus"></span>
                                                            </div>
                                                            <div class="col-6 mb-3">
                                                                <small class="text-muted d-block">Total Capacity</small>
                                                                <span id="modalCapacity"></span>
                                                            </div>
                                                            <div class="col-6 mb-3">
                                                                <small class="text-muted d-block">Current Occupants</small>
                                                                <span id="modalOccupancy"></span>
                                                            </div>
                                                        </div>
                                                        <div class="progress mb-2" style="height: 8px">
                                                            <div id="occupancyProgressBar" class="progress-bar bg-primary" style="width: 0%" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small id="occupancyProgressText" class="text-muted">Available spaces</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Features -->
                                            <div class="col-12">
                                                <div class="card shadow-none border">
                                                    <div class="card-header bg-transparent">
                                                        <h6 class="mb-0"><i class="bx bx-star me-2"></i>Features & Amenities</h6>
                                                    </div>
                                                    <div class="card-body pt-0">
                                                        <div id="modalFeatures" class="d-flex flex-wrap gap-1"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary book-room-btn-modal" data-bs-toggle="modal" data-bs-target="#bookingConfirmationModal">
                                            <i class="bx bx-check-circle me-1"></i>Book This Room
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Confirmation Modal -->
                        <div class="modal fade" id="bookingConfirmationModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Confirm Booking</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to book room <strong id="confirmRoomNumber"></strong> in <strong id="confirmBuilding"></strong>?</p>
                                        <p>This action will reserve the room for you.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary confirm-book-btn">Confirm Booking</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
    <script src="../../assets/js/app-room-list.js"></script>
</body>

</html>