<?php
require_once __DIR__ . "/../../app/controllers/MaintenanceController.php";

header("content-Type: application/json");

$request_id = $r_id;

$controller = new MaintenanceController();
$controller->getRequestDetails($request_id);
