<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-labelledby="recordPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recordPaymentModalLabel">Record Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="recordPaymentForm" class="row g-3">
                    <input type="hidden" id="paymentBillingId" name="billing_id">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="fw-semibold">Invoice #:</span>
                                <span id="paymentInvoiceNumber"></span>
                            </div>
                            <div>
                                <span class="fw-semibold">Outstanding:</span>
                                <span id="outstandingAmount" class="text-danger"></span>
                            </div>
                        </div>
                        <hr class="my-3">
                    </div>
                    <div class="col-md-6">
                        <label for="paymentAmount" class="form-label">Payment Amount (₵)</label>
                        <input type="number" step="0.01" class="form-control" id="paymentAmount" name="amount" placeholder="Enter amount" required>
                    </div>
                    <div class="col-md-6">
                        <label for="paymentDate" class="form-label">Payment Date</label>
                        <input type="text" class="form-control flatpickr-date" id="paymentDate" name="payment_date" placeholder="Select date" required>
                    </div>
                    <div class="col-md-6">
                        <label for="paymentMethod" class="form-label">Payment Method</label>
                        <select id="paymentMethod" name="payment_method" class="form-select" required>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Mobile Money">Mobile Money</option>
                            <option value="Cash">Cash</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="transactionId" class="form-label">Transaction ID</label>
                        <input type="text" class="form-control" id="transactionId" name="transaction_reference" placeholder="Enter ID" required>
                    </div>
                    <div class="col-12">
                        <label for="paymentNotes" class="form-label">Payment Notes</label>
                        <textarea class="form-control" id="paymentNotes" name="payment_notes" rows="2" placeholder="Enter any notes about this payment"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="recordPaymentForm" class="btn btn-success">Record Payment</button>
            </div>
        </div>
    </div>
</div>