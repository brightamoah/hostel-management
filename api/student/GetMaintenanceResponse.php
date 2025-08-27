<?php
require_once __DIR__ . "/../../app/models/MaintenanceRequest.php";

header("content-type: application/json");


$m_request = new MaintenanceRequest();
$response = $m_request->getRequestResponses($r_id);
echo json_encode(['data' => $response]);
