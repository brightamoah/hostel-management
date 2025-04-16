<?php
ob_start();

require_once "./app/controllers/RoomController.php";

$controller = new RoomController();
$controller->getAvailableRooms();


ob_end_clean();
exit();