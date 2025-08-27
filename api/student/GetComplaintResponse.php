<?php
require_once __DIR__ . "/../../app/controllers/ComplaintController.php";


$complaint_id = $c_id;
$student_id = $_SESSION['user']['student_id'] ?? 0;

$controller = new ComplaintController();
$controller->getComplaintResponses($complaint_id);
