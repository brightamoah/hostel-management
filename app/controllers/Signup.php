<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "Form submitted successfully.<br>";

            if (!is_csrf_valid()) {
                $_SESSION['message-signup'] = 'Invalid CSRF token.';
                $_SESSION['message_type'] = 'danger';
                header('Location: /signup');
                die("Invalid CSRF token.");
            }


            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
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
                    $_SESSION['message-signup'] = "All fields are required. Missing: $field";
                    $_SESSION['message_type'] = 'danger';
                    header('Location: /signup');
                    exit;
                }
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['message-signup'] = 'Invalid email format.';
                $_SESSION['message_type'] = 'danger';
                header('Location: /signup');
                exit;
            }

            if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*_|])[A-Za-z\d!@#$%^&*_|]{8,}$/', $password)) {
                $_SESSION['message-signup'] = 'Password must be at least 8 characters and include an uppercase letter, a number, and at least one special character (_!@#$%^&*).';
                $_SESSION['message_type'] = 'danger';
                header('Location: /signup');
                exit;
            }



            if ($this->user->emailExists($email)) {
                $_SESSION['message-signup'] = 'Email already exists.';
                $_SESSION['message_type'] = 'danger';
                header('Location: /signup');
                exit;
            }

            if (!in_array($gender, ['Male', 'Female'])) {
                $_SESSION['message-signup'] = 'Invalid gender selection.';
                $_SESSION['message_type'] = 'danger';
                header('Location: /signup');
                exit;
            }

            if (!preg_match('/^\+\d{9,14}$/', $phone_number) || !preg_match('/^\+\d{9,14}$/', $emergency_contact_number)) {
                $_SESSION['message-signup'] = 'Phone numbers must be in international format (e.g., +233501234567).';
                $_SESSION['message_type'] = 'danger';
                header('Location: /signup');
                exit;
            }

            if ($this->user->signup(
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
            )) {
                $_SESSION['message-signup'] = 'Registration successful. Please login.';
                $_SESSION['message_type'] = 'success';
                header('Location: /login');
                exit;
            }

            $_SESSION['message-signup'] = 'Registration failed. Please try again.';
            $_SESSION['message_type'] = 'danger';
            header('Location: /signup');
            exit;
        } else {
            $_SESSION['message-signup'] = 'Invalid request method.';
            $_SESSION['message_type'] = 'danger';
            header('Location: /signup');
            exit;
        }
    }
}

try {
    $signup = new Signup();
    $signup->signup();
    echo "Signup class instantiated successfully.";
    // header("Location: /");
} catch (Exception $e) {
    error_log("Signup error: " . $e->getMessage());
    $_SESSION['message-signup'] = 'An error occurred during registration.';
    $_SESSION['message_type'] = 'danger';
    header('Location: /signup');
    exit;
}
