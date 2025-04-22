<?php
require_once "./database/db.php";
header('Content-Type: application/json');

$db = new Database();
$conn = $db->connect();

$query = "
    SELECT 
        u.user_id, 
        u.name, 
        u.email, 
        u.role, 
        u.is_email_verified, 
        s.resident_status
    FROM users u
    LEFT JOIN students s ON u.user_id = s.user_id
";
$result = $conn->query($query);
if ($result === false) {
    echo json_encode(['data' => [], 'error' => "Database query failed: {$conn->error}"]);
    exit;
}
$users = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode(['data' => $users]);
} else {
    echo json_encode(['data' => [], 'error' => 'Failed to fetch users.']);
}
