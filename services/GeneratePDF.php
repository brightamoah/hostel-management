<?php
ob_start();

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../app/models/Billing.php";
require_once __DIR__ . "/../utils/format_currency.php";

use Dompdf\Dompdf;
use Dompdf\Options;

// header("Content-Type: application/json; charset=utf-8");

$action = $_GET['action'] ?? '';
$billing_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : null;
$student_email = $_GET['email'] ?? '';

// Clean any output that might have been generated
if ($action === 'email') {
    ob_clean();
    header("Content-Type: application/json; charset=utf-8");
}


if (!$billing_id) {
    if ($action === 'email') {
        ob_clean();
        header("Content-Type: application/json; charset=utf-8");
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid or missing billing ID']);
        exit;
    }
    header("Content-Type: application/json; charset=utf-8");
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid or missing billing ID']);
    exit;
}

if ($action !== 'download' && $action !== 'email') {
    ob_clean();
    header("Content-Type: application/json; charset=utf-8");
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
    exit;
}

$billingModel = new Billing();
$billingData = $billingModel->getBillingById($billing_id);

if ($billingData === null) {
    if ($action === 'email') {
        ob_clean();
        header("Content-Type: application/json; charset=utf-8");
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Billing record not found']);
        exit;
    }
    header("Content-Type: application/json; charset=utf-8");
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Billing record not found']);
    exit;
}

// Generate HTML for PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #' . sprintf("INV-%06d", $billingData['billing_id']) . '</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 12px; 
            margin: 0; 
            padding: 20px;
            color: #333;
        }
        .invoice-content { 
            max-width: 100%; 
            margin: 0 auto; 
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px;
            border-bottom: 2px solid #986886;
            padding-bottom: 15px;
        }
        .header h2 { 
            margin: 0; 
            color: #986886; 
            font-size: 24px;
        }
        .header p { 
            margin: 5px 0; 
            color: #666;
        }
        .invoice-info { 
            width: 100%; 
            margin-bottom: 20px;
        }
        .invoice-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-info td {
            padding: 8px 0;
            vertical-align: top;
        }
        .invoice-info .left {
            width: 50%;
            padding-right: 20px;
        }
        .invoice-info .right {
            width: 50%;
            text-align: right;
        }
        .billing-to {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #986886;
        }
        .billing-to h4 {
            margin: 0 0 10px 0;
            color: #986886;
        }
        .billing-to p {
            margin: 3px 0;
        }
        .items-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0;
        }
        .items-table th, .items-table td { 
            border: 1px solid #dee2e6; 
            padding: 12px 8px; 
            text-align: left;
        }
        .items-table th { 
            background-color: #986886; 
            color: white;
            font-weight: bold;
        }
        .items-table .text-right { 
            text-align: right; 
        }
        .items-table .text-center { 
            text-align: center; 
        }
        .totals-section {
            margin-top: 20px;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-left: auto;
            max-width: 300px;
        }
        .totals-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #dee2e6;
        }
        .totals-table .label {
            font-weight: bold;
            text-align: right;
            width: 60%;
        }
        .totals-table .amount {
            text-align: right;
            width: 40%;
        }
        .totals-table .total-row {
            border-top: 2px solid #986886;
            font-weight: bold;
            font-size: 14px;
        }
        .payment-info {
            margin: 30px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .payment-info h4 {
            margin: 0 0 15px 0;
            color: #986886;
        }
        .payment-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .payment-info td {
            padding: 5px 0;
        }
        .payment-info .label {
            font-weight: bold;
            width: 40%;
        }
        .transactions-section {
            margin: 30px 0;
        }
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
        }
        .transactions-table th,
        .transactions-table td {
            border: 1px solid #dee2e6;
            padding: 10px 8px;
        }
        .transactions-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .transactions-table .text-center {
            text-align: center;
            font-style: italic;
            color: #666;
        }
        .terms-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .terms-section h4 {
            margin: 0 0 10px 0;
            color: #986886;
        }
        .terms-section ol {
            margin: 0;
            padding-left: 20px;
        }
        .terms-section li {
            margin: 5px 0;
        }
        .strong { font-weight: bold; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="invoice-content">
        <!-- Header -->
        <div class="header">
            <h2>Kings Hostel Management</h2>
            <p>University Campus, Accra, Ghana</p>
            <p>kingshostelmgt@gmail.com | +233 30 277 8899</p>
        </div>

        <!-- Invoice Information -->
        <div class="invoice-info">
            <table>
                <tr>
                    <td class="left">
                        <p><span class="strong">INVOICE ID:</span> ' . sprintf("INV-%06d", $billingData['billing_id']) . '</p>
                        <p><span class="strong">Date Issued:</span> ' . (new DateTime($billingData['date_issued']))->format('F j, Y') . '</p>
                        <p><span class="strong">Due Date:</span> ' . (new DateTime($billingData['date_due']))->format('F j, Y') . '</p>
                    </td>
                    <td class="right">
                        <p><span class="strong">Status:</span> ' . htmlspecialchars($billingData['status']) . '</p>
                        <p><span class="strong">Academic Period:</span> ' . htmlspecialchars($billingData['academic_period'] ?? 'N/A') . '</p>
                        <p><span class="strong">Purpose:</span> ' . htmlspecialchars($billingData['purpose'] ?? 'Hostel Fee') . '</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Billed To -->
        <div class="billing-to">
            <h4>Billed To:</h4>
            <p><span class="strong">' . htmlspecialchars($billingData['student_name']) . '</span></p>
            <p>Student ID: ' . htmlspecialchars($billingData['student_id']) . '</p>
            <p>Email: ' . htmlspecialchars($billingData['student_email']) . '</p>
            <p>Phone: ' . htmlspecialchars($billingData['student_phone']) . '</p>
        </div>

        <!-- Invoice Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 10%;">#</th>
                    <th style="width: 70%;">Description</th>
                    <th class="text-right" style="width: 20%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>' . htmlspecialchars($billingData['description'] ?: $billingData['purpose'] ?: 'Hostel Fee') . '</td>
                    <td class="text-right">' . formatCurrency($billingData['amount']) . '</td>
                </tr>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="amount">' . formatCurrency($billingData['amount']) . '</td>
                </tr>
                <tr>
                    <td class="label">Tax (0%):</td>
                    <td class="amount">₵0.00</td>
                </tr>
                <tr class="total-row">
                    <td class="label">Total:</td>
                    <td class="amount">' . formatCurrency($billingData['amount']) . '</td>
                </tr>
                <tr>
                    <td class="label">Amount Paid:</td>
                    <td class="amount" style="color: #28a745;">' . formatCurrency($billingData['paid_amount']) . '</td>
                </tr>
                <tr class="total-row">
                    <td class="label">Balance Due:</td>
                    <td class="amount" style="color: #dc3545;">' . formatCurrency($billingData['amount'] - $billingData['paid_amount']) . '</td>
                </tr>
            </table>
        </div>

        <!-- Payment Information -->
        <div class="payment-info">
            <h4>Payment Information</h4>
            <table>
                <tr>
                    <td class="label">Bank Name:</td>
                    <td>Ghana Commercial Bank</td>
                </tr>
                <tr>
                    <td class="label">Account Name:</td>
                    <td>Kings Hostel Management</td>
                </tr>
                <tr>
                    <td class="label">Account Number:</td>
                    <td>1234567890</td>
                </tr>
                <tr>
                    <td class="label">Mobile Money:</td>
                    <td>+233 54 968 4848</td>
                </tr>
            </table>
        </div>

        <!-- Transaction History -->
        <div class="transactions-section">
            <h4>Transaction History</h4>
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Payment Method</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>';

if (!empty($billingData['transactions'])) {
    foreach ($billingData['transactions'] as $transaction) {
        $html .= '
                    <tr>
                        <td>' . (new DateTime($transaction['payment_date']))->format('M d, Y') . '</td>
                        <td>' . htmlspecialchars($transaction['payment_method']) . '</td>
                        <td class="text-right">' . formatCurrency($transaction['amount']) . '</td>
                    </tr>';
    }
} else {
    $html .= '
                    <tr>
                        <td colspan="3" class="text-center">No payment transactions recorded</td>
                    </tr>';
}

$html .= '
                </tbody>
            </table>
        </div>

        <!-- Terms & Conditions -->
        <div class="terms-section">
            <h4>Terms & Conditions</h4>
            <ol>
                <li>Payment is due within 30 days of invoice date.</li>
                <li>Late payments will incur a 5% penalty fee.</li>
                <li>No refunds will be issued after the academic term begins.</li>
                <li>For any payment inquiries, please contact our billing department.</li>
            </ol>
        </div>
    </div>
</body>
</html>';



$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', false); // Disable remote resources
$options->set('debugKeepTemp', false);
$options->set('debugCss', false);
$options->set('debugLayout', false);
$options->set('debugLayoutLines', false);
$options->set('debugLayoutBlocks', false);
$options->set('debugLayoutInline', false);
$options->set('debugLayoutPaddingBox', false);
$dompdf = new Dompdf($options);
// Load HTML content
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Handle the action
if ($action === 'download') {
    // Stream the PDF to the browser
    $dompdf->stream("invoice_INV-" . sprintf("%06d", $billing_id) . ".pdf", ["Attachment" => true]);
    exit;
} elseif ($action === 'email') {
    if (empty($student_email)) {
        ob_clean();
        header("Content-Type: application/json; charset=utf-8");
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Student email is required']);
        exit;
    }

    // Save PDF to a temporary file
    $output = $dompdf->output();
    $temp_file = sys_get_temp_dir() . "/invoice_INV-" . sprintf("%06d", $billing_id) . ".pdf";
    file_put_contents($temp_file, $output);

    // Send email with attachment
    $emailService = new EmailService();
    $subject = "Invoice #INV-" . sprintf("%06d", $billing_id) . " from Kings Hostel";
    $message = '<!DOCTYPE html>
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

    // Clean any output and return JSON response
    ob_clean();
    header("Content-Type: application/json; charset=utf-8");


    if ($email_result['success']) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Invoice emailed successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to send invoice email: ' . ($email_result['error'] ?? 'Unknown error')]);
    }
    exit;
}
