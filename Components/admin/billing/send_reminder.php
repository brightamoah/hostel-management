<div class="modal fade" id="sendReminderModal" tabindex="-1" aria-labelledby="sendReminderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendReminderModalLabel">Send Payment Reminder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="sendReminderForm" class="row g-3">
                    <?php set_csrf() ?>
                    <input type="hidden" id="reminderBillingId" name="billing_id">
                    <div class="col-12">
                        <div class="alert alert-info" role="alert">
                            <h6 class="alert-heading mb-1">Reminder Details</h6>
                            <p class="mb-0" id="reminderInvoiceDetails"></p>
                            <p class="mb-0" id="reminderAmountDue"></p>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="reminderSubject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="reminderSubject" name="subject" required>
                    </div>
                    <div class="col-12">
                        <label for="reminderMessage" class="form-label">Message</label>
                        <textarea class="form-control" id="reminderMessage" name="message" rows="10" required></textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="attachInvoice" name="attach_invoice" checked>
                            <label class="form-check-label" for="attachInvoice">
                                Attach invoice PDF
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="sendReminderForm" class="btn btn-primary" id="sendReminderButton">Send Reminder</button>
            </div>
        </div>
    </div>
</div>