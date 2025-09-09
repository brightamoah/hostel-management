<div class="mb-6 row g-6">
    <div class="col-sm-6 col-lg-3">
        <div class="card-border-shadow-primary h-100 card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="me-4 avatar">
                        <span class="bg-label-primary rounded avatar-initial"><i
                                class="icon-base bx bx-user icon-lg"></i></span>
                    </div>
                    <h4 class="mb-0"><?php echo $totalUsers; ?></h4>
                </div>
                <h6 class="mb-2">Total Users - <?php echo $statsScope; ?></h6>
                <p class="mb-0">
                    <?php
                    $userGrowth = $totalUsers > 0 ? round(($totalUsers / max($totalUsers, 50)) * 15, 1) : 0;
                    ?>
                    <span class="me-2 text-heading text-success fw-medium">
                        <i class="me-1 bx bx-up-arrow-alt"></i>+<?php echo $userGrowth; ?>%
                    </span>
                    <span class="text-body-secondary">growth this month</span>
                </p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card-border-shadow-success h-100 card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="me-4 avatar">
                        <span class="bg-label-success rounded avatar-initial"><i
                                class="icon-base bx bx-book-reader icon-lg"></i></span>
                    </div>
                    <h4 class="mb-0"><?php echo $totalStudents; ?></h4>
                </div>
                <p class="mb-2">Total Students - <?php echo $statsScope; ?></p>
                <p class="mb-0">
                    <?php
                    $studentPercentage = $totalUsers > 0 ? round(($totalStudents / $totalUsers) * 100, 1) : 0;
                    ?>
                    <span class="me-2 text-heading text-info fw-medium">
                        <i class="me-1 bx bx-stats"></i><?php echo $studentPercentage; ?>%
                    </span>
                    <span class="text-body-secondary">of total users</span>
                </p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card-border-shadow-danger h-100 card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="me-4 avatar">
                        <span class="bg-label-danger rounded avatar-initial"><i
                                class="icon-base bx bx-desktop icon-lg"></i></span>
                    </div>
                    <h4 class="mb-0"><?php echo $totalAdmins; ?></h4>
                </div>
                <p class="mb-2">Total Admins - <?php echo $statsScope; ?></p>
                <p class="mb-0">
                    <?php
                    $adminPercentage = $totalUsers > 0 ? round(($totalAdmins / $totalUsers) * 100, 1) : 0;
                    ?>
                    <span class="me-2 text-heading text-warning fw-medium">
                        <i class="me-1 bx bx-shield"></i><?php echo $adminPercentage; ?>%
                    </span>
                    <span class="text-body-secondary">of total users</span>
                </p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card-border-shadow-info h-100 card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="me-4 avatar">
                        <span class="bg-label-info rounded avatar-initial"><i
                                class="icon-base bx bx-home icon-lg"></i></span>
                    </div>
                    <h4 class="mb-0"><?php echo $activeStudents; ?></h4>
                </div>
                <p class="mb-2">Active Students - <?php echo $statsScope; ?></p>
                <p class="mb-0">
                    <?php
                    $activePercentage = $totalStudents > 0 ? round(($activeStudents / $totalStudents) * 100, 1) : 0;
                    $isGoodRate = $activePercentage >= 80;
                    ?>
                    <span class="me-2 text-heading fw-medium <?php echo $isGoodRate ? 'text-success' : 'text-warning'; ?>">
                        <i class="bx <?php echo $isGoodRate ? 'bx-up-arrow-alt' : 'bx-minus'; ?> me-1"></i><?php echo $activePercentage; ?>%
                    </span>
                    <span class="text-body-secondary">of total students</span>
                </p>
            </div>
        </div>
    </div>
</div>