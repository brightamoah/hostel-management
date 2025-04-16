<?php
require_once "./database/db.php";
require_once "./app/models/User.php";

class ResetPassword
{
    private $user;

    public function __construct()
    {
        $db = new Database();
        $this->user = new User($db->connect());
    }

    // Handle both GET and POST with token as route parameter
    public function handleReset($tkn = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->renderForm($tkn);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processReset($tkn);
        }
    }

    private function processReset($tkn)
    {
        if (!is_csrf_valid()) {
            $_SESSION['message-reset'] = 'Invalid CSRF token.';
            $_SESSION['message_type'] = 'danger';
            header('Location: /reset-password/' . urlencode($tkn));
            exit;
        }

        $token = $tkn; // Use route parameter instead of POST data
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($token) || empty($password) || empty($confirm_password)) {
            $_SESSION['message-reset'] = 'All fields are required.';
            $_SESSION['message_type'] = 'danger';
            header("Location: /reset-password/" . urlencode($token));
            exit;
        }

        if ($password !== $confirm_password) {
            $_SESSION['message-reset'] = 'Passwords do not match.';
            $_SESSION['message_type'] = 'danger';
            header("Location: /reset-password/" . urlencode($token));
            exit;
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*_|])[A-Za-z\d!@#$%^&*_|]{8,}$/', $password)) {
            $_SESSION['message-reset'] = 'Password must be at least 8 characters with an uppercase letter, number, and special character.';
            $_SESSION['message_type'] = 'danger';
            header("Location: /reset-password/" . urlencode($token));
            exit;
        }

        if ($this->user->resetPassword($token, $password)) {
            $_SESSION['message-reset'] = 'Password reset successfully. Please login with your new password.';
            $_SESSION['message_type'] = 'success';
            header('Location: /login');
            exit;
        } else {
            $_SESSION['message-reset'] = 'Invalid or expired reset token.';
            $_SESSION['message_type'] = 'danger';
            header("Location: /reset-password/" . urlencode($token));
            exit;
        }
    }

    private function renderForm($tkn)
    {
        if (empty($tkn)) {
            $_SESSION['message-reset'] = 'Invalid reset link.';
            $_SESSION['message_type'] = 'danger';
            header('Location: /forgot-password');
            exit;
        }

        // Include the view file with the token
        $token = $tkn;
        include_once "./pages/auth/reset_password.php";
    }
}

try {
    $resetPassword = new ResetPassword();
    // The router will pass the $tkn parameter automatically
    $resetPassword->handleReset($tkn ?? null);
} catch (Exception $e) {
    error_log("Reset password error: " . $e->getMessage());
    $_SESSION['message-reset'] = 'An error occurred. Please try again later.';
    $_SESSION['message_type'] = 'danger';
    header('Location: /forgot-password');
    exit;
}
