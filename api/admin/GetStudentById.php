<?php
require_once "./database/db.php";


header('Content-Type: application/json');

$db = new Database();
$conn = $db->connect();

$user_id = $id;

$query = "
    SELECT 
        s.first_name, 
        s.last_name, 
        u.email, 
        s.gender, 
        s.date_of_birth, 
        s.phone_number, 
        s.address, 
        s.emergency_contact_name, 
        s.emergency_contact_number, 
        s.health_condition, 
        s.resident_status,
        r.building,
        r.room_number
    FROM students s
    JOIN users u ON s.user_id = u.user_id
    LEFT JOIN allocations a ON s.student_id = a.student_id
    LEFT JOIN rooms r ON a.room_id = r.room_id
    WHERE s.user_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'data' => $row]);
} else {
    echo json_encode(['success' => false, 'message' => 'Student not found']);
}
$stmt->close();
