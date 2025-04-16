<?php
// session_start();
require_once './database/db.php'; // Adjust path

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Student') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$room_id = $id;

if (!$room_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid room ID']);
    exit;
}

$db = new Database();
$conn = $db->connect();

// Check if student already has an active allocation
$stmt = $conn->prepare("SELECT COUNT(*) FROM allocations WHERE student_id = (SELECT student_id FROM students WHERE user_id = ?) AND status = 'Active'");
$stmt->bind_param("i", $_SESSION['user']['user_id']);
$stmt->execute();
$has_allocation = $stmt->get_result()->fetch_row()[0] > 0;
$stmt->close();

if ($has_allocation) {
    echo json_encode(['success' => false, 'error' => 'You already have an active room allocation']);
    exit;
}

// Check room availability
$stmt = $conn->prepare("SELECT capacity, current_occupancy, status FROM rooms WHERE room_id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$room || $room['current_occupancy'] >= $room['capacity'] || $room['status'] === 'Under Maintenance') {
    echo json_encode(['success' => false, 'error' => 'Room is not available']);
    exit;
}

// Book the room
$student_id = $conn->query("SELECT student_id FROM students WHERE user_id = " . $_SESSION['user']['user_id'])->fetch_row()[0];
$stmt = $conn->prepare("INSERT INTO allocations (student_id, room_id, start_date, status) VALUES (?, ?, CURDATE(), 'Pending')");
$stmt->bind_param("ii", $student_id, $room_id);
$success = $stmt->execute();

if ($success) {
    // Update room occupancy
    $stmt = $conn->prepare("UPDATE rooms SET current_occupancy = current_occupancy + 1, status = IF(current_occupancy + 1 = capacity, 'Fully Occupied', 'Partially Occupied') WHERE room_id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to book room']);
}

$stmt->close();
$conn->close();
