<?php
require_once __DIR__ . "/../../../database/db.php"; 


header('Content-Type: application/json');

$db = new Database();
$conn = $db->connect();

//check if user is a super admin

$user = $_SESSION['user'];

if ($user['access_level'] !== 'Super Admin') {
    echo json_encode(['success' => false, 'message' => 'You are not authorized to perform this action']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$user_id = isset($input['user_id']) ? intval($input['user_id']) : 0;

if ($user_id) {
    $query = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
}
