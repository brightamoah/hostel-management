<?php
require_once __DIR__ . "/../../app/controllers/VisitorController.php";

$visitor_id = $id;

$controller = new VisitorController();
$controller->getVisitorLogs($visitor_id);
     