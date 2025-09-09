<?php
require_once __DIR__ . "/../../../app/controllers/BillingController.php";
require_once __DIR__ . "/../../../utils/hostel_helpers.php";

ob_start();

header('Content-Type: application/json; charset=utf-8');

// Check if user is authenticated and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Unauthorized: Admin access required']);
    http_response_code(401);
    exit;
}

$billing_id = $_GET['id'] ?? $_POST['id'] ?? $billingId ?? null;

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
