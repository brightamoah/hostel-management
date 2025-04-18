<?php
require_once "./database/db.php";

class MaintenanceRequest
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->connect();
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
    public function getPendingRequest($student_id)
    {
        $query = "SELECT COUNT(*) as count 
                 FROM maintenance_requests 
                 WHERE student_id = ? AND status = 'Pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }

    // Get in-progress maintenance requests count
    public function getInProgressRequest($student_id)
    {
        $query = "SELECT COUNT(*) as count 
                 FROM maintenance_requests 
                 WHERE student_id = ? AND status = 'In-Progress'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }

    // Get resolved maintenance requests count
    public function getResolvedRequest($student_id)
    {
        $query = "SELECT COUNT(*) as count 
                 FROM maintenance_requests 
                 WHERE student_id = ? AND status = 'Completed'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }

    // Submit a new maintenance request
    public function submitRequest($data)
    {
        $query = "INSERT INTO maintenance_requests (student_id, room_id, issue_type, description, priority, status) 
                 VALUES (?, ?, ?, ?, ?, 'Pending')";
        $stmt = $this->conn->prepare($query);
        $room_id = $data['room_id'] ? $data['room_id'] : null;
        $stmt->bind_param("iisss", $data['student_id'], $room_id, $data['issue_type'], $data['description'], $data['priority']);
        return $stmt->execute();
    }

    // Get maintenance request details by ID
    public function getRequestById($request_id)
    {
        $query = "SELECT mr.*, r.room_number, r.building 
                 FROM maintenance_requests mr 
                 LEFT JOIN rooms r ON mr.room_id = r.room_id 
                 WHERE mr.request_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Update request status and add response (Admin only)
    public function updateRequestStatus($request_id, $status, $admin_id, $response_text)
    {
        // Update status
        $query = "UPDATE maintenance_requests SET status = ? WHERE request_id = ?";
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
        $query = "INSERT INTO maintenance_responses (request_id, user_id, response_text, response_date) 
                 VALUES (?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iis", $request_id, $user_id, $response_text);
        return $stmt->execute();
    }

    // Get responses for a maintenance request
    /**
     * Summary of getRequestResponses
     * @param mixed $request_id
     * @return array
     */
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
        $response = $result->fetch_all(MYSQLI_ASSOC);
        return $response;
    }


    //validate $data
    public function validateMaintenanceRequest($data)
    {
        $errors = [];

        // Required fields validation
        if (empty($data['issue_type'])) {
            $errors['issue_type'] = 'Issue type is required';
        } elseif (!in_array($data['issue_type'], ['Plumbing', 'Electrical', 'Furniture', 'Appliance', 'Structural', 'Cleaning', 'Other'])) {
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
        } elseif (!in_array($data['priority'], ['Low', 'Medium', 'High', 'Critical'])) {
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
        $this->db->close();
    }
}
