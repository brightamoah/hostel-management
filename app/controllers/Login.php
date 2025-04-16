<?php
require_once "./database/db.php";
require_once "./app/models/User.php";

class Login
{
    private $user;

    public function __construct()
    {
        $db = new Database();
        $this->user = new User($db->connect());
    }

    public function login()
    {
        // Check for remember me cookie first
        if (isset($_COOKIE['remember_me'])) {
            $user = $this->user->validateRememberToken($_COOKIE['remember_me']);
            if ($user && is_array($user) && !isset($user['error'])) {
                $_SESSION['user'] = $user;
                $_SESSION['message'] = 'Welcome back! You were logged in automatically.';
                $_SESSION['message_type'] = 'success';
                $this->redirectUser($user['role']);
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $remember_me = isset($_POST['remember_me']) && $_POST['remember_me'] === 'on';

            if (empty($email) || empty($password)) {
                $_SESSION['message'] = 'All fields are required.';
                $_SESSION['message_type'] = 'danger';
                header('Location: /login');
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['message'] = 'Invalid email format.';
                $_SESSION['message_type'] = 'danger';
                header('Location: /login');
                exit;
            }

            if (strlen($password) < 8) {
                $_SESSION['message'] = 'Password must be at least 8 characters.';
                $_SESSION['message_type'] = 'danger';
                header('Location: /login');
                exit;
            }

            $user = $this->user->login($email, $password);

            if (is_array($user) && isset($user['error'])) {
                if ($user['error'] === 'Email not verified') {
                    $_SESSION['message'] = 'Please verify your email before logging in.';
                    $_SESSION['message_type'] = 'danger';
                    $_SESSION['email_to_verify'] = $email;
                    header('Location: /verify-email');
                    exit;
                } elseif ($user['error'] === 'Email does not exist') {
                    $_SESSION['message'] = 'No account found with this email. Please sign up.';
                    $_SESSION['message_type'] = 'danger';
                    header('Location: /login');
                    exit;
                }
            }

            if ($user && !isset($user['error'])) {
                if ($user['role'] === 'Student') {
                    $student_query = "SELECT * FROM students WHERE user_id = ?";
                    $stmt = $this->user->getConnection()->prepare($student_query);
                    if ($stmt) {
                        $stmt->bind_param("i", $user['user_id']);
                        $stmt->execute();
                        $student = $stmt->get_result()->fetch_assoc();
                        $stmt->close();

                        $_SESSION['user'] = [
                            'user_id' => $user['user_id'],
                            'student_id' => $student['student_id'],
                            'name' => $user['name'],
                            'email' => $user['email'],
                            'role' => $user['role'],
                            'gender' => $student['gender'],
                            'date_of_birth' => $student['date_of_birth'],
                            'phone_number' => $student['phone_number'],
                            'address' => $student['address'],
                            'first_name' => $student['first_name'],
                            'last_name' => $student['last_name'],
                            'emergency_contact_name' => $student['emergency_contact_name'],
                            'emergency_contact_number' => $student['emergency_contact_number'],
                            'health_condition' => $student['health_condition'],
                            'enrollment_date' => $student['enrollment_date'],
                            'is_email_verified' => $user['is_email_verified'],
                        ];
                    } else {
                        error_log("Failed to prepare student query: {$this->user->getConnection()->error}");
                    }
                } else {
                    $_SESSION['user'] = $user;
                }

                if ($remember_me) {
                    $token = $this->user->generateRememberToken($user['user_id']);
                    if ($token) {
                        setcookie('remember_me', $token, time() + 30 * 24 * 60 * 60, '/', '', false, true);
                    } else {
                        error_log("Failed to generate remember me token for user_id: {$user['user_id']}");
                    }
                }

                $_SESSION['message'] = 'User logged in successfully.';
                $_SESSION['message_type'] = 'success';
                $this->redirectUser($user['role']);
                exit;
            }

            $_SESSION['message'] = 'Login failed. Invalid email or password.';
            $_SESSION['message_type'] = 'danger';
            header('Location: /login');
            exit;
        }
    }

    private function redirectUser($role)
    {
        if (headers_sent()) {
            error_log("Headers already sent before redirecting user with role: $role");
        }
        if ($role === 'Admin') {
            header('Location: /admin/dashboard');
        } else {
            header('Location: /student/dashboard');
        }
        exit;
    }
}

try {
    $login = new Login();
    $login->login();
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    $_SESSION['message'] = 'An error occurred during login.';
    $_SESSION['message_type'] = 'danger';
    header('Location: /login');
    exit;
}
