<?php
require_once  "./app/models/Complaints.php";

class ComplaintController
{
    private $complaintModel;

    public function __construct()
    {
        $this->complaintModel = new Complaint();
    }

    /**
     * Handle GET /complaint-data
     */
    public function getComplaintData()
    {
        $this->requireStudent();
        $student_id = $_SESSION['user']['student_id'] ?? 0;

        $result = $this->complaintModel->getComplaintsByStudent($student_id);
        $this->sendJsonResponse($result);
    }

    /**
     * Handle GET /complaint/{id}
     */
    public function getComplaint($id)
    {
        $this->requireStudent();
        $student_id = $_SESSION['user']['student_id'] ?? 0;

        $result = $this->complaintModel->getComplaintById($id, $student_id)['data'] ?? null;
        $this->sendJsonResponse($result);
    }



    /**
     * Handle GET /complaint/{id}/responses
     */
    public function getComplaintResponses($id)
    {
        $this->requireStudent();
        // Verify complaint belongs to student
        $student_id = $_SESSION['user']['student_id'] ?? 0;
        $complaint_check = $this->complaintModel->getComplaintById($id, $student_id);
        if (!$complaint_check['success']) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Unauthorized or complaint not found']);
            return;
        }

        $result = $this->complaintModel->getComplaintResponses($id);
        $this->sendJsonResponse($result);
    }

    /**
     * Handle POST /complaint/submit
     */
    public function submitComplaint()
    {
        $this->requireStudent();
        $student_id = $_SESSION['user']['student_id'] ?? 0;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendJsonResponse(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        // Validate CSRF token (implement in production)
        if (!is_csrf_valid()) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Invalid CSRF token']);
            return;
        }
        

        $data = [
            'complaint_type' => $_POST['complaint_type'] ?? '',
            'room_id' => $_POST['room_id'] ?? '',
            'description' => $_POST['description'] ?? '',
            'priority' => $_POST['priority'] ?? ''
        ];

        $errors = $this->complaintModel->validateComplaintData($data);
        if (!empty($errors)) {
            $this->sendJsonResponse(['success' => false, 'error' => implode(', ', $errors)]);
            return;
        }

        $result = $this->complaintModel->createComplaint($student_id, $data);
        $this->sendJsonResponse($result);
    }

    /**
     * (Optional) Handle POST /complaint/{id}/status - Admin
     */
    public function updateComplaintStatus($id)
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendJsonResponse(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        $status = $_POST['status'] ?? '';
        $valid_statuses = ['Pending', 'In-Progress', 'Resolved', 'Rejected'];
        if (!in_array($status, $valid_statuses)) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Invalid status']);
            return;
        }

        $result = $this->complaintModel->updateComplaintStatus($id, $status);
        $this->sendJsonResponse($result);
    }

    /**
     * (Optional) Handle POST /complaint/{id}/response - Admin
     */
    public function addComplaintResponse($id)
    {
        $this->requireAdmin();
        $admin_id = $_SESSION['user']['admin_id'] ?? 0;
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendJsonResponse(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        $response_text = $_POST['response_text'] ?? '';
        $action_taken = $_POST['action_taken'] ?? '';
        $valid_actions = ['Assigned', 'Updated', 'Resolved', 'Rejected'];
        if (empty($response_text) || !in_array($action_taken, $valid_actions)) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Invalid response text or action']);
            return;
        }

        $result = $this->complaintModel->addComplaintResponse($id, $admin_id, $response_text, $action_taken);
        $this->sendJsonResponse($result);
    }

    /**
     * Require student authentication
     */
    private function requireStudent()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Student') {
            $this->sendJsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
            exit();
        }
    }

    /**
     * Require admin authentication
     */
    private function requireAdmin()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
            $this->sendJsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
            exit();
        }
    }


    /**
     * Send JSON response
     * @param array $data
     * @param int $status_code
     */
    private function sendJsonResponse($data, $status_code = 200)
    {
        header('Content-Type: application/json', true, $status_code);
        echo json_encode($data);
        exit();
    }
}
