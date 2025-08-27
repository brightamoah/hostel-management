<?php
require_once __DIR__ . "/../../app/controllers/VisitorController.php";
header('Content-Type: application/json');

$visitor_id = $id;
$visitor = new VisitorController();
$visitor->view($visitor_id);
