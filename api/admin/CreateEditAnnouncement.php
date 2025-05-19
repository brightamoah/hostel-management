<?php

require_once __DIR__ . "/../../app/controllers/AnnouncementController.php";

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid request'];

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    $response['message'] = 'Unauthorized access';
    echo json_encode($response);
    exit;
}

$announcementController = new AnnouncementController();
$adminId = $_SESSION['user']['admin_id'] ?? 0;

try {
    $action = $action_type ?? '';
    if (empty($action)) {
        throw new Exception('Invalid action');
    }


    if (!in_array($action, ['create', 'edit', 'delete', 'fetch_targets'])) {
        throw new Exception('Invalid action');
    }

    //echo the action
    echo json_encode(['action' => $action]);

    if ($action === 'create') {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $priority = $_POST['priority'] ?? 'Medium';
        $targetMode = $_POST['target_mode'] ?? 'bulk';
        $targetAudience = $_POST['bulk_target_audience'] ?? '';
        $specificTargetType = $_POST['specific_target_type'] ?? '';
        $specificTargetId = $_POST['specific_target_id'] ?? '';

        if (empty($title) || empty($content)) {
            throw new Exception('Title and content are required');
        }

        if ($targetMode === 'bulk') {
            if (!in_array($targetAudience, ['All', 'Students', 'Admins'])) {
                throw new Exception('Invalid bulk target audience');
            }
            $success = $announcementController->createAnnouncement($adminId, $title, $content, $priority, $targetAudience);
        } else {
            if (!in_array($specificTargetType, ['student', 'admin', 'building', 'room']) || empty($specificTargetId)) {
                throw new Exception('Invalid specific target');
            }
            $success = $announcementController->createAnnouncement($adminId, $title, $content, $priority, 'Specific', $specificTargetType, $specificTargetId);
        }

        if ($success) {
            $response = ['status' => 'success', 'message' => 'Announcement created successfully'];
        } else {
            throw new Exception('Failed to create announcement');
        }
    } elseif ($action === 'edit') {
        $announcementId = $_POST['announcement_id'] ?? 0;
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $priority = $_POST['priority'] ?? 'Medium';
        $targetAudience = $_POST['target_audience'] ?? 'All';

        if (empty($announcementId) || empty($title) || empty($content)) {
            throw new Exception('Invalid announcement data');
        }

        if ($announcementController->updateAnnouncement($announcementId, $title, $content, $priority, $targetAudience)) {
            $response = ['status' => 'success', 'message' => 'Announcement updated successfully'];
        } else {
            throw new Exception('Failed to update announcement');
        }
    } elseif ($action === 'delete') {
        $announcementId = $_POST['announcement_id'] ?? 0;

        if (empty($announcementId)) {
            throw new Exception('Invalid announcement ID');
        }

        if ($announcementController->deleteAnnouncement($announcementId)) {
            $response = ['status' => 'success', 'message' => 'Announcement deleted successfully'];
        } else {
            throw new Exception('Failed to delete announcement');
        }
    } elseif ($action === 'fetch_targets') {
        $targetType = $_POST['target_type'] ?? '';

        if (!in_array($targetType, ['student', 'admin', 'building', 'room'])) {
            throw new Exception('Invalid target type');
        }

        $targets = $announcementController->fetchSpecificTargets($targetType);
        $response = ['status' => 'success', 'message' => 'Targets fetched successfully', 'data' => $targets];
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
