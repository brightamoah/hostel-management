<?php
require_once __DIR__ . "/../../../app/models/Room.php";
header('Content-Type: application/json');

$room_id = $id;

$room = new Rooms();
$room_detail = $room->getRoomById($room_id);


echo json_encode($room_detail);
