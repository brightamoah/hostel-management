<?php
require_once "./app/controllers/RoomController.php";
?>

<!DOCTYPE html>
<html lang="en" class="layout-menu-fixed layout-navbar-fixed layout-navbar-sticky layout-compact" dir="ltr" data-skin="default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Kings Hostel - Admin Rooms Dashboard</title>

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
                        <!-- Room Statistics Cards -->
                        <div class="mb-4 row g-4">
                            <div class="col-xl-3 col-md-6">
                                <div class="card-border-shadow-primary card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="mb-1">Total Rooms</h6>
                                                <h4 class="mb-0" id="totalRooms">0</h4>
                                            </div>
                                            <div class="avatar">
                                                <div class="bg-label-primary rounded avatar-initial">
                                                    <i class="bx bx-building-house fs-4"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card-border-shadow-success card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="mb-1">Vacant Rooms</h6>
                                                <h4 class="mb-0" id="vacantRooms">0</h4>
                                            </div>
                                            <div class="avatar">
                                                <div class="bg-label-success rounded avatar-initial">
                                                    <i class="bx bx-door-open fs-4"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card-border-shadow-info card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="mb-1">Occupied Rooms</h6>
                                                <h4 class="mb-0" id="occupiedRooms">0</h4>
                                            </div>
                                            <div class="avatar">
                                                <div class="bg-label-info rounded avatar-initial">
                                                    <i class="bx bx-user-check fs-4"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card-border-shadow-warning card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="mb-1">Maintenance</h6>
                                                <h4 class="mb-0" id="maintenanceRooms">0</h4>
                                            </div>
                                            <div class="avatar">
                                                <div class="bg-label-warning rounded avatar-initial">
                                                    <i class="bx bx-wrench fs-4"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rooms DataTable -->
                        <div class="card">
                            <!-- <div class="d-flex align-items-center justify-content-between card-header">
                                <h5 class="mb-0 card-title">All Rooms</h5>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                                    <i class="me-1 bx bx-plus"></i>Add New Room
                                </button>
                            </div> -->
                            <div class="card-body">
                                <!-- Filters -->
                                <div class="mb-4 row">
                                    <div class="col-md-3">
                                        <input type="text" id="roomSearch" class="form-control" placeholder="Search rooms...">
                                    </div>
                                    <div class="col-md-3">
                                        <select id="buildingFilter" class="form-select">
                                            <option value="">All Buildings</option>
                                            <!-- Populated dynamically -->
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
                                            <!-- Populated dynamically -->
                                        </select>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table datatables-rooms">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Room #</th>
                                                <th>Building</th>
                                                <th>Floor</th>
                                                <th>Type</th>
                                                <th>Availability</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Add Room Modal -->
                        <div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addRoomModalLabel">Add New Room</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="addRoomForm">
                                            <?php set_csrf(); ?>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="room_number" class="form-label">Room Number</label>
                                                    <input type="text" class="form-control" id="room_number" name="room_number" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="building" class="form-label">Building</label>
                                                    <input type="text" class="form-control" id="building" name="building" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="floor" class="form-label">Floor</label>
                                                    <input type="number" class="form-control" id="floor" name="floor" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="room_type" class="form-label">Room Type</label>
                                                    <select class="form-select" id="room_type" name="room_type" required>
                                                        <option value="">Select Type</option>
                                                        <option value="Single">Single</option>
                                                        <option value="Double">Double</option>
                                                        <option value="Triple">Triple</option>
                                                        <option value="Quad">Quad</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="capacity" class="form-label">Capacity</label>
                                                    <input type="number" class="form-control" id="capacity" name="capacity" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="amount" class="form-label">Amount (GH₵)</label>
                                                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                                                </div>
                                                <div class="col-12">
                                                    <label for="features" class="form-label">Features (comma-separated)</label>
                                                    <textarea class="form-control" id="features" name="features" rows="4"></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="status" class="form-label">Status</label>
                                                    <select class="form-select" id="status" name="status" required>
                                                        <option value="Vacant">Vacant</option>
                                                        <option value="Under Maintenance">Under Maintenance</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" id="saveRoomBtn">Save Room</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Room Modal -->
                        <div class="modal fade" id="editRoomModal" tabindex="-1" aria-labelledby="editRoomModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editRoomModalLabel">Edit Room</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="editRoomForm">
                                            <input type="hidden" id="edit_room_id" name="room_id">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="edit_room_number" class="form-label">Room Number</label>
                                                    <input type="text" class="form-control" id="edit_room_number" name="room_number" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="edit_building" class="form-label">Building</label>
                                                    <input type="text" class="form-control" id="edit_building" name="building" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="edit_floor" class="form-label">Floor</label>
                                                    <input type="number" class="form-control" id="edit_floor" name="floor" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="edit_room_type" class="form-label">Room Type</label>
                                                    <select class="form-select" id="edit_room_type" name="room_type" required>
                                                        <option value="">Select Type</option>
                                                        <option value="Single">Single</option>
                                                        <option value="Double">Double</option>
                                                        <option value="Triple">Triple</option>
                                                        <option value="Quad">Quad</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="edit_capacity" class="form-label">Capacity</label>
                                                    <input type="number" class="form-control" id="edit_capacity" name="capacity" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="edit_amount" class="form-label">Amount (GH₵)</label>
                                                    <input type="number" step="0.01" class="form-control" id="edit_amount" name="amount" required>
                                                </div>
                                                <div class="col-12">
                                                    <label for="edit_features" class="form-label">Features (comma-separated)</label>
                                                    <textarea class="form-control" id="edit_features" name="features" rows="4"></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="edit_status" class="form-label">Status</label>
                                                    <select class="form-select" id="edit_status" name="status" required>
                                                        <option value="Vacant">Vacant</option>
                                                        <option value="Partially Occupied">Partially Occupied</option>
                                                        <option value="Fully Occupied">Fully Occupied</option>
                                                        <option value="Under Maintenance">Under Maintenance</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" id="updateRoomBtn">Update Room</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- View Room Details Modal -->
                        <div class="modal fade room-details-modal" id="roomDetailsModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="bg-primary modal-header">
                                        <h5 class="text-white modal-title" id="roomModalTitle">Room Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <!-- Room Overview Card -->
                                            <div class="mb-4 col-12">
                                                <div class="shadow-none border card">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <h5 class="mb-0" id="modalRoomNumberHeader"></h5>
                                                            <span class="bg-label-primary badge" id="modalRoomTypeHeader"></span>
                                                        </div>
                                                        <p class="mb-0"><i class="me-1 bx bx-map-pin"></i> <span id="modalBuildingHeader"></span>, Floor <span id="modalFloorHeader"></span></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Room Details -->
                                            <div class="col-md-6">
                                                <div class="shadow-none mb-4 border card">
                                                    <div class="bg-transparent card-header">
                                                        <h6 class="mb-0"><i class="me-2 bx bx-info-circle"></i>Room Information</h6>
                                                    </div>
                                                    <div class="pt-0 card-body">
                                                        <div class="row">
                                                            <div class="mb-3 col-6">
                                                                <small class="d-block text-muted">Room Number</small>
                                                                <span id="modalRoomNumber"></span>
                                                            </div>
                                                            <div class="mb-3 col-6">
                                                                <small class="d-block text-muted">Building</small>
                                                                <span id="modalBuilding"></span>
                                                            </div>
                                                            <div class="mb-3 col-6">
                                                                <small class="d-block text-muted">Floor</small>
                                                                <span id="modalFloor"></span>
                                                            </div>
                                                            <div class="mb-3 col-6">
                                                                <small class="d-block text-muted">Room Type</small>
                                                                <span id="modalRoomType"></span>
                                                            </div>
                                                            <div class="mb-3 col-6">
                                                                <small class="d-block text-muted">Amount: </small>
                                                                <strong id="modalAmount"></strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Occupancy Details -->
                                            <div class="col-md-6">
                                                <div class="shadow-none mb-4 border card">
                                                    <div class="bg-transparent card-header">
                                                        <h6 class="mb-0"><i class="me-2 bx bx-user"></i>Occupancy</h6>
                                                    </div>
                                                    <div class="pt-0 card-body">
                                                        <div class="row">
                                                            <div class="mb-3 col-12">
                                                                <small class="d-block text-muted">Status</small>
                                                                <span id="modalStatus"></span>
                                                            </div>
                                                            <div class="mb-3 col-6">
                                                                <small class="d-block text-muted">Total Capacity</small>
                                                                <span id="modalCapacity"></span>
                                                            </div>
                                                            <div class="mb-3 col-6">
                                                                <small class="d-block text-muted">Current Occupants</small>
                                                                <span id="modalOccupancy"></span>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2 progress" style="height: 8px">
                                                            <div id="occupancyProgressBar" class="bg-primary progress-bar" style="width: 0%" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small id="occupancyProgressText" class="text-muted">Available spaces</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Features -->
                                            <div class="col-12">
                                                <div class="shadow-none border card">
                                                    <div class="bg-transparent card-header">
                                                        <h6 class="mb-0"><i class="me-2 bx bx-star"></i>Features & Amenities</h6>
                                                    </div>
                                                    <div class="pt-0 card-body">
                                                        <div id="modalFeatures" class="d-flex flex-wrap gap-1"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
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
    <script src="../../assets/js/admin-room-list.js"></script>
</body>

</html>