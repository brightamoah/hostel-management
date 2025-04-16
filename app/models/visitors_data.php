<?php
require_once './database/db.php';
header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['student_id'])) {
    echo json_encode(['data' => []]); // Return empty array if not logged in
    exit;
}

$user_id = $_SESSION['user']['user_id'];

// Create connection

$db = new Database();
$conn = $db->connect();

// Prepare SQL query to fetch visitors for the student
$stmt = $conn->prepare("
    SELECT 
        v.visitor_id AS id,
        v.visitor_name AS full_name,
        v.relation AS role,
        v.phone_number AS email, -- Using phone_number as email for display
        v.visit_date AS visit_date,
        v.check_in_time AS check_in,
        v.check_out_time AS check_out,
        v.status AS status
    FROM visitors v
    JOIN students s ON v.student_id = s.student_id
    WHERE s.user_id = ?
    ORDER BY v.visit_date DESC
");
$stmt->bind_param("i", $user_id);

if (!$stmt->execute()) {
    echo json_encode(['data' => [], 'error' => $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}

$result = $stmt->get_result();
$visitors = [];
while ($row = $result->fetch_assoc()) {
    $visitors[] = $row;
}

// Close statement and connection
$stmt->close();
$conn->close();

// Return data in DataTables-compatible format
echo json_encode(['data' => $visitors]);
