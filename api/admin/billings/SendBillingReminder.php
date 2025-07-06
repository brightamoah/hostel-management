<?php
require_once __DIR__ . "/../../../app/controllers/BillingController.php";

header('Content-Type: application/json');

$billingController = new BillingController();

$billingController->sendBillingReminder();
