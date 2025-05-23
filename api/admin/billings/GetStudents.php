<?php
require_once __DIR__. "/../../../app/models/Billing.php";

header("Content-Type: application/json; charset=UTF-8");

$model = new Billing();
$students = $model->getStudents();
$students = json_encode($students);
echo $students;