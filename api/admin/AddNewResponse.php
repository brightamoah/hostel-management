<?php
require_once __DIR__ . "/../../app/controllers/MaintenanceController.php";

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

$request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : 0;
if ($request_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid or missing request ID']);
    exit();
}

$controller = new MaintenanceController();
$controller->addResponse();
