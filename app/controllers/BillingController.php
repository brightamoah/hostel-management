<?php
// require_once "./app/models/Student.php";
require_once __DIR__ . "/../models/Student.php";
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../models/Billing.php";


class BillingController
{
    private $studentModel;
    private $billingModel;

    public function __construct()
    {
        $this->studentModel = new Student(getDb());
        $this->billingModel = new Billing();
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

    public function payBilling($billing_id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_csrf_valid()) {
            header('Content-Type: application/json');
            $user_id = $_SESSION['user']['user_id'] ?? null;
            if (!$user_id) {
                echo json_encode(['success' => false, 'error' => 'User not authenticated']);
                exit();
            }

            try {
                $result = $this->studentModel->initiatePayment($user_id, $billing_id);
                echo json_encode([
                    'success' => true,
                    'message' => 'Payment initiated successfully',
                    // 'payment_url' => $result['payment_url'] // Uncomment for actual gateway
                ]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit();
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid request or CSRF token']);
            exit();
        }
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
}
