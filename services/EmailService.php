<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../app/models/PDFGenerator.php";
// require_once __DIR__ . "/../api/admin/billings/EmailHandler.php";
require_once __DIR__ . "/../utils/format_currency.php";

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
            $this->mailer->isHTML(true);
        } catch (Exception $e) {
            error_log("Failed to configure mailer: " . $e->getMessage());
            return false;
        }
        return true;
    }

    public function sendEmail($to, $subject, $body, $billing_id = null, $attach_invoice = false, $attachment_path = null)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();


            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;

            // Add attachment if provided
            if ($attach_invoice && $attachment_path && file_exists($attachment_path)) {
                $this->mailer->addAttachment($attachment_path, "invoice_INV-" . sprintf("%06d", $billing_id) . ".pdf");
            }

            $this->mailer->send();

            return [
                'success' => true,
                'message' => 'Email sent successfully',
                'email_sent' => true
            ];
        } catch (Exception $e) {
            error_log("Failed to send email: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage(),
                'email_sent' => false,
                'error' => $e->getMessage()
            ];
        } finally {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
        }
    }

    private function formatInvoiceId($billing_id)
    {
        return str_pad($billing_id, 6, '0', STR_PAD_LEFT);
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

    public function sendInvoiceNotification($to, $billing_id, $student_name, $amount, $due_date, $description, $billing_type)
    {
        try {
            $pdfGenerator = new PDFGenerator();
            $temp_file = $pdfGenerator->generateInvoicePDFFile($billing_id);


            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            $this->mailer->addAddress($to);
            $this->mailer->Subject = "New Billing #INV-{$this->formatInvoiceId($billing_id)} from Kings Hostel Management";

            $formatted_amount = formatCurrency($amount);
            $formatted_due_date = date('F j, Y H:i', strtotime($due_date));

            // Add PDF attachment
            if ($temp_file && file_exists($temp_file)) {
                $this->mailer->addAttachment($temp_file, "invoice_INV-" . sprintf("%06d", $billing_id) . ".pdf");
            }

            $this->mailer->Body = $this->buildNotificationHTML($student_name, $billing_id, $formatted_amount, $formatted_due_date, $description, $billing_type);

            $this->mailer->send();

            // Clean up temporary PDF file
            if ($temp_file && file_exists($temp_file)) {
                unlink($temp_file);
            }

            return [
                'success' => true,
                'message' => 'Invoice notification sent successfully',
                'email_sent' => true
            ];
        } catch (Exception $e) {
            // Clean up temporary PDF file even on error
            if (isset($temp_file) && $temp_file && file_exists($temp_file)) {
                unlink($temp_file);
            }

            error_log("Failed to send invoice notification: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send invoice notification: ' . $e->getMessage(),
                'email_sent' => false,
                'error' => $e->getMessage()
            ];
        } finally {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
        }
    }

    public function buildNotificationHTML($student_name, $billing_id, $formatted_amount, $formatted_due_date, $description, $billing_type)
    {

        $content = <<<HTML

            <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Notification</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #3333;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
           
        }

         .email-container {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(152, 104, 134, 0.15);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #986886 0%, #b8859c 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.03)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .logo-section {
            position: relative;
            z-index: 2;
            margin-bottom: 20px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header .subtitle {
            font-size: 16px;
            opacity: 0.9;
            position: relative;
            z-index: 2;
            font-weight: 300;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 25px;
            font-weight: 600;
        }
        
        .intro-text {
            color: #5a6c7d;
            margin-bottom: 30px;
            font-size: 16px;
            line-height: 1.7;
        }
        
        .invoice-card {
            background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
            border: 1px solid #e8ecf4;
            border-radius: 12px;
            padding: 0;
            margin: 30px 0;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(152, 104, 134, 0.08);
        }
        
        .invoice-header {
            background: linear-gradient(90deg, #986886, #b8859c);
            color: white;
            padding: 20px 25px;
            font-weight: 600;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .invoice-number {
            background: rgba(255, 255, 255, 0.2);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        
        .invoice-details {
            padding: 0;
        }
        
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .invoice-details tr:nth-child(even) {
            background-color: #f8f9fc;
        }
        
        .invoice-details th,
        .invoice-details td {
            padding: 18px 25px;
            text-align: left;
            border: none;
            font-size: 15px;
        }
        
        .invoice-details th {
            background-color: #f1f3f7;
            font-weight: 600;
            color: #2c3e50;
            width: 35%;
            border-right: 1px solid #e8ecf4;
        }
        
        .invoice-details td {
            color: #34495e;
            font-weight: 500;
        }
        
        .amount-highlight {
            font-size: 20px;
            font-weight: 700;
            color: #986886;
        }
        
        .due-date-highlight {
            color: #e74c3c;
            font-weight: 600;
        }
        
        .payment-section {
            background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            border-left: 4px solid #986886;
            box-shadow: 0 2px 8px rgba(152, 104, 134, 0.05);
        }
        
        .payment-text {
            color: #5a6c7d;
            margin-bottom: 20px;
            font-size: 15px;
            line-height: 1.6;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #986886 0%, #b8859c 100%);
            color: white;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(152, 104, 134, 0.3);
            text-align: center;
            letter-spacing: 0.5px;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(152, 104, 134, 0.4);
            text-decoration: none;
            color: white;
        }
        
        .contact-info {
            background: #f8f9fc;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
        }
        
        .contact-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .contact-details {
            color: #5a6c7d;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .contact-details a {
            color: #986886;
            text-decoration: none;
            font-weight: 500;
        }
        
        .contact-details a:hover {
            text-decoration: underline;
        }
        
        .closing {
            margin-top: 40px;
            padding-top: 25px;
            border-top: 1px solid #e8ecf4;
        }
        
        .closing-text {
            color: #5a6c7d;
            margin-bottom: 15px;
            font-size: 15px;
        }
        
        .signature {
            color: #2c3e50;
            font-weight: 600;
            font-size: 16px;
        }
        
        .footer {
            background: #2c3e50;
            color: #95a5a6;
            text-align: center;
            padding: 30px;
            font-size: 13px;
            line-height: 1.6;
        }
        
        .footer-title {
            color: #ecf0f1;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .footer a {
            color: #986886;
            text-decoration: none;
        }
        
        .footer a:hover {
            color: #b8859c;
            text-decoration: underline;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 12px;
            }
            
            .header,
            .content {
                padding: 25px 20px;
            }
            
            .invoice-details th,
            .invoice-details td {
                padding: 12px 15px;
                font-size: 14px;
            }
            
            .cta-button {
                display: block;
                text-align: center;
                margin: 0 auto;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            /* .logo {
                width: 70px;
                height: 70px;
            }
            
            .logo svg {
                width: 40px;
                height: 40px;
            } */
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo-section">
                <!-- <div class="logo">
                    <img src="cid:logo" alt="Kings Hostel Management Logo">
                    
                    
                </div> -->
                <h1>Kings Hostel Management</h1>
                <div class="subtitle">Invoice Notification</div>
            </div>
        </div>
        
        <div class="content">
            <div class="greeting">Dear {$student_name},</div>
            
            <div class="intro-text">
                We hope this message finds you well. A new invoice has been generated for your hostel account. Please review the details below and ensure timely payment to avoid any inconvenience.
            </div>
            
            <div class="invoice-card">
                <div class="invoice-header">
                    <span>Invoice Details</span>
                    <!-- <span class="invoice-number">#INV-{$this->formatInvoiceId($billing_id)}</span> -->
                </div>
                <div class="invoice-details">
                    <table>
                        <tr>
                            <th>Invoice Number</th>
                            <td><strong>#INV-{$this->formatInvoiceId($billing_id)}</strong></td>
                        </tr>
                        <tr>
                            <th>Amount Due</th>
                            <td class="amount-highlight">GHS {$formatted_amount}</td>
                        </tr>
                        <tr>
                            <th>Due Date</th>
                            <td class="due-date-highlight">{$formatted_due_date}</td>
                        </tr>
                        <tr>
                            <th>Billing Type</th>
                            <td><strong>{$billing_type}</strong></td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{$description}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- <div class="payment-section">
                <div class="payment-text">
                    <strong>Payment Instructions:</strong><br>
                    Please ensure payment is made by the due date to avoid late fees. You can make payments conveniently through your student portal or contact our office for alternative payment methods.
                </div>
                <a href="https://kingshostelmgt.com/student-portal" class="cta-button">
                    Make Payment Now
                </a>
            </div> -->
            
            <div class="contact-info">
                <div class="contact-title">Need Assistance?</div>
                <div class="contact-details">
                    If you have any questions or concerns regarding this invoice, please don't hesitate to contact our finance team:<br>
                    <strong>Email:</strong> <a href="mailto:kingshostelmgt@gmail.com">kingshostelmgt@gmail.com</a><br>
                    <strong>Phone:</strong> <a href="tel:+233549684848">+233 549 684 848</a>
                </div>
            </div>
            
            <div class="closing">
                <div class="closing-text">
                    Thank you for your prompt attention to this matter. We appreciate your continued trust in Kings Hostel Management.
                </div>
                <div class="signature">
                    Best regards,<br>
                    <strong>Kings Hostel Management Team</strong>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <div class="footer-title">Kings Hostel Management</div>
            University Campus, Accra<br>
            <a href="mailto:kingshostelmgt@gmail.com">kingshostelmgt@gmail.com</a> | 
            <a href="tel:+233549684848">+233 549 684 848</a><br>
            <a href="https://kingshostelmgt.com">www.kingshostelmgt.com</a>
        </div>
    </div>
</body>
</html>
HTML;

        return $content;
    }
}
