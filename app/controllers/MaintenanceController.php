<?php
require_once "./database/db.php";
require_once "./app/models/MaintenanceRequest.php";

class MaintenanceController
{
    private $model;
    private $db;

    public function __construct()
    {
        $this->model = new MaintenanceRequest();
    }

    // Handle AJAX request to submit a new maintenance request
    public function submitMaintenanceRequest()
    {

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
            'student_id' => $_SESSION['user']['student_id'],
            'room_id' => $_POST['room_id'] ?: null,
            'issue_type' => $_POST['issue_type'],
            'description' => $_POST['description'],
            'priority' => $_POST['priority']
        ];

        $errors = $this->model->validateMaintenanceRequest($data);
        if (!empty($errors)) {
            $this->sendJsonResponse(['success' => false, 'error' => implode(', ', $errors)]);
            return;
        }

        $result = $this->model->submitRequest($data);
        return $result;
    }

    // Handle AJAX request to fetch maintenance request data
    public function getRequestData()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $student_id = $_SESSION['user']['student_id'];
            $result = $this->model->getRequestsByStudent($student_id);
            echo json_encode($result);
        }
    }

    // Handle AJAX request to fetch maintenance request details
    public function getRequestDetails($request_id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $details = $this->model->getRequestById($request_id);
            if ($details) {
                $responses = $this->model->getRequestResponses($request_id);
                $details['responses'] = $responses;
                echo json_encode($details);
            } else {
                echo json_encode(['error' => 'Maintenance request not found']);
            }
        }
    }

    // Handle AJAX request to update request status (Admin only)
    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['user']['role'] === 'Admin') {
            $request_id = $_POST['request_id'];
            $status = $_POST['status'];
            $response_text = $_POST['response_text'] ?? '';

            if ($this->model->updateRequestStatus($request_id, $status, $_SESSION['user']['user_id'], $response_text)) {
                echo json_encode(['success' => true, 'message' => 'Request status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update request status']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Unauthorized action']);
        }
    }

    // Handle AJAX request to add a follow-up response (Student or Admin)
    public function addResponse()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request_id = $_POST['request_id'];
            $response_text = $_POST['response_text'];
            $user_id = $_SESSION['user']['user_id'];

            if ($this->model->addResponse($request_id, $user_id, $response_text)) {
                echo json_encode(['success' => true, 'message' => 'Response added successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to add response']);
            }
        }
    }


    private function sendJsonResponse($data, $status_code = 200)
    {
        header('Content-Type: application/json', true, $status_code);
        echo json_encode($data);
        exit();
    }
}
