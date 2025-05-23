<?php
require_once __DIR__ . "/../../../app/models/Billing.php";

header('Content-Type: application/json');

$billingModel = new Billing();

$billingId = $bill_id ?? null;

$response = $billingModel->getBillingById($billingId);

echo json_encode([
    'data' => $response
]);
