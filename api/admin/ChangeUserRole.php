<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../../app/models/User.php";
require_once __DIR__ . "/../../utils/hostel_helpers.php";

header('Content-Type: application/json');

// Debug incoming request
error_log("ChangeUserRole - POST data: " . print_r($_POST, true));

$current_user = $_SESSION['user'];

// Check authentication
if (!isset($current_user) || $current_user['role'] !== 'Admin') {
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

    // Fetch the target user
    $query = "SELECT name, role FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $target_user = $result->fetch_assoc();
    $stmt->close();

    if (!$target_user) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    if ($target_user['role'] === $new_role) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'User already has this role']);
        exit;
    }

    // Check permissions using helper functions
    if (!canManageUser($user_id)) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'You do not have permission to manage this user']);
        exit;
    }

    if (!canChangeRole($user_id, $new_role)) {
        $conn->rollback();
        $message = isSuperAdmin() ? 
            'Invalid role change' : 
            'Regular admins can only promote students to admin, not demote admins';
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }

    // Update the user role
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
        // Convert Admin → Student (only Super Admin can do this)
        
        // Remove from admins table
        $query = "DELETE FROM admins WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        if (!$stmt->execute()) {
            $conn->rollback();
            $stmt->close();
            echo json_encode(['success' => false, 'message' => 'Failed to remove admin record']);
            exit;
        }
        $stmt->close();

        // Split name into first_name and last_name
        $name_parts = explode(' ', trim($target_user['name']));
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
        $stmt->close();
        
    } else {
        // Convert Student → Admin
        
        // Remove from students table
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

        // Split name into first_name and last_name
        $name_parts = explode(' ', trim($target_user['name']));
        $first_name = array_shift($name_parts);
        $last_name = implode(' ', $name_parts) ?: '';

        // Determine hostel assignment and access level
        $hostel_id = null;
        $access_level = 'Regular Admin';
        
        if (!isSuperAdmin()) {
            // Regular admin promoting student - inherit current admin's hostel
            $hostel_id = getCurrentAdminHostelId();
        }
        // Super Admin can leave hostel_id as null (to be assigned later)

        // Insert into admins table
        $query = "INSERT INTO admins (user_id, first_name, last_name, department, access_level, hostel_id) VALUES (?, ?, ?, 'General', ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isssi", $user_id, $first_name, $last_name, $access_level, $hostel_id);
        if (!$stmt->execute()) {
            $conn->rollback();
            $stmt->close();
            echo json_encode(['success' => false, 'message' => 'Failed to add admin record']);
            exit;
        }
        $stmt->close();
    }

    // Commit transaction
    $conn->commit();
    
    $message = $new_role === 'Admin' && !isSuperAdmin() ? 
        'User promoted to admin and assigned to your hostel' : 
        'Role updated successfully';
        
    echo json_encode(['success' => true, 'message' => $message]);
    
} catch (Exception $e) {
    $conn->rollback();
    error_log("Toggle role error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
