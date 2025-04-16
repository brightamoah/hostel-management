<?php

require_once "./app/models/Visitor.php";

// Initialize Visitor model
$visitorModel = new Visitor();

// Fetch visitor statistics
$user = $_SESSION["user"];
$user_id = $user['user_id'] ?? null;
$role = $user['role'] ?? null;

if (!$user_id || $role !== 'Student') {
    header("Location: /login");
    exit();
}

// Fetch the student_id based on user_id
$db = new Database();
$conn = $db->connect();
$query = "SELECT student_id FROM students WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$student_id = $student['student_id'] ?? null;
$stmt->close();
$db->close();

if (!$student_id) {
    die("Student not found.");
}

// // Fetch visitor statistics
$totalVisitors = $visitorModel->getVisitorCountByStudent($student_id);
$approvedVisitors = $visitorModel->getVisitorCountByStudentAndStatus($student_id, 'Approved');
$pendingVisitors = $visitorModel->getVisitorCountByStudentAndStatus($student_id, 'Pending');
$checkedInVisitors = $visitorModel->getVisitorCountByStudentAndStatus($student_id, 'Checked-In');
$checkedOutVisitors = $visitorModel->getVisitorCountByStudentAndStatus($student_id, 'Checked-Out');