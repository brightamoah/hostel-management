<?php
require_once "./app/controllers/MaintenanceController.php";


header('Content-Type: application/json');

$m_controller = new MaintenanceController();
$result = $m_controller->submitMaintenanceRequest();

if ($result) {
    $response = json_encode(['success' => true, 'message' => 'Maintenance request submitted successfully']);
    out($response);
} else {
   $response = json_encode(['success' => false, 'error' => 'Failed to submit maintenance request']);
    out($response);
}   