<?php
require_once "./app/models/Student.php";
require_once "./database/db.php";

class BillingController
{
    private $studentModel;

    public function __construct()
    {
        $db = new Database();
        $conn = $db->connect();
        $this->studentModel = new Student($conn);
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
}
