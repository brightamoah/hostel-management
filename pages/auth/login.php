<?php
require_once "./app/controllers/Login.php";

?>

<!doctype html>

<html
   lang="en"
   class="layout-wide customizer-hide"
   dir="ltr"
   data-skin="default"
   data-assets-path="../../assets/"
   data-template="horizontal-menu-template"
   data-bs-theme="light">

<head>
   <meta charset="utf-8" />
   <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

   <title>Kings Hostel - Login</title>

   <meta name="description" content="" />


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
</head>

<body>
   <!-- Content -->
   <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
         <div class="authentication-inner">

            <!-- Login -->
            <div class="card px-sm-6 px-0">
               <div class="card-body">
                  <!-- Logo -->
                  <div class="app-brand justify-content-center">
                     <a href="/" class="app-brand-link gap-2">
                        <span class="app-brand-logo demo justify-center place-items-center text-center">

                           <img src="../../assets/img/logo.svg" alt="logo" class="text-primary" width="80%" height="70%" />

                        </span>
                        <!-- <span class="app-brand-text demo text-heading fw-bold">Kings Hostel</span> -->
                     </a>
                  </div>
                  <!-- /Logo -->
                  <h4 class="mb-1 text-center">Welcome to Kings Hostel!</h4>
                  <p class="mb-6 text-center">Please sign-in to your account and start the adventure</p>

                  <!-- Display the message -->
                  <?php
                  if (isset($_SESSION['message'])) {
                     echo "<div class='text-center text-sm fw-bold alert alert-{$_SESSION['message_type']}' role='alert'>{$_SESSION['message']}</div>";
                     unset($_SESSION['message'], $_SESSION['message_type']); // Clear the message after displaying
                  }
                  ?>

                  <form id="formAuthentication" class="mb-6" action="login" method="post">
                     <?= set_csrf();  ?>
                     <div class="mb-6 form-control-validation">
                        <label for="email" class="form-label">Email</label>
                        <input
                           type="text"
                           class="form-control"
                           id="email"
                           name="email"
                           autocomplete="email"
                           placeholder="Enter your email"
                           autofocus />
                     </div>
                     <div class="mb-6 form-password-toggle form-control-validation">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group input-group-merge">
                           <input
                              type="password"
                              id="password"
                              class="form-control"
                              name="password"
                              autocomplete="new-password"
                              placeholder="Enter your password"
                              aria-describedby="password" />
                           <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
                        </div>
                     </div>
                     <div class="mb-7">
                        <div class="d-flex justify-content-between">
                           <div class="form-check mb-0">
                              <input class="form-check-input" type="checkbox" id="remember_me" name="remember_me" />
                              <label class="form-check-label" for="remember_me"> Remember Me </label>
                           </div>
                           <a href="/forgot-password">
                              <span>Forgot Password?</span>
                           </a>
                        </div>
                     </div>
                     <div class="mb-6">
                        <input class="btn btn-primary d-grid w-100" type="submit" value="Login" />
                     </div>
                  </form>

                  <p class="text-center">
                     <span>Don't have an account?</span>
                     <a href="signup">
                        <span>Sign Up</span>
                     </a>
                  </p>

                  <!-- <div class="divider my-6">
                     <div class="divider-text">or</div>
                  </div>

                  <div class="d-flex justify-content-center">
                     <a href="javascript:;" class="btn btn-sm btn-icon rounded-circle btn-text-facebook me-1_5">
                        <i class="icon-base bx bxl-facebook-circle icon-20px"></i>
                     </a>

                     <a href="javascript:;" class="btn btn-sm btn-icon rounded-circle btn-text-twitter me-1_5">
                        <i class="icon-base bx bxl-twitter icon-20px"></i>
                     </a>

                     <a href="javascript:;" class="btn btn-sm btn-icon rounded-circle btn-text-github me-1_5">
                        <i class="icon-base bx bxl-github icon-20px"></i>
                     </a>

                     <a href="javascript:;" class="btn btn-sm btn-icon rounded-circle btn-text-google-plus">
                        <i class="icon-base bx bxl-google icon-20px"></i>
                     </a>
                  </div> -->
               </div>
            </div>
            <!-- /Login -->
         </div>
      </div>
   </div>

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
   <script src="../../assets/vendor/libs/@form-validation/popular.js"></script>
   <script src="../../assets/vendor/libs/@form-validation/bootstrap5.js"></script>
   <script src="../../assets/vendor/libs/@form-validation/auto-focus.js"></script>

   <!-- Main JS -->

   <script src="../../assets/js/main.js"></script>

   <!-- Page JS -->
   <script src="../../assets/js/pages-auth.js"></script>
</body>

</html>