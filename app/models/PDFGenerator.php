<?php
require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../models/Billing.php";
require_once __DIR__ . "/../../utils/format_currency.php";

use Dompdf\Dompdf;
use Dompdf\Options;

class PDFGenerator
{
    private $billingModel;

    public function __construct()
    {
        $this->billingModel = new Billing();
    }

    /**
     * Generate PDF for a billing record
     */

    public function generateInvoicePDF($billing_id, $output_to_browser = false)
    {
        $billingData = $this->billingModel->getBillingById($billing_id);

        if (!$billingData) {
            throw new Exception("Billing record not found for ID: $billing_id");
        }

        $html = $this->buildInvoiceHTML($billingData);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('debugKeepTemp', false);
        $options->set('debugCss', false);
        $options->set('debugLayout', false);
        $options->set('debugLayoutLines', false);
        $options->set('debugLayoutBlocks', false);
        $options->set('debugLayoutInline', false);
        $options->set('debugLayoutPaddingBox', false);


        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        if ($output_to_browser) {
            $dompdf->stream("invoice_INV-" . sprintf("%06d", $billing_id) . ".pdf", ["Attachment" => true]);
            exit;
        }

        return $dompdf->output();
    }


    /**
     * Save PDF to temporary file and return path
     */
    public function generateInvoicePDFFile($billing_id)
    {
        $output = $this->generateInvoicePDF($billing_id);
        $temp_file = sys_get_temp_dir() . "/invoice_INV-" . sprintf("%06d", $billing_id) . "_" . time() . ".pdf";
        file_put_contents($temp_file, $output);
        return $temp_file;
    }


    /**
     * Build the HTML content for the invoice
     */
    private function buildInvoiceHTML($billingData)
    {

        $html = '<!DOCTYPE html>
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

        return $html;
    }
}


// Handle direct access for PDF download

