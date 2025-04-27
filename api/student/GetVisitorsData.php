<?php
require_once __DIR__ . "/../../app/models/Visitor.php";

header("Content-Type: application/json");

// Get student ID from session
$student_id = $_SESSION['user']['student_id'] ?? 0;

$dateFilter = $_GET['dateFilter'] ?? '';

$visitorModel = new Visitor();
$visitors = $visitorModel->getVisitorsByStudent($student_id, $dateFilter);

echo $visitors;
exit;
