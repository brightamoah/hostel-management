<?php
require_once __DIR__ . "/../../../app/controllers/BillingController.php";


$billing_id = $bill_id;

$billingController = new BillingController();
$billingController->initializePayment($billing_id);
