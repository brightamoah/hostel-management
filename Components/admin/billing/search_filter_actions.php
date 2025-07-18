<div class="mb-4 card">
    <div class="card-body">
        <div class="align-items-center justify-content-between row">
            <div class="mb-3 mb-md-0 col-md-4">
                <div class="d-flex align-items-center">
                    <button type="button" class="me-2 btn btn-primary" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
                        <i class="me-1 bx bx-plus"></i> Create Invoice
                    </button>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row g-2">
                    <div class="col-md-4 col-sm-6">
                        <select id="statusFilter" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="Unpaid">Unpaid</option>
                            <option value="Fully Paid">Fully Paid</option>
                            <option value="Partially Paid">Partially Paid</option>
                            <option value="Overdue">Overdue</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <select id="filterBuilding" class="form-select">
                            <option value="">All Buildings</option>
                            <!-- Populated dynamically via JS -->
                        </select>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="input-group">
                            <input type="text" class="form-control" id="billingSearch" placeholder="Search..." aria-label="Search" aria-describedby="button-search">
                            <button class="btn-outline-primary btn" type="button" id="button-search"><i class="bx bx-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>