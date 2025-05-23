<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../models/User.php";

class Login
{
    private $user;

    public function __construct()
    {
        $this->user = new User(getDb());
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
                    // $_SESSION['message'] = 'Please verify your email before logging in.';
                    // $_SESSION['message_type'] = 'danger';
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
                            "last_login" => $user['last_login'],
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
                } elseif ($user['role'] === 'Admin') {
                    $admin_query = "SELECT * FROM admins WHERE user_id = ?";
                    $stmt = $this->user->getConnection()->prepare($admin_query);
                    if ($stmt) {
                        $stmt->bind_param("i", $user['user_id']);
                        $stmt->execute();
                        $admin = $stmt->get_result()->fetch_assoc();
                        $stmt->close();

                        $_SESSION['user'] = [
                            'user_id' => $user['user_id'],
                            'admin_id' => $admin['admin_id'],
                            'name' => $user['name'],
                            'email' => $user['email'],
                            'role' => $user['role'],
                            'last_login' => $user['last_login'],
                            'department' => $admin['department'],
                            'access_level' => $admin['access_level'],
                            'is_email_verified' => $user['is_email_verified'],
                        ];
                    } else {
                        error_log("Failed to prepare admin query: {$this->user->getConnection()->error}");
                    }
                } else {
                    $_SESSION['user'] = [
                        'user_id' => $user['user_id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'last_login' => $user['last_login']
                    ];
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


    /**
     * Redirects the user to the appropriate dashboard based on their role.
     *
     * If the user's role is 'Admin', they are redirected to the admin dashboard.
     * Otherwise, they are redirected to the student dashboard.
     * The method also prevents redirect loops by checking if the user is already
     * on the destination page before performing the redirect.
     *
     * @param string $role The role of the user ('Admin' or other).
     * @return void
     */
    private function redirectUser($role)
    {
        $destination = '';
        $destination = ($role === 'Admin') ? '/admin/dashboard' : '/student/dashboard';

        // Prevent redirect loops by checking if we're already on the destination page
        $current_url = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
        if ($current_url === $destination) {
            return; // Already on the destination page, so don't redirect
        }

        header("Location: $destination");
        exit;
    }






    // public function login()
    // {
    //     // Check for remember me cookie first
    //     if (isset($_COOKIE['remember_me'])) {
    //         $user = $this->user->validateRememberToken($_COOKIE['remember_me']);
    //         if ($user && is_array($user) && !isset($user['error'])) {
    //             // Get additional role-specific data for complete session
    //             $this->buildUserSession($user);
    //             $_SESSION['message'] = 'Welcome back! You were logged in automatically.';
    //             $_SESSION['message_type'] = 'success';
    //             $this->redirectUser($user['role']);
    //             exit;
    //         }
    //     }

    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    //         $password = $_POST['password'] ?? '';
    //         $remember_me = isset($_POST['remember_me']) && $_POST['remember_me'] === 'on';

    //         // Validate inputs
    //         if ($this->validateLoginInput($email, $password) === false) {
    //             return; // Validation failed and redirect already handled
    //         }

    //         // Attempt login
    //         $user = $this->user->login($email, $password);

    //         // Handle special cases like email verification
    //         if (is_array($user) && isset($user['error'])) {
    //             $this->handleLoginErrors($user, $email);
    //             return; // Error handled and redirect already performed
    //         }

    //         // If login successful
    //         if ($user && !isset($user['error'])) {
    //             // Build complete user session with role-specific data
    //             $this->buildUserSession($user);

    //             // Set remember me cookie if requested
    //             if ($remember_me) {
    //                 $token = $this->user->generateRememberToken($user['user_id']);
    //                 if ($token) {
    //                     setcookie('remember_me', $token, time() + 30 * 24 * 60 * 60, '/', '', false, true);
    //                 } else {
    //                     error_log("Failed to generate remember me token for user_id: {$user['user_id']}");
    //                 }
    //             }

    //             $_SESSION['message'] = 'User logged in successfully.';
    //             $_SESSION['message_type'] = 'success';
    //             $this->redirectUser($user['role']);
    //             exit;
    //         }

    //         // Login failed
    //         $_SESSION['message'] = 'Login failed. Invalid email or password.';
    //         $_SESSION['message_type'] = 'danger';
    //         header('Location: /login');
    //         exit;
    //     }
    // }


    // /**
    //  * Validates login form input
    
    
    //  * @param string $email User email
    //  * @param string $password User password
    //  * @return bool True if validation passes, false otherwise
    //  */
    // private function validateLoginInput($email, $password)
    // {
    //     if (empty($email) || empty($password)) {
    //         $_SESSION['message'] = 'All fields are required.';
    //         $_SESSION['message_type'] = 'danger';
    //         header('Location: /login');
    //         exit;
    //     }

    //     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    //         $_SESSION['message'] = 'Invalid email format.';
    //         $_SESSION['message_type'] = 'danger';
    //         header('Location: /login');
    //         exit;
    //     }

    //     if (strlen($password) < 8) {
    //         $_SESSION['message'] = 'Password must be at least 8 characters.';
    //         $_SESSION['message_type'] = 'danger';
    //         header('Location: /login');
    //         exit;
    //     }

    //     return true;
    // }

    // /**
    //  * Handles various login error scenarios and redirects the user accordingly.
    //  * @param array $user User data containing error information
    //  * @param string $email The email address used for login
    //  */
    // private function handleLoginErrors($user, $email)
    // {
    //     if ($user['error'] === 'Email not verified') {
    //         $_SESSION['email_to_verify'] = $email;
    //         header('Location: /verify-email');
    //         exit;
    //     } elseif ($user['error'] === 'Email does not exist') {
    //         $_SESSION['message'] = 'No account found with this email. Please sign up.';
    //         $_SESSION['message_type'] = 'danger';
    //         header('Location: /login');
    //         exit;
    //     } else {
    //         $_SESSION['message'] = 'Login failed: ' . $user['error'];
    //         $_SESSION['message_type'] = 'danger';
    //         header('Location: /login');
    //         exit;
    //     }
    // }




    // /**
    //  * Populates the user session with detailed, role-specific data.
    //  *
    //  * Fetches and merges additional profile information from the database based on the user's role
    //  * ('Student' or 'Admin'). If role-specific data is missing, clears persistent login cookies,
    //  * sets an error message, and redirects to the login page. Falls back to basic user data for
    //  * unrecognized roles or query failures.
    //  *
    //  * @param array $user Basic user data including 'user_id', 'name', 'email', 'role', and 'last_login'.
    //  * @return void
    //  */
    // private function buildUserSession($user)
    // {
    //     if ($user['role'] === 'Student') {
    //         $student_query = "SELECT * FROM students WHERE user_id = ?";
    //         $stmt = $this->user->getConnection()->prepare($student_query);
    //         if ($stmt) {
    //             $stmt->bind_param("i", $user['user_id']);
    //             $stmt->execute();
    //             $student = $stmt->get_result()->fetch_assoc();
    //             $stmt->close();

    //             if ($student) {
    //                 $_SESSION['user'] = [
    //                     'user_id' => $user['user_id'],
    //                     'student_id' => $student['student_id'],
    //                     'name' => $user['name'],
    //                     'email' => $user['email'],
    //                     'role' => $user['role'],
    //                     'last_login' => $user['last_login'],
    //                     'gender' => $student['gender'] ?? null,
    //                     'date_of_birth' => $student['date_of_birth'] ?? null,
    //                     'phone_number' => $student['phone_number'] ?? null,
    //                     'address' => $student['address'] ?? null,
    //                     'first_name' => $student['first_name'] ?? null,
    //                     'last_name' => $student['last_name'] ?? null,
    //                     'emergency_contact_name' => $student['emergency_contact_name'] ?? null,
    //                     'emergency_contact_number' => $student['emergency_contact_number'] ?? null,
    //                     'health_condition' => $student['health_condition'] ?? null,
    //                     'enrollment_date' => $student['enrollment_date'] ?? null,
    //                     'is_email_verified' => $user['is_email_verified'] ?? 1,
    //                 ];
    //             } else {
    //                 // Handle case where student record is missing
    //                 $this->clearRememberCookie();
    //                 $_SESSION['message'] = 'Student profile data missing. Please contact support.';
    //                 $_SESSION['message_type'] = 'danger';
    //                 header('Location: /login');
    //                 exit;
    //             }
    //         } else {
    //             error_log("Failed to prepare student query: {$this->user->getConnection()->error}");
    //             $_SESSION['user'] = $this->getBasicUserData($user);
    //         }
    //     } elseif ($user['role'] === 'Admin') {
    //         $admin_query = "SELECT * FROM admins WHERE user_id = ?";
    //         $stmt = $this->user->getConnection()->prepare($admin_query);
    //         if ($stmt) {
    //             $stmt->bind_param("i", $user['user_id']);
    //             $stmt->execute();
    //             $admin = $stmt->get_result()->fetch_assoc();
    //             $stmt->close();

    //             if ($admin) {
    //                 $_SESSION['user'] = [
    //                     'user_id' => $user['user_id'],
    //                     'admin_id' => $admin['admin_id'],
    //                     'name' => $user['name'],
    //                     'email' => $user['email'],
    //                     'role' => $user['role'],
    //                     // 'last_login' => $user['last_login'],
    //                     'department' => $admin['department'] ?? null,
    //                     'access_level' => $admin['access_level'] ?? null,
    //                     'is_email_verified' => $user['is_email_verified'] ?? 1,
    //                 ];
    //             } else {
    //                 // Handle case where admin record is missing
    //                 $this->clearRememberCookie();
    //                 $_SESSION['message'] = 'Admin profile data missing. Please contact support.';
    //                 $_SESSION['message_type'] = 'danger';
    //                 header('Location: /login');
    //                 exit;
    //             }
    //         } else {
    //             error_log("Failed to prepare admin query: {$this->user->getConnection()->error}");
    //             $_SESSION['user'] = $this->getBasicUserData($user);
    //         }
    //     } else {
    //         $_SESSION['user'] = $this->getBasicUserData($user);
    //     }
    // }


    // /**
    //  * Get basic user data for session
    //  * @param array $user User data
    //  * @return array Basic user data
    //  */
    // private function getBasicUserData($user)
    // {
    //     return [
    //         'user_id' => $user['user_id'],
    //         'name' => $user['name'],
    //         'email' => $user['email'],
    //         'role' => $user['role'],
    //         'last_login' => $user['last_login'],
    //         'is_email_verified' => $user['is_email_verified'] ?? 1
    //     ];
    // }


    // /**
    //  * Clear remember me cookie
    //  */
    // private function clearRememberCookie()
    // {
    //     if (isset($_COOKIE['remember_me'])) {
    //         unset($_COOKIE['remember_me']);
    //         setcookie('remember_me', '', time() - 3600, '/');
    //     }
    // }


    
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
