<?php
require_once __DIR__ . "/../../../app/controllers/VisitorController.php";

header('Content-Type: application/json');

$controller = new VisitorController();
$controller->delete($id);
