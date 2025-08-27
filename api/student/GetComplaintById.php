<?php
require_once __DIR__ . "/../../app/controllers/ComplaintController.php";


$complaint_id = $c_id;
$controller = new ComplaintController();
$controller->getComplaint($complaint_id);
