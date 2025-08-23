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
                echo json_encode(['success' => false, 'message' => 'Failed to register the visitor. Please try again']);
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
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $visitor_id = $id;
            $visitor_name = $_POST['visitor_name'] ?? '';
            $relation = $_POST['relation'] ?? '';
            $phone_number = $_POST['phone_number'] ?? '';
            $visit_date = $_POST['visit_date'] ?? '';
            $purpose = $_POST['purpose'] ?? '';

            if (empty($visitor_name) || empty($relation) || empty($phone_number) || empty($visit_date) || empty($purpose)) {
                echo json_encode(['success' => false, 'message' => 'All fields are required']);
                exit();
            }

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

            $result = $this->visitorModel->update(
                $visitor_id,
                $visitor_name,
                $relation,
                $phone_number,
                $visit_date,
                $purpose
            );

            if ($result  && $result["success"]) {
                $message = 'Visitor details updated successfully.';

                // Add additional message if status was changed from Approved to Pending
                if ($result["status_changed"]) {
                    $message .= ' Since this was an approved visitor, it has been moved back to pending status and will require admin approval again.';
                }

                echo json_encode([
                    'success' => true,
                    'message' => $message,
                    'status_changed' => $result['status_changed'] ?? false,
                    'new_status' => $result['new_status'] ?? 'Unknown'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update visitor details. The visitor may not be in Pending or Approved status.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        exit();
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
                echo json_encode(['success' => false, 'message' => 'Failed to cancel the visitor request. The visitor may not be in a cancellable status.']);
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
                echo json_encode(['success' => false, 'message' => 'No visitor found with this ID.']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
        }
        exit();
    }

    // Approve visitor request (admin action)
    public function approve($id)
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin access required']);
                exit();
            }
            $visitor_id = $id ?? '';
            if (empty($visitor_id)) {
                echo json_encode(['success' => false, 'message' => 'Visitor ID is required']);
                exit();
            }
            if ($this->visitorModel->approve($visitor_id)) {
                echo json_encode(['success' => true, 'message' => 'Visitor request approved']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to approve visitor request']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        exit();
    }

    // Deny visitor request (admin action)
    public function deny($id)
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin access required']);
                exit();
            }
            $visitor_id = $id ?? '';
            if (empty($visitor_id)) {
                echo json_encode(['success' => false, 'message' => 'Visitor ID is required']);
                exit();
            }
            if ($this->visitorModel->deny($visitor_id)) {
                echo json_encode(['success' => true, 'message' => 'Visitor request denied']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to deny visitor request']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        exit();
    }

    // Check-in visitor (admin action)
    public function checkIn($id)
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin access required']);
                exit();
            }
            $visitor_id = $id ?? '';
            if (empty($visitor_id)) {
                echo json_encode(['success' => false, 'message' => 'Visitor ID is required']);
                exit();
            }
            $result = $this->visitorModel->checkIn(
                $visitor_id
            );


            // Check if the result is an array with success/message keys (error case)
            if (is_array($result) && isset($result['success']) && $result['success'] === false) {
                echo json_encode($result); // Return the error message from the model
            } else if ($result) {
                echo json_encode(['success' => true, 'message' => 'Visitor checked in successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to check in visitor']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        exit();
    }

    // Check-out visitor (admin action)
    public function checkOut($id)
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin access required']);
                exit();
            }
            $visitor_id = $id ?? '';
            if (empty($visitor_id)) {
                echo json_encode(['success' => false, 'message' => 'Visitor ID is required']);
                exit();
            }
            $result = $this->visitorModel->checkOut($visitor_id);

            // Check if the result is an array with success/message keys (error case)
            if (is_array($result) && isset($result['success']) && $result['success'] === false) {
                echo json_encode($result); // Return the error message from the model
            } else if ($result) {
                echo json_encode(['success' => true, 'message' => 'Visitor checked out successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to check out visitor']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        exit();
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

    // Get all visitors for admin DataTable
    public function getAllVisitors()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin access required']);
                exit();
            }
            $dateFilter = $_GET['dateFilter'] ?? '';
            $visitors = $this->visitorModel->getAllVisitors($dateFilter);
            echo json_encode(['data' => $visitors]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        exit();
    }

    // Get visitor logs (admin/student action)
    public function getVisitorLogs($id)
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $visitor_id = $id;
            $logs = $this->visitorModel->getVisitorLogs($visitor_id);
            echo json_encode(['success' => true, 'data' => $logs]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        exit();
    }
}
