<?php
require_once __DIR__ . "/../../../app/models/Billing.php";
require_once __DIR__ . "/../../../utils/hostel_helpers.php";

header("Content-Type: application/json; charset=UTF-8");

// Check if user is authenticated and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized: Admin access required']);
    exit;
}

try {
    $model = new Billing();
    $students = $model->getStudents();
    echo json_encode($students);
} catch (Exception $e) {
    error_log("Error in GetStudents API: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to retrieve students']);
}
