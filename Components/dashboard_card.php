<?php
// require_once "./Components/visitors.php";

$user = $_SESSION['user'] ?? null;

// Dummy data for demonstration (replace with actual database queries)
$roomNumber = $user['room_number'] ?? null;
$pendingBills = $user['pending_bills'] ?? null;
$maintenanceRequests = $user['maintenance_requests'] ?? null;
$visitorCount = $user['visitor_count'] ?? null;

// Placeholder logic
$roomNumberDisplay = $roomNumber ?: '--';
$pendingBillsDisplay = $pendingBills !== null ? "$" . number_format($pendingBills, 2) : '--';
$maintenanceRequestsDisplay = $maintenanceRequests !== null ? $maintenanceRequests : '--';
$visitorCountDisplay = $visitorCount !== null ? $visitorCount : '--';

// Dummy percentage changes (replace with real logic if needed)
$roomChange = $roomNumber ? '+0%' : '';
$billsChange = $pendingBills !== null ? ($pendingBills > 100 ? '-5.2%' : '+3.8%') : '';
$maintenanceChange = $maintenanceRequests !== null ? ($maintenanceRequests > 0 ? '+2.1%' : '-1.5%') : '';
$visitorChange = $visitorCount !== null ? ($visitorCount > 1 ? '+4.3%' : '-2.5%') : '';
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-6 mb-6">
        <!-- Room Number -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i class='bx bx-building-house icon-base icon-lg'></i></span>
                        </div>
                        <h4 class="mb-0"><?= $roomNumberDisplay ?></h4>
                    </div>
                    <p class="mb-2">Room Number</p>
                    <?php if ($roomChange): ?>
                        <p class="mb-0">
                            <span class="text-heading fw-medium me-2"><?= $roomChange ?></span>
                            <span class="text-body-secondary">status change</span>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Pending Bills -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning"><i class="icon-base bx bx-receipt icon-lg"></i></span>
                        </div>
                        <h4 class="mb-0"><?= $pendingBillsDisplay ?></h4>
                    </div>
                    <p class="mb-2">Pending Bills</p>
                    <?php if ($billsChange): ?>
                        <p class="mb-0">
                            <span class="text-heading fw-medium me-2"><?= $billsChange ?></span>
                            <span class="text-body-secondary">than last month</span>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Maintenance Requests -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-danger"><i class="icon-base bx bx-wrench icon-lg"></i></span>
                        </div>
                        <h4 class="mb-0"><?= $maintenanceRequestsDisplay ?></h4>
                    </div>
                    <p class="mb-2">Maintenance Requests</p>
                    <?php if ($maintenanceChange): ?>
                        <p class="mb-0">
                            <span class="text-heading fw-medium me-2"><?= $maintenanceChange ?></span>
                            <span class="text-body-secondary">than last week</span>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Visitor Count -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-info"><i class="icon-base bx bx-group icon-lg"></i></span>
                        </div>
                        <h4 class="mb-0"><?= $visitorCountDisplay ?></h4>
                    </div>
                    <p class="mb-2">Visitors This Week</p>
                    <?php if ($visitorChange): ?>
                        <p class="mb-0">
                            <span class="text-heading fw-medium me-2"><?= $visitorChange ?></span>
                            <span class="text-body-secondary">than last week</span>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>