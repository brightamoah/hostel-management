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

// Track resend attempts and cooldown periods
if (!isset($_SESSION['resend_count'])) {
    $_SESSION['resend_count'] = 0;
    $_SESSION['next_resend_time'] = 0;
}

$current_time = time();
$can_resend = $current_time >= $_SESSION['next_resend_time'];
$time_remaining = max(0, $_SESSION['next_resend_time'] - $current_time);

if (isset($_GET['resend']) && $can_resend) {
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

            // Increment resend count and set next allowed resend time
            $_SESSION['resend_count']++;

            // Set cooldown period based on number of resends
            switch ($_SESSION['resend_count']) {
                case 1:
                    $cooldown = 60; // 1 minute
                    break;
                case 2:
                    $cooldown = 120; // 2 minutes
                    break;
                default:
                    $cooldown = 300; // 5 minutes
                    break;
            }

            $_SESSION['next_resend_time'] = $current_time + $cooldown;
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
            unset($_SESSION['resend_count']);
            unset($_SESSION['next_resend_time']);
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
<html lang="en" class="layout-wide" dir="ltr" data-skin="default" data-assets-path="../../assets/" data-template="front-pages" data-bs-theme="light">

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

    <!-- Custom CSS for Spinner Alignment -->
    <style>
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            vertical-align: middle;
        }

        .resend-container {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
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

                    <form id="verifyEmailForm" method="post" action="/verify-email">
                        <?php set_csrf(); ?>
                        <div class="mb-3">
                            <label for="verification_code" class="form-label">Verification Code</label>
                            <input type="text" class="form-control" id="verification_code" name="verification_code" placeholder="Enter 6-digit code" required maxlength="6" />
                        </div>
                        <button type="submit" class="btn btn-primary d-grid w-100" id="verifyButton">
                            <span class="button-content">Verify Email</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <?php if ($can_resend): ?>
                            <p class="resend-container">
                                Didn't receive the code?
                                <a href="/verify-email?resend=1" class="text-primary" id="resendLink">
                                    <span class="resend-text">Resend</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </a>
                            </p>
                        <?php else: ?>
                            <p>
                                Resend available in
                                <span id="countdown" class="text-danger fw-bold" data-time-remaining="<?php echo $time_remaining; ?>">
                                    <?php echo gmdate("i:s", $time_remaining); ?>
                                </span>
                            </p>
                        <?php endif; ?>
                        <p><a href="/signup" class="text-muted">Back to Sign Up</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/js/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle form submission with spinner and 2-second delay
            const verifyForm = document.getElementById('verifyEmailForm');
            const verifyButton = document.getElementById('verifyButton');
            const buttonContent = verifyButton.querySelector('.button-content');
            const buttonSpinner = verifyButton.querySelector('.spinner-border');

            if (verifyForm) {
                verifyForm.addEventListener('submit', function(e) {
                    e.preventDefault(); // Prevent immediate form submission
                    // Show spinner and disable button
                    buttonContent.classList.add('d-none');
                    buttonSpinner.classList.remove('d-none');
                    verifyButton.disabled = true;

                    // Delay form submission by 2 seconds
                    setTimeout(function() {
                        verifyForm.submit(); // Programmatically submit the form
                    }, 2000);
                });
            }

            // Handle resend link click with spinner
            const resendLink = document.getElementById('resendLink');
            if (resendLink) {
                resendLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    const resendText = resendLink.querySelector('.resend-text');
                    const resendSpinner = resendLink.querySelector('.spinner-border');

                    // Show spinner and disable link
                    resendText.classList.add('d-none');
                    resendSpinner.classList.remove('d-none');
                    resendLink.style.pointerEvents = 'none';

                    // Navigate to resend URL
                    window.location.href = resendLink.href;
                });
            }

            // Countdown timer for resend cooldown
            const countdownEl = document.getElementById('countdown');
            if (countdownEl) {
                let timeRemaining = parseInt(countdownEl.dataset.timeRemaining, 10);

                const updateCountdown = function() {
                    if (timeRemaining <= 0) {
                        // Replace the countdown with the resend link when time is up
                        const resendContainer = countdownEl.parentElement;
                        resendContainer.innerHTML = `
                            <span class="resend-container">
                                Didn't receive the code?
                                <a href="/verify-email?resend=1" class="text-primary" id="resendLink">
                                    <span class="resend-text">Resend</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </a>
                            </span>
                        `;

                        // Re-attach event listener to the new resend link
                        const newResendLink = document.getElementById('resendLink');
                        if (newResendLink) {
                            newResendLink.addEventListener('click', function(e) {
                                e.preventDefault();
                                const resendText = newResendLink.querySelector('.resend-text');
                                const resendSpinner = newResendLink.querySelector('.spinner-border');

                                // Show spinner and disable link
                                resendText.classList.add('d-none');
                                resendSpinner.classList.remove('d-none');
                                newResendLink.style.pointerEvents = 'none';

                                // Navigate to resend URL
                                window.location.href = newResendLink.href;
                            });
                        }
                        return;
                    }

                    // Format the time as mm:ss
                    const minutes = Math.floor(timeRemaining / 60);
                    const seconds = timeRemaining % 60;
                    countdownEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                    timeRemaining--;
                    setTimeout(updateCountdown, 1000);
                };

                // Start the countdown
                updateCountdown();
            }
        });
    </script>
</body>

</html>