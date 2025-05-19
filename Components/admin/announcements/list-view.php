<div class="announcement-list-view d-none">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Posted By</th>
                    <th>Priority</th>
                    <th>Target Audience</th>
                    <th>Date Posted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($announcements as $announcement): ?>
                    <tr class="announcement-item"
                        data-priority="<?php echo htmlspecialchars($announcement['priority']); ?>"
                        data-audience="<?php echo htmlspecialchars($announcement['target_audience']); ?>"
                        data-date="<?php echo strtotime($announcement['date_posted']); ?>"
                        data-title="<?php echo htmlspecialchars($announcement['title']); ?>">
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="priority-indicator priority-<?php echo strtolower($announcement['priority']); ?> me-2"></span>
                                <a href="javascript:void(0);" class="fw-semibold view-announcement"
                                    data-id="<?php echo $announcement['announcement_id']; ?>"
                                    data-title="<?php echo htmlspecialchars($announcement['title']); ?>"
                                    data-content="<?php echo htmlspecialchars($announcement['content']); ?>"
                                    data-priority="<?php echo htmlspecialchars($announcement['priority']); ?>"
                                    data-target-audience="<?php echo htmlspecialchars($announcement['target_audience']); ?>"
                                    data-date="<?php echo date('M d, Y H:i', strtotime($announcement['date_posted'])); ?>">
                                    <?php echo htmlspecialchars($announcement['title']); ?>
                                </a>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($announcement['posted_by_name']); ?></td>
                        <td>
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
                                                                ?>">
                                <?php echo htmlspecialchars($announcement['priority']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($announcement['target_audience']); ?></td>
                        <td><?php echo date('M d, Y H:i', strtotime($announcement['date_posted'])); ?></td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item view-announcement"
                                        href="javascript:void(0);"
                                        data-id="<?php echo $announcement['announcement_id']; ?>"
                                        data-title="<?php echo htmlspecialchars($announcement['title']); ?>"
                                        data-content="<?php echo htmlspecialchars($announcement['content']); ?>"
                                        data-priority="<?php echo htmlspecialchars($announcement['priority']); ?>"
                                        data-target-audience="<?php echo htmlspecialchars($announcement['target_audience']); ?>"
                                        data-date="<?php echo date('M d, Y H:i', strtotime($announcement['date_posted'])); ?>">
                                        <i class="bx bx-show me-1"></i> View
                                    </a>
                                    <a class="dropdown-item edit-announcement"
                                        href="/admin/announcements/edit/<?php echo $announcement['announcement_id']; ?>"
                                        data-id="<?php echo $announcement['announcement_id']; ?>">
                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                    </a>
                                    <a class="dropdown-item delete-announcement"
                                        href="javascript:void(0);"
                                        data-id="<?php echo $announcement['announcement_id']; ?>">
                                        <i class="bx bx-trash me-1"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>