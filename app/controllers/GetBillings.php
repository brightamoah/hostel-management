<?php
require_once "./app/controllers/BillingController.php";

$controller = new BillingController();
$controller->getBillings();