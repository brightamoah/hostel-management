<?php
require_once __DIR__ . "/../../../app/models/Billing.php";
require_once __DIR__ . "/../../../utils/hostel_helpers.php";

header('Content-Type: application/json');

// Check if user is authenticated and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized: Admin access required']);
    exit;
}

$billingModel = new Billing();

$billingId = $_GET['id'] ?? $_POST['id'] ?? $bill_id ?? null;

if (!$billingId) {
    echo json_encode(['success' => false, 'error' => 'Billing ID is required']);
    exit;
}

try {
    $response = $billingModel->getBillingById($billingId);

    if ($response === null) {
        echo json_encode(['success' => false, 'error' => 'Billing record not found or access denied']);
    } else {
        echo json_encode(['success' => true, 'data' => $response]);
    }
} catch (Exception $e) {
    error_log("Error in GetBillingById API: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to retrieve billing details']);
}
