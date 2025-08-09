<div class="modal fade" id="viewInvoiceModal" tabindex="-1" aria-labelledby="viewInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="shadow-lg border-0 modal-content">
            <div class="align-items-center justify-content-center bg-primary py-5 border-0 modal-header">
                <h4 class="d-flex align-items-center gap-2 text-white modal-title fw-bold" id="viewInvoiceModalLabel">
                    <i class="bx bx-file icon-xl"></i>
                    <span>Invoice Details</span>
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="p-4 modal-body">
                <div class="mb-0 invoice-preview-card invoice-content">
                    <!-- Invoice Header -->
                    <div class="d-flex flex-column flex-md-row align-items-md-center align-items-start justify-content-between mb-4 invoice-header">
                        <div class="mb-3 mb-md-0 logo-details">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 me-3 p-3 rounded-circle" style="width: 80px; height: 80px;">
                                    <img src="../../../assets/img/logo-no-text.svg" alt="Kings Hostel Logo" width="60" style="display: block;">
                                </div>
                                <div>
                                    <h4 class="mb-1 text-primary fw-bold">Kings Hostel</h4>
                                    <p class="mb-0 text-muted"><i class="me-1 icon-base bx bx-map-pin"></i>University Campus, Accra, Ghana</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-md-end invoice-details">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="bg-primary badge">INVOICE ID:</span>
                                <h5 class="mb-0 text-primary" id="modalInvoiceId"></h5>
                            </div>
                            <div class="d-flex flex-column">
                                <div class="mb-2">
                                    <span class="text-muted fw-medium"><i class="me-1 icon-base bx bx-calendar"></i>Date Issued:</span>
                                    <span id="modalDateIssued" class="ms-1"></span>
                                </div>
                                <div>
                                    <span class="text-muted fw-medium"><i class="me-1 icon-base bx bx-calendar-exclamation"></i>Due Date:</span>
                                    <span id="modalDueDate" class="ms-1"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-dashed">

                    <!-- Billing Information -->
                    <div class="mb-4 row invoice-to">
                        <div class="mb-3 mb-sm-0 col-sm-6">
                            <div class="shadow-lg border-0 h-100 card">
                                <div class="p-3 card-body">
                                    <h5 class="mb-2 small">
                                        <i class="me-1 icon-base bx bx-user"></i>Billed To: <span id="modalStudentName" class="ms-1 mb-2 ml-2 text-primary text-uppercase fw-bold"> </span>
                                    </h5>
                                    <p class="mb-2 text-muted"><i class="me-1 icon-base bx bx-id-card"></i><span id="modalStudentId"></span></p>
                                    <p class="mb-2 text-muted"><i class="me-1 icon-base bx bx-phone"></i><span id="modalStudentPhone"></span></p>
                                    <p class="mb-0 text-muted"><i class="me-1 icon-base bx bx-envelope"></i><span id="modalStudentEmail"></span></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="shadow-lg border-0 h-100 card">
                                <div class="p-3 card-body">
                                    <h5 class="mb-2 small">
                                        <i class="me-1 icon-base bx bx-building"></i>From: <span class="ms-1 mb-0 text-primary text-uppercase fw-bold">Kings Hostel Management</span>
                                    </h5>

                                    <p class="mb-2 text-muted"><i class="me-1 icon-base bx bx-map-pin"></i>University Campus, Accra</p>
                                    <p class="mb-2 text-muted"><i class="me-1 icon-base bx bx-envelope"></i>kingshostelmgt@gmail.com</p>
                                    <p class="mb-0 text-muted"><i class="me-1 icon-base bx bx-phone"></i>+233 549 684 848</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Items -->
                    <div class="shadow-sm mb-4 border-0 card">
                        <div class="p-0 card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0" id="modalInvoiceItems">
                                    <thead class="bg-primary bg-opacity-20">
                                        <tr>
                                            <th scope="col" class="ps-4 rounded-start-5 text-white fw-bold">#</th>
                                            <th scope="col" class="text-white fw-bold">Description</th>
                                            <th scope="col" class="pe-4 rounded-end-5 text-white text-end fw-bold">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot class="border-top">
                                        <tr>
                                            <td colspan="2" class="ps-4 text-muted text-end fw-semibold">Subtotal:</td>
                                            <td class="pe-4 text-end" id="modalSubtotal"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="ps-4 text-muted text-end fw-semibold">Late Payment Fee (5% per month):</td>
                                            <td class="pe-4 text-end" id="modalLatePaymentFee">GH₵0.00</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td colspan="2" class="ps-4 text-end fw-bold">Total:</td>
                                            <td class="pe-4 text-end fw-bold" id="modalTotal"></td>
                                        </tr>
                                        <tr class="mb-2">
                                            <td colspan="2" class="ps-4 rounded-start-5 text-end fw-bold">Amount Paid:</td>
                                            <td class="pe-4 rounded-end-5 text-success text-end fw-bold" id="modalAmountPaid"></td>
                                        </tr>
                                        <tr class="">
                                            <td colspan="2" class="ps-4 rounded-start-5 text-end fw-bold">Balance Due:</td>
                                            <td class="pe-4 rounded-end-5 text-warning text-end fw-bold" id="modalBalanceDue"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information and Transaction History -->
                    <div class="row">
                        <div class="mb-4 mb-md-0 col-md-7">
                            <div class="shadow-sm border-0 h-100 card">
                                <div class="bg-primary bg-opacity-10 border-0 card-header">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="me-1 icon-base bx bx-credit-card"></i>Payment Information
                                    </h6>
                                </div>
                                <div class="p-0 card-body">
                                    <table class="table table-hover mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="ps-4"><i class="me-2 text-primary icon-base bx bxs-bank"></i>Bank Name:</td>
                                                <td class="fw-medium">Ghana Commercial Bank</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-4"><i class="me-2 text-primary icon-base bx bx-user"></i>Account Name:</td>
                                                <td class="fw-medium">Kings Hostel Management</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-4"><i class="me-2 text-primary icon-base bx bx-hash"></i>Account Number:</td>
                                                <td class="fw-medium">1234567890</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-4"><i class="me-2 text-primary icon-base bx bx-mobile-alt"></i>Mobile Money:</td>
                                                <td class="fw-medium">+233 54 968 4848</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="shadow-sm border-0 h-100 card">
                                <div class="bg-primary bg-opacity-10 border-0 card-header">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="me-1 icon-base bx bx-history"></i>Transaction History
                                    </h6>
                                </div>
                                <div class="p-0 card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0" id="modalTransactionHistory">
                                            <thead class="">
                                                <tr>
                                                    <th class="ps-4">Date</th>
                                                    <th>Method</th>
                                                    <th class="pe-4 text-end">Amount</th>
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
                    <div class="bg-light bg-opacity-10 mt-4 border-0 card">
                        <div class="p-3 card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="me-2 text-primary icon-base bx bx-file fs-5"></i>
                                <h6 class="mb-0 fw-bold">Terms & Conditions</h6>
                            </div>
                            <div class="ps-4">
                                <p class="mb-1 small">1. Payment is due within 30 days of invoice date.</p>
                                <p class="mb-1 small">2. Late payments will incur a 5% penalty fee.</p>
                                <p class="mb-0 small">3. No refunds will be issued after the academic term begins.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4 border-0 modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                    <i class="me-1 icon-base bx bx-x"></i>Close
                </button>
                <button type="button" class="me-1 btn-outline-primary btn download-invoice" id="downloadInvoiceBtn" data-billing-id="">
                    <i class="me-1 icon-base bx bx-download"></i>Download
                </button>
                <button type="button" class="btn btn-primary send-invoice" id="emailInvoiceBtn" data-student-email="" data-billing-id="">
                    <i class="me-1 icon-base bx bx-send"></i>Email Invoice
                </button>
            </div>
        </div>
    </div>
</div>