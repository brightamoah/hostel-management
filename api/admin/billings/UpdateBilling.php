<?php
require_once __DIR__ . "/../../../app/controllers/BillingController.php";

$billingController = new BillingController();

$billing_id = $billingId;

if (!$billing_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Billing ID is required']);
    exit;
}

$billingController->updateInvoice($billing_id);
