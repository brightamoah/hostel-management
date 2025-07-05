<div class="modal fade" id="editBillingModal" tabindex="-1" aria-labelledby="editBillingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBillingModalLabel">Edit Billing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editBillingForm" class="row g-3">
                    <?php set_csrf(); ?>
                    <input type="hidden" id="editBillingId" name="billing_id" readonly>
                    <input type="hidden" id="editStudentId" name="student_id" readonly>
                    <div class="col-md-6">
                        <label for="editStudentSelect" class="form-label">Student</label>
                        <input type="text" class="form-control" id="editStudentName" name="student_name" placeholder="Enter Student ID or Name" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="editInvoiceType" class="form-label">Billing Type</label>
                        <select id="editInvoiceType" name="billing_type" class="form-select" required>
                            <option value="Hostel Fee">Hostel Fee</option>
                            <option value="Security Deposit">Security Deposit</option>
                            <option value="Utility Fee">Utility Fee</option>
                            <option value="Maintenance Fee">Maintenance Fee</option>
                            <option value="Late Payment Penalty">Late Payment Penalty</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="editAcademicPeriod" class="form-label">Academic Period</label>
                        <select id="editAcademicPeriod" name="academic_period" class="form-select" required>
                            <option value="Semester 1">Semester 1</option>
                            <option value="Semester 2">Semester 2</option>
                            <option value="Entire Year">Entire Year</option>
                            <option value="Vacation Period">Vacation Period</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="editPaymentTerms" class="form-label">Payment Terms</label>
                        <select id="editPaymentTerms" name="payment_terms" class="form-select" required>
                            <option value="30">Net 30 Days</option>
                            <option value="15">Net 15 Days</option>
                            <option value="45">Net 45 Days</option>
                            <option value="immediate">Immediate Payment</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="editAmount" class="form-label">Amount (GH₵)</label>
                        <input type="number" step="0.01" class="form-control" id="editAmount" name="amount" required>
                    </div>
                    <div class="col-md-6">
                        <label for="editDueDateInput" class="form-label">Due Date</label>
                        <input type="datetime-local" class="form-control flatpickr-date" id="editDueDateInput" name="date_due" placeholder="Select date" required>
                    </div>
                    <div class="col-12">
                        <label for="editInvoiceDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editInvoiceDescription" name="description" rows="4" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editBillingForm" id="editBillingButton" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>