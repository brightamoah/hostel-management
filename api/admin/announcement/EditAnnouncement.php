<?php
require_once __DIR__ . "/../../../app/controllers/AnnouncementController.php";


$announcementController = new AnnouncementController();
$announcementId = $a_id ?? 0;
$announcement = $announcementController->getAnnouncementById($announcementId);

if (!$announcement) {
    header("Location: /admin/announcements?error=Announcement not found");
    exit;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $priority = $_POST['priority'] ?? 'Medium';
    $target_audience = $_POST['target_audience'] ?? 'All';

    if (empty($title) || empty($content)) {
        $error = "Title and content are required.";
        error_log("Edit announcement validation error: $error");
    } else {

        $result = $announcementController->updateAnnouncement(
            $announcementId,
            $title,
            $content,
            $priority,
            $target_audience
        );

        if ($result) {
            error_log("Announcement updated successfully: ID $announcementId");

            // For AJAX requests, return JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success']);
                exit;
            }
            header("Location: /admin/announcements?status=success");
            exit;
        } else {
            $error = "Failed to update announcement.";
            error_log("Failed to update announcement: $announcementId");
        }
    }
}
