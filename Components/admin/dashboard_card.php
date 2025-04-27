<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-lg border-0 bg-gradient-primary text-white">
            <div class="d-flex align-items-center row g-0">
                <div class="col-sm-7">
                    <div class="card-body p-5">
                        <div class="badge bg-white text-primary mb-4 p-2 shadow-sm">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2">
                                    <span class="badge rounded-pill bg-primary text-white">
                                        <i class='icon-base bx bx-crown'></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold"><?= htmlspecialchars($admin_role) ?></span>
                                </div>
                            </div>
                        </div>

                        <h3 class="card-title fw-bold text-white mb-3">
                            Welcome back, <?= htmlspecialchars($first_name) ?>! <span class="fs-2">!</span>
                        </h3>

                        <p class="mb-4 text-white opacity-75">
                            <i class='bx bx-time-five me-1'></i> Last login: <?= htmlspecialchars($last_login) ?>
                        </p>

                        <p class="mb-4 text-white">
                            Manage Kings Hostel operations efficiently from your dashboard.
                        </p>

                        <div class="d-flex flex-wrap gap-3 mt-4">
                            <a href="/admin/profile" class="btn btn-light text-primary shadow-sm">
                                <i class="bx bx-user me-1"></i> My Profile
                            </a>
                            <a href="/admin/analytics" class="btn btn-outline-light text-white">
                                <i class="bx bx-bar-chart me-1"></i> View Analytics
                            </a>
                            <a href="/admin/rooms" class="btn btn-outline-light text-white">
                                <i class="bx bx-home me-1"></i> Manage Rooms
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-5 text-center">
                    <div class="card-body p-4">
                        <div class="position-relative">
                            <div class="rounded-circle bg-white bg-opacity-25 p-4 position-absolute bottom-0 end-0 "></div>
                            <img
                                src="../../assets/img/illustrations/dash.png"
                                height="150"
                                class="img-fluid drop-shadow"
                                alt="Admin Dashboard" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom styles to complement Sneat theme */
    .bg-gradient-primary {
        background: linear-gradient(72.47deg, #7367f0 22.16%, #9e95f5 76.47%);
    }

    .drop-shadow {
        filter: drop-shadow(0px 10px 15px rgba(0, 0, 0, 0.1));
    }

    .transform-zoom {
        transform: scale(1.5);
        opacity: 0.2;
    }

    .btn {
        border-radius: 0.375rem;
        padding: 0.5rem 1.25rem;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 .25rem .5rem rgba(0, 0, 0, .1);
    }

    .btn-light:hover {
        background-color: #f8f8f8 !important;
    }

    .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.2) !important;
    }
</style>
