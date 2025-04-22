<?php
require_once __DIR__ . "/../../database/db.php";
header('Content-Type: application/json');

$db = new Database();
$conn = $db->connect();

$input = json_decode(file_get_contents('php://input'), true);
$user_id = isset($input['user_id']) ? intval($input['user_id']) : 0;
$new_role = $input['new_role'] ?? '';

if ($user_id && in_array($new_role, ['Student', 'Admin'])) {
    $conn->begin_transaction();
    try {
        // Update user role
        $query = "UPDATE users SET role = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $new_role, $user_id);
        $stmt->execute();
        $stmt->close();

        // If changing to Student, check if student record exists
        if ($new_role === 'Student') {
            $query = "SELECT COUNT(*) as count FROM students WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['count'];
            $stmt->close();

            if ($count == 0) {
                // Insert basic student record (you may need to collect additional data)
                $query = "SELECT name FROM users WHERE user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $name = $stmt->get_result()->fetch_assoc()['name'];
                $stmt->close();

                $name_parts = explode(' ', trim($name));
                $first_name = array_shift($name_parts);
                $last_name = implode(' ', $name_parts);

                $student_query = "INSERT INTO students (user_id, first_name, last_name, gender, date_of_birth, phone_number, address, emergency_contact_name, emergency_contact_number, health_condition, enrollment_date) VALUES (?, ?, ?, 'Male', '2000-01-01', '', '', '', '', NULL, CURDATE())";
                $stmt = $conn->prepare($student_query);
                $stmt->bind_param("iss", $user_id, $first_name, $last_name);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            // If changing to Admin, check if admin record exists
            $query = "SELECT COUNT(*) as count FROM admins WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['count'];
            $stmt->close();

            if ($count == 0) {
                // Insert basic admin record
                $query = "SELECT name FROM users WHERE user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $name = $stmt->get_result()->fetch_assoc()['name'];
                $stmt->close();

                $name_parts = explode(' ', trim($name));
                $first_name = array_shift($name_parts);
                $last_name = implode(' ', $name_parts);

                $admin_query = "INSERT INTO admins (user_id, first_name, last_name, department, access_level) VALUES (?, ?, ?, 'General', 'Regular Admin')";
                $stmt = $conn->prepare($admin_query);
                $stmt->bind_param("iss", $user_id, $first_name, $last_name);
                $stmt->execute();
                $stmt->close();
            }
        }

        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}
