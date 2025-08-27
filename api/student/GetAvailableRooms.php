<?php
ob_start();

require_once __DIR__ . "/../../app/controllers/RoomController.php";

$controller = new RoomController();
$controller->getAvailableRooms();


ob_end_clean();
exit();
