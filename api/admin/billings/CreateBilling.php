<?php

require_once __DIR__ . "/../../../app/controllers/BillingController.php";

$billingController = new BillingController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $billingController->createInvoice();
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}