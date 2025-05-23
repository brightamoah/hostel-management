<?php
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
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card h-100 card-border-shadow-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0">Total Billings</h6>
                    <i class="bx bx-wallet text-primary icon-xl"></i>
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
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card h-100 card-border-shadow-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0">Total Paid</h6>
                    <i class="bx bx-check-circle text-success icon-xl"></i>
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
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card h-100 card-border-shadow-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0">Pending Invoices</h6>
                    <i class="bx bx-time-five text-warning icon-xl"></i>
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
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card h-100 card-border-shadow-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0">Overdue</h6>
                    <i class="bx bx-error text-danger icon-xl"></i>
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