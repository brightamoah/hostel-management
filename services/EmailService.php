<?php

require_once __DIR__ . "/../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


class EmailService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configureMailer();
    }

    private function configureMailer()
    {
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = 'kingshostelmgt@gmail.com';
            $this->mailer->Password = 'fnuzctkvhqqdafjk';
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this->mailer->Port = 465;
            $this->mailer->setFrom('no-reply@kingshostelmgt.com', 'Kings Hostel Management');
            $this->mailer->isHTML(false);
        } catch (Exception $e) {
            error_log("Failed to configure mailer: " . $e->getMessage());
        }
    }

    public function sendEmail($to, $subject, $body, $billing_id = null, $attach_invoice = false)
    {
        try {
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;

            if ($attach_invoice && $billing_id) {
                $invoice_path = $this->getInvoicePath($billing_id);
                if ($invoice_path) {
                    $this->mailer->addAttachment($invoice_path);
                }
            }

            $this->mailer->send();
        } catch (Exception $e) {
            error_log("Failed to send email: " . $e->getMessage());
        }
    }

    private function getInvoicePath($billing_id)
    {
        try {
            $invoice_dir = __DIR__ . "/../invoices/";
            $invoice_file = "invoice_$billing_id.pdf";
            $invoice_path = "$invoice_dir$invoice_file";

            if (file_exists($invoice_path)) {
                return $invoice_path;
            }

            error_log("Invoice file not found: $invoice_path");
            return null;
        } catch (Exception $e) {
            error_log("Error getting invoice path: " . $e->getMessage());
            return null;
        }
    }
}
