<?php
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . "/../../../app/controllers/AnnouncementController.php";
require_once __DIR__ . "/../../../app/models/User.php";
require_once __DIR__ . "/../../../database/db.php";


$user = new User(getDb());
$announcementId = basename($_SERVER['REQUEST_URI']);
$announcementController = new AnnouncementController();




$announcement = $announcementController->getAnnouncementById($announcementId);
$readStat = $announcementController->getAnnouncementReadStats($announcementId);
$admin_name = $user->getAdminById($announcement['posted_by']);


if ($announcement) {
    echo json_encode([
        'announcement' => [
            'announcement_id' => $announcement['announcement_id'],
            'title' => htmlspecialchars($announcement['title']),
            'content' => $announcement['content'], // Content is already safe from Summernote
            'priority' => $announcement['priority'],
            'target_audience' => $announcement['target_audience'],
            'date_posted' => $announcement['date_posted'],
            'read_count' => $readStat['read_count'],
            'total_users' => $readStat['total_students'],
            'posted_by' => $admin_name['first_name'] . ' ' . $admin_name['last_name'] . ' (' . $admin_name['access_level'] . ')',
        ],
    ]);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Announcement not found']);
}
