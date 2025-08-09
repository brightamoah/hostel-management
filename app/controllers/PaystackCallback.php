<?php
require_once __DIR__ . "/../../app/controllers/BillingController.php";


class PaystackCallback
{
    private $paymentService;
    public function __construct()
    {
        $this->paymentService = new PaymentService();
    }

    public function handleCallback()
    {
        $billingController = new BillingController();
        $billingController->verifyPayment();
    }
}


$callback = new PaystackCallback();
$callback->handleCallback();
