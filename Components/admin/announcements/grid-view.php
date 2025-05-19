<div class="announcement-grid-view">
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mb-5">
        <?php foreach ($announcements as $announcement): ?>
            <div class="col announcement-item"
                data-priority="<?php echo htmlspecialchars($announcement['priority']); ?>"
                data-audience="<?php echo htmlspecialchars($announcement['target_audience']); ?>"
                data-date="<?php echo strtotime($announcement['date_posted']); ?>"
                data-title="<?php echo htmlspecialchars($announcement['title']); ?>">
                <div class="card announcement-card h-100 shadow-sm">
                    <div class="card-header announcement-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 announcement-title"><?php echo htmlspecialchars($announcement['title']); ?></h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="icon-base bx bx-dots-vertical-rounded icon-md"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item view-announcement" href="javascript:void(0);"
                                        data-id="<?php echo $announcement['announcement_id']; ?>"
                                        data-title="<?php echo htmlspecialchars($announcement['title']); ?>"
                                        data-content="<?php echo htmlspecialchars($announcement['content']); ?>"
                                        data-priority="<?php echo htmlspecialchars($announcement['priority']); ?>"
                                        data-target-audience="<?php echo htmlspecialchars($announcement['target_audience']); ?>"
                                        data-date="<?php echo date('M d, Y H:i', strtotime($announcement['date_posted'])); ?>">
                                        <i class="bx bx-show me-2"></i>View Details
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item edit-announcement"
                                        href="/admin/announcements/edit/<?php echo $announcement['announcement_id']; ?>"
                                        data-id="<?php echo $announcement['announcement_id']; ?>">
                                        <i class="bx bx-edit-alt me-2"></i>Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item delete-announcement"
                                        href="javascript:void(0);"
                                        data-id="<?php echo $announcement['announcement_id']; ?>">
                                        <i class="bx bx-trash me-2"></i>Delete
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body announcement-content pb-0">
                        <div class="announcement-preview">
                            <?php echo substr(strip_tags($announcement['content']), 0, 150) . (strlen(strip_tags($announcement['content'])) > 150 ? '...' : ''); ?>
                        </div>
                    </div>
                    <div class="card-footer announcement-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-label-primary rounded-pill">
                                    <i class="bx bx-group me-1"></i><?php echo htmlspecialchars($announcement['target_audience']); ?>
                                </span>
                                <span class="badge 
                                                                    <?php
                                                                    switch ($announcement['priority']) {
                                                                        case 'Urgent':
                                                                            echo 'bg-label-danger';
                                                                            break;
                                                                        case 'High':
                                                                            echo 'bg-label-warning';
                                                                            break;
                                                                        case 'Medium':
                                                                            echo 'bg-label-info';
                                                                            break;
                                                                        default:
                                                                            echo 'bg-label-success';
                                                                    }
                                                                    ?> rounded-pill">
                                    <span class="priority-indicator priority-<?php echo strtolower($announcement['priority']); ?>"></span>
                                    <?php echo htmlspecialchars($announcement['priority']); ?>
                                </span>
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-calendar-alt me-1"></i>
                                <?php echo date('M d, Y', strtotime($announcement['date_posted'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>