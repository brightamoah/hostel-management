<?php
require_once "./app/controllers/MaintenanceController.php";

header("Content-Type: application/json");

$controller = new MaintenanceController();
$controller->getRequestData();