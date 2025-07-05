<?php
require_once __DIR__ . "/../../../app/controllers/BillingController.php";

ob_start();

header('Content-Type: application/json; charset=utf-8');

$billing_id = $billingId ?? null;

try {


    if (!$billing_id || !is_numeric($billing_id)) {
        echo json_encode(['success' => false, 'error' => 'Invalid or missing billing ID']);
        http_response_code(400);
        exit;
    }

    $controller = new BillingController();
    $controller->deleteInvoice($billing_id);
} catch (Exception $e) {
    error_log("Delete billing API error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to delete invoice: ' . $e->getMessage()]);
    http_response_code(500);
    exit;
} finally {
    ob_end_clean(); // Discard any buffered output
}
