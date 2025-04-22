<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../../app/models/User.php";

header('Content-Type: application/json');

// Debug incoming request
error_log("ChangeUserRole - POST data: " . print_r($_POST, true));



$user = $_SESSION['user'];

if ($user['access_level'] !== 'Super Admin') {
    echo json_encode(['success' => false, 'message' => 'You are not authorized to perform this action']);
    exit();
}


// Check for required fields
if (!isset($_POST['user_id']) || !isset($_POST['new_role'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

if (!is_csrf_valid()) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$user_id = (int)$_POST['user_id'];
$new_role = $_POST['new_role'];

if (!in_array($new_role, ['Student', 'Admin'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid role specified']);
    exit;
}


try {
    $db = new Database();
    $conn = $db->connect();
    $user_obj = new User($conn);


    $conn->begin_transaction();

    //fetch the user
    $query = "SELECT name, role FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    if ($user['role'] === $new_role) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'User already has this role']);
        exit;
    }

    //update the user role
    $query = "UPDATE users SET role = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $new_role, $user_id);
    if (!$stmt->execute()) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Failed to update user role']);
        exit;
    }
    $stmt->close();


    if ($new_role === 'Student') {
        // Split name into first_name and last_name
        $name_parts = explode(' ', trim($user['name']));
        $first_name = array_shift($name_parts);
        $last_name = implode(' ', $name_parts) ?: '';

        // Insert into students table with minimal data
        $query = "INSERT INTO students (user_id, first_name, last_name, enrollment_date) VALUES (?, ?, ?, CURDATE())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iss", $user_id, $first_name, $last_name);
        if (!$stmt->execute()) {
            $conn->rollback();
            $stmt->close();
            echo json_encode(['success' => false, 'message' => 'Failed to add student record']);
            exit;
        }
        $stmt->close() ;
    } else {
        // Remove from students table if changing to Admin
        $query = "DELETE FROM students WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        if (!$stmt->execute()) {
            $conn->rollback();
            $stmt->close();
            echo json_encode(['success' => false, 'message' => 'Failed to remove student record']);
            exit;
        }
        $stmt->close();
    }
    // Commit transaction
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Role updated successfully']);
} catch (Exception $e) {

    $conn->rollback();
    error_log("Toggle role error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
