<?php
require_once __DIR__. "/../controllers/BillingController.php";

// header('text/html; charset=utf-8');

$billingController = new BillingController();
$billingData = $billingController->getBillingData();


$stats = $billingData["stats"];

// $total_billings = $billingData["stats"]["total_billings"];
// $total_paid = $billingData["stats"]["total_paid"];
// $overdue_billings = $billingData["stats"]["overdue_invoices"];
// $pending_billings = $billingData["stats"]["pending_invoices"];
// $collection_rate = $billingData["stats"]["collection_rate"];
// $total_invoices = $billingData["stats"]["total_invoices"];

// $total_billing_change = $billingData["stats"]["total_change"];
// $paid_change = $billingData["stats"]["paid_change"];
// $pending_change = $billingData["stats"]["pending_change"];
// $overdue_change = $billingData["stats"]["overdue_change"];

// $paid_percentage = $billingData["stats"]["paid_percentage"];
// $pending_percentage = $billingData["stats"]["pending_percentage"];
// $overdue_percentage = $billingData["stats"]["overdue_percentage"];


// stats": {
//     "total_billings": 12000,
//     "total_paid": 1900,
//     "paid_invoices": 0,
//     "pending_invoices": 10100,
//     "overdue_invoices": 0,
//     "total_invoices": 4,
//     "paid_count": 0,
//     "pending_count": 4,
//     "overdue_count": 0,
//     "total_change": 33.3,
//     "paid_change": 0,
//     "pending_change": 42.3,
//     "overdue_change": 0,
//     "collection_rate": 15.8,
//     "paid_percentage": 0,
//     "pending_percentage": 100,
//     "overdue_percentage": 0
//   }