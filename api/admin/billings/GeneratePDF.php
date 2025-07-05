<?php
require_once __DIR__ . "/../../../app/models/PDFGenerator.php";


$action = $_GET['action'] ?? '';
$billing_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : null;

if ($action === 'download' && $billing_id) {
    try {
        $pdfGenerator = new PDFGenerator();
        $pdfGenerator->generateInvoicePDF($billing_id, true);
    } catch (Exception $e) {
        http_response_code(404);
        echo "Error: " . $e->getMessage();
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid or missing billing ID']);
}
// exit();
