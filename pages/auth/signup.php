<?php
session_start();

?>

<!doctype html>

<html
    lang="en"
    class="layout-wide customizer-hide"
    dir="ltr"
    data-skin="default"
    data-assets-path="../../assets/"
    data-template="vertical-menu-template"
    data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Kings Hostel - Sign Up</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../assets/img/favicon_io/favicon.ico">
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
    <link rel="stylesheet" href="../../assets/css/signup.css" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- endbuild -->

    <!-- Vendor -->
    <link rel="stylesheet" href="../../assets/vendor/libs/bs-stepper/bs-stepper.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/bootstrap-select/bootstrap-select.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/select2/select2.css" />
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
</head>

<body>
    <!-- Content -->

    <div class="authentication-wrapper authentication-cover">
        <!-- Logo -->
        <a href="/" class="app-brand auth-cover-brand gap-2">
            <span class="app-brand-logo demo">
                <img src="../../assets/img/logo.svg" alt="logo" class="text-primary" width="80%" height="70%" />
            </span>
            <!-- <span class="app-brand-text demo text-heading fw-bold">Kings Hostel</span> -->
        </a>
        <!-- /Logo -->


        <div class="authentication-inner row m-0">
            <!-- Left Text -->
            <div class="d-none d-lg-flex col-lg-4 align-items-center justify-content-end p-5 pe-0">
                <div class="w-px-400">
                    <img
                        src="../../assets/img/illustrations/create-account-light.png"
                        class="img-fluid"
                        alt="multi-steps"
                        width="600"
                        data-app-dark-img="illustrations/create-account-dark.png"
                        data-app-light-img="illustrations/create-account-light.png" />
                </div>
            </div>
            <!-- /Left Text -->

            <!--  Multi Steps Registration -->
            <div class="d-flex col-lg-8 align-items-center justify-content-center authentication-bg p-5">
                <div class="w-px-700">
                    <h3 class="mb-2 text-center fw-bold">Welcome to Kings Hostel!</h3>
                    <p class="text-center text-muted mb-4">Sign up to get started</p>

                    <div id="multiStepsValidation" class="bs-stepper border-none shadow-none mt-5">
                        <div class="bs-stepper-header border-none pt-12 px-0 justify-content-center place-items-center">
                            <div class="step" data-target="#accountDetailsValidation">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle"><i class="icon-base bx bx-home"></i></span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">Account</span>
                                        <span class="bs-stepper-subtitle">Account Details</span>
                                    </span>
                                </button>
                            </div>
                            <div class="line">
                                <i class="icon-base bx bx-chevron-right icon-22px"></i>
                            </div>
                            <div class="step" data-target="#personalInfoValidation">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle"><i class="icon-base bx bx-user"></i></span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">Personal</span>
                                        <span class="bs-stepper-subtitle">Enter Information</span>
                                    </span>
                                </button>
                            </div>

                        </div>

                        <?php
                        if (isset($_SESSION['message-signup'])) {
                            echo "<div class='text-center fw-bold alert alert-{$_SESSION['message_type']} mt-3' role='alert'>{$_SESSION['message-signup']}</div>";
                            unset($_SESSION['message-signup'], $_SESSION['message_type']);
                        }
                        ?>



                        <div class="bs-stepper-content px-0">
                            <form id="multiStepsForm" method="post" action="/signup" onSubmit="return false" novalidate>
                                <?php
                                ?>
                                <?php set_csrf(); ?>
                                <!-- Account Details -->
                                <div id="accountDetailsValidation" class="content">

                                    <div class="row g-6">
                                        <div class="col-sm-6 form-control-validation">
                                            <label class="form-label" for="name">Full Name</label>
                                            <input
                                                type="text"
                                                name="name"
                                                id="name"
                                                class="form-control"
                                                placeholder="Enter Your Full Name" />
                                        </div>
                                        <div class="col-sm-6 form-control-validation">
                                            <label class="form-label" for="email">Email</label>
                                            <input
                                                type="email"
                                                name="email"
                                                id="email"
                                                class="form-control"
                                                placeholder="Enter Your Email Address"
                                                aria-label="email" />
                                        </div>
                                        <div class="col-sm-6 form-password-toggle form-control-validation">
                                            <label class="form-label" for="password">Password</label>
                                            <div class="input-group input-group-merge">
                                                <input
                                                    type="password"
                                                    id="password"
                                                    name="password"
                                                    class="form-control"
                                                    placeholder="Enter Your Password"
                                                    aria-describedby="password" />
                                                <span class="input-group-text cursor-pointer" id="multiStepsPass2"><i class="icon-base bx bx-hide"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 form-password-toggle form-control-validation">
                                            <label class="form-label" for="confirm_password">Confirm Password</label>
                                            <div class="input-group input-group-merge">
                                                <input
                                                    type="password"
                                                    id="confirm_password"
                                                    name="confirm_password""
                                                    class=" form-control"
                                                    placeholder="Confirm Your Password"
                                                    aria-describedby="multiStepsConfirmPass2" />
                                                <span class="input-group-text cursor-pointer" id="multiStepsConfirmPass2"><i class="icon-base bx bx-hide"></i></span>
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex justify-content-between">
                                            <button class="btn btn-label-secondary btn-prev" disabled>
                                                <i class="icon-base bx bx-left-arrow-alt icon-sm ms-sm-n2 me-sm-2"></i>
                                                <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                            </button>
                                            <button class="btn btn-primary btn-next">
                                                <span class="align-middle d-sm-inline-block d-none me-sm-2 me-0">Next</span>
                                                <i class="icon-base bx bx-right-arrow-alt icon-sm me-sm-n2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Personal Info -->
                                <div id="personalInfoValidation" class="content">
                                    <div class="content-header mb-6">
                                        <h4 class="mb-0">Personal Information</h4>
                                        <p class="mb-0">Enter Your Personal Information</p>
                                    </div>
                                    <div class="row g-6">
                                        <div class="col-sm-6 form-control-validation">
                                            <label class="form-label" for="gender">Select Your Gender</label>
                                            <select class="form-select" id="gender" name="gender" data-allow-clear="true">
                                                <option value="" selected>Select Gender</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                                            <input type="date" id="date_of_birth" name="date_of_birth" class="form-control">
                                        </div>
                                        <div class="col-sm-6 form-control-validation">
                                            <label class="form-label" for="phone_number">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone_number" name="phone_number"
                                                placeholder="+233501234567" pattern="^\+\d{9,14}$" required />
                                        </div>

                                        <div class="col-sm-6 form-control-validation">
                                            <label class="form-label" for="emergency_contact_name">Emergency Contact's Full
                                                Name</label>
                                            <input type="text" class="form-control" id="emergency_contact_name"
                                                name="emergency_contact_name" placeholder="Jane Doe" required />
                                        </div>
                                        <div class="col-sm-6 form-control-validation">
                                            <label class="form-label" for="emergency_contact_number">Emergency Contact
                                                Number</label>
                                            <input type="tel" class="form-control" id="emergency_contact_number"
                                                name="emergency_contact_number" placeholder="+233501234567"
                                                pattern="^\+\d{9,14}$" required />
                                        </div>
                                        <div class="col-md-12 form-control-validation">
                                            <label class="form-label" for="address">Address</label>
                                            <textarea class="form-control" id="address" name="address"
                                                placeholder="123 Hostel Lane, City" required></textarea>
                                        </div>
                                        <div class="col-md-12 form-control-validation">
                                            <label class="form-label" for="health_condition">Health Conditions
                                                (Optional)</label>
                                            <textarea class="form-control" id="health_condition" name="health_condition"
                                                placeholder="E.g., Asthma, Allergies"></textarea>
                                        </div>
                                        <div class="col-md-12 form-control-validation">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="checkbox" id="terms" name="terms"
                                                    required />
                                                <label class="form-check-label" for="terms">
                                                    I agree to the <a href="javascript:void(0);">privacy policy &
                                                        terms</a>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex justify-content-between form-control-validation">
                                            <button class="btn btn-label-secondary btn-prev">
                                                <i class="icon-base bx bx-left-arrow-alt icon-sm ms-sm-n2 me-sm-2"></i>
                                                <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                            </button>
                                            <input type="submit" value="Sign Up" class="btn btn-primary btn-next btn-submit">
                                        </div>
                                    </div>
                                    <!--/ Credit Card Details -->
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Login Link -->
                    <div class="login-link fs-b">
                        <p>Already have an account? <a href="/login" class="text-primary fw-bold pl-5 fs-big"> Login</a></p>
                    </div>
                </div>
            </div>
            <!-- / Multi Steps Registration -->
        </div>
    </div>

    <script>
        // Check selected custom option
        window.Helpers.initCustomOptionCheck();
    </script>

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/theme.js  -->

    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>

    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/@algolia/autocomplete-js.js"></script>

    <script src="../../assets/vendor/libs/pickr/pickr.js"></script>

    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../../assets/vendor/libs/hammer/hammer.js"></script>

    <script src="../../assets/vendor/libs/i18n/i18n.js"></script>

    <script src="../../assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../../assets/vendor/libs/cleave-zen/cleave-zen.js"></script>
    <script src="../../assets/vendor/libs/bs-stepper/bs-stepper.js"></script>
    <script src="../../assets/vendor/libs/select2/select2.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/auto-focus.js"></script>

    <!-- Main JS -->

    <script src="../../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../../assets/js/pages-auth-multisteps.js"></script>
</body>

</html>