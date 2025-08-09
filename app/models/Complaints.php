<?php
require_once "./database/db.php";

class Complaint
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Fetch all complaints for a student
     * @param int $student_id
     * @return array
     */
    public function getComplaintsByStudent($student_id)
    {
        $query = "
            SELECT c.complaint_id, c.complaint_type, c.description, c.priority, c.status, 
                   c.submitted_at, c.resolved_at, r.room_number, r.building
            FROM complaints c
            LEFT JOIN rooms r ON c.room_id = r.room_id
            WHERE c.student_id = ?
            ORDER BY c.complaint_id DESC
        ";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $complaints = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return ['success' => true, 'data' => $complaints];
        } catch (Exception $e) {
            return ['success' => false, 'error' => "Failed to fetch complaints: " . $e->getMessage()];
        }
    }

    /**
     * Fetch a single complaint by ID
     * @param int $complaint_id
     * @param int $student_id
     * @return array
     */
    public function getComplaintById($complaint_id, $student_id)
    {
        $query = "
            SELECT c.complaint_id, c.complaint_type, c.description, c.priority, c.status, 
                   c.submitted_at, c.resolved_at, r.room_number, r.building
            FROM complaints c
            LEFT JOIN rooms r ON c.room_id = r.room_id
            WHERE c.complaint_id = ? AND c.student_id = ?
        ";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $complaint_id, $student_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $complaint = $result->fetch_assoc();
            $stmt->close();
            if ($complaint) {
                return ['success' => true, 'data' => $complaint];
            }
            return ['success' => false, 'error' => "Complaint not found or unauthorized"];
        } catch (Exception $e) {
            return ['success' => false, 'error' => "Failed to fetch complaint: " . $e->getMessage()];
        }
    }

    /**
     * Fetch responses for a complaint
     * @param int $complaint_id
     * @return array
     */
    public function getComplaintResponses($complaint_id)
    {
        $query = "
            SELECT cr.response_id, cr.response_text, cr.action_taken, cr.response_date, 
                   CONCAT(a.first_name, ' ', a.last_name) AS admin_name
            FROM complaint_responses cr
            JOIN admins a ON cr.admin_id = a.admin_id
            WHERE cr.complaint_id = ?
            ORDER BY cr.response_date ASC
        ";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $complaint_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $responses = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return ['success' => true, 'data' => $responses];
        } catch (Exception $e) {
            return ['success' => false, 'error' => "Failed to fetch responses: " . $e->getMessage()];
        }
    }

    /**
     * Create a new complaint
     * @param int $student_id
     * @param array $data
     * @return array
     */
    public function createComplaint($student_id, $data)
    {
        $query = "
            INSERT INTO complaints (student_id, room_id, complaint_type, description, priority, status, submitted_at)
            VALUES (?, ?, ?, ?, ?, 'Pending', NOW())
        ";
        try {
            $stmt = $this->conn->prepare($query);
            $room_id = !empty($data['room_id']) ? $data['room_id'] : null;
            $stmt->bind_param(
                "iisss",
                $student_id,
                $room_id,
                $data['complaint_type'],
                $data['description'],
                $data['priority']
            );
            $success = $stmt->execute();
            $complaint_id = $this->conn->insert_id;
            $stmt->close();
            if ($success) {
                return ['success' => true, 'complaint_id' => $complaint_id];
            }
            return ['success' => false, 'error' => "Failed to create complaint"];
        } catch (Exception $e) {
            return ['success' => false, 'error' => "Failed to create complaint: " . $e->getMessage()];
        }
    }

    /**
     * (Optional) Update complaint status - for admin use
     * @param int $complaint_id
     * @param string $status
     * @return array
     */
    public function updateComplaintStatus($complaint_id, $status)
    {
        $query = "UPDATE complaints SET status = ?, resolved_at = CASE WHEN ? IN ('Resolved', 'Rejected') THEN NOW() ELSE NULL END WHERE complaint_id = ?";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssi", $status, $status, $complaint_id);
            $success = $stmt->execute();
            $affected = $this->conn->affected_rows;
            $stmt->close();
            if ($success && $affected > 0) {
                return ['success' => true];
            }
            // If no rows affected, check if complaint exists (could be same status)
            $check = $this->conn->prepare("SELECT complaint_id FROM complaints WHERE complaint_id = ?");
            $check->bind_param("i", $complaint_id);
            $check->execute();
            $result = $check->get_result();
            $exists = $result && $result->num_rows > 0;
            $check->close();
            if ($success && $exists) {
                // No changes made, but complaint exists, treat as success
                return ['success' => true, 'message' => 'No changes made (status was already set)'];
            }
            return ['success' => false, 'error' => "Complaint not found"];
        } catch (Exception $e) {
            return ['success' => false, 'error' => "Failed to update status: " . $e->getMessage()];
        }
    }

    /**
     * (Optional) Add a response to a complaint - for admin use
     * @param int $complaint_id
     * @param int $admin_id
     * @param string $response_text
     * @param string $action_taken
     * @return array
     */
    public function addComplaintResponse($complaint_id, $admin_id, $response_text, $action_taken)
    {
        $query = "INSERT INTO complaint_responses (complaint_id, admin_id, response_text, action_taken, response_date) VALUES (?, ?, ?, ?, NOW())";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("iiss", $complaint_id, $admin_id, $response_text, $action_taken);
            $success = $stmt->execute();
            $response_id = $this->conn->insert_id;
            $stmt->close();
            if ($success) {
                return ['success' => true, 'response_id' => $response_id];
            }
            return ['success' => false, 'error' => "Failed to add response"];
        } catch (Exception $e) {
            return ['success' => false, 'error' => "Failed to add response: " . $e->getMessage()];
        }
    }

    /**
     * Validate complaint data
     * @param array $data
     * @return array
     */
    public function validateComplaintData($data)
    {
        $errors = [];
        $valid_types = ['Room Condition', 'Maintenance', 'Staff Behavior', 'Amenities', 'Noise', 'Security', 'Billing', 'Other'];
        $valid_priorities = ['Low', 'Medium', 'High', 'Emergency'];

        if (empty($data['complaint_type']) || !in_array($data['complaint_type'], $valid_types)) {
            $errors[] = "Invalid complaint type";
        }
        if (empty($data['description']) || strlen(trim($data['description'])) < 10) {
            $errors[] = "Description must be at least 10 characters long";
        }
        if (empty($data['priority']) || !in_array($data['priority'], $valid_priorities)) {
            $errors[] = "Invalid priority";
        }
        if (!empty($data['room_id'])) {
            $stmt = $this->conn->prepare("SELECT room_id FROM rooms WHERE room_id = ?");
            $stmt->bind_param("i", $data['room_id']);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                $errors[] = "Invalid room ID";
            }
            $stmt->close();
        }

        return $errors;
    }

    public function getPendingComplaint($student_id)
    {
        $query = "SELECT COUNT(*) AS pending_complaints FROM complaints WHERE status = 'Pending' AND student_id = $student_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['pending_complaints'] ?? 0;
    }

    public function getResolvedComplaint($student_id)
    {
        $query = "SELECT COUNT(*) AS resolved_complaints FROM complaints WHERE status = 'Resolved' AND student_id = $student_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $result = $row['resolved_complaints'] ?? 0;
        return $result;
    }

    public function getInProgressComplaint($student_id)
    {
        $query = "SELECT COUNT(*) AS in_progress_complaints FROM complaints WHERE status = 'In-Progress' AND student_id = $student_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $result =  $row['in_progress_complaints'] ?? 0;
        return $result;
    }

    // Fetch total complaints for all students
    public function getTotalComplaint()
    {
        $query = "SELECT COUNT(*) AS total_complaints FROM complaints";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['total_complaints'] ?? 0;
    }

    public function getPendingComplaintCount()
    {
        $query = "SELECT COUNT(*) AS pending_complaints FROM complaints WHERE status = 'Pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['pending_complaints'] ?? 0;
    }

    public function getResolvedComplaintCount()
    {
        $query = "SELECT COUNT(*) AS resolved_complaints FROM complaints WHERE status = 'Resolved'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['resolved_complaints'] ?? 0;
    }

    public function getInProgressComplaintCount()
    {
        $query = "SELECT COUNT(*) AS in_progress_complaints FROM complaints WHERE status = 'In-Progress'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['in_progress_complaints'] ?? 0;
    }

    public function getAllStudents()
    {
        $query = "SELECT student_id, CONCAT(first_name, ' ', last_name) AS student_name FROM students ORDER BY student_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $students = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $students;
    }


    /**
     * Summary of getAllComplaints
     * @return array
     */
    public function getAllComplaints()
    {
        $query = "
        SELECT c.complaint_id, c.complaint_type, c.description, c.priority, c.status, 
               c.submitted_at, r.room_number, r.building, 
               CONCAT(s.first_name, ' ', s.last_name) AS student_name
        FROM complaints c
        LEFT JOIN rooms r ON c.room_id = r.room_id
        JOIN students s ON c.student_id = s.student_id
        ORDER BY c.complaint_id DESC
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $complaints = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $complaints;
    }

    /**
     * Get complaint by ID for Admin
     */
    public function getComplaintByIdAdmin($complaint_id)
    {
        $query = "
        SELECT c.complaint_id, c.complaint_type, c.description, c.priority, c.status, 
               c.submitted_at, r.room_number, r.building, 
               CONCAT(s.first_name, ' ', s.last_name) AS student_name
        FROM complaints c
        LEFT JOIN rooms r ON c.room_id = r.room_id
        JOIN students s ON c.student_id = s.student_id
        WHERE c.complaint_id = ?
        ORDER BY c.complaint_id DESC
    ";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $complaint_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $complaint = $result->fetch_assoc();
            $stmt->close();

            if ($complaint) {
                return ['success' => true, 'data' => $complaint];
            }
            return ['success' => false, 'error' => "Complaint not found"];
        } catch (Exception $e) {
            return ['success' => false, 'error' => "Failed to fetch complaint: " . $e->getMessage()];
        }
    }

    public function __destruct()
    {
        $this->db->close();
    }
}
