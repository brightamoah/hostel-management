<?php
require_once "./app/controllers/ComplaintController.php";

//$c_id is the complaint id gotten from the url

$complaint_id = $c_id;
$student_id = $_SESSION['user']['student_id'] ?? 0;

$controller = new ComplaintController();
$controller->getComplaintResponses($complaint_id);