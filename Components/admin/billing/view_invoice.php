<div class="modal fade" id="viewInvoiceModal" tabindex="-1" aria-labelledby="viewInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary border-0 justify-content-center align-items-center py-5">
                <h4 class="modal-title fw-bold text-white d-flex align-items-center gap-2" id="viewInvoiceModalLabel">
                    <i class="bx bx-file icon-xl"></i>
                    <span>Invoice Details</span>
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="invoice-preview-card mb-0">
                    <!-- Invoice Header -->
                    <div class="invoice-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                        <div class="logo-details mb-3 mb-md-0">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="me-3 bg-primary bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <img src="../../../assets/img/logo-no-text.svg" alt="Kings Hostel Logo" width="60" style="display: block;">
                                </div>
                                <div>
                                    <h4 class="mb-1 fw-bold text-primary">Kings Hostel</h4>
                                    <p class="mb-0 text-muted"><i class="icon-base bx bx-map-pin me-1"></i>University Campus, Accra, Ghana</p>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-details text-md-end">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge bg-primary">INVOICE ID:</span>
                                <h5 class="text-primary mb-0" id="modalInvoiceId"></h5>
                            </div>
                            <div class="d-flex flex-column">
                                <div class="mb-2">
                                    <span class="fw-medium text-muted"><i class="icon-base bx bx-calendar me-1"></i>Date Issued:</span>
                                    <span id="modalDateIssued" class="ms-1"></span>
                                </div>
                                <div>
                                    <span class="fw-medium text-muted"><i class="icon-base bx bx-calendar-exclamation me-1"></i>Due Date:</span>
                                    <span id="modalDueDate" class="ms-1"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-dashed">

                    <!-- Billing Information -->
                    <div class="row invoice-to mb-4">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <div class="card shadow-lg border-0 h-100">
                                <div class="card-body p-3">
                                    <h5 class="small mb-2">
                                        <i class="icon-base bx bx-user me-1"></i>Billed To: <span id="modalStudentName" class="ms-1 ml-2 mb-2 fw-bold text-primary text-uppercase"> </span>
                                    </h5>
                                    <p class="mb-2 text-muted"><i class="icon-base bx bx-id-card me-1"></i><span id="modalStudentId"></span></p>
                                    <p class="mb-2 text-muted"><i class="icon-base bx bx-phone me-1"></i><span id="modalStudentPhone"></span></p>
                                    <p class="mb-0 text-muted"><i class="icon-base bx bx-envelope me-1"></i><span id="modalStudentEmail"></span></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card border-0 shadow-lg h-100">
                                <div class="card-body p-3">
                                    <h5 class="small mb-2">
                                        <i class="icon-base bx bx-building me-1"></i>From: <span class="ms-1 mb-0 fw-bold text-uppercase text-primary ">Kings Hostel Management</span>
                                    </h5>

                                    <p class="mb-2 text-muted"><i class="icon-base bx bx-map-pin me-1"></i>University Campus, Accra</p>
                                    <p class="mb-2 text-muted"><i class="icon-base bx bx-envelope me-1"></i>kingshostelmgt@gmail.com</p>
                                    <p class="mb-0 text-muted"><i class="icon-base bx bx-phone me-1"></i>+233 30 277 8899</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Items -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0" id="modalInvoiceItems">
                                    <thead class="bg-primary bg-opacity-20">
                                        <tr>
                                            <th scope="col" class="ps-4 rounded-start-5 text-white fw-bold">#</th>
                                            <th scope="col" class=" text-white fw-bold">Description</th>
                                            <th scope="col" class="text-end pe-4 rounded-end-5 text-white fw-bold">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot class="border-top">
                                        <tr>
                                            <td colspan="2" class="text-end fw-semibold text-muted ps-4">Subtotal:</td>
                                            <td class="text-end pe-4" id="modalSubtotal"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="text-end fw-semibold text-muted ps-4">Tax (0%):</td>
                                            <td class="text-end pe-4">₵0.00</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td colspan="2" class="text-end fw-bold ps-4">Total:</td>
                                            <td class="text-end fw-bold pe-4" id="modalTotal"></td>
                                        </tr>
                                        <tr class=" mb-2">
                                            <td colspan="2" class="text-end fw-bold ps-4 rounded-start-5">Amount Paid:</td>
                                            <td class="text-end fw-bold text-success pe-4 rounded-end-5" id="modalAmountPaid"></td>
                                        </tr>
                                        <tr class="">
                                            <td colspan="2" class="text-end fw-bold ps-4 rounded-start-5">Balance Due:</td>
                                            <td class="text-end fw-bold text-warning pe-4 rounded-end-5" id="modalBalanceDue"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information and Transaction History -->
                    <div class="row">
                        <div class="col-md-7 mb-4 mb-md-0">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-primary bg-opacity-10 border-0">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="icon-base bx bx-credit-card me-1"></i>Payment Information
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-hover mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="ps-4"><i class="icon-base bx bxs-bank me-2 text-primary"></i>Bank Name:</td>
                                                <td class="fw-medium">Ghana Commercial Bank</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-4"><i class="icon-base bx bx-user me-2 text-primary"></i>Account Name:</td>
                                                <td class="fw-medium">Kings Hostel Management</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-4"><i class="icon-base bx bx-hash me-2 text-primary"></i>Account Number:</td>
                                                <td class="fw-medium">1234567890</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-4"><i class="icon-base bx bx-mobile-alt me-2 text-primary"></i>Mobile Money:</td>
                                                <td class="fw-medium">+233 54 968 4848</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-primary bg-opacity-10 border-0">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="icon-base bx bx-history me-1"></i>Transaction History
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0" id="modalTransactionHistory">
                                            <thead class="">
                                                <tr>
                                                    <th class="ps-4">Date</th>
                                                    <th>Method</th>
                                                    <th class="text-end pe-4">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="card mt-4 border-0 bg-light bg-opacity-10">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="icon-base bx bx-file text-primary me-2 fs-5"></i>
                                <h6 class="fw-bold mb-0">Terms & Conditions</h6>
                            </div>
                            <div class="ps-4">
                                <p class="small mb-1">1. Payment is due within 30 days of invoice date.</p>
                                <p class="small mb-1">2. Late payments will incur a 5% penalty fee.</p>
                                <p class="small mb-0">3. No refunds will be issued after the academic term begins.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer  border-0 p-4">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                    <i class="icon-base bx bx-x me-1"></i>Close
                </button>
                <button type="button" class="btn btn-outline-primary me-1 download-invoice">
                    <i class="icon-base bx bx-download me-1"></i>Download
                </button>
                <button type="button" class="btn btn-primary send-invoice">
                    <i class="icon-base bx bx-send me-1"></i>Email Invoice
                </button>
            </div>
        </div>
    </div>
</div>