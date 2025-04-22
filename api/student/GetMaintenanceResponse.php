<?php
require_once "./app/models/MaintenanceRequest.php";

header("content-type: application/json");

//$r_id is the request id gotten from the url


$m_request = new MaintenanceRequest();
$response = $m_request->getRequestResponses($r_id);
echo json_encode(['data' => $response]);