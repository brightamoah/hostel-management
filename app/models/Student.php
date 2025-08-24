<?php
class Student
{
    private $connection;

    public function __construct($db)
    {
        $this->connection = $db;
    }

    // Fetch Room Allocation Status
    public function getRoomAllocation($user_id)
    {
        // First query to get room allocation details
        $query = "
    SELECT r.room_id, r.room_number, r.room_type, r.building, r.floor, r.capacity, r.amount, r.status 
    FROM allocations a 
    JOIN rooms r ON a.room_id = r.room_id 
    JOIN students s ON a.student_id = s.student_id 
    WHERE s.user_id = ? AND a.status = 'Active'
    LIMIT 1";

        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();

        // If no room found, return null
        if (!$data) {
            return null;
        }

        // Second query to get other residents in the same room
        $room_id = $data['room_id'];
        $current_student_query = "
    SELECT s.student_id 
    FROM students s 
    WHERE s.user_id = ?";

        $stmt = $this->connection->prepare($current_student_query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $current_student = $result->fetch_assoc();
        $current_student_id = $current_student['student_id'];
        $stmt->close();

        // Query to get other residents in the same room
        $residents_query = "
    SELECT CONCAT(s.first_name, ' ', s.last_name) AS resident_name, s.phone_number, s.health_condition 
    FROM allocations a 
    JOIN students s ON a.student_id = s.student_id 
    WHERE a.room_id = ? AND a.status = 'Active' AND s.student_id != ?";

        $stmt = $this->connection->prepare($residents_query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        $stmt->bind_param("ii", $room_id, $current_student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Add other residents to the room data
        $data['other_residents'] = [];
        while ($resident = $result->fetch_assoc()) {
            $data['other_residents'][] = [
                'resident_name' => $resident['resident_name'],
                'phone_number' => $resident['phone_number'],
                'health_condition' => $resident['health_condition'] ?? 'None',
            ];
        }
        $stmt->close();

        return $data;
    }

    // Fetch Payment Status (Total Paid)
    public function getTotalPaid($user_id)
    {
        $query = "
        SELECT SUM(amount) as total_paid 
        FROM payments p 
        JOIN students s ON p.student_id = s.student_id 
        WHERE s.user_id = ? AND p.status = 'Completed'";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data['total_paid'] ?? 0;
    }

    // Fetch Pending Balance
    public function getPendingBalance($user_id)
    {
        $query = "
        SELECT SUM(b.amount) as pending_balance 
        FROM billing b 
        JOIN students s ON b.student_id = s.student_id 
        WHERE s.user_id = ? AND b.status IN ('Unpaid', 'Partially Paid', 'Overdue')";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data['pending_balance'] ?? 0;
    }

    // Fetch Maintenance Requests
    public function getOpenMaintenanceRequests($user_id)
    {
        $query = "
        SELECT COUNT(*) as open_requests 
        FROM maintenance_requests mr 
        JOIN students s ON mr.student_id = s.student_id 
        WHERE s.user_id = ? AND mr.status = 'Pending'";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data['open_requests'] ?? 0;
    }

    // Fetch Pending Visitors (last 30 days)
    public function getPendingVisitors($user_id)
    {
        $query = "
        SELECT COUNT(*) as pending_visitors 
        FROM visitors v 
        JOIN students s ON v.student_id = s.student_id 
        WHERE s.user_id = ? AND v.status = 'Pending' 
        AND v.visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data['pending_visitors'] ?? 0;
    }

    // Fetch the student's first name from the users table
    public function getFirstName($user_id)
    {
        $query = "
        SELECT first_name 
        FROM students s 
        JOIN users u ON s.user_id = u.user_id 
        WHERE u.user_id = ?";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data['first_name'] ?? '';
    }

    // Fetch billings for a user
    public function getBillings($user_id)
    {
        $query = "
        SELECT billing_id, description, amount, date_due, status, billing_type, paid_amount, outstanding_amount
        FROM billing b
        JOIN students s ON b.student_id = s.student_id
        WHERE s.user_id = ?
        ORDER BY billing_id DESC";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $billings = [];
        while ($row = $result->fetch_assoc()) {
            $billings[] = $row;
        }
        $stmt->close();
        return $billings;
    }

    // Confirm payment (for gateway callback or manual confirmation)
    public function confirmPayment($payment_id, $transaction_ref)
    {
        $this->connection->begin_transaction();
        try {
            require_once __DIR__ . "/../../services/PaymentService.php";
            $paymentService = new PaymentService();

            $result = $paymentService->verifyPayment($transaction_ref);
            if ($result['success']) {
                return ['success' => true, 'message' => 'Payment confirmed successfully'];
            } else {
                return ['success' => false, 'error' => $result['error'] ?? 'Payment verification failed'];
            }
        } catch (Exception $e) {
            $this->connection->rollback();
            error_log("Payment confirmation failed: " . $e->getMessage());
            throw new Exception("Payment confirmation failed: " . $e->getMessage());
        }
    }



    // Get recent activities for a student
    public function getRecentActivities($user_id, $limit = 10)
    {
        $activities = [];

        // Get student_id first
        $student_query = "SELECT student_id FROM students WHERE user_id = ?";
        $stmt = $this->connection->prepare($student_query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student_data = $result->fetch_assoc();
        $stmt->close();

        if (!$student_data) {
            return $activities;
        }

        $student_id = $student_data['student_id'];

        // Recent Payments
        $payment_query = "
            SELECT 'payment' as activity_type, amount, payment_date as activity_date, 
                   CONCAT('Payment of GHS ', FORMAT(amount, 2), ' - ', purpose) as description,
                   status, transaction_reference
            FROM payments 
            WHERE student_id = ? 
            ORDER BY payment_date DESC 
            LIMIT 5";

        $stmt = $this->connection->prepare($payment_query);
        if ($stmt) {
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $activities[] = $row;
            }
            $stmt->close();
        }

        // Recent Maintenance Requests
        $maintenance_query = "
            SELECT 'maintenance' as activity_type, request_date as activity_date,
                   CONCAT('Maintenance Request: ', issue_type, ' - ', LEFT(description, 50), '...') as description,
                   status, priority
            FROM maintenance_requests 
            WHERE student_id = ? 
            ORDER BY request_date DESC 
            LIMIT 5";

        $stmt = $this->connection->prepare($maintenance_query);
        if ($stmt) {
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $activities[] = $row;
            }
            $stmt->close();
        }

        // Recent Visitor Registrations
        $visitor_query = "
            SELECT 'visitor' as activity_type, registered_at as activity_date,
                   CONCAT('Visitor Registration: ', visitor_name, ' (', relation, ')') as description,
                   status, visit_date
            FROM visitors 
            WHERE student_id = ? 
            ORDER BY registered_at DESC 
            LIMIT 5";

        $stmt = $this->connection->prepare($visitor_query);
        if ($stmt) {
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $activities[] = $row;
            }
            $stmt->close();
        }

        // Recent Complaints
        $complaint_query = "
            SELECT 'complaint' as activity_type, submitted_at as activity_date,
                   CONCAT('Complaint: ', complaint_type, ' - ', LEFT(description, 50), '...') as description,
                   status, priority
            FROM complaints 
            WHERE student_id = ? 
            ORDER BY submitted_at DESC 
            LIMIT 5";

        $stmt = $this->connection->prepare($complaint_query);
        if ($stmt) {
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $activities[] = $row;
            }
            $stmt->close();
        }

        // Recent Room Allocations
        $allocation_query = "
            SELECT 'allocation' as activity_type, allocated_at as activity_date,
                   CONCAT('Room Allocation: ', r.room_number, ' (', r.building, ')') as description,
                   a.status, a.start_date
            FROM allocations a
            JOIN rooms r ON a.room_id = r.room_id
            WHERE a.student_id = ? 
            ORDER BY allocated_at DESC 
            LIMIT 3";

        $stmt = $this->connection->prepare($allocation_query);
        if ($stmt) {
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $activities[] = $row;
            }
            $stmt->close();
        }

        // Sort all activities by date and limit
        usort($activities, function ($a, $b) {
            return strtotime($b['activity_date']) - strtotime($a['activity_date']);
        });

        return array_slice($activities, 0, $limit);
    }

    // Summarize payment status
    public function getPaymentStatusSummary($user_id)
    {
        $query = "
        SELECT 
            (SELECT COUNT(*) FROM payments p JOIN students s ON p.student_id = s.student_id 
             WHERE s.user_id = ? AND p.status = 'Completed') as completed_count,
            (SELECT COUNT(*) FROM payments p JOIN students s ON p.student_id = s.student_id 
             WHERE s.user_id = ? AND p.status = 'Pending') as pending_count,
            (SELECT COUNT(*) FROM payments p JOIN students s ON p.student_id = s.student_id 
             WHERE s.user_id = ? AND p.status = 'Failed') as failed_count,
            (SELECT COUNT(*) FROM payments p JOIN students s ON p.student_id = s.student_id 
             WHERE s.user_id = ? AND p.status = 'Refunded') as refunded_count";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        $stmt->bind_param("iiii", $user_id, $user_id, $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();

        // Determine overall status
        if ($data['completed_count'] > 0 && $data['pending_count'] == 0 && $data['failed_count'] == 0) {
            return ['status' => 'Cleared', 'class' => 'text-success'];
        } elseif ($data['pending_count'] > 0) {
            return ['status' => 'Pending', 'class' => 'text-warning'];
        } elseif ($data['failed_count'] > 0) {
            return ['status' => 'Failed', 'class' => 'text-danger'];
        } elseif ($data['refunded_count'] > 0) {
            return ['status' => 'Refunded', 'class' => 'text-info'];
        } else {
            return ['status' => 'No Payments', 'class' => 'text-muted'];
        }
    }
}
