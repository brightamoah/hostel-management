<nav class="layout-navbar shadow-none py-0">
    <div class="container">
        <div class="navbar navbar-expand-lg landing-navbar px-3 px-md-8">
            <!-- Menu logo wrapper -->
            <?php include_once __DIR__ . '/logo.php'; ?>



            <!-- Menu wrapper -->
            <div class="collapse navbar-collapse landing-nav-menu justify-content-center" id="navbarSupportedContent">
                <button
                    class="navbar-toggler border-0 text-heading position-absolute end-0 top-0 scaleX-n1-rtl p-2"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                    <i class="icon-base bx bx-x icon-lg"></i>
                </button>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link fw-medium" aria-current="page" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="/#Features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="/#FAQ">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="#Contact">Contact us</a>
                    </li>
                </ul>
            </div>

            <!-- Toolbar -->
            <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- Style Switcher -->
                <?php include_once __DIR__ . '/toggle-theme.php'; ?>


                <!-- Login/Register -->
                <?php include_once __DIR__ . '/auth-button.php'; ?>
            </ul>
        </div>
    </div>
</nav>