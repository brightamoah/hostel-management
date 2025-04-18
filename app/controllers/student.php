<?php
require_once "./database/db.php";
require_once "./app/models/Student.php";
require_once "./app/models/Visitor.php";

// Check if user is authenticated
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Student') {
    header("Location: /login");
    exit();
}

$user_id = $_SESSION['user']['user_id']; // Get the logged-in user's ID
$db = new Database();
$conn = $db->connect();
$student = new Student($conn);
$visitor = new Visitor();

// Fetch student data
$first_name = $student->getFirstName($user_id);
$room_allocation = $student->getRoomAllocation($user_id);
$total_paid = $student->getTotalPaid($user_id);
$pending_balance = $student->getPendingBalance($user_id);
$open_requests = $student->getOpenMaintenanceRequests($user_id);
$total_visitors = $visitor->getVisitorCountByStudent($_SESSION['user']['student_id'] ?? 0);
$payment_status = $student->getPaymentStatusSummary($user_id);
