<?php
require_once "./app/controllers/RoomController.php";

if (!isset($_POST['room_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Room ID not provided']);
    exit();
}

$room_id = filter_var($_POST['room_id'], FILTER_VALIDATE_INT);
if ($room_id === false) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Invalid Room ID']);
    exit();
}

$controller = new RoomController();
$controller->bookRoom($room_id);
