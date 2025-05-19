<?php

// Set content type
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Authentication required. Please log in as administrator.'
    ]);
    exit;
}

require_once __DIR__ . "/../../../app/controllers/AnnouncementController.php";

try {
    // Get target type
    $targetType = $type;

    if (empty($targetType)) {
        throw new Exception('Target type is required');
    }

    if (!in_array($targetType, ['student', 'admin', 'building', 'room'])) {
        throw new Exception('Invalid target type');
    }

    $announcementController = new AnnouncementController();
    $targets = $announcementController->fetchSpecificTargets($targetType);

    echo json_encode([
        'status' => 'success',
        'data' => $targets,
        'message' => 'Targets fetched successfully'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
