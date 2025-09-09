<div class="col-md-4 col-12">
    <div class="h-100 card">
        <div class="d-flex align-items-center justify-content-between card-header">
            <div>
                <h5 class="mb-0 card-title">Expense Ratio</h5>
                <p class="my-0 card-subtitle">Spending on various categories</p>
            </div>
            <div class="d-sm-flex dropdown d-none">
                <button
                    type="button"
                    class="px-0 btn dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="icon-base bx bx-calendar"></i>
                    <span>All Time</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a href="javascript:void(0);" class="d-flex align-items-center dropdown-item" data-expense-period="today">Today</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="d-flex align-items-center dropdown-item" data-expense-period="yesterday">Yesterday</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="d-flex align-items-center dropdown-item" data-expense-period="last7days">Last 7 Days</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="d-flex align-items-center dropdown-item" data-expense-period="last30days">Last 30 Days</a>
                    </li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="d-flex align-items-center dropdown-item" data-expense-period="currentmonth">Current Month</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="d-flex align-items-center dropdown-item" data-expense-period="lastmonth">Last Month</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="d-flex align-items-center dropdown-item" data-expense-period="alltime">All Time</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div id="donutChart"></div>
        </div>
    </div>
</div>