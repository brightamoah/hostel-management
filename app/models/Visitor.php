<?php
require_once './database/db.php';

class Visitor
{
    private $db;
    private $conn;

    public function __construct()
    {
        try {
            $this->db = new Database();
            $this->conn = $this->db->connect();
        } catch (Exception $e) {
            error_log("Error in Visitor constructor: " . $e->getMessage());
            throw new Exception("Failed to initialize database connection: " . $e->getMessage());
        }
    }

    // Register a new visitor (student action)
    public function register($student_id, $visitor_name, $relation, $phone_number, $visit_date, $purpose)
    {
        try {
            $query = "INSERT INTO visitors (student_id, visitor_name, relation, phone_number, visit_date, purpose, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("isssss", $student_id, $visitor_name, $relation, $phone_number, $visit_date, $purpose);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error in register: " . $e->getMessage());
            return false;
        }
    }

    // Get visitor by ID (for viewing details)
    public function getVisitorById($visitor_id)
    {
        try {
            $query = "SELECT * FROM visitors WHERE visitor_id = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("i", $visitor_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: {$stmt->error}");
            }
            $result = $stmt->get_result();
            $visitor = $result->num_rows > 0 ? $result->fetch_assoc() : null;
            $stmt->close();
            return $visitor;
        } catch (Exception $e) {
            error_log("Error in getVisitorById: " . $e->getMessage());
            return null;
        }
    }

    // Get all visitors for a student (for the DataTable)
    public function getVisitorsByStudent($student_id)
    {
        try {
            $query = "SELECT * FROM visitors WHERE student_id = ? ORDER BY visit_date DESC";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("i", $student_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: {$stmt->error}");
            }
            $result = $stmt->get_result();
            $visitors = [];
            while ($row = $result->fetch_assoc()) {
                $visitors[] = $row;
            }
            $stmt->close();
            return json_encode(['data' => $visitors]);
        } catch (Exception $e) {
            error_log("Error in getVisitorsByStudent: " . $e->getMessage());
            return [];
        }
    }

    // Get total visitor count for a student
    public function getVisitorCountByStudent($student_id)
    {
        try {
            $query = "SELECT COUNT(*) as count FROM visitors WHERE student_id = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("i", $student_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: {$stmt->error}");
            }
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
            return $data['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Error in getVisitorCountByStudent: " . $e->getMessage());
            return 0;
        }
    }

    // Get number of visitors for a student by status
    public function getVisitorCountByStudentAndStatus($student_id, $status)
    {
        try {
            $query = "SELECT COUNT(*) as count FROM visitors WHERE student_id = ? AND status = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("is", $student_id, $status);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: {$stmt->error}");
            }
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
            return $data['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Error in getVisitorCountByStudentAndStatus: " . $e->getMessage());
            return 0;
        }
    }

    // Update visitor details (student action)
    public function update($visitor_id, $visitor_name, $relation, $phone_number, $visit_date, $purpose)
    {
        try {
            $query = "UPDATE visitors SET visitor_name = ?, relation = ?, phone_number = ?, visit_date = ?, purpose = ? WHERE visitor_id = ? AND status IN ('Pending', 'Approved')";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("sssssi", $visitor_name, $relation, $phone_number, $visit_date, $purpose, $visitor_id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error in update: " . $e->getMessage());
            return false;
        }
    }

    // Cancel visitor request (student action)
    public function cancel($visitor_id)
    {
        try {
            $query = "UPDATE visitors SET status = 'Cancelled' WHERE visitor_id = ? AND status IN ('Pending', 'Approved', 'Checked-In')";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("i", $visitor_id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error in cancel: " . $e->getMessage());
            return false;
        }
    }

    // Approve visitor request (admin action)
    public function approve($visitor_id)
    {
        try {
            $query = "UPDATE visitors SET status = 'Approved' WHERE visitor_id = ? AND status = 'Pending'";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("i", $visitor_id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error in approve: " . $e->getMessage());
            return false;
        }
    }

    // Deny visitor request (admin action)
    public function deny($visitor_id)
    {
        try {
            $query = "UPDATE visitors SET status = 'Denied' WHERE visitor_id = ? AND status = 'Pending'";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("i", $visitor_id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error in deny: " . $e->getMessage());
            return false;
        }
    }

    // Check-in visitor (admin action)
    public function checkIn($visitor_id)
    {
        try {
            $check_in_time = date('Y-m-d H:i:s');
            $query = "UPDATE visitors SET status = 'Checked-In', check_in_time = ? WHERE visitor_id = ? AND status = 'Approved'";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("si", $check_in_time, $visitor_id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error in checkIn: " . $e->getMessage());
            return false;
        }
    }

    // Check-out visitor (admin action)
    public function checkOut($visitor_id)
    {
        try {
            $check_out_time = date('Y-m-d H:i:s');
            $query = "UPDATE visitors SET status = 'Checked-Out', check_out_time = ? WHERE visitor_id = ? AND status = 'Checked-In'";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("si", $check_out_time, $visitor_id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error in checkOut: " . $e->getMessage());
            return false;
        }
    }

    // Delete visitor (admin or student action)
    public function delete($visitor_id)
    {
        try {
            $query = "DELETE FROM visitors WHERE visitor_id = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("i", $visitor_id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error in delete: " . $e->getMessage());
            return false;
        }
    }

    public function __destruct()
    {
        try {
            $this->conn->close();
        } catch (Exception $e) {
            error_log("Error in __destruct: " . $e->getMessage());
        }
    }
}
