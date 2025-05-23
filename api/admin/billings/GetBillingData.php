<?php
require_once __DIR__. "/../../../app/controllers/BillingController.php";


header("content-type: application/json; charset=utf-8");

$billingController = new BillingController();
$billingData = $billingController->getBillingData();
echo json_encode(["data" => $billingData["data"]]);   
