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
}
