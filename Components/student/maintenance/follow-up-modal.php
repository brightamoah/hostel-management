<div class="modal fade" id="followUpModal" tabindex="-1" aria-labelledby="followUpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="followUpForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="followUpModalLabel">Add Follow-up Response</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php set_csrf(); ?>
                    <input type="hidden" id="followUpRequestId" name="request_id" value="">
                    <div class="mb-3">
                        <label for="followUpText" class="form-label">Your Message</label>
                        <textarea id="followUpText" name="response_text" class="form-control" rows="4" required placeholder="Type your follow-up..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline-secondary btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Follow-up</button>
                </div>
            </form>
        </div>
    </div>
</div>