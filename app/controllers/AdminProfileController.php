<?php
require_once "./database/db.php";
require_once "./app/models/User.php";
require_once "./utils/functions.php";

class AdminProfileController
{
    private $db;
    private $conn;
    private $user;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->connect();
        $this->user = new User($this->conn);
    }

    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_csrf_valid()) {
            $user_id = $_SESSION['user']['user_id'] ?? null;
            if (!$user_id || $_SESSION['user']['role'] !== 'Admin') {
                $_SESSION['message-update'] = 'User not authenticated or not authorized';
                $_SESSION['message_type'] = 'danger';
                header('Location: /login');
                exit();
            }

            $first_name = sanitizeInput($_POST['first_name']);
            $last_name = sanitizeInput($_POST['last_name']);
            $email = sanitizeInput($_POST['email']);
            $department = sanitizeInput($_POST['department']);

            // Only super admins can change access levels
            $access_level = null;
            $current_admin_details = $this->user->getAdminByUserId($user_id);
            $current_access_level = $current_admin_details['access_level'] ?? 'Regular Admin';

            // If user is Super Admin and access_level is provided, use it
            if ($current_access_level === 'Super Admin' && isset($_POST['access_level'])) {
                $access_level = sanitizeInput($_POST['access_level']);
            } else {
                // Keep current access level
                $access_level = $current_access_level;
            }

            // Validation
            $errors = [];
            if (empty($first_name) || strlen($first_name) < 2 || strlen($first_name) > 50) {
                $errors[] = 'First name must be between 2 and 50 characters';
            }
            if (empty($last_name) || strlen($last_name) < 2 || strlen($last_name) > 50) {
                $errors[] = 'Last name must be between 2 and 50 characters';
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Valid email is required';
            }
            if (empty($department)) {
                $errors[] = 'Department is required';
            }
            if (empty($access_level) || !in_array($access_level, ['Super Admin', 'Regular Admin', 'Support Staff'])) {
                $errors[] = 'Valid access level is required';
            }

            if (!empty($errors)) {
                $_SESSION['message-update'] = implode(', ', $errors);
                $_SESSION['message_type'] = 'danger';
                header('Location: /admin/profile');
                exit();
            }

            try {
                $this->conn->begin_transaction();

                // Update users table
                $query = "UPDATE users SET email = ? WHERE user_id = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("si", $email, $user_id);
                $stmt->execute();
                $stmt->close();

                // Check if admin record exists
                $check_query = "SELECT admin_id FROM admins WHERE user_id = ?";
                $check_stmt = $this->conn->prepare($check_query);
                $check_stmt->bind_param("i", $user_id);
                $check_stmt->execute();
                $result = $check_stmt->get_result();
                $admin_exists = $result->fetch_assoc();
                $check_stmt->close();

                if ($admin_exists) {
                    // Update existing admin record
                    $query = "UPDATE admins SET first_name = ?, last_name = ?, department = ?, access_level = ? WHERE user_id = ?";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bind_param("ssssi", $first_name, $last_name, $department, $access_level, $user_id);
                } else {
                    // Create new admin record
                    $query = "INSERT INTO admins (user_id, first_name, last_name, department, access_level) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bind_param("issss", $user_id, $first_name, $last_name, $department, $access_level);
                }

                $stmt->execute();
                $stmt->close();

                // Update session
                $_SESSION['user'] = array_merge($_SESSION['user'], [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email
                ]);

                $this->conn->commit();

                $_SESSION['message-update'] = 'Admin profile updated successfully';
                $_SESSION['message_type'] = 'success';
                header('Location: /admin/profile');
                exit();
            } catch (Exception $e) {
                $this->conn->rollback();
                error_log("Admin profile update failed: " . $e->getMessage());
                $_SESSION['message-update'] = 'Failed to update profile: ' . $e->getMessage();
                $_SESSION['message_type'] = 'danger';
                header('Location: /admin/profile');
                exit();
            }
        } else {
            $_SESSION['message-update'] = 'Invalid request or CSRF token';
            $_SESSION['message_type'] = 'danger';
            header('Location: /admin/profile');
            exit();
        }
    }
}

try {
    $controller = new AdminProfileController();
    $controller->updateProfile();
} catch (Exception $e) {
    error_log("Admin profile controller error: " . $e->getMessage());
    $_SESSION['message-update'] = 'An error occurred';
    $_SESSION['message_type'] = 'danger';
    header('Location: /admin/profile');
    exit();
}
