<div class="modal fade" id="createInvoiceModal" tabindex="-1" aria-labelledby="createInvoiceModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createInvoiceModalLabel">Create New Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createInvoiceForm" class="row g-3" novalidate>
                    <?= set_csrf() ?>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="studentSelect" class="form-label">Student</label>
                            <select id="studentSelect" name="student_id" class="form-select" required>
                                <option></option>
                                <!-- Options will be populated dynamically -->
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="amountInput" class="form-label">Amount (GH₵)</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount"
                            placeholder="Enter amount" required>
                    </div>

                    <div class="col-md-6">
                        <label for="dueDateInput" class="form-label">Due Date</label>
                        <input type="text" class="form-control flatpickr-date" id="dueDateInput" name="due_date"
                            placeholder="Select due date">

                    </div>

                    <div class="col-md-6">
                        <label for="invoiceType" class="form-label">Invoice Type</label>
                        <select id="invoiceType" name="purpose" class="form-select" required>
                            <option value="Hostel Fee">Hostel Fee</option>
                            <option value="Security Deposit">Security Deposit</option>
                            <option value="Utility Fee">Utility Fee</option>
                            <option value="Maintenance Fee">Maintenance Fee</option>
                            <option value="Penalty">Late Payment Penalty</option>
                            <option value="Other">Other (specify)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="academicPeriod" class="form-label">Academic Period</label>
                        <select id="academicPeriod" name="academic_period" class="form-select">
                            <option value="">Select period</option>
                            <option value="first_semester">First Semester</option>
                            <option value="second_semester">Second Semester</option>
                            <option value="entire_year">Entire Year</option>
                            <option value="vacation">Vacation Period</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="paymentTerms" class="form-label">Payment Terms</label>
                        <select id="paymentTerms" name="payment_terms" class="form-select">
                            <option value="15">Net 15 Days</option>
                            <option value="30" selected>Net 30 Days</option>
                            <option value="45">Net 45 Days</option>
                            <option value="immediate">Immediate Payment</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="invoiceDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="invoiceDescription" name="description" rows="3"
                            placeholder="Enter invoice description or additional details" required></textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sendNotification"
                                name="send_notification" checked>

                            <label class="form-check-label" for="sendNotification">
                                Send email notification to student
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="createInvoiceForm" class="btn btn-primary">Create Invoice</button>
            </div>
        </div>
    </div>
</div>