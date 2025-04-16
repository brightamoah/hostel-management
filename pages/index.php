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
    class="layout-navbar-fixed layout-wide"
    dir="ltr"
    data-skin="default"
    data-assets-path="../assets/"
    data-template="front-pages"
    data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>Kings Hostel - Home Page</title>

    <meta name="description" content="" />

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

    <link rel="stylesheet" href="../../assets/vendor/libs/pickr/pickr-themes.css" />

    <link rel="stylesheet" href="../../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />

    <link rel="stylesheet" href="../../assets/vendor/css/pages/front-page.css" />

    <!-- Vendors CSS -->

    <!-- endbuild -->

    <link rel="stylesheet" href="../../assets/vendor/libs/nouislider/nouislider.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/swiper/swiper.css" />

    <!-- Page CSS -->

    <link rel="stylesheet" href="../../assets/vendor/css/pages/front-page-landing.css" />

    <!-- Helpers -->
    <script src="../../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="../../assets/vendor/js/template-customizer.js"></script>

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="../../assets/js/front-config.js"></script>
</head>


<body>
    <script src="../assets/vendor/js/dropdown-hover.js"></script>
    <script src="../assets/vendor/js/mega-dropdown.js"></script>

    <!-- Navbar -->
    <nav class="layout-navbar shadow-none py-0">
        <div class="container">
            <div class="navbar navbar-expand-lg landing-navbar px-3 px-md-8">
                <!-- Menu logo wrapper -->
                <div class="navbar-brand app-brand demo d-flex py-0 me-4 me-xl-8">
                    <button
                        class="navbar-toggler border-0 px-0 me-4"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent"
                        aria-expanded="false"
                        aria-label="Toggle navigation">
                        <i class="icon-base bx bx-menu icon-lg align-middle text-heading fw-medium"></i>
                    </button>
                    <a href="/" class="app-brand-link">
                        <img src="./assets/img/logo.svg" alt="logo" class="app-brand-logo demo" width="80%" height="70%" />
                        <!-- <span class="app-brand-text demo menu-text fw-bold text-primary ms-2 ps-1">Hostel</span> -->
                    </a>
                </div>

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
                    <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
                        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                            <i class="icon-base bx bx-sun icon-lg theme-icon-active"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <button type="button" class="dropdown-item active" data-bs-theme-value="light">
                                    <span><i class="icon-base bx bx-sun icon-md me-3"></i>Light</span>
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item" data-bs-theme-value="dark">
                                    <span><i class="icon-base bx bx-moon icon-md me-3"></i>Dark</span>
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item" data-bs-theme-value="system">
                                    <span><i class="icon-base bx bx-desktop icon-md me-3"></i>System</span>
                                </button>
                            </li>
                        </ul>
                    </li>
                    <!-- Login/Register -->
                    <?php if (isset($user) && !empty($_SESSION['user'])): ?>
                        <li>
                            <a href="<?= getRoute(); ?>" class="btn btn-success">
                                <span class="tf-icons icon-lg bx bx-user-circle scaleX-n1-rtl me-md-1"></span>
                                <span class="d-none d-md-block">
                                    <?php echo isset($user['role']) && $user['role'] === 'Student' ? 'Student Dashboard' : 'Admin Dashboard'; ?>
                                </span>
                            </a>
                        </li>
                      
                    <?php else: ?>
                        <li>
                            <a href="login" class="btn btn-primary">
                                <span class="tf-icons icon-base bx bx-log-in-circle scaleX-n1-rtl me-md-1"></span>
                                <span class="d-none d-md-block">Login/Register</span>
                            </a>
                        </li>
                    <?php endif; ?></a></a>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sections:Start -->

    <div data-bs-spy="scroll" class="scrollspy-example" style="margin-top: -2rem;">
        <!-- Hero: Start -->\
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
        <?php include_once 'home/features.php'; ?>
        <!-- Useful features: End -->

        <!-- FAQ: Start -->
        <?php include_once 'home/faq.php'; ?>

        <!-- FAQ: End -->

        <!-- contact us -->
        <?php include_once 'home/contact-us.php'; ?>
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
                    <a href="https://github.com/themeselection" class="me-4 text-white">
                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M10.7184 2.19556C6.12757 2.19556 2.40674 5.91639 2.40674 10.5072C2.40674 14.1789 4.78757 17.2947 8.0909 18.3947C8.50674 18.4697 8.65674 18.2139 8.65674 17.9939C8.65674 17.7964 8.65007 17.2731 8.64757 16.5806C6.33507 17.0822 5.84674 15.4656 5.84674 15.4656C5.47007 14.5056 4.92424 14.2497 4.92424 14.2497C4.17007 13.7339 4.98174 13.7456 4.98174 13.7456C5.81674 13.8039 6.25424 14.6022 6.25424 14.6022C6.9959 15.8722 8.2009 15.5056 8.67257 15.2931C8.7484 14.7556 8.96507 14.3889 9.20174 14.1814C7.35674 13.9722 5.41674 13.2589 5.41674 10.0731C5.41674 9.16722 5.74091 8.42389 6.27007 7.84389C6.1859 7.63306 5.89841 6.78722 6.35257 5.64389C6.35257 5.64389 7.05007 5.41972 8.63757 6.49472C9.31557 6.31028 10.0149 6.21614 10.7176 6.21472C11.4202 6.21586 12.1196 6.31001 12.7976 6.49472C14.3859 5.41889 15.0826 5.64389 15.0826 5.64389C15.5367 6.78722 15.2517 7.63306 15.1651 7.84389C15.6984 8.42389 16.0184 9.16639 16.0184 10.0731C16.0184 13.2672 14.0767 13.9689 12.2251 14.1747C12.5209 14.4314 12.7876 14.9381 12.7876 15.7131C12.7876 16.8247 12.7776 17.7214 12.7776 17.9939C12.7776 18.2164 12.9259 18.4747 13.3501 18.3931C16.6517 17.2914 19.0301 14.1781 19.0301 10.5072C19.0301 5.91639 15.3092 2.19556 10.7184 2.19556Z"
                                fill="currentColor" />
                        </svg>
                    </a>
                    <a href="https://www.facebook.com/ThemeSelections/" class="me-4 text-white">
                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M11.8609 18.0262V11.1962H14.1651L14.5076 8.52204H11.8609V6.81871C11.8609 6.04704 12.0759 5.51871 13.1834 5.51871H14.5868V3.13454C13.904 3.06136 13.2176 3.02603 12.5309 3.02871C10.4943 3.02871 9.09593 4.27204 9.09593 6.55454V8.51704H6.80676V11.1912H9.10093V18.0262H11.8609Z"
                                fill="currentColor" />
                        </svg>
                    </a>
                    <a href="https://x.com/Theme_Selection" class="me-4 text-white">
                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M17.0576 7.19293C17.0684 7.33876 17.0684 7.48376 17.0684 7.62876C17.0684 12.0663 13.6909 17.1796 7.5184 17.1796C5.61674 17.1796 3.85007 16.6288 2.3634 15.6721C2.6334 15.7029 2.8934 15.7138 3.17424 15.7138C4.68506 15.7174 6.15311 15.2122 7.34174 14.2796C6.64125 14.2669 5.96222 14.0358 5.39943 13.6185C4.83665 13.2013 4.41822 12.6187 4.20257 11.9521C4.41007 11.9829 4.6184 12.0038 4.83674 12.0038C5.13757 12.0038 5.44007 11.9621 5.7209 11.8896C4.9607 11.7361 4.27713 11.3241 3.78642 10.7235C3.29571 10.1229 3.02815 9.37097 3.02924 8.59543V8.55376C3.47674 8.80293 3.9959 8.95876 4.5459 8.9796C4.08514 8.67342 3.70734 8.25795 3.44619 7.77026C3.18504 7.28256 3.04866 6.73781 3.04924 6.1846C3.04924 5.56126 3.21507 4.9896 3.5059 4.49126C4.34935 5.52878 5.40132 6.37756 6.59368 6.98265C7.78604 7.58773 9.0922 7.93561 10.4276 8.00376C10.3759 7.75376 10.3442 7.4946 10.3442 7.2346C10.344 6.79373 10.4307 6.35715 10.5993 5.9498C10.7679 5.54245 11.0152 5.17233 11.3269 4.86059C11.6386 4.54885 12.0088 4.30161 12.4161 4.133C12.8235 3.96438 13.26 3.87771 13.7009 3.87793C14.6676 3.87793 15.5401 4.28293 16.1534 4.93793C16.9049 4.79261 17.6255 4.51828 18.2834 4.1271C18.0329 4.90278 17.5082 5.56052 16.8076 5.9771C17.4741 5.90108 18.1254 5.72581 18.7401 5.4571C18.281 6.12635 17.7122 6.71322 17.0576 7.19293Z"
                                fill="currentColor" />
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/themeselection/" class="text-white">
                        <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_1833_185630)">
                                <path
                                    d="M17.5869 6.33973C17.5774 5.62706 17.444 4.9215 17.1926 4.25456C16.9747 3.69202 16.6418 3.18112 16.2152 2.75453C15.7886 2.32793 15.2776 1.995 14.7151 1.77703C14.0568 1.5299 13.3613 1.39627 12.6582 1.38183C11.753 1.34137 11.466 1.33008 9.16819 1.33008C6.87039 1.33008 6.57586 1.33008 5.67725 1.38183C4.97451 1.39637 4.27932 1.53 3.62127 1.77703C3.05863 1.99485 2.54765 2.32772 2.12103 2.75434C1.69442 3.18096 1.36155 3.69193 1.14373 4.25456C0.896101 4.91242 0.76276 5.60776 0.749471 6.31056C0.70901 7.2167 0.696777 7.50368 0.696777 9.8015C0.696777 12.0993 0.696777 12.3928 0.749471 13.2924C0.763585 13.9963 0.89626 14.6907 1.14373 15.3503C1.36192 15.9128 1.69503 16.4236 2.1218 16.85C2.54855 17.2765 3.05957 17.6091 3.6222 17.8269C4.27846 18.084 4.97377 18.2272 5.67819 18.2504C6.58433 18.2908 6.87133 18.303 9.16913 18.303C11.4669 18.303 11.7615 18.303 12.6601 18.2504C13.3632 18.2365 14.0587 18.1032 14.717 17.8561C15.2794 17.6378 15.7902 17.3048 16.2167 16.8782C16.6433 16.4517 16.9763 15.941 17.1945 15.3785C17.442 14.7198 17.5746 14.0254 17.5888 13.3207C17.6293 12.4155 17.6414 12.1285 17.6414 9.82973C17.6396 7.53191 17.6396 7.24021 17.5869 6.33973ZM9.16255 14.1468C6.75935 14.1468 4.81251 12.2 4.81251 9.79679C4.81251 7.39359 6.75935 5.44676 9.16255 5.44676C10.3163 5.44676 11.4227 5.90506 12.2385 6.72085C13.0543 7.53664 13.5126 8.64309 13.5126 9.79679C13.5126 10.9505 13.0543 12.057 12.2385 12.8727C11.4227 13.6885 10.3163 14.1468 9.16255 14.1468ZM13.6857 6.3002C13.5525 6.30033 13.4206 6.27417 13.2974 6.22325C13.1743 6.17231 13.0624 6.09759 12.9682 6.00338C12.874 5.90917 12.7992 5.79729 12.7483 5.67417C12.6974 5.55105 12.6712 5.41909 12.6713 5.28585C12.6713 5.15271 12.6976 5.02087 12.7485 4.89786C12.7994 4.77485 12.8742 4.66308 12.9683 4.56893C13.0625 4.47479 13.1743 4.4001 13.2973 4.34915C13.4202 4.2982 13.5521 4.27197 13.6853 4.27197C13.8184 4.27197 13.9503 4.2982 14.0732 4.34915C14.1962 4.4001 14.3081 4.47479 14.4022 4.56893C14.4963 4.66308 14.571 4.77485 14.622 4.89786C14.6729 5.02087 14.6991 5.15271 14.6991 5.28585C14.6991 5.84666 14.2456 6.3002 13.6857 6.3002Z"
                                    fill="currentColor" />
                                <path
                                    d="M9.16296 12.6226C10.7236 12.6226 11.9887 11.3575 11.9887 9.79688C11.9887 8.23629 10.7236 6.97119 9.16296 6.97119C7.60238 6.97119 6.33728 8.23629 6.33728 9.79688C6.33728 11.3575 7.60238 12.6226 9.16296 12.6226Z"
                                    fill="currentColor" />
                            </g>
                            <defs>
                                <clipPath id="clip0_1833_185630">
                                    <rect width="16.9412" height="18" fill="currentColor" transform="translate(0.696777 0.528809)" />
                                </clipPath>
                            </defs>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer: End -->


    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/@algolia/autocomplete-js.js"></script>

    <script src="../../assets/vendor/libs/pickr/pickr.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../../assets/vendor/libs/nouislider/nouislider.js"></script>
    <script src="../../assets/vendor/libs/swiper/swiper.js"></script>

    <!-- Main JS -->

    <script src="../../assets/js/front-main.js"></script>

    <!-- Page JS -->
    <script src="../../assets/js/front-page-landing.js"></script>

</body>

</html>