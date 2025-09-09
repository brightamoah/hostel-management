<div class="mb-4 col-12">
    <div class="h-100 card">
        <div class="d-flex align-items-center justify-content-between card-header">
            <div>
                <h5 class="mb-0 card-title">Monthly Growth</h5>
                <p class="mb-0 card-subtitle">Booking trends comparison over periods</p>
            </div>
            <div class="dropdown">
                <button class="btn-outline-secondary btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span>Last 12 Months</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="javascript:void(0);" data-order-period="last3months">Last 3 Months</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0);" data-order-period="last6months">Last 6 Months</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0);" data-order-period="last12months">Last 12 Months</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div id="monthlyGrowthChart"></div>
        </div>
    </div>
</div>