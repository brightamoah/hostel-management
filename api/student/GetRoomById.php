<?php

require_once "./app/models/Room.php";
header('Content-Type: application/json');

$room_id = $id;

$room = new Rooms();
$room_detail = $room->getRoomById($room_id);


echo json_encode($room_detail);

// echo "Room ID: $room_id";