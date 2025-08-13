<?php
require_once "./app/controllers/Login.php";

?>

<!doctype html>

<html
   lang="en"
   class="layout-menu-collapsed layout-menu-fixed layout-navbar-fixed layout-navbar-sticky layout-compact"
   dir="ltr"
   data-skin="default"
   data-assets-path="../../assets/"
   data-template="front-pages"
   data-bs-theme="system">

<head>
   <meta charset=" utf-8" />
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

   <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
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

      <div class="container-p-y authentication-wrapper authentication-basic">
         <div class="authentication-inner">

            <!-- Login -->
            <div class="px-0 px-sm-6 card">
               <div class="card-body">
                  <!-- Logo -->
                  <div class="justify-content-center app-brand">
                     <a href="/" class="gap-2 app-brand-link">
                        <span class="justify-center place-items-center text-center app-brand-logo demo">

                           <img src="../../assets/img/logo.svg" alt="logo" class="text-primary" width="80%" height="70%" />

                        </span>
                        <!-- <span class="text-heading app-brand-text demo fw-bold">Kings Hostel</span> -->
                     </a>
                  </div>
                  <!-- /Logo -->
                  <h4 class="mb-1 text-center">Welcome to Kings Hostel!</h4>
                  <p class="mb-6 text-center">Please sign-in to your account and start the adventure</p>

                  <!-- Display the message -->
                  <?php
                  if (isset($_SESSION['message'])) {
                     echo "<div id='flash-message' class='text-center text-sm fw-bold alert alert-{$_SESSION['message_type']}' role='alert'>{$_SESSION['message']}</div>";
                     unset($_SESSION['message'], $_SESSION['message_type']); // Clear the message after displaying
                  }
                  ?>
                  <script>
                     document.addEventListener('DOMContentLoaded', function() {
                        const flashMessage = document.getElementById('flash-message');
                        if (flashMessage) {
                           setTimeout(() => {
                              flashMessage.style.transition = 'opacity 0.5s';
                              flashMessage.style.opacity = '0';
                              setTimeout(() => flashMessage.remove(), 500); // Remove after fade-out
                           }, 6000); // 6 seconds
                        }
                     });
                  </script>

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
                           <div class="mb-0 form-check">
                              <input class="form-check-input" type="checkbox" id="remember_me" name="remember_me" />
                              <label class="form-check-label" for="remember_me"> Remember Me </label>
                           </div>
                           <a href="/forgot-password">
                              <span>Forgot Password?</span>
                           </a>
                        </div>
                     </div>
                     <div class="mb-6">
                        <input class="d-grid w-100 btn btn-primary" type="submit" value="Login" />
                     </div>
                  </form>

                  <p class="text-center">
                     <span>Don't have an account?</span>
                     <a href="signup">
                        <span>Sign Up</span>
                     </a>
                  </p>

                  <!-- <div class="my-6 divider">
                     <div class="divider-text">or</div>
                  </div>

                  <div class="d-flex justify-content-center">
                     <a href="javascript:;" class="me-1_5 rounded-circle btn-text-facebook btn btn-sm btn-icon">
                        <i class="icon-base bx bxl-facebook-circle icon-20px"></i>
                     </a>

                     <a href="javascript:;" class="me-1_5 rounded-circle btn-text-twitter btn btn-sm btn-icon">
                        <i class="icon-base bx bxl-twitter icon-20px"></i>
                     </a>

                     <a href="javascript:;" class="me-1_5 rounded-circle btn-text-github btn btn-sm btn-icon">
                        <i class="icon-base bx bxl-github icon-20px"></i>
                     </a>

                     <a href="javascript:;" class="rounded-circle btn-text-google-plus btn btn-sm btn-icon">
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