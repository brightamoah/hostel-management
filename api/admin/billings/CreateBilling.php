<?php
require_once __DIR__ . "/../../../app/controllers/BillingController.php";
require_once __DIR__ . "/../../../utils/hostel_helpers.php";

// Check if user is authenticated and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized: Admin access required']);
    exit;
}

$billingController = new BillingController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $billingController->createInvoice();
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}
