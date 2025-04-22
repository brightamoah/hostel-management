<?php
require_once __DIR__ . "/../../app/controllers/VisitorController.php";


try {
    $visitor_id = $id;
    $controller = new VisitorController();
    $controller->view($visitor_id ?? null);
} catch (Exception $e) {
    error_log("Error in GetVisitorById: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while fetching visitor data.']);
}
