<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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


        if (isset($_SESSION['user']) && isset(($_COOKIE['remember_me']))) {
            $user = $this->user->validateRememberToken($_COOKIE['remember_me']);

            if ($user && is_array($user)) {
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
                header('Location: /login'); // Redirect back to the login page

                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['message'] = 'Invalid email format.';
                $_SESSION['message_type'] = 'danger';
                header('Location: /login');
                // echo "$_SESSION[message]";
                exit;
            }

            if (strlen($password) < 8) {
                $_SESSION['message'] = 'Password must be at least 8 characters.';
                $_SESSION['message_type'] = 'danger';
                header('Location: /login');
                // echo "$_SESSION[message]";
                exit;
            }

            $user = $this->user->login($email, $password);

            if ($user) {
                if ($user['role'] === 'Student') {
                    $student_query = "SELECT * FROM students WHERE user_id = ?";
                    $stmt = $this->user->getConnection()->prepare($student_query);
                    if ($stmt) {
                        $stmt->bind_param("i", $user['user_id']);
                        $stmt->execute();
                        $student = $stmt->get_result()->fetch_assoc();
                        $stmt->close();


                        session_start();
                        // Merge user and student data into session
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
                            'enrollment_date' => $student['enrollment_date']
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
                        setcookie('remember_me', $token, time() + 30 * 24 * 60 * 60, '/', '', false, true); // 30 days, HTTP-only
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
            // echo "$_SESSION[message]";
            exit;
        }
    }


    private function redirectUser($role)
    {
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
    echo "User Logged In successfully";
} catch (Exception $e) {
    echo "An error occurred: {$e->getMessage()}";
}
