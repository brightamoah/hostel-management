<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/BaseModel.php";

class MaintenanceRequest extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }


    // Get all maintenance requests (Admin only)
    public function getAllRequests()
    {
        $query = "SELECT mr.*, r.room_number, r.building, h.hostel_name, CONCAT(s.first_name, ' ', s.last_name) AS student_name 
                 FROM maintenance_requests mr
                 LEFT JOIN rooms r ON mr.room_id = r.room_id
                 LEFT JOIN students s ON mr.student_id = s.student_id
                 LEFT JOIN hostels h ON mr.hostel_id = h.hostel_id
                 WHERE 1=1";

        // Apply hostel filtering for non-super admins
        $query = $this->addHostelFilter($query, 'mr');
        $query .= " ORDER BY mr.request_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return ['data' => $result->fetch_all(MYSQLI_ASSOC)];
    }

    // Get all maintenance requests by student ID
    public function getRequestsByStudent($student_id)
    {
        $query = "SELECT mr.*, r.room_number, r.building 
                 FROM maintenance_requests mr 
                 LEFT JOIN rooms r ON mr.room_id = r.room_id 
                 WHERE mr.student_id = ? 
                 ORDER BY mr.request_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return ['data' => $result->fetch_all(MYSQLI_ASSOC)];
    }

    // Get pending maintenance requests count
    public function getPendingRequest($student_id = 0)
    {
        $query = "SELECT COUNT(*) as count 
                 FROM maintenance_requests mr
                 WHERE mr.status = 'Pending'";

        if ($student_id > 0) {
            $query .= " AND mr.student_id = ?";
        } else {
            // Apply hostel filtering for admin counts
            $query = $this->addHostelFilter($query, 'mr');
        }

        $stmt = $this->conn->prepare($query);
        if ($student_id > 0) {
            $stmt->bind_param("i", $student_id);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }
    // Get in-progress maintenance requests count
    public function getInProgressRequest($student_id = 0)
    {
        $query = "SELECT COUNT(*) as count 
                 FROM maintenance_requests mr
                 WHERE mr.status = 'In-Progress'";

        if ($student_id > 0) {
            $query .= " AND mr.student_id = ?";
        } else {
            // Apply hostel filtering for admin counts
            $query = $this->addHostelFilter($query, 'mr');
        }

        $stmt = $this->conn->prepare($query);
        if ($student_id > 0) {
            $stmt->bind_param("i", $student_id);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }

    // Get resolved maintenance requests count
    public function getResolvedRequest($student_id = 0)
    {
        $query = "SELECT COUNT(*) as count 
                 FROM maintenance_requests mr
                 WHERE mr.status = 'Completed'";

        if ($student_id > 0) {
            $query .= " AND mr.student_id = ?";
        } else {
            // Apply hostel filtering for admin counts
            $query = $this->addHostelFilter($query, 'mr');
        }

        $stmt = $this->conn->prepare($query);
        if ($student_id > 0) {
            $stmt->bind_param("i", $student_id);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }

    // Get rejected maintenance requests count
    public function getRejectedRequest($student_id = 0)
    {
        $query = "SELECT COUNT(*) as count 
                 FROM maintenance_requests mr
                 WHERE mr.status = 'Rejected'";

        if ($student_id > 0) {
            $query .= " AND mr.student_id = ?";
        } else {
            // Apply hostel filtering for admin counts
            $query = $this->addHostelFilter($query, 'mr');
        }

        $stmt = $this->conn->prepare($query);
        if ($student_id > 0) {
            $stmt->bind_param("i", $student_id);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }

    // Submit a new maintenance request
    public function submitRequest($data)
    {
        // Determine hostel_id based on room or student's current allocation
        $hostel_id = null;

        if ($data['room_id']) {
            // Get hostel from room
            $room_query = "SELECT hostel_id FROM rooms WHERE room_id = ?";
            $stmt = $this->conn->prepare($room_query);
            $stmt->bind_param("i", $data['room_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $hostel_id = $row['hostel_id'];
            }
            $stmt->close();
        } else {
            // Get hostel from student's current allocation
            $allocation_query = "SELECT r.hostel_id 
                               FROM allocations a 
                               JOIN rooms r ON a.room_id = r.room_id 
                               WHERE a.student_id = ? AND a.status = 'Active' 
                               LIMIT 1";
            $stmt = $this->conn->prepare($allocation_query);
            $stmt->bind_param("i", $data['student_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $hostel_id = $row['hostel_id'];
            }
            $stmt->close();
        }

        $query = "INSERT INTO maintenance_requests (student_id, room_id, hostel_id, issue_type, description, priority, status) 
                 VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
        $stmt = $this->conn->prepare($query);
        $room_id = $data['room_id'] ?: null;
        $stmt->bind_param("iiisss", $data['student_id'], $room_id, $hostel_id, $data['issue_type'], $data['description'], $data['priority']);
        return $stmt->execute();
    }

    // Get maintenance request details by ID
    public function getRequestById($request_id)
    {
        $query = "SELECT mr.*, r.room_number, r.building, h.hostel_name, CONCAT(s.first_name, ' ', s.last_name) AS student_name 
                 FROM maintenance_requests mr 
                 LEFT JOIN rooms r ON mr.room_id = r.room_id 
                 LEFT JOIN students s ON mr.student_id = s.student_id 
                 LEFT JOIN hostels h ON mr.hostel_id = h.hostel_id
                 WHERE mr.request_id = ?";

        // Apply hostel filtering for non-super admins
        $query = $this->addHostelFilter($query, 'mr');

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        // Additional validation for admin access
        if ($result && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Admin') {
            $this->validateHostelAccess($result['hostel_id']);
        }

        return $result;
    }

    // Update request status and add response (Admin only)
    public function updateRequestStatus($request_id, $status, $admin_id, $response_text)
    {
        // First validate that the admin can access this request
        $check_query = "SELECT hostel_id FROM maintenance_requests WHERE request_id = ?";
        $check_query = $this->addHostelFilter($check_query);

        $stmt = $this->conn->prepare($check_query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result->fetch_assoc()) {
            throw new Exception('Access denied: Cannot access this maintenance request');
        }
        $stmt->close();

        // Update status with completion date if status is 'Completed'
        if ($status === 'Completed') {
            $query = "UPDATE maintenance_requests SET status = ?, completion_date = NOW() WHERE request_id = ?";
        } else {
            $query = "UPDATE maintenance_requests SET status = ? WHERE request_id = ?";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $request_id);
        $status_updated = $stmt->execute();

        // Add response if provided
        if ($response_text && $status_updated) {
            return $this->addResponse($request_id, $admin_id, $response_text);
        }

        return $status_updated;
    }

    // Add a response to a maintenance request
    public function addResponse($request_id, $user_id, $response_text)
    {
        // Validate access to the request (for admin users)
        if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Admin') {
            $check_query = "SELECT hostel_id FROM maintenance_requests WHERE request_id = ?";
            $check_query = $this->addHostelFilter($check_query);

            $stmt = $this->conn->prepare($check_query);
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if (!$result->fetch_assoc()) {
                throw new Exception('Access denied: Cannot access this maintenance request');
            }
            $stmt->close();
        }

        $query = "INSERT INTO maintenance_responses (request_id, user_id, response_text, response_date) 
                 VALUES (?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iis", $request_id, $user_id, $response_text);
        return $stmt->execute();
    }

    // Get responses for a maintenance request
    public function getRequestResponses($request_id)
    {
        $query = "SELECT mr.*, u.name, u.role 
                 FROM maintenance_responses mr 
                 JOIN users u ON mr.user_id = u.user_id 
                 WHERE mr.request_id = ? 
                 ORDER BY mr.response_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Validate $data
    public function validateMaintenanceRequest($data)
    {
        $errors = [];

        // Required fields validation
        if (empty($data['issue_type'])) {
            $errors['issue_type'] = 'Issue type is required';
        } elseif (!in_array($data['issue_type'], ['Plumbing', 'Electrical', 'Furniture', 'Appliance', 'Structural', 'Pest Control', 'Internet/Wi-Fi', 'Other'])) {
            $errors['issue_type'] = 'Invalid issue type';
        }

        if (empty($data['description'])) {
            $errors['description'] = 'Description is required';
        } elseif (strlen($data['description']) > 500) {
            $errors['description'] = 'Description cannot exceed 500 characters';
        } elseif (strlen($data['description']) < 10) {
            $errors['description'] = 'Description must be at least 10 characters long';
        }

        if (empty($data['priority'])) {
            $errors['priority'] = 'Priority is required';
        } elseif (!in_array($data['priority'], ['Low', 'Medium', 'High', 'Emergency'])) {
            $errors['priority'] = 'Invalid priority level';
        }

        // Room validation
        if (!empty($data['room_id'])) {
            $room_query = "SELECT room_id FROM rooms WHERE room_id = ?";
            $stmt = $this->conn->prepare($room_query);
            $stmt->bind_param("i", $data['room_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $errors['room_id'] = 'Invalid room selected';
            }
            $stmt->close();
        }

        // Student validation
        if (!empty($data['student_id'])) {
            $student_query = "SELECT student_id FROM students WHERE student_id = ?";
            $stmt = $this->conn->prepare($student_query);
            $stmt->bind_param("i", $data['student_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $errors['student_id'] = 'Invalid student ID';
            }
            $stmt->close();
        } else {
            $errors['student_id'] = 'Student ID is required';
        }

        return $errors;
    }

    // Close the database connection

    public function __destruct()
    {
        $this->conn->close();
    }
}
