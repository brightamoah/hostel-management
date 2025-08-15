<div class="mb-4 row">
    <div class="col-12">
        <div class="bg-gradient-primary shadow-lg border-0 text-white card">
            <div class="d-flex align-items-center row g-0">
                <div class="col-sm-7">
                    <div class="p-5 card-body">
                        <div class="bg-white shadow-sm mb-4 p-2 text-primary badge">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2">
                                    <span class="bg-primary rounded-pill text-white badge">
                                        <i class='icon-base bx bx-crown'></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold"><?= htmlspecialchars($admin_role) ?></span>
                                </div>
                            </div>
                        </div>

                        <h3 class="mb-3 text-white card-title fw-bold">
                            Welcome back, <?= htmlspecialchars($first_name) ?>! <span class="fs-2">!</span>
                        </h3>

                        <p class="opacity-75 mb-4 text-white">
                            <i class='me-1 bx bx-time-five'></i> Last login: <?= htmlspecialchars($last_login) ?>
                        </p>

                        <p class="mb-4 text-white">
                            Manage Kings Hostel operations efficiently from your dashboard.
                        </p>

                        <div class="d-flex flex-wrap gap-3 mt-4">
                            <a href="/admin/profile" class="shadow-sm text-primary btn btn-light">
                                <i class="me-1 bx bx-user"></i> My Profile
                            </a>
                            <a href="/admin/analytics" class="btn-outline-light text-white btn">
                                <i class="me-1 bx bx-bar-chart"></i> View Analytics
                            </a>
                            <a href="/admin/rooms" class="btn-outline-light text-white btn">
                                <i class="me-1 bx bx-home"></i> Manage Rooms
                            </a>
                        </div>
                    </div>
                </div>

                <div class="text-center col-sm-5">
                    <div class="p-4 card-body">
                        <div class="position-relative">
                            <!-- <div class="bottom-0 position-absolute bg-white bg-opacity-25 p-4 rounded-circle end-0"></div> -->
                            <img
                                src="../../assets/img/new.png"
                                height="100"
                                style="height: 13rem; margin-bottom: 2.25rem;  transform: scale(1.5); opacity: 0.8; overflow: hidden;"
                                class="drop-shadow img-fluid"
                                alt="Admin Dashboard" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <style>
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
</style> -->