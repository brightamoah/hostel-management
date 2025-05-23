<div class="modal fade" id="paymentHistoryModal" tabindex="-1" aria-labelledby="paymentHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentHistoryModalLabel">Payment History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0">Student: <span class="text-primary" id="paymentHistoryStudent"></span></h6>
                        <p class="text-muted mb-0" id="paymentHistoryInvoiceDetails"></p>
                    </div>
                    <div class="text-end">
                        <h6 class="mb-0">Total Invoice: <span class="text-primary" id="paymentHistoryTotal"></span></h6>
                        <h6 class="mb-0">Balance Due: <span class="text-danger" id="paymentHistoryBalance"></span></h6>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="paymentHistoryTable">
                        <thead class="bg-light">
                            <tr>
                                <th>Transaction ID</th>
                                <th>Date</th>
                                <th>Payment Method</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Recorded By</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <button type="button" class="btn btn-primary btn-sm record-payment-btn" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                        <i class="ti ti-plus me-1"></i> Record New Payment
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-outline-primary export-payment-history"><i class="ti ti-download me-1"></i>Export</button>
            </div>
        </div>
    </div>
</div>