<?php
require_once __DIR__ . "/../../app/controllers/AnnouncementController.php";

$controller = new AnnouncementController();
// $data = $controller->getAnnouncements();
header('Content-Type: application/json');
$announcements = $controller->getAnnouncements();
$_SESSION['totalAnnouncements'] = count($announcements);
echo json_encode($announcements);
