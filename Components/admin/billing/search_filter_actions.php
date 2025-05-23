<div class="card mb-4">
    <div class="card-body">
        <div class="row justify-content-between align-items-center">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
                        <i class="bx bx-plus me-1"></i> Create Invoice
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="bulkActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-cog me-1"></i> Bulk Actions
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="bulkActionsDropdown">
                            <li><a class="dropdown-item bulk-action-send-reminders" href="javascript:void(0);"><i class="bx bx-envelope me-1"></i> Send Reminders</a></li>
                            <li><a class="dropdown-item bulk-action-export-csv" href="javascript:void(0);"><i class="bx bx-download me-1"></i> Export CSV</a></li>
                            <li><a class="dropdown-item bulk-action-archive" href="javascript:void(0);"><i class="bx bx-archive me-1"></i> Archive Selected</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger bulk-action-delete" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete Selected</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row g-2">
                    <div class="col-md-3 col-sm-6">
                        <select id="statusFilter" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="Unpaid">Unpaid</option>
                            <option value="Fully Paid">Fully Paid</option>
                            <option value="Partially Paid">Partially Paid</option>
                            <option value="Overdue">Overdue</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <input type="text" class="form-control" id="filterDueDate" placeholder="Due Date">
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <select id="filterBuilding" class="form-select">
                            <option value="">All Buildings</option>
                            <!-- Populated dynamically via JS -->
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="input-group">
                            <input type="text" class="form-control" id="billingSearch" placeholder="Search..." aria-label="Search" aria-describedby="button-search">
                            <button class="btn btn-outline-primary" type="button" id="button-search"><i class="bx bx-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>