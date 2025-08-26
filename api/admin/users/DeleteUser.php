<?php
require_once __DIR__ . "/../../../database/db.php";
require_once __DIR__ . "/../../../utils/hostel_helpers.php";

header('Content-Type: application/json');

$db = new Database();
$conn = $db->connect();

// Check if user can delete users (only Super Admin)
if (!canDeleteUser(0)) {
    echo json_encode(['success' => false, 'message' => 'Only Super Admins can delete users']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$user_id = isset($input['user_id']) ? intval($input['user_id']) : 0;

if ($user_id) {
    $query = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
}
