<?php
require_once __DIR__ . "/../../app/controllers/MaintenanceController.php";

header("Content-Type: application/json");
$controller = new MaintenanceController();
$controller->addResponse();
