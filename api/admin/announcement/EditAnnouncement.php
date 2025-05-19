<?php
require_once __DIR__ . "/../../../app/controllers/AnnouncementController.php";


$announcementController = new AnnouncementController();
$announcementId = $a_id;
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

    $result = $announcementController->updateAnnouncement(
        $announcementId,
        $title,
        $content,
        $priority,
        $target_audience
    );

    if ($result) {
        header("Location: /admin/announcements?status=success");
        exit;
    } else {
        $error = "Failed to update announcement.";
    }
}
