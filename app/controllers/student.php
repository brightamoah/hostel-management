<?php
require_once "./app/models/Student.php";
require_once "./database/db.php";

class StudentController
{
    private $studentModel;

    public function __construct()
    {
        $db = new Database();
        $this->studentModel = new Student($db->connect());
    }

    public function dashboard()
    {
        // Ensure the user is logged in and is a student
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
            header("Location: /login");
            exit();
        }

        $user = $_SESSION['user'];
        $user_id = $user['user_id'];

        try {
            // Fetch first name if not in session
            $first_name = $_SESSION['first_name'] ?? $this->studentModel->getFirstName($user_id);

            // Fetch data using the model
            $room_data = $this->studentModel->getRoomAllocation($user_id);
            $total_paid = $this->studentModel->getTotalPaid($user_id);
            $pending_balance = $this->studentModel->getPendingBalance($user_id);
            $open_requests = $this->studentModel->getOpenMaintenanceRequests($user_id);
            $pending_visitors = $this->studentModel->getPendingVisitors($user_id);

            // Pass data to the view
            $data = [
                'first_name' => $first_name,
                'room_data' => $room_data,
                'total_paid' => $total_paid,
                'pending_balance' => $pending_balance,
                'open_requests' => $open_requests,
                'pending_visitors' => $pending_visitors,
            ];

           require_once "./pages/student/dashboard.php";
        } catch (Exception $e) {
            error_log($e->getMessage());
            die("An error occurred while loading the dashboard. Please try again later.");
        }
    }
}
