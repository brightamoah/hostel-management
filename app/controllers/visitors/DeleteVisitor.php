<?php
require_once "./app/controllers/VisitorController.php";

header('Content-Type: application/json');

$controller = new VisitorController();
$controller->delete($id);