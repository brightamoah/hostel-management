<?php
require_once "./app/controllers/MaintenanceController.php";

header("content-type: application/json");


//$r_id is the request id gotten from the url
$request_id = $r_id;

$controller = new MaintenanceController();
$controller -> getRequestDetails($request_id);