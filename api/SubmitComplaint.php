<?php
require_once __DIR__ . "/../app/controllers/ComplaintController.php";


$complaintController = new ComplaintController();
$complaintController->submitComplaint();
