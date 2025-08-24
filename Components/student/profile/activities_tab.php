<div class="tab-pane fade" id="profile-activities" role="tabpanel">
    <div class="row">
        <div class="col-12">
            <h5 class="d-flex align-items-center mt-4 mb-4">
                <i class="me-2 text-primary bx bx-history icon-lg"></i>
                <span>Recent Activities</span>
            </h5>

            <?php if (!empty($recent_activities)): ?>
                <div class="timeline">
                    <?php foreach ($recent_activities as $activity): ?>
                        <div class="d-flex align-items-start mb-4 timeline-item">
                            <div class="d-flex align-items-center me-3 timeline-point-wrapper">
                                <span class="timeline-point timeline-point-lg timeline-point-<?php
                                                                                                switch ($activity['activity_type']) {
                                                                                                    case 'payment':
                                                                                                        echo $activity['status'] === 'Completed' ? 'success' : ($activity['status'] === 'Failed' ? 'danger' : 'warning');
                                                                                                        break;
                                                                                                    case 'maintenance':
                                                                                                        echo $activity['status'] === 'Completed' ? 'success' : ($activity['status'] === 'In Progress' ? 'info' : 'warning');
                                                                                                        break;
                                                                                                    case 'visitor':
                                                                                                        echo $activity['status'] === 'Approved' ? 'success' : ($activity['status'] === 'Denied' ? 'danger' : 'warning');
                                                                                                        break;
                                                                                                    case 'complaint':
                                                                                                        echo $activity['status'] === 'Resolved' ? 'success' : ($activity['status'] === 'Rejected' ? 'danger' : 'warning');
                                                                                                        break;
                                                                                                    case 'allocation':
                                                                                                        echo $activity['status'] === 'Active' ? 'success' : 'info';
                                                                                                        break;
                                                                                                    default:
                                                                                                        echo 'primary';
                                                                                                }
                                                                                                ?> d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;">
                                    <i class="<?php
                                                switch ($activity['activity_type']) {
                                                    case 'payment':
                                                        echo 'bx bx-credit-card';
                                                        break;
                                                    case 'maintenance':
                                                        echo 'bx bx-wrench';
                                                        break;
                                                    case 'visitor':
                                                        echo 'bx bx-user-check';
                                                        break;
                                                    case 'complaint':
                                                        echo 'bx bx-message-alt-error';
                                                        break;
                                                    case 'allocation':
                                                        echo 'bx bx-home-circle';
                                                        break;
                                                    default:
                                                        echo 'bx bx-circle';
                                                }
                                                ?> icon-base text-white"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 timeline-event" style="padding-top: 4px;">
                                <div class="d-flex align-items-center justify-content-between me-2 mb-1">
                                    <h6 class="mb-0 fw-semibold fs-5">
                                        <?php
                                        switch ($activity['activity_type']) {
                                            case 'payment':
                                                echo 'Payment Transaction';
                                                break;
                                            case 'maintenance':
                                                echo 'Maintenance Request';
                                                break;
                                            case 'visitor':
                                                echo 'Visitor Registration';
                                                break;
                                            case 'complaint':
                                                echo 'Complaint Submitted';
                                                break;
                                            case 'allocation':
                                                echo 'Room Allocation';
                                                break;
                                            default:
                                                echo 'Activity';
                                        }
                                        ?>
                                    </h6>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-<?php
                                                                switch ($activity['activity_type']) {
                                                                    case 'payment':
                                                                        echo $activity['status'] === 'Completed' ? 'success' : ($activity['status'] === 'Failed' ? 'danger' : 'warning');
                                                                        break;
                                                                    case 'maintenance':
                                                                        echo $activity['status'] === 'Completed' ? 'success' : ($activity['status'] === 'In Progress' ? 'info' : 'warning');
                                                                        break;
                                                                    case 'visitor':
                                                                        echo $activity['status'] === 'Approved' ? 'success' : ($activity['status'] === 'Denied' ? 'danger' : 'warning');
                                                                        break;
                                                                    case 'complaint':
                                                                        echo $activity['status'] === 'Resolved' ? 'success' : ($activity['status'] === 'Rejected' ? 'danger' : 'warning');
                                                                        break;
                                                                    case 'allocation':
                                                                        echo $activity['status'] === 'Active' ? 'success' : 'info';
                                                                        break;
                                                                    default:
                                                                        echo 'secondary';
                                                                }
                                                                ?> me-2 fs-6"><?= htmlspecialchars($activity['status']) ?></span>
                                        <small class="text-muted fs-6">
                                            <?= date('M d, Y g:i A', strtotime($activity['activity_date'])) ?>
                                        </small>
                                    </div>
                                </div>
                                <p class="mb-1 text-muted fs-6">
                                    <?= htmlspecialchars($activity['description']) ?>
                                </p>

                                <?php if ($activity['activity_type'] === 'payment' && isset($activity['transaction_reference'])): ?>
                                    <small class="d-block text-muted">
                                        <i class="bx bx-receipt"></i>
                                        Ref: <?= htmlspecialchars($activity['transaction_reference']) ?>
                                    </small>
                                <?php endif; ?>

                                <?php if ($activity['activity_type'] === 'visitor' && isset($activity['visit_date'])): ?>
                                    <small class="d-block text-muted">
                                        <i class="bx bx-calendar"></i>
                                        Visit Date: <?= date('M d, Y', strtotime($activity['visit_date'])) ?>
                                    </small>
                                <?php endif; ?>

                                <?php if (in_array($activity['activity_type'], ['maintenance', 'complaint']) && isset($activity['priority'])): ?>
                                    <small class="badge bg-<?= $activity['priority'] === 'Emergency' ? 'danger' : ($activity['priority'] === 'High' ? 'warning' : 'info') ?> ms-2 fs-6">
                                        <?= htmlspecialchars($activity['priority']) ?> Priority
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="py-5 text-center">
                    <div class="mb-3">
                        <i class="text-muted bx bx-time-five" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="text-muted">No Recent Activities</h6>
                    <p class="mb-0 text-muted small">
                        Your recent activities will appear here once you start using the system.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>