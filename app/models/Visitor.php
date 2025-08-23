<?php
require_once __DIR__ . "/../../database/db.php";

class Visitor
{
    private $conn;

    public function __construct()
    {
        try {
            date_default_timezone_set('Africa/Accra');

            $this->conn = getDb();
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
            $query = "SELECT 
                    v.*, 
                    vl.check_in_time, 
                    vl.check_out_time,
                    s.first_name,
                    s.last_name,
                    s.phone_number AS student_phone,
                    u.email AS student_email,
                    CONCAT(s.first_name, ' ', s.last_name) AS student_name,
                    r.building,
                    r.room_number,
                    r.room_type
                  FROM visitors v 
                  LEFT JOIN visitor_logs vl ON v.visitor_id = vl.visitor_id 
                  AND vl.log_id = (
                      SELECT MAX(log_id) 
                      FROM visitor_logs 
                      WHERE visitor_id = v.visitor_id
                  )
                  JOIN students s ON v.student_id = s.student_id
                  LEFT JOIN users u ON s.user_id = u.user_id
                  LEFT JOIN allocations a ON s.student_id = a.student_id AND a.status = 'Active'
                  LEFT JOIN rooms r ON a.room_id = r.room_id
                  WHERE v.visitor_id = ?";
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
    public function getVisitorsByStudent($student_id, $dateFilter = '')
    {
        try {
            $query = "SELECT 
                    v.visitor_id, 
                    v.visitor_name, 
                    v.relation, 
                    v.phone_number, 
                    v.visit_date,
                    v.purpose, 
                    v.status, 
                    v.registered_at,
                    vl.check_in_time, 
                    vl.check_out_time 
                  FROM visitors v 
                  LEFT JOIN visitor_logs vl ON v.visitor_id = vl.visitor_id 
                  AND vl.log_id = (
                      SELECT MAX(log_id) 
                      FROM visitor_logs 
                      WHERE visitor_id = v.visitor_id
                  )
                  WHERE v.student_id = ?";

            // Add date filter conditions
            $params = [$student_id];
            $types = "i";
            if ($dateFilter) {
                $today = date('Y-m-d');
                switch ($dateFilter) {
                    case 'today':
                        $query .= " AND v.visit_date = ?";
                        $params[] = $today;
                        $types .= "s";
                        break;
                    case 'tomorrow':
                        $tomorrow = date('Y-m-d', strtotime('+1 day'));
                        $query .= " AND v.visit_date = ?";
                        $params[] = $tomorrow;
                        $types .= "s";
                        break;
                    case 'this_week':
                        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
                        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
                        $query .= " AND v.visit_date BETWEEN ? AND ?";
                        $params[] = $startOfWeek;
                        $params[] = $endOfWeek;
                        $types .= "ss";
                        break;
                    case 'next_week':
                        $startOfNextWeek = date('Y-m-d', strtotime('monday next week'));
                        $endOfNextWeek = date('Y-m-d', strtotime('sunday next week'));
                        $query .= " AND v.visit_date BETWEEN ? AND ?";
                        $params[] = $startOfNextWeek;
                        $params[] = $endOfNextWeek;
                        $types .= "ss";
                        break;
                    case 'this_month':
                        $startOfMonth = date('Y-m-01');
                        $endOfMonth = date('Y-m-t');
                        $query .= " AND v.visit_date BETWEEN ? AND ?";
                        $params[] = $startOfMonth;
                        $params[] = $endOfMonth;
                        $types .= "ss";
                        break;
                    case 'next_month':
                        $startOfNextMonth = date('Y-m-01', strtotime('+1 month'));
                        $endOfNextMonth = date('Y-m-t', strtotime('+1 month'));
                        $query .= " AND v.visit_date BETWEEN ? AND ?";
                        $params[] = $startOfNextMonth;
                        $params[] = $endOfNextMonth;
                        $types .= "ss";
                        break;
                    case 'past':
                        $query .= " AND v.visit_date < ?";
                        $params[] = $today;
                        $types .= "s";
                        break;
                    case 'future':
                        $query .= " AND v.visit_date > ?";
                        $params[] = $today;
                        $types .= "s";
                        break;
                    default:
                        // No filter applied
                        break;
                }
            }

            $query .= " ORDER BY v.visit_date DESC";

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param($types, ...$params);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: {$stmt->error}");
            }
            $result = $stmt->get_result();
            $visitors = [];
            while ($row = $result->fetch_assoc()) {
                // Map database field names to what DataTable expects
                $visitors[] = [
                    'id' => $row['visitor_id'],
                    'full_name' => $row['visitor_name'],
                    'role' => $row['relation'],
                    'visit_date' => $row['visit_date'],
                    'check_in' => $row['check_in_time'],
                    'check_out' => $row['check_out_time'],
                    'status' => $row['status'],
                    'email' => $row['phone_number'], // Used in the name display
                    'purpose' => $row['purpose'],
                    // Include original fields too for the modal
                    'visitor_id' => $row['visitor_id'],
                    'visitor_name' => $row['visitor_name'],
                    'phone_number' => $row['phone_number'],
                    'registered_at' => $row['registered_at'],
                    'student_id' => $student_id
                ];
            }
            $stmt->close();
            return json_encode(['data' => $visitors]);
        } catch (Exception $e) {
            error_log("Error in getVisitorsByStudent: " . $e->getMessage());
            return json_encode(['data' => []]);
        }
    }

    // Get all visitors for admin (for the DataTable)
    public function getAllVisitors($dateFilter = '')
    {
        try {
            $query = "SELECT
                v.visitor_id,
                v.visitor_name,
                v.relation,
                v.phone_number,
                v.visit_date,
                v.purpose,
                v.status,
                vl.check_in_time,
                vl.check_out_time,
                CONCAT(s.first_name, ' ', s.last_name) AS student_name,
                s.student_id,
                u.email AS student_email,
                s.phone_number AS student_phone,
                r.building,
                r.room_number,
                r.room_type
            FROM
                visitors v
                LEFT JOIN visitor_logs vl ON v.visitor_id = vl.visitor_id
                AND vl.log_id = (
                    SELECT MAX(log_id)
                    FROM visitor_logs
                    WHERE visitor_id = v.visitor_id
                )
                JOIN students s ON v.student_id = s.student_id
                LEFT JOIN users u ON s.user_id = u.user_id
                LEFT JOIN allocations a ON s.student_id = a.student_id AND a.status = 'Active'
                LEFT JOIN rooms r ON a.room_id = r.room_id
            WHERE 1=1";

            // Add date filter conditions
            $params = [];
            $types = "";
            if ($dateFilter) {
                $today = date('Y-m-d');
                switch ($dateFilter) {
                    case 'today':
                        $query .= " AND v.visit_date = ?";
                        $params[] = $today;
                        $types .= "s";
                        break;
                    case 'tomorrow':
                        $tomorrow = date('Y-m-d', strtotime('+1 day'));
                        $query .= " AND v.visit_date = ?";
                        $params[] = $tomorrow;
                        $types .= "s";
                        break;
                    case 'this_week':
                        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
                        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
                        $query .= " AND v.visit_date BETWEEN ? AND ?";
                        $params[] = $startOfWeek;
                        $params[] = $endOfWeek;
                        $types .= "ss";
                        break;
                    case 'next_week':
                        $startOfNextWeek = date('Y-m-d', strtotime('monday next week'));
                        $endOfNextWeek = date('Y-m-d', strtotime('sunday next week'));
                        $query .= " AND v.visit_date BETWEEN ? AND ?";
                        $params[] = $startOfNextWeek;
                        $params[] = $endOfNextWeek;
                        $types .= "ss";
                        break;
                    case 'this_month':
                        $startOfMonth = date('Y-m-01');
                        $endOfMonth = date('Y-m-t');
                        $query .= " AND v.visit_date BETWEEN ? AND ?";
                        $params[] = $startOfMonth;
                        $params[] = $endOfMonth;
                        $types .= "ss";
                        break;
                    case 'next_month':
                        $startOfNextMonth = date('Y-m-01', strtotime('+1 month'));
                        $endOfNextMonth = date('Y-m-t', strtotime('+1 month'));
                        $query .= " AND v.visit_date BETWEEN ? AND ?";
                        $params[] = $startOfNextMonth;
                        $params[] = $endOfNextMonth;
                        $types .= "ss";
                        break;
                    case 'past':
                        $query .= " AND v.visit_date < ?";
                        $params[] = $today;
                        $types .= "s";
                        break;
                    case 'future':
                        $query .= " AND v.visit_date > ?";
                        $params[] = $today;
                        $types .= "s";
                        break;
                    default:
                        // No filter applied
                        break;
                }
            }

            $query .= " ORDER BY v.visit_date DESC";

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: {$stmt->error}");
            }

            $result = $stmt->get_result();
            $visitors = [];
            while ($row = $result->fetch_assoc()) {
                $visitors[] = $row;
            }
            $stmt->close();

            // Return in DataTable-compatible format
            return $visitors;
        } catch (Exception $e) {
            error_log("Error in getAllVisitors: " . $e->getMessage());
            return json_encode(['data' => []]);
        }
    }

    // Get visitor logs (check-in/check-out times) for a visitor
    public function getVisitorLogs($visitor_id)
    {
        try {
            $query = "SELECT log_id, check_in_time, check_out_time FROM visitor_logs WHERE visitor_id = ? ORDER BY check_in_time DESC";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("i", $visitor_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: {$stmt->error}");
            }
            $result = $stmt->get_result();
            $logs = [];
            while ($row = $result->fetch_assoc()) {
                $logs[] = $row;
            }
            $stmt->close();
            return $logs;
        } catch (Exception $e) {
            error_log("Error in getVisitorLogs: " . $e->getMessage());
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

            $checkStatusQuery = "SELECT status FROM visitors WHERE visitor_id = ?";
            $checkStatusStmt = $this->conn->prepare($checkStatusQuery);
            if (!$checkStatusStmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $checkStatusStmt->bind_param("i", $visitor_id);
            if (!$checkStatusStmt->execute()) {
                throw new Exception("Execute failed: {$checkStatusStmt->error}");
            }

            $checkStatusResult = $checkStatusStmt->get_result();
            $visitor = $checkStatusResult->fetch_assoc();
            $checkStatusStmt->close();

            if (!$visitor) {
                return false;
            }

            // Determine the new status based on current status
            $newStatus = $visitor['status'];
            if ($visitor['status'] === 'Approved') {
                $newStatus = 'Pending';
            } elseif ($visitor['status'] === 'Pending') {
                $newStatus = 'Pending';
            } else {
                return false;
            }

            $query = "UPDATE visitors SET visitor_name = ?, relation = ?, phone_number = ?, visit_date = ?, purpose = ?, status = ? WHERE visitor_id = ? AND status IN ('Pending', 'Approved')";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("ssssssi", $visitor_name, $relation, $phone_number, $visit_date, $purpose, $newStatus, $visitor_id);
            $result = $stmt->execute();
            $stmt->close();
            return [
                'success' => $result,
                'status_changed' => $visitor['status'] === 'Approved' && $newStatus === 'Pending',
                'new_status' => $newStatus
            ];
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
            $today = date('Y-m-d');
            $this->conn->begin_transaction();

            // Check if the visitor exists and has a valid visit_date
            $query = "SELECT visit_date, status FROM visitors WHERE visitor_id = ?";
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

            if (!$visitor) {
                return ['success' => false, 'message' => 'No visitor found with this ID'];
            }

            // Fix: First check the status, then check the date
            // Allow check-in if status is Approved or Checked-Out
            switch ($visitor['status']) {
                case 'Checked-In':
                    return ['success' => false, 'message' => 'Visitor is already checked in.'];
                case 'Denied':
                    return ['success' => false, 'message' => 'Visitor request has been denied.'];
                case 'Pending':
                    return ['success' => false, 'message' => 'Visitor request is still pending approval.'];
                case 'Cancelled':
                    return ['success' => false, 'message' => 'Visitor request has been cancelled.'];
                default:
                    if (!in_array($visitor['status'], ['Approved', 'Checked-Out'])) {
                        return ['success' => false, 'message' => 'Visitor cannot be checked in. Current status: ' . $visitor['status']];
                    }
            }

            // Validate visit_date (e.g., allow check-in only on the visit_date)
            if ($visitor['visit_date'] != $today) {
                return ['success' => false, 'message' => 'Check-in is only allowed on the visit date: ' . $visitor['visit_date']];
            }

            // Update visitor status to Checked-In
            $query = "UPDATE visitors SET status = 'Checked-In' WHERE visitor_id = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("i", $visitor_id);
            $result = $stmt->execute();
            $stmt->close();

            // Insert new log entry
            if ($result) {
                $query = "INSERT INTO visitor_logs (visitor_id, check_in_time) VALUES (?, ?)";
                $stmt = $this->conn->prepare($query);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $this->conn->error);
                }
                $stmt->bind_param("is", $visitor_id, $check_in_time);
                $result = $stmt->execute();
                $stmt->close();
            }

            if ($result) {
                $this->conn->commit();
            } else {
                $this->conn->rollback();
            }
            return $result;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error in checkIn: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }


    // Check-out visitor (admin action)
    public function checkOut($visitor_id)
    {
        try {
            $check_out_time = date('Y-m-d H:i:s');
            $today = date('Y-m-d');
            $this->conn->begin_transaction();

            // Check if the visitor exists and has a valid visit_date
            $query = "SELECT visit_date, status FROM visitors WHERE visitor_id = ?";
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

            if (!$visitor) {
                return ['success' => false, 'message' => 'No visitor found with this ID'];
            }

            // Fix: First check the status, then check the date
            // Allow check-out only if status is Checked-In
            switch ($visitor['status']) {
                case 'Checked-Out':
                    return ['success' => false, 'message' => 'Visitor is already checked out.'];
                case 'Denied':
                    return ['success' => false, 'message' => 'Visitor request has been denied.'];
                case 'Pending':
                    return ['success' => false, 'message' => 'Visitor request is still pending approval.'];
                case 'Cancelled':
                    return ['success' => false, 'message' => 'Visitor request has been cancelled.'];
                default:
                    if ($visitor['status'] !== 'Checked-In') {
                        return ['success' => false, 'message' => 'Visitor cannot be checked out. Current status: ' . $visitor['status']];
                    }
            }

            // Validate visit_date (e.g., allow check-out only on the visit_date)
            if ($visitor['visit_date'] != $today) {
                return ['success' => false, 'message' => 'Check-out is only allowed on the visit date: ' . $visitor['visit_date']];
            }

            // Update visitor status to Checked-Out
            $query = "UPDATE visitors SET status = 'Checked-Out' WHERE visitor_id = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("i", $visitor_id);
            $result = $stmt->execute();
            $stmt->close();

            // Update latest log entry
            if ($result) {
                $query = "UPDATE visitor_logs SET check_out_time = ? WHERE visitor_id = ? AND check_out_time IS NULL ORDER BY check_in_time DESC LIMIT 1";
                $stmt = $this->conn->prepare($query);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $this->conn->error);
                }
                $stmt->bind_param("si", $check_out_time, $visitor_id);
                $result = $stmt->execute();
                $stmt->close();

                if (!$result) {
                    throw new Exception("No open check-in record found for this visitor. Please check in first.");
                }
            }

            if ($result) {
                $this->conn->commit();
                return ['success' => true, 'message' => 'Visitor checked out successfully.'];
            } else {
                $this->conn->rollback();
                return ['success' => false, 'message' => 'Failed to check out the visitor. Please try again.'];
            }
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error in checkOut: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }

    // Delete visitor (admin or student action)
    public function delete($visitor_id)
    {
        try {
            $this->conn->begin_transaction();
            // Delete logs first
            $query = "DELETE FROM visitor_logs WHERE visitor_id = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("i", $visitor_id);
            $stmt->execute();
            $stmt->close();

            // Delete visitor
            $query = "DELETE FROM visitors WHERE visitor_id = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("i", $visitor_id);
            $result = $stmt->execute();
            $stmt->close();

            if ($result) {
                $this->conn->commit();
            } else {
                $this->conn->rollback();
            }
            return $result;
        } catch (Exception $e) {
            $this->conn->rollback();
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
