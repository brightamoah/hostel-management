<?php
// filepath: c:\xampp\htdocs\api\student\MarkAnnouncementAsRead.php
require_once __DIR__ . "/../../app/controllers/AnnouncementController.php";

// Set proper content type for JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

// Check if announcement ID is provided
if (empty($_POST['announcement_id'])) {
    echo json_encode(['success' => false, 'message' => 'Announcement ID is required']);
    exit;
}

// Get the announcement ID directly
$announcementId = $_POST['announcement_id'];
$studentId = $_SESSION['user']['student_id'];

// Mark the announcement as read
$controller = new AnnouncementController();
$result = $controller->markAnnouncementAsRead($announcementId, $studentId);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Announcement marked as read']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to mark announcement as read']);
}
