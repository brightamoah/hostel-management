<?php
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../database/db.php";
require_once __DIR__ . "/../utils/load_env.php";

use Matscode\Paystack\Paystack;


class PaymentService
{
    private $paystackSecretKey;
    private $Paystack;
    private $db;

    public function __construct()
    {
        loadEnvFile();
        $this->paystackSecretKey = $_ENV['PAYSTACK_SECRETE_KEY'];

        if (empty($this->paystackSecretKey)) {
            throw new Exception('Paystack secret key not found in environment variables');
        }

        $this->Paystack = new Paystack($this->paystackSecretKey);
        $this->db = getDb();
    }


    /**
     * Initialize a payment with Paystack
     * @param int $billingId
     * @param int $studentId  
     * @param float $amount Amount in Ghana Cedis
     * @param string $email
     * @param string $purpose Dynamic purpose from billing (e.g., 'Hostel Fee', 'Maintenance Fee', 'Security Deposit', etc.)
     * @param string $description Additional description
     */
    public function initializePayment($billingId, $studentId, $amount, $email,  $purpose, $description = '')
    {
        try {
            //reference format: KH_{billingId}_{timestamp}_{studentId}_{random}
            $reference = "KH_{$billingId}_" . time() . "_{$studentId}_" . rand(1000, 9999);

            // Convert Ghana Cedis to pesewas (1 GHS = 100 pesewas)
            $amountInPesewas = $amount * 100;


            $billingDetails = $this->getBillingDetails($billingId);
            $studentDetails = $this->getStudentDetails($studentId);

            $paymentData = [
                'email' => $email,
                'amount' => $amountInPesewas,
                'reference' => $reference,
                'currency' => 'GHS',
                'callback_url' => $_ENV['PAYSTACK_CALLBACK_URL'],
                'metadata' => [
                    'billing_id' => $billingId,
                    'student_id' => $studentId,
                    'purpose' => $purpose,
                    'description' => $description ?: $billingDetails['description'] ?? '',
                    'billing_type' => $billingDetails['billing_type'] ?? 'General',
                    'student_name' => $studentDetails['full_name'] ?? '',
                    'phone_number' => $studentDetails['phone_number'] ?? '',
                    'room_number' => $studentDetails['room_number'] ?? 'N/A',
                    'amount_ghs' => number_format($amount, 2),
                    'custom_fields' => [
                        [
                            'display_name' => 'Billing ID',
                            'variable_name' => 'billing_id',
                            'value' => $billingId
                        ],
                        [
                            'display_name' => 'Purpose',
                            'variable_name' => 'purpose',
                            'value' => $purpose
                        ],
                        [
                            'display_name' => 'Amount (GHS)',
                            'variable_name' => 'amount_ghs',
                            'value' => 'GH₵' . number_format($amount, 2)
                        ],
                        [
                            'display_name' => 'Phone Number',
                            'variable_name' => 'phone_number',
                            'value' => $studentDetails['phone_number'] ?? 'N/A'
                        ]
                    ]
                ],
                'channels' => ['card', 'bank', 'ussd', 'qr', 'mobile_money'],
                'bearer' => 'account'
            ];

            $response = $this->Paystack->transaction->initialize($paymentData);

            if ($response->status) {
                // Store payment intent in database
                $this->storePaymentIntent($reference, $billingId, $studentId, $amount, $purpose, $description, 'Pending');

                return [
                    'success' => true,
                    'authorization_url' => $response->data->authorization_url,
                    'access_code' => $response->data->access_code,
                    'reference' => $reference,
                    'amount_ghs' => number_format($amount, 2),
                    'purpose' => $purpose
                ];
            }

            return [
                'success' => false,
                'error' => $response['message'] ?? 'Failed to initialize payment'
            ];
        } catch (Exception $e) {
            error_log("Paystack initialization error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Payment initialization failed: ' . $e->getMessage()
            ];
        }
    }


    /**
     * Verify payment with Paystack
     */
    public function verifyPayment($reference)
    {
        try {
            $response = $this->Paystack->transaction->verify($reference);

            if ($response->status && $response->data->status === 'success') {
                $transactionData = $response->data;

                // Convert amount back from pesewas to Ghana Cedis
                $amountInGHS = $transactionData->amount / 100;

                $this->updatePaymentStatus($reference, 'Completed', $transactionData, $amountInGHS);

                return [
                    'success' => true,
                    'data' => $transactionData,
                    'amount_ghs' => $amountInGHS
                ];
            }

            if ($response->data->status === 'failed') {
                $this->updatePaymentStatus($reference, 'Failed', $response->data);
            }

            return [
                'success' => false,
                'error' => 'Payment verification failed or payment not successful',
                'status' => $response->data->status ?? 'unknown'
            ];
        } catch (Exception $e) {
            error_log("Paystack verification error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Payment verification failed: ' . $e->getMessage()
            ];
        }
    }


    /**
     * Store payment intent in database
     */
    private function storePaymentIntent($reference, $billingId, $studentId, $amount, $purpose, $description, $status)
    {

        $validPurposes = [
            'Hostel Fee' => 'Hostel Fee',
            'Security Deposit' => 'Security Deposit',
            'Maintenance Charge' => 'Maintenance Charge',
            'Maintenance Fee' => 'Maintenance Charge', // Map this to valid ENUM
            'Penalty' => 'Penalty',
            'Late Payment Penalty' => 'Penalty', // Map this to valid ENUM
            'Other' => 'Other'
        ];

        $mappedPurpose = $validPurposes[$purpose] ?? 'Other';

        $query = "
            INSERT INTO payments (
                student_id, billing_id, amount, payment_date, transaction_reference, 
                payment_method, purpose, description, status, created_at
            ) VALUES (?, ?, ?, NOW(), ?, 'Paystack', ?, ?, ?, NOW())
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iidssss', $studentId, $billingId, $amount, $reference, $mappedPurpose, $description, $status);

        if (!$stmt->execute()) {
            error_log("Failed to store payment intent: {$stmt->error}");
        }
        $stmt->close();
    }


    /**
     * Update payment status after verification
     */
    private function updatePaymentStatus($reference, $status, $transactionData = null, $amountGHS = null)
    {
        $this->db->begin_transaction();

        try {
            // Ensure status matches ENUM values
            $validStatuses = ['Pending', 'Completed', 'Failed', 'Refunded'];
            $mappedStatus = in_array($status, $validStatuses) ? $status : 'Failed';

            $paymentMethod = 'Credit Card';
            if ($transactionData && isset($transactionData->channel)) {
                switch (strtolower($transactionData->channel)) {
                    case 'card':
                        $paymentMethod = 'Credit Card';
                        break;
                    case 'bank':
                        $paymentMethod = 'Bank Transfer';
                        break;
                    case 'mobile_money':
                        $paymentMethod = 'Mobile Money';
                        break;
                    default:
                        $paymentMethod = 'Credit Card';
                }
            }

            // Update payment record 
            $query = "
            UPDATE payments 
            SET status = ?, payment_date = NOW(), payment_method = ?
            WHERE transaction_reference = ?
        ";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param('sss', $mappedStatus, $paymentMethod, $reference);
            $stmt->execute();
            $stmt->close();

            if ($mappedStatus === 'Completed' && $transactionData) {
                // Get payment details
                $paymentQuery = "SELECT billing_id, amount FROM payments WHERE transaction_reference = ?";
                $stmt = $this->db->prepare($paymentQuery);
                $stmt->bind_param('s', $reference);
                $stmt->execute();
                $result = $stmt->get_result();
                $payment = $result->fetch_assoc();
                $stmt->close();

                if ($payment) {
                    $this->updateBillingAfterPayment($payment['billing_id'], $payment['amount']);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Failed to update payment status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update billing record after successful payment
     */
    private function updateBillingAfterPayment($billingId, $paidAmount)
    {
        // Get current billing details
        $query = "SELECT amount, paid_amount, billing_type FROM billing WHERE billing_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $billingId);
        $stmt->execute();
        $result = $stmt->get_result();
        $billing = $result->fetch_assoc();
        $stmt->close();

        if ($billing) {
            $newPaidAmount = $billing['paid_amount'] + $paidAmount;
            $totalAmount = $billing['amount'];

            // Determine new status based on payment completion
            if ($newPaidAmount >= $totalAmount) {
                $newStatus = 'Fully Paid';
            } else {
                $newStatus = 'Partially Paid';
            }

            // Update billing
            $updateQuery = "
                UPDATE billing 
                SET paid_amount = ?, status = ?, updated_at = NOW()
                WHERE billing_id = ?
            ";
            $stmt = $this->db->prepare($updateQuery);
            $stmt->bind_param('dsi', $newPaidAmount, $newStatus, $billingId);
            $stmt->execute();
            $stmt->close();

            // Log payment completion for audit
            error_log("Payment completed for Billing ID: $billingId, Type: {$billing['billing_type']}, Amount: GH₵$paidAmount, New Status: $newStatus");
        }
    }

    /**
     * Get billing details for metadata
     */
    private function getBillingDetails($billingId)
    {
        $query = "
        SELECT billing_type, description
        FROM billing 
        WHERE billing_id = ?
    ";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $billingId);
        $stmt->execute();
        $result = $stmt->get_result();
        $billing = $result->fetch_assoc();
        $stmt->close();

        return $billing ?: [];
    }

    /**
     * Get student details for metadata
     */
    private function getStudentDetails($studentId)
    {
        $query = "
            SELECT 
                CONCAT(s.first_name, ' ', s.last_name) as full_name,
                s.phone_number,
                r.room_number
            FROM students s
            LEFT JOIN allocations a ON s.student_id = a.student_id AND a.status = 'Active'
            LEFT JOIN rooms r ON a.room_id = r.room_id
            WHERE s.student_id = ?
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        $stmt->close();

        return $student ?: [];
    }


    /**
     * Get payment by reference
     */
    public function getPaymentByReference($reference)
    {
        $query = "
        SELECT 
            p.*, 
            b.description, 
            b.billing_type,
            CONCAT(s.first_name, ' ', s.last_name) as student_name
        FROM payments p
        LEFT JOIN billing b ON p.billing_id = b.billing_id
        LEFT JOIN students s ON p.student_id = s.student_id
        WHERE p.transaction_reference = ?
    ";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $reference);
        $stmt->execute();
        $result = $stmt->get_result();
        $payment = $result->fetch_assoc();
        $stmt->close();

        return $payment;
    }

    /**
     * Get payment history for a student
     */
    public function getStudentPaymentHistory($studentId, $limit = 50)
    {
        $query = "
            SELECT 
                p.*,
                b.billing_type,
                b.description as billing_description
            FROM payments p
            LEFT JOIN billing b ON p.billing_id = b.billing_id
            WHERE p.student_id = ?
            ORDER BY p.created_at DESC
            LIMIT ?
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $studentId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $payments = [];
        while ($row = $result->fetch_assoc()) {
            $payments[] = $row;
        }
        $stmt->close();

        return $payments;
    }
}
