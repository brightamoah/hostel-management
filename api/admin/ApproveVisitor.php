<?php
require_once __DIR__ . "/../../app/controllers/VisitorController.php";


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin access required']);
    exit;
}

try {
    $visitor_id = $id ?? '';
    $controller = new VisitorController();
    $controller->approve($visitor_id);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
