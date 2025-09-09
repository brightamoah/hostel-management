<?php
require_once __DIR__ . "/../../../utils/hostel_helpers.php";

// Assuming $stats is passed from the controller with the getBillings response
$stats = $stats ?? [
    'total_billings' => 0,
    'total_paid' => 0,
    'pending_invoices' => 0,
    'overdue_invoices' => 0,
    'total_change' => 0,
    'paid_change' => 0,
    'pending_change' => 0,
    'overdue_change' => 0
];

// Determine the scope for statistics display
$statsScope = isSuperAdmin() ? "All Hostels" : "Your Hostel";
$currentHostelDetails = getCurrentHostelDetails();
$scopeDetails = "";

if (!isSuperAdmin() && $currentHostelDetails) {
    $scopeDetails = " ({$currentHostelDetails['hostel_name']})";
}

// Helper function to determine class and icon based on percentage change
function getChangeClasses($change)
{
    if ($change > 0) {
        return [
            'text_class' => 'text-success',
            'icon_class' => 'bx-up-arrow-alt',
            'sign' => '+'
        ];
    } elseif ($change < 0) {
        return [
            'text_class' => 'text-danger',
            'icon_class' => 'bx-down-arrow-alt',
            'sign' => ''
        ];
    } else {
        return [
            'text_class' => 'text-muted',
            'icon_class' => 'bx-minus',
            'sign' => ''
        ];
    }
}

$totalChange = getChangeClasses($stats['total_change']);
$paidChange = getChangeClasses($stats['paid_change']);
$pendingChange = getChangeClasses($stats['pending_change']);
$overdueChange = getChangeClasses($stats['overdue_change']);
?>

<div class="row">
    <!-- Total Billings -->
    <div class="mb-4 col-md-6 col-lg-3">
        <div class="card-border-shadow-primary h-100 card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 card-title">Total Billings</h6>
                    <i class="text-primary bx bx-wallet icon-xl"></i>
                </div>
                <h4 class="mb-2"><?= formatCurrency($stats['total_billings']) ?></h4>
                <small class="d-flex align-items-center <?= $totalChange['text_class'] ?>">
                    <i class="icon-base  bx <?= $totalChange['icon_class'] ?> me-1"></i>
                    <?= $totalChange['sign'] ?><?= number_format(abs($stats['total_change']), 1) ?>%
                    <span class="ms-1">vs last month</span>
                </small>
            </div>
        </div>
    </div>

    <!-- Total Paid -->
    <div class="mb-4 col-md-6 col-lg-3">
        <div class="card-border-shadow-success h-100 card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 card-title">Total Paid</h6>
                    <i class="text-success bx bx-check-circle icon-xl"></i>
                </div>
                <h4 class="mb-2"><?= formatCurrency($stats['total_paid']) ?></h4>
                <small class="d-flex align-items-center <?= $paidChange['text_class'] ?>">
                    <i class="icon-base  bx <?= $paidChange['icon_class'] ?> me-1"></i>
                    <?= $paidChange['sign'] ?><?= number_format(abs($stats['paid_change']), 1) ?>%
                    <span class="ms-1">vs last month</span>
                </small>
            </div>
        </div>
    </div>

    <!-- Pending Invoices -->
    <div class="mb-4 col-md-6 col-lg-3">
        <div class="card-border-shadow-warning h-100 card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 card-title">Pending Invoices</h6>
                    <i class="text-warning bx bx-time-five icon-xl"></i>
                </div>
                <h4 class="mb-2"><?= formatCurrency($stats['pending_invoices']) ?></h4>
                <small class="d-flex align-items-center <?= $pendingChange['text_class'] ?>">
                    <i class="icon-base  bx <?= $pendingChange['icon_class'] ?> me-1"></i>
                    <?= $pendingChange['sign'] ?><?= number_format(abs($stats['pending_change']), 1) ?>%
                    <span class="ms-1">vs last month</span>
                </small>
            </div>
        </div>
    </div>

    <!-- Overdue -->
    <div class="mb-4 col-md-6 col-lg-3">
        <div class="card-border-shadow-danger h-100 card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 card-title">Overdue</h6>
                    <i class="text-danger bx bx-error icon-xl"></i>
                </div>
                <h4 class="mb-2"><?= formatCurrency($stats['overdue_invoices']) ?></h4>
                <small class="d-flex align-items-center <?= $overdueChange['text_class'] ?>">
                    <i class="icon-base  bx <?= $overdueChange['icon_class'] ?> me-1"></i>
                    <?= $overdueChange['sign'] ?><?= number_format(abs($stats['overdue_change']), 1) ?>%
                    <span class="ms-1">vs last month</span>
                </small>
            </div>
        </div>
    </div>
</div>