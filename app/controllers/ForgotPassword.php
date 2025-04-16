<?php
require_once "./database/db.php";
require_once "./app/models/User.php";
require_once "./utils/functions.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once "vendor/autoload.php";

class ForgotPassword
{
    private $user;

    public function __construct()
    {
        $db = new Database();
        $this->user = new User($db->connect());
    }

    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

            if (!is_csrf_valid()) {
                $_SESSION['message'] = 'Invalid CSRF token.';
                $_SESSION['message_type'] = 'danger';
                header('Location: /forgot-password');
                exit;
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['message'] = 'Please enter a valid email address.';
                $_SESSION['message_type'] = 'danger';
                header('Location: /forgot-password');
                exit;
            }

            if (!$this->user->emailExists($email)) {
                $_SESSION['message'] = 'No account found with this email.';
                $_SESSION['message_type'] = 'danger';
                header('Location: /forgot-password');
                exit;
            }

            $token = $this->user->generatePasswordResetToken($email);
            if ($token) {
                // Use route parameter format instead of query string
                $reset_link = "http://localhost/reset-password/" . urlencode($token);
                $name = "User"; // Could fetch actual name from DB
                if ($this->sendResetEmail($email, $name, $reset_link)) {
                    $_SESSION['message'] = 'A password reset link has been sent to your email.';
                    $_SESSION['message_type'] = 'success';
                    header('Location: /login');
                    exit;
                } else {
                    $_SESSION['message'] = 'Failed to send reset email. Please try again.';
                    $_SESSION['message_type'] = 'danger';
                }
            } else {
                $_SESSION['message'] = 'Failed to generate reset token. Please try again.';
                $_SESSION['message_type'] = 'danger';
            }
            header('Location: /forgot-password');
            exit;
        }
    }

    private function sendResetEmail($email, $name, $reset_link)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'kingshostelmgt@gmail.com';
            $mail->Password = 'fnuzctkvhqqdafjk';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->setFrom('kingshostelmgt@gmail.com', 'Kings Hostel Management');
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request - Kings Hostel';
            $mail->Body = '
                <div style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; padding: 20px; text-align: center;">
                    <h2 style="color: #986886;">Password Reset Request</h2>
                    <p style="text-align: center;">Hello ' . htmlspecialchars($name) . ',</p>
                    <p style="text-align: center;">We received a request to reset your password. Click the link below to reset it:</p>
                    <p style="text-align: center;"><strong><a href="' . htmlspecialchars($reset_link) . '">' . htmlspecialchars($reset_link) . '</a></strong></p>
                    <p style="text-align: center;">Or click the button below:</p>
                    <p style="text-align: center;"><a href="' . htmlspecialchars($reset_link) . '" style="background-color: #986886; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Reset Password</a></p>
                    <p style="text-align: center;">This link will expire in 1 hour.</p>
                    <p style="text-align: center;">If you didn’t request this, please ignore this email.</p>
                    <p style="text-align: center;">Best regards,<br>Kings Hostel Team</p>
                </div>';
            $mail->AltBody = "Hello {$name},\n\nWe received a request to reset your password. Visit this link to reset it: {$reset_link}\n\nThis link expires in 1 hour.\n\nKings Hostel Team";

            return $mail->send();
        } catch (Exception $e) {
            error_log("Failed to send password reset email to $email: {$mail->ErrorInfo}");
            return false;
        }
    }
}

try {
    $forgotPassword = new ForgotPassword();
    $forgotPassword->handleRequest();
} catch (Exception $e) {
    error_log("Forgot password error: " . $e->getMessage());
    $_SESSION['message-forgot'] = 'An error occurred. Please try again.';
    $_SESSION['message_type'] = 'danger';
    header('Location: /forgot-password');
    exit;
}
