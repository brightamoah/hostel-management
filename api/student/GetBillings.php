<?php
require_once __DIR__. "/../../app/controllers/BillingController.php";

$controller = new BillingController();
$controller->getBillings();