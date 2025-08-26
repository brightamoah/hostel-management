<?php
require_once __DIR__ . "/../../../app/controllers/BillingController.php";
require_once __DIR__ . "/../../../utils/hostel_helpers.php";

// Check if user is authenticated and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized: Admin access required']);
    exit;
}

$billingController = new BillingController();

$billing_id = $_GET['id'] ?? $_POST['id'] ?? $billingId ?? null;

if (!$billing_id) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Billing ID is required']);
    exit;
}

$billingController->updateInvoice($billing_id);
