<?php
require_once __DIR__ . "/../../../app/controllers/BillingController.php";
require_once __DIR__ . "/../../../utils/hostel_helpers.php";

header("content-type: application/json; charset=utf-8");

// Check if user is authenticated and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized: Admin access required']);
    exit;
}

try {
    $billingController = new BillingController();
    $billingData = $billingController->getBillingData();
    echo json_encode(["data" => $billingData["data"]], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    error_log("Error in GetBillingData API: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to retrieve billing data']);
}
