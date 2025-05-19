<div class="modal fade" id="viewAnnouncementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header  border-bottom-0 position-relative py-4">
                <div class="position-absolute start-0 top-0 w-100 bg-primary opacity-10" style="height:10px;"></div>
                <div class="d-flex flex-column w-100 pe-4">
                    <h4 class="modal-title mb-2 text-primary fw-bold" id="view_title"></h4>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-pill me-1" id="view_priority_badge"></span>
                            <span class="badge bg-label-primary rounded-pill" id="view_audience_badge"></span>
                        </div>
                        <div class="text-muted small">
                            <i class="bx bx-calendar-alt me-1"></i> <span id="view_date"></span>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close position-absolute end-3 top-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="bg-lighter p-4 rounded-3 mb-4 position-relative announcement-content-container">
                    <div class="announcement-content" id="view_content"></div>
                    <div class="announcement-shadow-overlay"></div>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3 bg-label-primary rounded-circle">
                            <span class="avatar-initial rounded-circle"><i class="bx bx-user"></i></span>
                        </div>
                        <div>
                            <span class="text-muted small">Posted by</span>
                            <h6 class="mb-0" id="view_posted_by">Admin</h6>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="text-muted small me-2">Read Status:</span>
                        <div class="badge bg-success d-flex align-items-center">
                            <i class="bx bx-check-circle me-1"></i> 235 reads
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0  gap-2 py-3">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i> Close
                </button>
                <a href="#" id="editFromView" class="btn btn-primary">
                    <i class="bx bx-edit-alt me-1"></i> Edit
                </a>
            </div>
        </div>
    </div>
</div>