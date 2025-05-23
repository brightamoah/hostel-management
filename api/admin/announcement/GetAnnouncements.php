<?php

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . "/../../../app/controllers/AnnouncementController.php";

$announcementController = new AnnouncementController();
$announcements = $announcementController->getAllAnnouncements();

$data = [];
foreach ($announcements as $announcement) {
    $data[] = [
        'announcement_id' => $announcement['announcement_id'],
        'title' => htmlspecialchars($announcement['title']),
        'posted_by_name' => htmlspecialchars($announcement['posted_by_name']),
        'priority' => $announcement['priority'],
        'target_audience' => $announcement['target_audience'],
        'date_posted' => $announcement['date_posted'],
        'content' => htmlspecialchars($announcement['content']), // For view modal
    ];
}

echo json_encode([
    'data' => $data,
]);
