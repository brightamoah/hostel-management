<?php
require_once __DIR__ . "/../../../app/models/Billing.php";
require_once __DIR__ . "/../../../utils/hostel_helpers.php";

header("content-type: application/json; charset=utf-8");

// Check if user is authenticated and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized: Admin access required']);
    exit;
}

try {
    $billingModel = new Billing();
    echo json_encode($billingModel->getBuildings());
} catch (Exception $e) {
    error_log("Error in GetBuilding API: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to retrieve buildings']);
}
