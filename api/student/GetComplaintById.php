<?php
require_once "./app/controllers/ComplaintController.php";


//$c_id is the complaint id gotten from the url


$complaint_id = $c_id;
$controller = new ComplaintController();
$controller->getComplaint( $complaint_id );