<?php
ob_start();

require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/../../../app/models/Billing.php";
require_once __DIR__ . "/../../../utils/format_currency.php";
require_once __DIR__ . "/../../../app/models/PDFGenerator.php";
require_once __DIR__ . "/../../../services/EmailService.php";

ob_clean();
header("Content-Type: application/json; charset=utf-8");


$billing_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : null;
$student_email = $_GET['email'] ?? '';


if (!$billing_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid or missing billing ID']);
    exit;
}

if (empty($student_email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Student email is required']);
    exit;
}

try {
    $billingModel = new Billing();
    $billingDataResponse = $billingModel->getBillingById($billing_id);

    if (!$billingDataResponse || !isset($billingDataResponse['details'])) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Billing record not found']);
        exit;
    }

    $billingData = $billingDataResponse['details'];

    // Generate PDF
    $pdfGenerator = new PDFGenerator();
    $temp_file = $pdfGenerator->generateInvoicePDFFile($billing_id);

    // Prepare email content
    $subject = "Invoice #INV-" . sprintf("%06d", $billing_id) . " from Kings Hostel";
    $message = buildEmailHTML($billingData, $billing_id);

    // Send email
    $emailService = new EmailService();
    $email_result = $emailService->sendEmail(
        $student_email,
        $subject,
        $message,
        $billing_id,
        true,
        $temp_file
    );

    // Clean up temporary file
    if (file_exists($temp_file)) {
        unlink($temp_file);
    }

    // Return response
    if ($email_result['success']) {
        echo json_encode(['success' => true, 'message' => 'Invoice emailed successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to send invoice email: ' . ($email_result['error'] ?? 'Unknown error')]);
    }
} catch (Exception $e) {
    // Clean up temp file if it exists
    if (isset($temp_file) && file_exists($temp_file)) {
        unlink($temp_file);
    }

    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}


function buildEmailHTML($billingData, $billing_id)
{
    return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .email-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #986886;
        }
        .header h1 {
            color: #986886;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .invoice-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #986886;
        }
        .invoice-details h3 {
            color: #986886;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .invoice-details ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .invoice-details li {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .invoice-details li:last-child {
            border-bottom: none;
        }
        .invoice-details .label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        .amount-due {
            background-color: #dc3545;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            text-align: center;
            margin: 15px 0;
            font-weight: bold;
        }
        .payment-options {
            margin: 25px 0;
        }
        .payment-options h3 {
            color: #986886;
            margin-bottom: 15px;
        }
        .payment-method {
            background-color: #e8f4f8;
            padding: 15px;
            border-radius: 6px;
            margin: 10px 0;
        }
        .payment-method h4 {
            margin: 0 0 10px 0;
            color: #2c3e50;
        }
        .payment-method ul {
            margin: 0;
            padding-left: 20px;
        }
        .contact-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
        }
        .contact-info h4 {
            color: #986886;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
        }
        .footer .signature {
            margin-bottom: 15px;
        }
        .footer .company-info {
            color: #666;
            font-size: 14px;
        }
        .disclaimer {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 10px;
            border-radius: 4px;
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Kings Hostel Management</h1>
            <p>University Campus, Accra, Ghana</p>
            <p>kingshostelmgt@gmail.com | +233 30 277 8899</p>
        </div>

        <div class="greeting">
            <p>Dear <strong>' . htmlspecialchars($billingData['student_name']) . '</strong>,</p>
            <p>We hope this message finds you well.</p>
            <p>Please find attached your invoice from Kings Hostel Management. We kindly request your prompt attention to this matter.</p>
        </div>

        <div class="invoice-details">
            <h3>📄 Invoice Details</h3>
            <ul>
                <li><span class="label">Invoice ID:</span> INV-' . sprintf("%06d", $billing_id) . '</li>
                <li><span class="label">Date Issued:</span> ' . (new DateTime($billingData['date_issued']))->format('F j, Y') . '</li>
                <li><span class="label">Due Date:</span> ' . (new DateTime($billingData['date_due']))->format('F j, Y') . '</li>
                <li><span class="label">Total Amount:</span> ' . formatCurrency($billingData['amount']) . '</li>
                <li><span class="label">Amount Paid:</span> ' . formatCurrency($billingData['paid_amount']) . '</li>
            </ul>
            
            <div class="amount-due">
                <strong>Amount Due: ' . formatCurrency($billingData['amount'] - $billingData['paid_amount']) . '</strong>
            </div>
        </div>

        <div class="payment-options">
            <h3>💳 Payment Options</h3>
            <p>You can make your payment through any of the following convenient methods:</p>
            
            <div class="payment-method">
                <h4>🏦 Bank Transfer</h4>
                <ul>
                    <li><strong>Bank:</strong> Ghana Commercial Bank</li>
                    <li><strong>Account Name:</strong> Kings Hostel Management</li>
                    <li><strong>Account Number:</strong> 1234567890</li>
                </ul>
            </div>

            <div class="payment-method">
                <h4>📱 Mobile Money</h4>
                <ul>
                    <li><strong>MTN/Vodafone:</strong> +233 54 968 4848</li>
                    <li><strong>Reference:</strong> INV-' . sprintf("%06d", $billing_id) . '</li>
                </ul>
            </div>
        </div>

        <div class="contact-info">
            <h4>📞 Need Help?</h4>
            <p>For any questions regarding your invoice or payment options, please contact our billing department:</p>
            <p><strong>Email:</strong> kingshostelmgt@gmail.com</p>
            <p><strong>Phone:</strong> +233 30 277 8899</p>
            <p><strong>Office Hours:</strong> Monday - Friday, 8:00 AM - 5:00 PM</p>
        </div>

        <div class="footer">
            <div class="signature">
                <p>Thank you for choosing Kings Hostel. We appreciate your prompt attention to this matter.</p>
                <p><strong>Best regards,</strong></p>
                <p><strong>Kings Hostel Management Team</strong></p>
            </div>
            
            <div class="company-info">
                <p>Kings Hostel Management<br>
                University Campus, Accra, Ghana<br>
                Email: kingshostelmgt@gmail.com | Phone: +233 30 277 8899</p>
            </div>
        </div>

        <div class="disclaimer">
            <p>⚠️ This is an automated message. Please do not reply directly to this email. For inquiries, use the contact information provided above.</p>
        </div>
    </div>
</body>
</html>';
}
