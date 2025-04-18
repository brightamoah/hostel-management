<?php
require_once "./database/db.php";
require_once "./app/models/Student.php";
require_once "./utils/functions.php";

class ProfileController
{
    private $db;
    private $conn;
    private $student;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->connect();
        $this->student = new Student($this->conn);
    }

    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_csrf_valid()) {
            $user_id = $_SESSION['user']['user_id'] ?? null;
            if (!$user_id) {
                $_SESSION['message-update'] = 'User not authenticated';
                $_SESSION['message_type'] = 'danger';
                header('Location: /login');
                exit();
            }

            // $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
            // $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
            // $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            // $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING);
            // $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
            // $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
            // $emergency_contact_name = filter_input(INPUT_POST, 'emergency_contact_name', FILTER_SANITIZE_STRING);
            // $emergency_contact_number = filter_input(INPUT_POST, 'emergency_contact_number', FILTER_SANITIZE_STRING);
            // $health_condition = filter_input(INPUT_POST, 'health_condition', FILTER_SANITIZE_STRING);

            $first_name = sanitizeInput($_POST['first_name']);
            $last_name = sanitizeInput($_POST['last_name']);
            $email = sanitizeInput($_POST['email']);
            $phone_number = sanitizeInput($_POST['phone_number']);
            $gender = sanitizeInput($_POST['gender']);
            $address = sanitizeInput($_POST['address']);
            $emergency_contact_name = sanitizeInput($_POST['emergency_contact_name']);
            $emergency_contact_number = sanitizeInput($_POST['emergency_contact_number']);
            $health_condition = sanitizeInput($_POST['health_condition']);  


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
            if (empty($phone_number) || !preg_match('/^(\+233|0)\d{9}$/', $phone_number)) {
                $errors[] = 'Phone number must be in +233XXXXXXXXX or 0XXXXXXXXX format';
            }
            if (empty($gender) || !in_array($gender, ['Male', 'Female', 'Other'])) {
                $errors[] = 'Valid gender is required';
            }
            if (empty($address)) {
                $errors[] = 'Address is required';
            }
            if (empty($emergency_contact_name)) {
                $errors[] = 'Emergency contact name is required';
            }
            if (empty($emergency_contact_number) || !preg_match('/^(\+233|0)\d{9}$/', $emergency_contact_number)) {
                $errors[] = 'Emergency contact number must be in +233XXXXXXXXX or 0XXXXXXXXX format';
            }

            if (!empty($errors)) {
                $_SESSION['message-update'] = implode(', ', $errors);
                $_SESSION['message_type'] = 'danger';
                header('Location: /student/profile');
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

                // Update students table
                $query = "UPDATE students SET first_name = ?, last_name = ?, phone_number = ?, gender = ?, address = ?, emergency_contact_name = ?, emergency_contact_number = ?, health_condition = ? WHERE user_id = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("ssssssssi", $first_name, $last_name, $phone_number, $gender, $address, $emergency_contact_name, $emergency_contact_number, $health_condition, $user_id);
                $stmt->execute();
                $stmt->close();

                // Update session
                $_SESSION['user'] = array_merge($_SESSION['user'], [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'phone_number' => $phone_number,
                    'gender' => $gender,
                    'address' => $address,
                    'emergency_contact_name' => $emergency_contact_name,
                    'emergency_contact_number' => $emergency_contact_number,
                    'health_condition' => $health_condition
                ]);

                $this->conn->commit();

                $_SESSION['message-update'] = 'Profile updated successfully';
                $_SESSION['message_type'] = 'success';
                header('Location: /student/profile');
                exit();
            } catch (Exception $e) {
                $this->conn->rollback();
                error_log("Profile update failed: " . $e->getMessage());
                $_SESSION['message-update'] = 'Failed to update profile: ' . $e->getMessage();
                $_SESSION['message_type'] = 'danger';
                header('Location: /student/profile');
                exit();
            }
        } else {
            $_SESSION['message-update'] = 'Invalid request or CSRF token';
            $_SESSION['message_type'] = 'danger';
            header('Location: /student/profile');
            exit();
        }
    }
}

try {
    $controller = new ProfileController();
    $controller->updateProfile();
} catch (Exception $e) {
    error_log("Profile controller error: " . $e->getMessage());
    $_SESSION['message-update'] = 'An error occurred';
    $_SESSION['message_type'] = 'danger';
    header('Location: /student/profile');
    exit();
}
