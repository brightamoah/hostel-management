<?php

require_once __DIR__ .  "/../../../app/controllers/VisitorController.php";

header("Content-Type: application/json");

$visitor_id = $id;
$controller = new VisitorController();
$controller->edit($id);
