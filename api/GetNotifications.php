<?php
require_once __DIR__ . "/../app/controllers/NotificationController.php";

// Check authentication
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$controller = new NotificationController();
$notifications = $controller->getNotifications();

echo json_encode([
    'success' => true,
    'data' => $notifications
]);
