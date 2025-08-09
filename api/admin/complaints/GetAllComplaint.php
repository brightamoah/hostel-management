<?php
require_once __DIR__ . "/../../../app/controllers/ComplaintController.php";
$controller = new ComplaintController();
$controller->getAllComplaints();
