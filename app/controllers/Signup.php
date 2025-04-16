<?php

require_once "./database/db.php";
require_once "./app/models/User.php";
require_once "./utils/functions.php";

class Signup
{
    private $user;

    public function __construct()
    {
        $db = new Database();
        $this->user = new User($db->connect());
    }

    public function signup()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['message-signup'] = 'Invalid request method. Please use the signup form.';
            $_SESSION['message_type_signup'] = 'danger';
            header('Location: /signup');
            exit;
        }

        if (!is_csrf_valid()) {
            $_SESSION['message-signup'] = 'Invalid CSRF token. Please try submitting the form again.';
            $_SESSION['message_type_signup'] = 'danger';
            header('Location: /signup');
            die("Invalid CSRF token.");
        }

        $name = sanitizeInput($_POST['name']);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $gender = sanitizeInput($_POST['gender']);
        $date_of_birth = sanitizeInput($_POST['date_of_birth']);
        $phone_number = sanitizeInput($_POST['phone_number']);
        $address = sanitizeInput($_POST['address']);
        $emergency_contact_name = sanitizeInput($_POST['emergency_contact_name']);
        $emergency_contact_number = sanitizeInput($_POST['emergency_contact_number']);
        $health_condition = sanitizeInput($_POST['health_condition']);

        $required_fields = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'confirm_password' => $confirm_password,
            'gender' => $gender,
            'date_of_birth' => $date_of_birth,
            'phone_number' => $phone_number,
            'address' => $address,
            'emergency_contact_name' => $emergency_contact_name,
            'emergency_contact_number' => $emergency_contact_number
        ];

        foreach ($required_fields as $field => $value) {
            if (empty($value)) {
                $_SESSION['message-signup'] = "Missing required field: $field. Please fill in all required fields.";
                $_SESSION['message_type_signup'] = 'danger';
                header('Location: /signup');
                exit;
            }
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['message-signup'] = 'Invalid email format. Please enter a valid email address (e.g., example@domain.com).';
            $_SESSION['message_type_signup'] = 'danger';
            header('Location: /signup');
            exit;
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*_|])[A-Za-z\d!@#$%^&*_|]{8,}$/', $password)) {
            $_SESSION['message-signup'] = 'Password must be at least 8 characters long and include an uppercase letter, a number, and a special character (e.g., !@#$%^&*_|).';
            $_SESSION['message_typ_signup'] = 'danger';
            header('Location: /signup');
            exit;
        }

        if ($this->user->emailExists($email)) {
            $_SESSION['message-signup'] = 'This email is already registered. Please use a different email or log in.';
            $_SESSION['message_type_signup'] = 'danger';
            header('Location: /signup');
            exit;
        }

        if (!in_array($gender, ['Male', 'Female'])) {
            $_SESSION['message-signup'] = 'Invalid gender selection. Please choose either Male or Female.';
            $_SESSION['message_type_signup'] = 'danger';
            header('Location: /signup');
            exit;
        }

        if (!preg_match('/^\+\d{9,14}$/', $phone_number) || !preg_match('/^\+\d{9,14}$/', $emergency_contact_number)) {
            $_SESSION['message-signup'] = 'Phone numbers must be in international format (e.g., +233501234567) with 9 to 14 digits.';
            $_SESSION['message_type_signup'] = 'danger';
            header('Location: /signup');
            exit;
        }

        $user_id = $this->user->signup(
            $name,
            $email,
            $password,
            $confirm_password,
            $gender,
            $date_of_birth,
            $phone_number,
            $address,
            $emergency_contact_name,
            $emergency_contact_number,
            $health_condition
        );

        if ($user_id) {
            $verification_code = $this->user->generateVerificationCode($user_id);
            if ($verification_code) {
                // Attempt to send the verification email
                if (sendVerificationEmail($email, $name, $verification_code)) {
                    $_SESSION['email_to_verify'] = $email;
                    $_SESSION['message-signup'] = 'Registration successful! A verification code has been sent to your email. Please check your inbox (and spam/junk folder).';
                    $_SESSION['message_type_signup'] = 'success';
                    header('Location: /verify-email');
                    exit;
                } else {
                    // Detailed error message if email sending fails
                    $_SESSION['message-signup'] = 'Registration succeeded, but we couldn’t send the verification email. This might be due to a mail server issue. Please try again or contact support.';
                    $_SESSION['message_type_signup'] = 'danger';
                    error_log("Failed to send verification email to $email after successful signup for user ID: $user_id");
                    header('Location: /signup');
                    exit;
                }
            } else {
                // Detailed error message if verification code generation fails
                $_SESSION['message-signup'] = 'Registration succeeded, but we couldn’t generate a verification code. Please try again or contact support.';
                $_SESSION['message_type_signup'] = 'danger';
                error_log("Failed to generate verification code for user ID: $user_id");
                header('Location: /signup');
                exit;
            }
        }

        // Detailed error message if signup fails entirely
        $_SESSION['message-signup'] = 'Registration failed. This could be due to a database error or mismatched passwords. Please try again or contact support.';
        $_SESSION['message_type_signup'] = 'danger';
        error_log("Signup failed for email: $email");
        header('Location: /signup');
        exit;
    }
}

try {
    $signup = new Signup();
    $signup->signup();
} catch (Exception $e) {
    error_log("Signup error: " . $e->getMessage());
    $_SESSION['message-signup'] = 'An unexpected error occurred during registration: ' . htmlspecialchars($e->getMessage()) . '. Please try again or contact support.';
    $_SESSION['message_type_signup'] = 'danger';
    header('Location: /signup');
    exit;
}
