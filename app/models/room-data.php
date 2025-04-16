<?php
require_once "./database/db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Student') {
    header('Location: /login');
    exit();
}


$db = new Database();
$conn = $db->connect();

// Fetch available rooms (Vacant or Partially Occupied with space)
$query = "
    SELECT room_id, room_number, building, floor, room_type, capacity, current_occupancy, features, status
    FROM rooms
    WHERE status IN ('Vacant', 'Partially Occupied')
    AND current_occupancy < capacity
    ORDER BY building, room_number
";
$stmt = $conn->prepare($query);
$stmt->execute();

$result = $stmt->get_result();
$rooms = [];
while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}

echo json_encode(['data' => $rooms]);
$first_name = $_SESSION['user']['name'] ?? 'Student';
