<div class="mb-6 col-md-8 col-12">
    <div class="card">
        <div class="row-bordered row g-0">
            <div class="col-lg-8">
                <div class="d-flex align-items-center justify-content-between card-header">
                    <div class="mb-0 card-title">
                        <h5 class="m-0 me-2">Total Revenue Report</h5>
                        <p class="mb-0 card-subtitle">Comparison over periods</p>
                    </div>
                    <div class="dropdown">
                        <button
                            class="btn-outline-secondary btn btn-sm dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <span>Last 12 Months</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="javascript:void(0);" data-report-period="last3months">Last 3 Months</a>
                            <a class="dropdown-item" href="javascript:void(0);" data-report-period="last6months">Last 6 Months</a>
                            <a class="dropdown-item" href="javascript:void(0);" data-report-period="last12months">Last 12 Months</a>
                        </div>
                    </div>
                </div>
                <div>
                    <div id="totalRevenueChart" class="px-3"></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="d-flex flex-column align-items-center px-xl-9 py-12 card-body">
                    <div class="mb-6 text-center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-label-primary" id="currentYearBtn">
                                <script>
                                    document.write(new Date().getFullYear());
                                </script>
                            </button>
                            <button
                                type="button"
                                class="btn btn-label-primary dropdown-toggle dropdown-toggle-split"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu">
                                <?php
                                $currentYear = date('Y');
                                for ($i = 0; $i < 3; $i++) {
                                    $year = $currentYear - $i;
                                    echo "<li><a class=\"dropdown-item\" href=\"javascript:void(0);\" data-growth-year=\"$year\">$year</a></li>";
                                }
                                ?>
                            </ul>
                        </div>
                    </div>

                    <div>
                        <div id="growthChart"></div>
                    </div>
                    <div class="my-6 text-center fw-medium" id="growthText">Company Growth</div>

                    <div class="d-flex justify-content-between gap-11">
                        <div class="d-flex">
                            <div class="me-2 avatar">
                                <span class="bg-label-primary rounded-2 avatar-initial"><i class="text-primary icon-base bx bx-dollar icon-lg"></i></span>
                            </div>
                            <div class="d-flex flex-column">
                                <small id="currentYearLabel">
                                    <script>
                                        document.write(new Date().getFullYear());
                                    </script>
                                </small>
                                <h6 class="mb-0" id="currentYearRevenue">GH₵0</h6>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="me-2 avatar">
                                <span class="bg-label-info rounded-2 avatar-initial"><i class="text-info icon-base bx bx-wallet icon-lg"></i></span>
                            </div>
                            <div class="d-flex flex-column">
                                <small id="previousYearLabel">
                                    <script>
                                        document.write(new Date().getFullYear() - 1);
                                    </script>
                                </small>
                                <h6 class="mb-0" id="previousYearRevenue">GH₵0</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>