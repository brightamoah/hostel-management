<div class="row g-6 mb-6">
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-primary h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-primary"><i
                                class="bx bx-receipt icon-lg"></i></span>
                    </div>
                    <h4 class="mb-0" id="total-billings"><?= formatCurrency($total_billings); ?></h4>
                </div>
                <p class="mb-2">Total Billings</p>
                <p class="mb-0">
                    <?php $isPositive = $total_billing_change >= 0; ?>
                    <span class="<?= $isPositive ? 'text-success' : 'text-danger' ?> fw-semibold" id="total-billings-change">
                        <i class="icon-base bx <?= $isPositive ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' ?>"></i>
                        <?= abs($total_billing_change) ?>%
                    </span>
                    <span class="text-muted ms-1">vs last month</span>
                </p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-success h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-success"><i
                                class="bx bx-check-circle icon-lg"></i></span>
                    </div>
                    <h4 class="mb-0" id="paid-invoices"><?= formatCurrency($total_paid); ?></h4>
                </div>
                <p class="mb-2">Total Paid</p>
                <p class="mb-0">
                    <?php $isPositive = $paid_change >= 0; ?>
                    <span class="<?= $isPositive ? 'text-success' : 'text-danger' ?> fw-semibold" id="paid-invoices-change">
                        <i class="icon-base bx <?= $isPositive ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' ?>"></i>
                        <?= abs($paid_change) ?>%
                    </span>
                    <span class="text-muted ms-1">vs last month</span>
                </p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-warning h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-warning"><i
                                class="bx bx-time icon-lg"></i></span>
                    </div>
                    <h4 class="mb-0" id="pending-invoices">
                        <?= formatCurrency($pending_billings) ?>
                    </h4>
                </div>
                <p class="mb-2">Pending Invoices</p>
                <p class="mb-0">
                    <?php
                    // For pending invoices, a decrease is actually positive (good)
                    $isPendingPositive = $pending_change <= 0;
                    ?>
                    <span class="<?= $isPendingPositive ? 'text-success' : 'text-danger' ?> fw-semibold" id="pending-invoices-change">
                        <i class="icon-base bx <?= $isPendingPositive ? 'bx-down-arrow-alt' : 'bx-up-arrow-alt' ?>"></i>
                        <?= abs($pending_change) ?>%
                    </span>
                    <span class="text-muted ms-1">vs last month</span>
                </p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-danger h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-danger"><i
                                class="bx bx-error icon-lg"></i></span>
                    </div>
                    <h4 class="mb-0" id="overdue-invoices">
                        <?= formatCurrency($overdue_billings) ?>
                    </h4>
                </div>
                <p class="mb-2">Overdue</p>
                <p class="mb-0">
                    <?php
                    // For overdue invoices, a decrease is positive (good)
                    $isOverduePositive = $overdue_change <= 0;
                    ?>
                    <span class="<?= $isOverduePositive ? 'text-success' : 'text-danger' ?> fw-semibold" id="overdue-invoices-change">
                        <i class="icon-base bx <?= $isOverduePositive ? 'bx-down-arrow-alt' : 'bx-up-arrow-alt' ?>"></i>
                        <?= abs($overdue_change) ?>%
                    </span>
                    <span class="text-muted ms-1">vs last month</span>
                </p>
            </div>
        </div>
    </div>
</div>