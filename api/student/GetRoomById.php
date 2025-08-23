<?php
require_once __DIR__ . "/../../app/models/Room.php";


$room_id = $id;

if (!$room_id || !is_numeric($room_id)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid room ID']);
    exit();
}

$room = new Rooms();
$room_detail = $room->getRoomById($room_id);

if (!$room_detail) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Room not found']);
    exit();
}

header('Content-Type: application/json');
echo json_encode($room_detail);

// echo "Room ID: $room_id";