<div class="col-12 mb-4">
    <div class="h-100 card">
        <div class="d-flex align-items-center justify-content-between card-header">
            <div>
                <h5 class="mb-0 card-title">Monthly Growth</h5>
                <p class="mb-0 card-subtitle">Booking trends over time</p>
            </div>
            <div class="dropdown">
                <button class="btn-outline-secondary btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span>This Month</span>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="javascript:void(0);" data-order-period="thisweek">This Week</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0);" data-order-period="thismonth">This Month</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0);" data-order-period="last30days">Last 30 Days</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body chart-container">
            <div class="chart-loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div id="monthlyGrowthChart"></div>
        </div>
    </div>
</div>