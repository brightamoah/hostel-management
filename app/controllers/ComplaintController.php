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
     * get all complaints for admin
     * Handle GET /admin/complaints-data
     */
    public function getAllComplaints()
    {
        $this->requireAdmin();
        try {
            $complaints = $this->complaintModel->getAllComplaints();
            $this->sendJsonResponse(['success' => true, 'data' => $complaints]);
        } catch (Exception $e) {
            $this->sendJsonResponse(['success' => false, 'error' => "Access denied: " . $e->getMessage()]);
        }
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
     * Handle GET /admin/complaint/{id}
     */
    public function getComplaintById($id)
    {
        $this->requireAdmin();
        try {
            $result = $this->complaintModel->getComplaintByIdAdmin($id);
            if (!$result['success']) {
                $this->sendJsonResponse(['success' => false, 'error' => $result['error']]);
                return;
            }
            $this->sendJsonResponse($result['data']);
        } catch (Exception $e) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Access denied: ' . $e->getMessage()]);
        }
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
     * Handle GET /admin/complaint/{id}/responses
     */
    public function getComplaintResponsesAdmin($complaint_id)
    {
        $this->requireAdmin();
        try {
            // Validate admin access to complaint first
            $complaint_check = $this->complaintModel->getComplaintByIdAdmin($complaint_id);
            if (!$complaint_check['success']) {
                $this->sendJsonResponse(['success' => false, 'error' => $complaint_check['error']]);
                return;
            }

            $result = $this->complaintModel->getComplaintResponses($complaint_id);
            if (!$result['success']) {
                $this->sendJsonResponse(['success' => false, 'error' => 'Complaint not found']);
                return;
            }
            $this->sendJsonResponse(['data' => $result['data']]);
        } catch (Exception $e) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Access denied: ' . $e->getMessage()]);
        }
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
            return $this->sendJsonResponse(['success' => false, 'error' => 'Method not allowed']);
        }
        if (!is_csrf_valid()) {
            return $this->sendJsonResponse(['success' => false, 'error' => 'Invalid CSRF token.'], 403);
        }
        $status = $_POST['status'] ?? null;
        if (!$status) {
            return $this->sendJsonResponse(['success' => false, 'error' => 'Status is required.'], 400);
        }
        $valid_statuses = ['Pending', 'In-Progress', 'Resolved', 'Rejected'];
        if (!in_array($status, $valid_statuses)) {
            return $this->sendJsonResponse(['success' => false, 'error' => 'Invalid status']);
        }

        try {
            $complaint = $this->complaintModel->getComplaintByIdAdmin($id);
            if (!$complaint['success'] ?? false) {
                return $this->sendJsonResponse(['success' => false, 'error' => $complaint['error'] ?? 'Complaint not found.'], 404);
            }
            $currentStatus = $complaint['data']['status'] ?? null;
            if ($currentStatus === null) {
                return $this->sendJsonResponse(['success' => false, 'error' => 'Complaint record is missing status field.'], 500);
            }
            if ($currentStatus === $status) {
                return $this->sendJsonResponse(['success' => false, 'error' => "No changes made. Status is already $status."], 200);
            }
            $result = $this->complaintModel->updateComplaintStatus($id, $status);
            if ($result['success'] ?? false) {
                return $this->sendJsonResponse(['success' => true, 'message' => "Complaint status updated to $status."]);
            }
            return $this->sendJsonResponse(['success' => false, 'error' => $result['error'] ?? 'Failed to update complaint status. Please try again.'], 500);
        } catch (Exception $e) {
            return $this->sendJsonResponse(['success' => false, 'error' => 'Access denied: ' . $e->getMessage()], 403);
        }
    }

    /**
     * Handle POST /complaint/{id}/response - Admin
     */
    public function addComplaintResponse($id)
    {
        $this->requireAdmin();

        // Get admin_id from database using user_id
        $user_id = $_SESSION['user']['user_id'] ?? 0;
        if ($user_id <= 0) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Invalid user session']);
            return;
        }

        // Fetch admin_id from admins table
        $admin_query = "SELECT admin_id FROM admins WHERE user_id = ?";
        $stmt = $this->complaintModel->getConnection()->prepare($admin_query);
        if (!$stmt) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Database error']);
            return;
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin_data = $result->fetch_assoc();
        $stmt->close();

        if (!$admin_data || !$admin_data['admin_id']) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Admin record not found']);
            return;
        }

        $admin_id = $admin_data['admin_id'];

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

        try {
            $result = $this->complaintModel->addComplaintResponse($id, $admin_id, $response_text, $action_taken);
            $this->sendJsonResponse($result);
        } catch (Exception $e) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Access denied: ' . $e->getMessage()]);
        }
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
