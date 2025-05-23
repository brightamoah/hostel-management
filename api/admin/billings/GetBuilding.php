<?php
require_once __DIR__ . "/../../../app/models/Billing.php";

header("content-type: application/json; charset=utf-8");

$billingModel = new Billing();
echo json_encode($billingModel->getBuildings());
