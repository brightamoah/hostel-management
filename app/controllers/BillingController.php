<?php
// require_once "./app/models/Student.php";
require_once __DIR__ . "/../models/Student.php";
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../models/Billing.php";
require_once __DIR__ . "/../../services/PaymentService.php";


class BillingController
{
    private $studentModel;
    private $billingModel;
    private $paymentService;

    public function __construct()
    {
        $this->studentModel = new Student(getDb());
        $this->billingModel = new Billing();
        $this->paymentService = new PaymentService();
    }

    public function getBillings()
    {
        header('Content-Type: application/json');
        $user_id = $_SESSION['user']['user_id'] ?? null;
        if (!$user_id) {
            echo json_encode(['success' => false, 'error' => 'User not authenticated']);
            exit();
        }

        try {
            $billings = $this->studentModel->getBillings($user_id);
            echo json_encode(['data' => $billings]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit();
    }

    public function getBillingData()
    {
        // header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['error' => 'Invalid request method'], JSON_PRETTY_PRINT);
            http_response_code(405);
            exit;
        }

        try {
            $billings = $this->billingModel->getBillings($_GET);
            // echo json_encode(['data' => $billings]);
            // echo json_encode($billings, JSON_PRETTY_PRINT);
            return $billings;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit();
    }

    public function createInvoice()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_csrf_valid()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid request or CSRF token']);
            http_response_code(403);
            exit;
        }

        $user_id = $_SESSION['user']['user_id'] ?? null;
        if (!$user_id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'User not authenticated']);
            http_response_code(401);
            exit;
        }

        $data = [
            'student_id' => $_POST['student_id'] ?? '',
            'amount' => $_POST['amount'] ?? '',
            'description' => $_POST['description'] ?? '',
            'due_date' => $_POST['due_date'] ?? '',
            'purpose' => $_POST['purpose'] ?? '',
            'academic_period' => $_POST['academic_period'] ?? '',
            'payment_terms' => $_POST['payment_terms'] ?? '',
            'send_notification' => $_POST['send_notification'] ?? '',
        ];

        try {
            $result = $this->billingModel->createInvoice($data);


            if ($result['success']) {
                // Add email status to result
                $email_result = $result['email_result'] ?? ['success' => false];
                $result['email_sent'] = $email_result['success'] ?? false;
                $result['email_error'] = $email_result['error'] ?? null;
            }

            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Failed to create invoice: ' . $e->getMessage()]);
            http_response_code(500);
        }
        exit();
    }

    public function updateInvoice($billing_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_csrf_valid()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid request or CSRF token']);
            http_response_code(403);
            exit;
        }

        $user_id = $_SESSION['user']['user_id'] ?? null;
        if (!$user_id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'User not authenticated']);
            http_response_code(401);
            exit;
        }



        $data = [
            'billing_id' => $_POST['billing_id'] ?? '',
            'student_id' => $_POST['student_id'] ?? '',
            'amount' => $_POST['amount'] ?? '',
            'description' => $_POST['description'] ?? '',
            'date_due' => $_POST['date_due'] ?? $_POST['due_date'] ?? '',
            'billing_type' => $_POST['billing_type'] ?? $_POST['purpose'] ?? '', // Handle both field names
            'academic_period' => $_POST['academic_period'] ?? '',
            'payment_terms' => $_POST['payment_terms'] ?? '',
        ];

        try {
            $result = $this->billingModel->updateInvoice($billing_id, $data);

            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Failed to update invoice: ' . $e->getMessage()]);
            http_response_code(500);
        }
        exit();
    }

    public function deleteInvoice($billing_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_csrf_valid()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid request or CSRF token']);
            http_response_code(403);
            exit;
        }

        $user_id = $_SESSION['user']['user_id'] ?? null;
        $user_role = $_SESSION['user']['role'] ?? null;

        if (!$user_id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'User not authenticated']);
            http_response_code(401);
            exit;
        }

        if ($user_role !== 'Admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'You do not have permission to perform this action']);
            http_response_code(403);
            exit;
        }

        try {
            $result = $this->billingModel->deleteInvoice($billing_id);
            header('Content-Type: application/json');
            echo json_encode($result);
            http_response_code($result['success'] ? 200 : 400);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Failed to delete invoice: ' . $e->getMessage()]);
            http_response_code(500);
        }
        exit();
    }

    public function sendBillingReminder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_csrf_valid()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid request or CSRF token']);
            http_response_code(403);
            exit;
        }

        $user_id = $_SESSION['user']['user_id'] ?? null;
        $user_role = $_SESSION['user']['role'] ?? null;

        if (!$user_id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'User not authenticated']);
            http_response_code(401);
            exit;
        }

        if ($user_role !== 'Admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'You do not have permission to perform this action']);
            http_response_code(403);
            exit;
        }

        $data = [
            'billing_id' => $_POST['billing_id'] ?? '',
            'subject' => $_POST['subject'] ?? '',
            'message' => nl2br(htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8')),
            'attach_invoice' => isset($_POST['attach_invoice']) ? true : false,
        ];

        try {
            $result = $this->billingModel->sendReminder($data);

            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Failed to send billing reminder: ' . $e->getMessage()]);
            http_response_code(500);
        }
        exit();
    }

    public function recordPayment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_csrf_valid()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid request or CSRF token']);
            http_response_code(403);
            exit;
        }

        $user_id = $_SESSION['user']['user_id'] ?? null;
        $user_role = $_SESSION['user']['role'] ?? null;

        if (!$user_id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'User not authenticated']);
            http_response_code(401);
            exit;
        }

        if ($user_role !== 'Admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'You do not have permission to perform this action']);
            http_response_code(403);
            exit;
        }

        $data = [
            'billing_id' => $_POST['billing_id'] ?? '',
            'amount' => $_POST['amount'] ?? '',
            'payment_date' => $_POST['payment_date'] ?? '',
            'payment_method' => $_POST['payment_method'] ?? '',
            'transaction_reference' => $_POST['transaction_reference'] ?? '',
        ];

        try {
            $result = $this->billingModel->recordPayment($data);

            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Failed to record payment: ' . $e->getMessage()]);
            http_response_code(500);
        }
        exit();
    }

    public function getBillingDetails($billing_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            http_response_code(405);
            exit;
        }

        $user_id = $_SESSION['user']['user_id'] ?? null;
        if (!$user_id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'User not authenticated']);
            http_response_code(401);
            exit;
        }

        try {
            $result = $this->billingModel->getBillingDetails($billing_id);
            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Failed to get billing details: ' . $e->getMessage()]);
            http_response_code(500);
        }
        exit();
    }


    public function initializePayment($billing_id)
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_csrf_valid()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid request or CSRF token']);
            http_response_code(403);
            exit;
        }

        $user_id = $_SESSION['user']['user_id'] ?? null;
        if (!$user_id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'User not authenticated']);
            http_response_code(401);
            exit;
        }

        $student_id = $_SESSION['user']['student_id'] ?? null;
        if (!$student_id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Student record not found']);
            http_response_code(404);
            exit;
        }

        $user_email = $_SESSION['user']['email'] ?? null;
        if (!$user_email) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'User email not found']);
            http_response_code(404);
            exit;
        }

        try {
            $billingData = $this->billingModel->getBillingById($billing_id);
            if (!$billingData || !isset($billingData['details'])) {
                throw new Exception('Billing record not found');
            }

            $billing = $billingData['details'];

            if ($billing['status'] === 'Fully Paid') {
                throw new Exception('This invoice is already fully paid');
            }

            $outstandingAmount = floatval($billing['outstanding_balance']);

            if ($outstandingAmount <= 0) {
                throw new Exception('No outstanding amount to pay');
            }

            // Get custom payment amount from request, default to outstanding amount
            $requestedAmount = floatval($_POST['payment_amount'] ?? $outstandingAmount);

            // Validate requested amount
            if ($requestedAmount < 1) {
                throw new Exception('Payment amount must be at least GH₵1.00');
            }

            if ($requestedAmount > $outstandingAmount) {
                throw new Exception('Payment amount cannot exceed outstanding balance of GH₵' . number_format($outstandingAmount, 2));
            }

            $billing_type_to_purpose = [
                'Hostel Fee' => 'Hostel Fee',
                'Security Deposit' => 'Security Deposit',
                'Utility Fee' => 'Other',
                'Maintenance Fee' => 'Maintenance Charge',
                'Late Payment Penalty' => 'Penalty',
                'Other' => 'Other'
            ];

            $billing_type = $billing['billing_type'] ?? 'Other';
            $mapped_purpose = $billing_type_to_purpose[$billing_type] ?? 'Other';

            $result = $this->paymentService->initializePayment(
                $billing_id,
                $student_id,
                $requestedAmount,
                $user_email,
                $mapped_purpose,
                $billing['description']
            );
            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            http_response_code(500);
        }
        exit();
    }

    public function verifyPayment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            header("Location: /student/billing?error=" . urlencode('Invalid request method'));
            exit;
        }

        $reference = $_GET['reference'] ?? $_GET['trxref'] ?? '';
        if (!$reference) {
            header("Location: /student/billing?error=" . urlencode('Payment reference is missing'));
            exit;
        }

        try {
            $result = $this->paymentService->verifyPayment($reference);

            if ($result['success']) {

                $payment = $this->paymentService->getPaymentByReference($reference);

                if ($payment) {

                    $_SESSION['payment_success'] = [
                        'reference' => $reference,
                        'amount' => $payment['amount'],
                        'billing_id' => $payment['billing_id'],
                        'payment_method' => $payment['payment_method'],
                        'payment_date' => $payment['payment_date'],
                        'purpose' => $payment['purpose'],
                        'status' => $payment['status']
                    ];

                    // Redirect to success page with payment details
                    header("Location: /student/payment-success?reference=" . urlencode($reference));
                    exit;
                }
            }

            $_SESSION['payment_error'] = [
                'reference' => $reference,
                'message' => 'Payment verification failed or was not successful'
            ];

            header("Location: /student/payment-failed?reference=" . urlencode($reference));
            exit;
        } catch (Exception $e) {
            error_log("Payment verification error: " . $e->getMessage());

            // Set error data in session
            $_SESSION['payment_error'] = [
                'reference' => $reference,
                'message' => $e->getMessage()
            ];

            header("Location: /student/payment-failed?reference=" . urlencode($reference));
            exit;
        }
    }

    public function getPaymentStatus($reference)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            http_response_code(405);
            exit;
        }

        try {

            $payment = $this->paymentService->getPaymentByReference($reference);

            if (!$payment) {
                throw new Exception('Payment not found');
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'payment' => $payment
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            http_response_code(500);
        }
        exit();
    }
}
