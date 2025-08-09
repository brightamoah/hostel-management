<?php
require_once __DIR__ . "/../../../app/controllers/ComplaintController.php";

$complaint_id = $c_id;

$complaintController = new ComplaintController();
$complaintController->updateComplaintStatus($complaint_id);
