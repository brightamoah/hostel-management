<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../../app/controllers/AnnouncementController.php"; 

// Check if user is logged in and is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

$db = new Database();
$conn = $db->connect();
$adminId = $_SESSION['user']['admin_id'] ?? 0;

// Process form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Create new announcement
    if ($action === 'create') {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $priority = $_POST['priority'];
        $targetAudience = $_POST['target_audience'];

        if (empty($title) || empty($content)) {
            $_SESSION['error_message'] = "Title and content are required!";
        } else {
            $query = "INSERT INTO announcements 
                     (posted_by, title, content, priority, target_audience) 
                     VALUES (?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($query);
            $stmt->bind_param("issss", $adminId, $title, $content, $priority, $targetAudience);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Announcement created successfully!";
                header("Location: announcements.php");
                exit;
            } else {
                $_SESSION['error_message'] = "Error creating announcement: " . $conn->error;
            }
        }
    }

    // Update existing announcement
    elseif ($action === 'update') {
        $announcementId = $_POST['announcement_id'];
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $priority = $_POST['priority'];
        $targetAudience = $_POST['target_audience'];

        if (empty($title) || empty($content)) {
            $_SESSION['error_message'] = "Title and content are required!";
        } else {
            $query = "UPDATE announcements 
                     SET title = ?, content = ?, priority = ?, target_audience = ? 
                     WHERE announcement_id = ?";

            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssi", $title, $content, $priority, $targetAudience, $announcementId);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Announcement updated successfully!";
                header("Location: announcements.php");
                exit;
            } else {
                $_SESSION['error_message'] = "Error updating announcement: " . $conn->error;
            }
        }
    }

    // Delete announcement
    elseif ($action === 'delete') {
        $announcementId = $_POST['announcement_id'];

        // First delete related read records to maintain referential integrity
        $deleteReadsQuery = "DELETE FROM announcement_reads WHERE announcement_id = ?";
        $stmt = $conn->prepare($deleteReadsQuery);
        $stmt->bind_param("i", $announcementId);
        $stmt->execute();

        // Then delete the announcement
        $query = "DELETE FROM announcements WHERE announcement_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $announcementId);

        $response = array();

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Announcement deleted successfully!";
        } else {
            $response['success'] = false;
            $response['message'] = "Error deleting announcement: " . $conn->error;
        }

        // Send JSON response for AJAX calls
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Fetch all announcements for admin view
$query = "SELECT a.announcement_id, a.title, a.content, a.priority, 
          a.target_audience, a.date_posted, 
          CONCAT(adm.first_name, ' ', adm.last_name) as posted_by_name
          FROM announcements a
          JOIN admins adm ON a.posted_by = adm.admin_id
          ORDER BY a.date_posted DESC";

$result = $conn->query($query);
$announcements = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}

// For specific audience announcements (if implemented)
$studentsQuery = "SELECT student_id, CONCAT(first_name, ' ', last_name) as student_name FROM students WHERE resident_status = 'Active'";
$studentsResult = $conn->query($studentsQuery);
$students = [];

if ($studentsResult) {
    while ($row = $studentsResult->fetch_assoc()) {
        $students[] = $row;
    }
}
