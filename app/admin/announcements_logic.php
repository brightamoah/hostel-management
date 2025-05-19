<?php
require_once __DIR__ . "/../../app/controllers/AnnouncementController.php";
header('Content-Type: application/json');

$announcementController = new AnnouncementController();
$response = ['status' => 'error', 'message' => 'Invalid action'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'fetch_targets':
            $targetType = $_POST['target_type'] ?? '';
            $targets = $targetType ? $announcementController->fetchSpecificTargets($targetType) : [];
            $response = [
                'status' => 'success',
                'data' => $targets,
                'message' => 'Targets fetched successfully'
            ];
            break;

        case 'delete':
            $announcementId = $_POST['announcement_id'] ?? 0;
            $response = ($announcementId && $announcementController->deleteAnnouncement($announcementId)) ? [
                'status' => 'success',
                'message' => 'Announcement deleted successfully'
            ] : [
                'status' => 'error',
                'message' => 'Failed to delete announcement'
            ];
            break;

        default:
            $response = ['status' => 'error', 'message' => 'Invalid action'];
            break;
    }
}

echo json_encode($response);
