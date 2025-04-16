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
        $query = "
        SELECT r.room_number, r.room_type, r.capacity, r.amount, r.status 
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
        return $data ?: null;
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
        SELECT billing_id, description, amount, date_due, status
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

    // Initiate payment for a billing
    public function initiatePayment($user_id, $billing_id)
    {
        $this->connection->begin_transaction();
        try {
            // Step 1: Verify billing exists and belongs to user
            $query = "
                SELECT b.billing_id, b.student_id, b.amount, b.status, s.student_id
                FROM billing b
                JOIN students s ON b.student_id = s.student_id
                WHERE b.billing_id = ? AND s.user_id = ? AND b.status IN ('Unpaid', 'Partially Paid')";
            $stmt = $this->connection->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->connection->error);
            }
            $stmt->bind_param("ii", $billing_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $billing = $result->fetch_assoc();
            $stmt->close();

            if (!$billing) {
                throw new Exception("Billing not found or not payable");
            }

            // Step 2: Create payment record
            $payment_query = "
                INSERT INTO payments (student_id, amount, transaction_reference, payment_method, purpose, status)
                VALUES (?, ?, ?, ?, ?, 'Pending')";
            $stmt = $this->connection->prepare($payment_query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->connection->error);
            }
            $transaction_ref = "TXN" . time();
            $payment_method = "Pending"; // To be updated by gateway
            $purpose = "Hostel Fee";
            $stmt->bind_param("idsss", $billing['student_id'], $billing['amount'], $transaction_ref, $payment_method, $purpose);
            $stmt->execute();
            $payment_id = $this->connection->insert_id;
            $stmt->close();

            // Step 3: Link payment to billing
            $billing_update_query = "
                UPDATE billing
                SET payment_id = ?
                WHERE billing_id = ?";
            $stmt = $this->connection->prepare($billing_update_query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->connection->error);
            }
            $stmt->bind_param("ii", $payment_id, $billing_id);
            $stmt->execute();
            $stmt->close();

            // Commit transaction
            $this->connection->commit();
            return ['success' => true, 'payment_id' => $payment_id, 'transaction_ref' => $transaction_ref];
        } catch (Exception $e) {
            $this->connection->rollback();
            error_log("Payment initiation failed: " . $e->getMessage());
            throw new Exception("Payment initiation failed: " . $e->getMessage());
        }
    }

    // Confirm payment (for gateway callback or manual confirmation)
    public function confirmPayment($payment_id, $transaction_ref)
    {
        $this->connection->begin_transaction();
        try {
            // Step 1: Verify payment exists and is Pending
            $query = "
                SELECT payment_id, student_id, amount
                FROM payments
                WHERE payment_id = ? AND transaction_reference = ? AND status = 'Pending'";
            $stmt = $this->connection->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->connection->error);
            }
            $stmt->bind_param("is", $payment_id, $transaction_ref);
            $stmt->execute();
            $result = $stmt->get_result();
            $payment = $result->fetch_assoc();
            $stmt->close();

            if (!$payment) {
                throw new Exception("Payment not found or not pending");
            }

            // Step 2: Update payment status
            $payment_update_query = "
                UPDATE payments
                SET status = 'Completed', payment_date = NOW()
                WHERE payment_id = ?";
            $stmt = $this->connection->prepare($payment_update_query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->connection->error);
            }
            $stmt->bind_param("i", $payment_id);
            $stmt->execute();
            $stmt->close();

            // Step 3: Update billing status
            $billing_update_query = "
                UPDATE billing
                SET status = 'Fully Paid'
                WHERE payment_id = ?";
            $stmt = $this->connection->prepare($billing_update_query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->connection->error);
            }
            $stmt->bind_param("i", $payment_id);
            $stmt->execute();
            $stmt->close();

            // Commit transaction
            $this->connection->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->connection->rollback();
            error_log("Payment confirmation failed: " . $e->getMessage());
            throw new Exception("Payment confirmation failed: " . $e->getMessage());
        }
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
