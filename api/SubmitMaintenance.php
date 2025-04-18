<?php
require_once "./app/controllers/MaintenanceController.php";


header('Content-Type: application/json');

$m_controller = new MaintenanceController();
$result = $m_controller->submitMaintenanceRequest();

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Maintenance request submitted successfully']);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to submit maintenance request']);
}