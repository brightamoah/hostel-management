<?php
require_once "./database/db.php";
require_once "./app/models/User.php";
require_once "./utils/functions.php";
require_once "./vendor/autoload.php";


if (!isset($_SESSION['email_to_verify'])) {
    header('Location: /signup');
    exit;
}

$db = new Database();
$user = new User($db->connect());


if (isset($_GET['resend'])) {
    $email = $_SESSION['email_to_verify'];
    if ($user->emailExists($email)) {

        $query = "SELECT user_id FROM {$user->getConnection()->real_escape_string('users')} WHERE email = ?";
        $stmt = $user->getConnection()->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $user_id = $result['user_id'];
        $stmt->close();

        $verification_code = $user->generateVerificationCode($user_id);
        if ($verification_code && sendVerificationEmail($email, '', $verification_code)) {
            $_SESSION['message-verify'] = 'Verification code resent successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message-verify'] = 'Failed to resend verification code.';
            $_SESSION['message_type'] = 'danger';
        }
    }
    header('Location: /verify-email');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_csrf_valid()) {
        $_SESSION['message-verify'] = 'Invalid CSRF token.';
        $_SESSION['message_type'] = 'danger';
    } else {
        $code = sanitizeInput($_POST['verification_code']);
        $email = $_SESSION['email_to_verify'];

        if ($user->verifyEmail($email, $code)) {
            unset($_SESSION['email_to_verify']);
            header('Location: /email-verified');
            exit;
        } else {
            $_SESSION['message-verify'] = 'Invalid or expired verification code.';
            $_SESSION['message_type'] = 'danger';
        }
    }
}
?>

<!doctype html>
<html lang="en" class="layout-wide" dir="ltr" data-skin="default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Kings Hostel - Verify Email</title>

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="../../assets/img/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../assets/img/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/img/favicon_io/favicon-16x16.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/vendor/fonts/iconify-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />
    <link rel="stylesheet" href="../../assets/vendor/css/pages/page-auth.css" />

    <!-- Helpers -->
    <script src="../../assets/vendor/js/helpers.js"></script>
    <script src="../../assets/vendor/js/template-customizer.js"></script>
    <script src="../../assets/js/config.js"></script>
</head>

<body>
    <div class="authentication-wrapper authentication-cover">
        <div class="authentication-inner row m-0">
            <div class="d-flex col-12 align-items-center justify-content-center authentication-bg p-5">
                <div class="w-px-400">
                    <h3 class="mb-2 text-center fw-bold">Verify Your Email</h3>
                    <p class="text-center text-muted mb-4">A verification code has been sent to <?php echo htmlspecialchars($_SESSION['email_to_verify']); ?></p>

                    <?php if (isset($_SESSION['message-verify'])): ?>
                        <div class='text-center fw-bold alert alert-<?php echo $_SESSION['message_type']; ?> mt-3' role='alert'>
                            <?php echo $_SESSION['message-verify']; ?>
                        </div>
                        <?php unset($_SESSION['message-verify'], $_SESSION['message_type']); ?>
                    <?php endif; ?>

                    <form method="post" action="/verify-email">
                        <?php set_csrf(); ?>
                        <div class="mb-3">
                            <label for="verification_code" class="form-label">Verification Code</label>
                            <input type="text" class="form-control" id="verification_code" name="verification_code" placeholder="Enter 6-digit code" required maxlength="6" />
                        </div>
                        <button type="submit" class="btn btn-primary d-grid w-100">Verify Email</button>
                    </form>

                    <<p class="text-center mt-3">Didn't receive the code? <a href="/verify-email?resend=1" class="text-primary">Resend</a></p>
                        <p class="text-center"><a href="/signup" class="text-muted">Back to Sign Up</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>

</html>