<?php

require_once __DIR__ . "/../../../app/controllers/BillingController.php";

$billingController = new BillingController();
$billingController->recordPayment();
