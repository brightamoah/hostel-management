<?php

require_once "./app/controllers/Login.php";

$user = $_SESSION['user'] ?? null;

// if ($user) {
//     echo "<pre>";
//     print_r($user);
//     echo "</pre>";
// } else {
//     echo "No user is logged in.";
// }

function getRoute()
{
    $role = $user['role'] ?? null;
    if ($role === 'Admin') {
        return 'admin/dashboard';
    } elseif ($role === 'Student') {
        return 'student/dashboard';
    } else {
        return 'login';
    }
}

?>

<!DOCTYPE html>
<html
    lang="en"
    class="layout-navbar-fixed layout-navbar-sticky layout-menu-fixed layout-menu-collapsed layout-compact"
    dir="ltr"
    data-skin="default"
    data-assets-path="../../assets/"
    data-template="front-pages"
    data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>Kings Hostel - Home Page</title>

    <meta name="description" content="Kings Hostel Management System - Seamlessly manage hostel bookings, tenants, and payments. Designed for students and admins to simplify hostel life and administration." />

    <base href="/">

    <!-- favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/img/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/img/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/img/favicon_io/favicon-16x16.png">

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="../../assets/vendor/fonts/iconify-icons.css" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link href='https://cdn.boxicons.com/fonts/animations.min.css' rel='stylesheet'>

    <link rel="stylesheet" href="../../assets/vendor/libs/pickr/pickr-themes.css" />

    <link rel="stylesheet" href="../../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="../../assets/vendor/css/pages/front-page.css" />

    <link rel="stylesheet" href="../../assets/vendor/libs/nouislider/nouislider.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/swiper/swiper.css" />

    <!-- Page CSS -->

    <link rel="stylesheet" href="../../assets/vendor/css/pages/front-page-landing.css" />

    <!-- Helpers -->
    <script src="../../assets/vendor/js/helpers.js"></script>
    <script src="../../assets/vendor/js/template-customizer.js"></script>
    <script src="../../assets/js/config.js"></script>

    <script src="../../assets/js/front-config.js"></script>
</head>


<body>
    <!-- <script src="../assets/vendor/js/dropdown-hover.js"></script>
    <script src="../assets/vendor/js/mega-dropdown.js"></script> -->

    <!-- Navbar -->
    <?php include_once __DIR__ . '/../Components/home/navbar.php'; ?>

    <!-- Sections:Start -->

    <div data-bs-spy="scroll" class="scrollspy-example" style="margin-top: -2rem;">
        <!-- Hero: Start -->
        <section id="hero-animation">
            <div id="landingHero" class="section-py landing-hero position-relative">
                <img
                    src="../assets/img/front-pages/backgrounds/hero-bg.png"
                    alt="hero background"
                    class="position-absolute top-20 start-50 translate-middle-x object-fit-cover w-100 h-100"
                    data-speed="1" />
                <div class="container">
                    <div class="hero-text-box text-center position-relative">
                        <h1 class="text-primary hero-title display-6 fw-extrabold">
                            Welcome to Kings Hostel Management System
                        </h1>
                        <h2 class="hero-sub-title h6 mb-6">
                            Seamless Hostel Management for Everyone.<br class="d-none d-lg-block" />
                            Easily book rooms, manage tenants, and track payments.Whether you're a resident or an admin, our system keeps everything organized and hassle-free!
                        </h2>
                        <div class="landing-hero-btn d-inline-block position-relative">
                            <span class="hero-btn-item position-absolute d-none d-md-flex fw-medium">Join community
                                <img
                                    src="../assets/img/front-pages/icons/Join-community-arrow.png"
                                    alt="Join community arrow"
                                    class="scaleX-n1-rtl" /></span>
                            <a href="login" class="btn btn-primary btn-lg">Start Your Journey</a>
                        </div>
                    </div>
                    <div id="heroDashboardAnimation" class="hero-animation-img">
                        <a href="/">
                            <div id="heroAnimationImg" class="position-relative hero-dashboard-img">
                                <img
                                    src="../assets/img/front-bg.png"
                                    alt="hero dashboard"
                                    class="animation-img" />
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="landing-hero-blank"></div>
        </section>
        <!-- Hero: End -->

        <!-- Useful features: Start -->
        <?php include_once __DIR__ . '/home/features.php'; ?>
        <!-- Useful features: End -->

        <!-- FAQ: Start -->
        <?php include_once __DIR__ . '/home/faq.php'; ?>

        <!-- FAQ: End -->

        <!-- contact us -->
        <?php include_once __DIR__ . '/home/contact-us.php'; ?>
        <!-- contact us: End -->

    </div>

    <!-- Footer: Start -->
    <footer class="landing-footer bg-body footer-text">

        <div class="footer-bottom py-3 py-md-5">
            <div
                class="container d-flex flex-wrap justify-content-between flex-md-row flex-column text-center text-md-start">
                <div class="mb-2 mb-md-0">
                    <span class="footer-bottom-text">©
                        <script>
                            document.write(new Date().getFullYear());
                        </script>
                    </span>
                    <a href="https://themeselection.com" class="text-white">Developed by Bright</a>
                    <!-- <span class="footer-bottom-text"> Made with ❤️ for a better web.</span> -->
                </div>
                <div>
                    <a href="https://github.com/brightamoah" target="_blank" class="me-4 text-white">
                        <i class="icon-base bx bxl-github icon-xl"></i>
                    </a>
                    <a href="https://www.facebook.com/ThemeSelections/" class="me-4 text-white">
                        <i class="icon-base bx bxl-facebook-circle icon-xl"></i>
                    </a>
                    <a href="https://x.com/Theme_Selection" class="me-4 text-white">
                        <i class="icon-base bx bxl-twitter icon-xl"></i>
                    </a>
                    <a href="https://www.instagram.com/themeselection/" class="text-white">
                        <i class="icon-base bx bxl-instagram-alt icon-xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer: End -->


    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../assets/vendor/libs/@algolia/autocomplete-js.js"></script>
    <script src="../../assets/vendor/libs/select2/select2.js"></script>
    <script src="../../assets/vendor/libs/pickr/pickr.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../../assets/vendor/libs/nouislider/nouislider.js"></script>
    <script src="../../assets/vendor/libs/swiper/swiper.js"></script>

    <!-- Main JS -->

    <script src="../../assets/js/front-main.js"></script>
    <script src="../../assets/js/main.js"></script>
    <!-- Page JS -->
    <script src="../../assets/js/front-page-landing.js"></script>

    <script>
        $(document).ready(function() {
            $('#contact-form-subject').select2();
        });
    </script>

</body>

</html>