<div class="tab-pane fade" id="admin-activities" role="tabpanel">
    <div class="row">
        <div class="col-12">
            <h5 class="d-flex align-items-center mt-4 mb-4">
                <i class="me-2 text-success bx bx-history icon-lg"></i>
                <span>Recent Admin Activities</span>
            </h5>

            <?php if (!empty($recent_activities)): ?>
                <div class="timeline">
                    <?php foreach ($recent_activities as $activity): ?>
                        <div class="d-flex align-items-start mb-4 timeline-item">
                            <div class="d-flex align-items-center me-3 timeline-point-wrapper">
                                <span class="timeline-point timeline-point-lg timeline-point-<?php
                                                                                                switch ($activity['activity_type']) {
                                                                                                    case 'user_management':
                                                                                                        echo $activity['status'] === 'Completed' ? 'success' : 'info';
                                                                                                        break;
                                                                                                    case 'room_management':
                                                                                                        echo $activity['status'] === 'Completed' ? 'success' : 'warning';
                                                                                                        break;
                                                                                                    case 'maintenance_response':
                                                                                                        echo $activity['status'] === 'Responded' ? 'success' : 'info';
                                                                                                        break;
                                                                                                    case 'visitor_approval':
                                                                                                        echo $activity['status'] === 'Approved' ? 'success' : ($activity['status'] === 'Denied' ? 'danger' : 'warning');
                                                                                                        break;
                                                                                                    case 'complaint_response':
                                                                                                        echo $activity['status'] === 'Resolved' ? 'success' : ($activity['status'] === 'Rejected' ? 'danger' : 'info');
                                                                                                        break;
                                                                                                    case 'billing_management':
                                                                                                        echo $activity['status'] === 'Processed' ? 'success' : 'info';
                                                                                                        break;
                                                                                                    case 'announcement':
                                                                                                        echo 'info';
                                                                                                        break;
                                                                                                    case 'login':
                                                                                                        echo 'primary';
                                                                                                        break;
                                                                                                    case 'system_access':
                                                                                                        echo 'success';
                                                                                                        break;
                                                                                                    default:
                                                                                                        echo 'secondary';
                                                                                                }
                                                                                                ?> d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;">
                                    <i class="<?php
                                                switch ($activity['activity_type']) {
                                                    case 'user_management':
                                                        echo 'bx bx-user-plus';
                                                        break;
                                                    case 'room_management':
                                                        echo 'bx bx-home-alt';
                                                        break;
                                                    case 'maintenance_response':
                                                        echo 'bx bx-wrench';
                                                        break;
                                                    case 'visitor_approval':
                                                        echo 'bx bx-user-check';
                                                        break;
                                                    case 'complaint_response':
                                                        echo 'bx bx-message-square-check';
                                                        break;
                                                    case 'billing_management':
                                                        echo 'bx bx-receipt';
                                                        break;
                                                    case 'announcement':
                                                        echo 'bx bxs-megaphone';
                                                        break;
                                                    case 'login':
                                                        echo 'bx bx-log-in';
                                                        break;
                                                    case 'system_access':
                                                        echo 'bx bx-shield-check';
                                                        break;
                                                    default:
                                                        echo 'bx bx-cog';
                                                }
                                                ?> icon-base text-white"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 timeline-event" style="padding-top: 4px;">
                                <div class="d-flex align-items-center justify-content-between me-2 mb-1">
                                    <h6 class="mb-0 fw-semibold fs-5">
                                        <?php
                                        switch ($activity['activity_type']) {
                                            case 'user_management':
                                                echo 'User Management';
                                                break;
                                            case 'room_management':
                                                echo 'Room Management';
                                                break;
                                            case 'maintenance_response':
                                                echo 'Maintenance Response';
                                                break;
                                            case 'visitor_approval':
                                                echo 'Visitor Approval';
                                                break;
                                            case 'complaint_response':
                                                echo 'Complaint Response';
                                                break;
                                            case 'billing_management':
                                                echo 'Billing Management';
                                                break;
                                            case 'announcement':
                                                echo 'Announcement Posted';
                                                break;
                                            case 'login':
                                                echo 'System Login';
                                                break;
                                            case 'system_access':
                                                echo 'System Access';
                                                break;
                                            default:
                                                echo 'Admin Activity';
                                        }
                                        ?>
                                    </h6>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-<?php
                                                                switch ($activity['activity_type']) {
                                                                    case 'user_management':
                                                                        echo $activity['status'] === 'Completed' ? 'success' : 'info';
                                                                        break;
                                                                    case 'room_management':
                                                                        echo $activity['status'] === 'Completed' ? 'success' : 'warning';
                                                                        break;
                                                                    case 'maintenance_response':
                                                                        echo $activity['status'] === 'Responded' ? 'success' : 'info';
                                                                        break;
                                                                    case 'visitor_approval':
                                                                        echo $activity['status'] === 'Approved' ? 'success' : ($activity['status'] === 'Denied' ? 'danger' : 'warning');
                                                                        break;
                                                                    case 'complaint_response':
                                                                        echo $activity['status'] === 'Resolved' ? 'success' : ($activity['status'] === 'Rejected' ? 'danger' : 'info');
                                                                        break;
                                                                    case 'billing_management':
                                                                        echo $activity['status'] === 'Processed' ? 'success' : 'info';
                                                                        break;
                                                                    case 'announcement':
                                                                        echo 'info';
                                                                        break;
                                                                    case 'login':
                                                                        echo 'primary';
                                                                        break;
                                                                    case 'system_access':
                                                                        echo 'success';
                                                                        break;
                                                                    default:
                                                                        echo 'secondary';
                                                                }
                                                                ?> me-2 fs-6"><?= htmlspecialchars($activity['status'] ?? 'Active') ?></span>
                                        <small class="text-muted fs-6">
                                            <?= date('M d, Y g:i A', strtotime($activity['activity_date'])) ?>
                                        </small>
                                    </div>
                                </div>
                                <p class="mb-1 text-muted fs-6">
                                    <?= htmlspecialchars($activity['description']) ?>
                                </p>

                                <?php if ($activity['activity_type'] === 'user_management' && isset($activity['target_user'])): ?>
                                    <small class="d-block text-muted">
                                        <i class="bx bx-user"></i>
                                        Target User: <?= htmlspecialchars($activity['target_user']) ?>
                                    </small>
                                <?php endif; ?>

                                <?php if ($activity['activity_type'] === 'announcement' && isset($activity['announcement_title'])): ?>
                                    <small class="d-block text-muted">
                                        <i class="bx bx-bookmark"></i>
                                        Title: <?= htmlspecialchars($activity['announcement_title']) ?>
                                    </small>
                                <?php endif; ?>

                                <?php if (in_array($activity['activity_type'], ['maintenance_response', 'complaint_response']) && isset($activity['priority'])): ?>
                                    <small class="badge bg-<?= $activity['priority'] === 'Emergency' ? 'danger' : ($activity['priority'] === 'High' ? 'warning' : 'info') ?> ms-2 fs-6">
                                        <?= htmlspecialchars($activity['priority']) ?> Priority
                                    </small>
                                <?php endif; ?>

                                <?php if (isset($activity['affected_hostel']) && !empty($activity['affected_hostel'])): ?>
                                    <small class="d-block mt-1 text-muted">
                                        <i class="bx bx-building"></i>
                                        Hostel: <?= htmlspecialchars($activity['affected_hostel']) ?>
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
                        Your administrative activities will appear here as you use the system.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>