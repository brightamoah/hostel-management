<?php
require_once __DIR__ . "/../../app/models/User.php";
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../../utils/functions.php";


header('Content-Type: application/json');

$db = null;
$conn = null;


try {
    $db = new Database();
    $conn = $db->connect();

    // Check connection after connect() call
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: {$conn->connect_error}");
    }

    $userController = new User($conn);


    if (!is_csrf_valid()) {
        $_SESSION['message-add-user'] = 'Invalid CSRF token. Please try submitting the form again.';
        $_SESSION['message_type_admin'] = 'danger';
        header('Content-Type: application/json');
        die("Invalid CSRF token.");
    }


    // Get POST data
    $name = sanitizeInput($_POST['userFullname'] ?? '');
    $email = sanitizeInput($_POST['userEmail'] ?? '');
    $password = $_POST['userPassword'] ?? '';
    $role = sanitizeInput($_POST['userRole'] ?? '');

    // Server-side Validation
    if (empty($name) || empty($email) || empty($password) || !in_array($role, ['Student', 'Admin'])) {
        throw new Exception('Invalid input. Please provide name, email, password, and role.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format provided.");
    }
    if (strlen($password) < 8) { // Example minimum length
        throw new Exception("Password must be at least 8 characters long.");
    }

    // --- Start Transaction ---
    $conn->begin_transaction();

    // Call the NEW method specifically for admin creation
    // This only creates the record in the 'users' table
    $user_id = $userController->adminCreateUser(
        $name,
        $email,
        $password,
        $role
    );


    // If the role is Admin, add the admin-specific record
    if ($role === 'Admin') {
        $name_parts = explode(' ', $name);
        $first_name = array_shift($name_parts) ?: 'Admin';
        $last_name = implode(' ', $name_parts);
        if (empty($last_name))
            $last_name = 'User';


        $query_admin = "INSERT INTO admins (user_id, first_name, last_name, department, access_level) VALUES (?, ?, ?, 'General', 'Regular Admin')";
        $stmt_admin = $conn->prepare($query_admin);
        if (!$stmt_admin) {
            throw new Exception("Failed to prepare admin insert statement: {$conn->error}");
        }
        $stmt_admin->bind_param("iss", $user_id, $first_name, $last_name);
        if (!$stmt_admin->execute()) {
            $error = $stmt_admin->error;
            $stmt_admin->close();
            throw new Exception("Failed to insert admin record: $error");
        }
        $stmt_admin->close();
        error_log("Admin record created for user_id: $user_id");
    } elseif ($role === 'Student') {
        //Create Student Profile with placeholder values
        $name_parts = explode(' ', $name, 2);
        $first_name = $name_parts[0] ?: 'Student';
        $last_name = $name_parts[1] ?? 'User';

        $placeholder_gender = 'Male';
        $placeholder_dob = '2000-01-01';
        $placeholder_phone = '+233000000000';
        $placeholder_address = 'N/A - Please Update';
        $placeholder_emergency_name = 'N/A - Please Update';
        $placeholder_emergency_phone = '+233000000000';
        $placeholder_health = null;
        $default_resident_status = 'Inactive';

        $student_query = "INSERT INTO students (
                              user_id, first_name, last_name, gender, date_of_birth,
                              phone_number, address, emergency_contact_name,
                              emergency_contact_number, health_condition, enrollment_date, resident_status
                          ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), ?)";


        $stmt_student = $conn->prepare($student_query);
        if (!$stmt_student) {
            throw new Exception("Failed to prepare student insert statement: {$conn->error}");
        }

        $stmt_student->bind_param(
            "issssssssss",
            $user_id,
            $first_name,
            $last_name,
            $placeholder_gender,
            $placeholder_dob,
            $placeholder_phone,
            $placeholder_address,
            $placeholder_emergency_name,
            $placeholder_emergency_phone,
            $placeholder_health, // Can bind null directly for TEXT
            $default_resident_status
        );


        if (!$stmt_student->execute()) {
            $error = $stmt_student->error;
            $stmt_student->close();
            // Check if it was a duplicate user_id issue (shouldn't happen if user insert worked)


            if ($conn->errno == 1062) {
                throw new Exception("Failed to insert student record: User ID already exists in students table (unexpected).");
            }
            throw new Exception("Failed to insert student record: " . $error);
        }

        $stmt_student->close();
        error_log("Student record created with placeholders for user_id: $user_id");
    }

    // --- Commit Transaction ---
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'User added successfully.']);
} catch (Exception $e) {
    // --- Rollback Transaction ---
    if ($conn && $conn->connect_errno == 0) {
        $conn->rollback();
    }
    error_log("Admin Add User Error: " . $e->getMessage() . " - Input: " . json_encode($_POST));
    echo json_encode(['success' => false, 'message' => "An error occurred while adding the user. {$e->getMessage()}"]);
} 
