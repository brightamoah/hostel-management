<?php
require_once './app/models/Visitor.php';

class VisitorController
{
    private $visitorModel;

    public function __construct()
    {
        $this->visitorModel = new Visitor();
    }

    // Register a new visitor (student action)
    public function register()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }

        // Check if user is authenticated and has the Student role
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Student') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized: You must be logged in as a Student']);
            exit();
        }

        // Fetch student_id from session
        $student_id = $_SESSION['user']['student_id'] ?? null;
        if (!$student_id) {
            echo json_encode(['success' => false, 'message' => 'Student ID not found in session']);
            exit();
        }

        // Get form data
        $visitor_name = $_POST['visitor_name'] ?? '';
        $relation = $_POST['relation'] ?? '';
        $phone_number = $_POST['phone_number'] ?? '';
        $visit_date = $_POST['visit_date'] ?? '';
        $purpose = $_POST['purpose'] ?? '';

        // Validate inputs
        if (empty($visitor_name) || empty($relation) || empty($phone_number) || empty($visit_date) || empty($purpose)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit();
        }

        // Additional validation (e.g., phone number format, date format)
        if (!preg_match('/^(\+233|0)\d{9}$/', $phone_number)) {
            echo json_encode(['success' => false, 'message' => 'Invalid phone number format. Must be in +233XXXXXXXXX or 0XXXXXXXXX format']);
            exit();
        }

        // Validate visit date (should be today or future)
        $today = date('Y-m-d');
        if ($visit_date < $today) {
            echo json_encode(['success' => false, 'message' => 'Visit date must be today or in the future']);
            exit();
        }

        try {
            if ($this->visitorModel->register($student_id, $visitor_name, $relation, $phone_number, $visit_date, $purpose)) {
                echo json_encode(['success' => true, 'message' => 'Visitor registered successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to register visitor']);
            }
        } catch (Exception $e) {
            error_log("Error registering visitor: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred while registering the visitor']);
        }
        exit();
    }


    // Edit visitor details (student action)
    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $visitor_id = $id;
            $visitor_name = $_POST['visitor_name'];
            $relation = $_POST['relation'];
            $phone_number = $_POST['phone_number'];
            $visit_date = $_POST['visit_date'];
            $purpose = $_POST['purpose'];

            if ($this->visitorModel->update($visitor_id, $visitor_name, $relation, $phone_number, $visit_date, $purpose)) {
                echo json_encode(['success' => true, 'message' => 'Visitor updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update visitor']);
            }
        }
    }

    // Cancel visitor request (student action)
    /**
     * Summary of cancel
     * @param number $id
     * @return void
     */
    public function cancel($id)
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $visitor_id = $id;
            if ($this->visitorModel->cancel($visitor_id)) {
                echo json_encode(['success' => true, 'message' => 'Visitor request cancelled']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to cancel visitor request']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        exit();
    }

    // View visitor details (student/admin action)
    /**
     * Summary of view
     * @param number $id
     * @return never
     */
    public function view($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($id)) {
            $visitor_id = $id;
            $visitor = $this->visitorModel->getVisitorById($visitor_id);
            header('Content-Type: application/json');
            if ($visitor) {
                echo json_encode(['success' => true, 'data' => $visitor]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Visitor not found']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
        }
        exit();
    }

    // Approve visitor request (admin action)
    public function approve()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $visitor_id = $_POST['visitor_id'];
            if ($this->visitorModel->approve($visitor_id)) {
                echo json_encode(['success' => true, 'message' => 'Visitor request approved']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to approve visitor request']);
            }
        }
    }

    // Deny visitor request (admin action)
    public function deny()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $visitor_id = $_POST['visitor_id'];
            if ($this->visitorModel->deny($visitor_id)) {
                echo json_encode(['success' => true, 'message' => 'Visitor request denied']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to deny visitor request']);
            }
        }
    }

    // Check-in visitor (admin action)
    public function checkIn()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $visitor_id = $_POST['visitor_id'];
            if ($this->visitorModel->checkIn($visitor_id)) {
                echo json_encode(['success' => true, 'message' => 'Visitor checked in']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to check in visitor']);
            }
        }
    }

    // Check-out visitor (admin action)
    public function checkOut()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $visitor_id = $_POST['visitor_id'];
            if ($this->visitorModel->checkOut($visitor_id)) {
                echo json_encode(['success' => true, 'message' => 'Visitor checked out']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to check out visitor']);
            }
        }
    }

    // Delete visitor (admin/student action)
    public function delete($id)
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $visitor_id = $id;
            if ($this->visitorModel->delete($visitor_id)) {
                echo json_encode(['success' => true, 'message' => 'Visitor deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete visitor']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        exit();
    }
}
