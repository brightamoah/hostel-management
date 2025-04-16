<?php
require_once "./app/controllers/VisitorController.php";
header('Content-Type: application/json');

$visitor = new VisitorController();
 $visitor->view($id);

