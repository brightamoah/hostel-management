<?php
function getRouteAndText()
{
    $user = $_SESSION['user'] ?? null;
    if ($user) {
        switch ($user['role']) {
            case 'Admin':
                $route = '/admin/dashboard';
                $text = 'Back to Dashboard';
                break;
            case 'Student':
                $route = '/student/dashboard';
                $text = 'Back to Dashboard';
                break;
            default:
                $route = '/';
                $text = 'Home';
        }
    } else {
        $route = '/';
        $text = 'Home';
    }
    return [$route, $text];
}
?>


<!DOCTYPE html>
<html lang="en"
    class="layout-menu-collapsed layout-menu-fixed layout-navbar-fixed layout-navbar-sticky layout-compact"
    dir="ltr"
    data-skin="bordered"
    data-assets-path="../../assets/"
    data-template="front-pages"
    data-bs-theme="system">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | Kings Hostel</title>
    <base href="/">
    <!-- favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="../../assets/img/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../assets/img/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/img/favicon_io/favicon-16x16.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="../../assets/vendor/fonts/iconify-icons.css" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="../../assets/vendor/libs/pickr/pickr-themes.css" />

    <link rel="stylesheet" href="../../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- endbuild -->

    <!-- Vendor -->
    <link rel="stylesheet" href="../../assets/vendor/libs/@form-validation/form-validation.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="../../assets/vendor/css/pages/page-auth.css" />

    <!-- Helpers -->
    <script src="../../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="../../assets/vendor/js/template-customizer.js"></script>

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="../../assets/js/config.js"></script>
    <!-- Custom Styles -->
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            /* background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); */
            padding: 2rem;
        }

        .error-container {
            max-width: 600px;
            text-align: center;
            /* background: #fff; */
            border-radius: 12px;
            padding: 3rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .error-title {
            font-size: 6rem;
            font-weight: 700;
            color: var(--bs-primary);
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .error-subtitle {
            font-size: 1.5rem;
            /* color: #444; */
            margin-bottom: 1.5rem;
        }

        .error-message {
            font-size: 1rem;
            /* color: #666; */
            margin-bottom: 2rem;
        }

        .search-bar {
            max-width: 400px;
            margin: 0 auto 2rem;
        }

        .btn-home {
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            border-radius: 50px;
            transition: transform 0.3s ease;
        }

        .btn-home:hover {
            transform: scale(1.05);
        }

        .error-image {
            max-width: 100%;
            height: auto;
            margin-bottom: 2rem;
        }

        @media (max-width: 576px) {
            .error-title {
                font-size: 4rem;
            }

            .error-subtitle {
                font-size: 1.2rem;
            }

            .error-container {
                padding: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="error-page">
        <div class="error-container">
            <!-- Hostel-themed Image -->
            <img src="../../assets/img/illustrations/404.png" alt="Hostel Room" class="error-image" width="300">
            <!-- Error Code -->
            <h1 class="error-title">404</h1>
            <!-- Error Subtitle -->
            <h2 class="error-subtitle">Oops! Room Not Found</h2>
            <!-- Error Message -->
            <p class="error-message">
                It looks like this page has checked out of HostelSync. The room or resource you're looking for might have been moved or doesn't exist.
            </p>
            <!-- Search Bar -->
            <!-- <div class="search-bar">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for a room or feature..." aria-label="Search">
                    <button class="btn btn-primary" type="button">
                        <i class="bx bx-search"></i>
                    </button>
                </div>
            </div> -->
            <!-- CTA Button -->
            <a href="<?= getRouteAndText()[0]; ?>" class="btn btn-primary btn-home">
                <i class="icon-base bx <?= getRouteAndText()[1] === 'Back to Dashboard' ? 'bx-grid-alt' : 'bx-home' ?> me-2 icon-lg"></i> <?= getRouteAndText()[1]; ?>
            </a>
        </div>
    </div>

    <!-- Sneat Core JS -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>
</body>

</html>