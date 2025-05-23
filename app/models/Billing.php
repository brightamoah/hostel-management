<?php
require_once __DIR__ . "/../../database/db.php";

class Billing
{
    private $db;

    public function __construct()
    {
        $this->db = getDb();
    }

    public function __destruct()
    {
        if ($this->db) {
            $this->db->close();
        }
    }

    /**
     * Get all billings for DataTables server-side processing
     */
    public function getBillings($request)
    {
        $draw = isset($request['draw']) ? intval($request['draw']) : 1;
        $start = isset($request['start']) ? intval($request['start']) : 0;
        $length = isset($request['length']) ? intval($request['length']) : 10;
        $search = isset($request['search']['value']) ? $this->db->real_escape_string($request['search']['value']) : '';
        $status = isset($request['columns'][6]['search']['value']) ? $this->db->real_escape_string($request['columns'][6]['search']['value']) : '';
        $dueDate = isset($request['columns'][5]['search']['value']) ? $this->db->real_escape_string($request['columns'][5]['search']['value']) : '';
        $building = isset($request['columns'][2]['search']['value']) ? $this->db->real_escape_string($request['columns'][2]['search']['value']) : '';

        // Base query for current period
        $query = "
        SELECT b.billing_id, b.student_id, b.allocation_id, b.amount, b.description, b.date_issued, b.date_due, 
               b.status, b.paid_amount, s.first_name, s.last_name, r.building
        FROM billing b
        LEFT JOIN students s ON b.student_id = s.student_id
        LEFT JOIN allocations a ON b.allocation_id = a.allocation_id
        LEFT JOIN rooms r ON a.room_id = r.room_id
        ";

        // Filtering
        $where = [];
        if ($search) {
            $where[] = "(b.billing_id LIKE '%$search%' OR CONCAT(s.first_name, ' ', s.last_name) LIKE '%$search%' OR b.description LIKE '%$search%')";
        }
        if ($status) {
            $where[] = "b.status = '$status'";
        }
        if ($dueDate) {
            $where[] = "DATE(b.date_due) = '$dueDate'";
        }
        if ($building) {
            $where[] = "r.building = '$building'";
        }

        // Build WHERE clause
        $whereClause = "";
        if (!empty($where)) {
            $whereClause = " WHERE " . implode(" AND ", $where);
            $query .= $whereClause;
        }

        // Ordering
        $orderColumnIndex = isset($request['order'][0]['column']) ? intval($request['order'][0]['column']) : 4;
        $orderDir = $request['order'][0]['dir'] ?? 'desc';
        $columns = ['b.billing_id', 'b.billing_id', 's.first_name', 'b.amount', 'b.date_issued', 'b.date_due', 'b.status', 'b.paid_amount'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'b.date_issued';
        $query .= " ORDER BY $orderColumn $orderDir";

        // Get total record count before filtering
        $totalRecordsQuery = "SELECT COUNT(*) as total FROM billing";
        $totalRecordsResult = $this->db->query($totalRecordsQuery);
        $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

        // Get filtered record count
        $totalFiltered = $totalRecords;
        if (!empty($where)) {
            $filteredQuery = "
            SELECT COUNT(*) as total 
            FROM billing b
            LEFT JOIN students s ON b.student_id = s.student_id
            LEFT JOIN allocations a ON b.allocation_id = a.allocation_id
            LEFT JOIN rooms r ON a.room_id = r.room_id
            $whereClause
            ";
            $filteredResult = $this->db->query($filteredQuery);
            $totalFiltered = $filteredResult->fetch_assoc()['total'];
        }

        // Apply pagination
        $paginatedQuery = "$query LIMIT $start, $length";
        $result = $this->db->query($paginatedQuery);

        // Process results
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'billing_id' => $row['billing_id'],
                'student_name' => $row['first_name'] . ' ' . $row['last_name'],
                'student_id' => $row['student_id'],
                'amount' => $row['amount'],
                'description' => $row['description'],
                'date_issued' => $row['date_issued'],
                'date_due' => $row['date_due'],
                'status' => $row['status'],
                'paid_amount' => $row['paid_amount'],
                'building' => $row['building'],
                'balance' => max(0, $row['amount'] - $row['paid_amount']),
            ];
        }

        // Calculate statistics for current period
        $statsQuery = "
        SELECT 
            COALESCE(SUM(b.amount), 0) as total_billings,
            COALESCE(SUM(b.paid_amount), 0) as total_paid,
            COALESCE(SUM(CASE WHEN b.status = 'Fully Paid' THEN b.amount ELSE 0 END), 0) as paid_invoices,
            COALESCE(SUM(CASE WHEN b.status IN ('Partially Paid', 'Unpaid') THEN b.amount - COALESCE(b.paid_amount, 0) ELSE 0 END), 0) as pending_invoices,
            COALESCE(SUM(CASE WHEN b.status = 'Overdue' THEN b.amount - COALESCE(b.paid_amount, 0) ELSE 0 END), 0) as overdue_invoices,
            COUNT(*) as total_invoices,
            COALESCE(SUM(CASE WHEN b.status = 'Fully Paid' THEN 1 ELSE 0 END), 0) as paid_count,
            COALESCE(SUM(CASE WHEN b.status IN ('Partially Paid', 'Unpaid') THEN 1 ELSE 0 END), 0) as pending_count,
            COALESCE(SUM(CASE WHEN b.status = 'Overdue' THEN 1 ELSE 0 END), 0) as overdue_count
        FROM billing b
        LEFT JOIN students s ON b.student_id = s.student_id
        LEFT JOIN allocations a ON b.allocation_id = a.allocation_id
        LEFT JOIN rooms r ON a.room_id = r.room_id
        " . (!empty($whereClause) ? $whereClause : "");

        $statsResult = $this->db->query($statsQuery);
        if (!$statsResult) {
            error_log("Stats query error: " . $this->db->error);
            error_log("Stats query: " . $statsQuery);
            $stats = [
                'total_billings' => 0,
                'total_paid' => 0,
                'paid_invoices' => 0,
                'pending_invoices' => 0,
                'overdue_invoices' => 0,
                'total_invoices' => 0,
                'paid_count' => 0,
                'pending_count' => 0,
                'overdue_count' => 0,
                'collection_rate' => 0,
                'total_change' => 0,
                'paid_change' => 0,
                'pending_change' => 0,
                'overdue_change' => 0
            ];
        } else {
            $stats = $statsResult->fetch_assoc();

            // Ensure numeric values
            $stats['total_billings'] = floatval($stats['total_billings']);
            $stats['total_paid'] = floatval($stats['total_paid']);
            $stats['paid_invoices'] = floatval($stats['paid_invoices']);
            $stats['pending_invoices'] = floatval($stats['pending_invoices']);
            $stats['overdue_invoices'] = floatval($stats['overdue_invoices']);
            $stats['total_invoices'] = intval($stats['total_invoices']);
            $stats['paid_count'] = intval($stats['paid_count']);
            $stats['pending_count'] = intval($stats['pending_count']);
            $stats['overdue_count'] = intval($stats['overdue_count']);
            $stats['collection_rate'] = ($stats['total_billings'] > 0)
                ? round(($stats['total_paid'] / $stats['total_billings']) * 100, 1)
                : 0;

            // Calculate statistics for previous period (last month)
            $prevStatsQuery = "
            SELECT 
                COALESCE(SUM(b.amount), 0) as total_billings,
                COALESCE(SUM(b.paid_amount), 0) as total_paid,
                COALESCE(SUM(CASE WHEN b.status IN ('Partially Paid', 'Unpaid') THEN b.amount - COALESCE(b.paid_amount, 0) ELSE 0 END), 0) as pending_invoices,
                COALESCE(SUM(CASE WHEN b.status = 'Overdue' THEN b.amount - COALESCE(b.paid_amount, 0) ELSE 0 END), 0) as overdue_invoices
            FROM billing b
            LEFT JOIN students s ON b.student_id = s.student_id
            LEFT JOIN allocations a ON b.allocation_id = a.allocation_id
            LEFT JOIN rooms r ON a.room_id = r.room_id
            WHERE b.date_issued >= DATE_SUB(LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 2 MONTH)), INTERVAL 1 MONTH)
            AND b.date_issued < LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
            " . (!empty($whereClause) ? $whereClause : "");

            $prevStatsResult = $this->db->query($prevStatsQuery);
            if ($prevStatsResult) {
                $prevStats = $prevStatsResult->fetch_assoc();
                $prev_total_billings = floatval($prevStats['total_billings']);
                $prev_total_paid = floatval($prevStats['total_paid']);
                $prev_pending_invoices = floatval($prevStats['pending_invoices']);
                $prev_overdue_invoices = floatval($prevStats['overdue_invoices']);

                // Calculate percentage changes
                $stats['total_change'] = $prev_total_billings > 0
                    ? round((($stats['total_billings'] - $prev_total_billings) / $prev_total_billings) * 100, 1)
                    : ($stats['total_billings'] > 0 ? 100 : 0);
                $stats['paid_change'] = $prev_total_paid > 0
                    ? round((($stats['total_paid'] - $prev_total_paid) / $prev_total_paid) * 100, 1)
                    : ($stats['total_paid'] > 0 ? 100 : 0);
                $stats['pending_change'] = $prev_pending_invoices > 0
                    ? round((($stats['pending_invoices'] - $prev_pending_invoices) / $prev_pending_invoices) * 100, 1)
                    : ($stats['pending_invoices'] > 0 ? 100 : 0);
                $stats['overdue_change'] = $prev_overdue_invoices > 0
                    ? round((($stats['overdue_invoices'] - $prev_overdue_invoices) / $prev_overdue_invoices) * 100, 1)
                    : ($stats['overdue_invoices'] > 0 ? 100 : 0);
            } else {
                // If no previous data, set changes to 0 or 100 based on current values
                $stats['total_change'] = $stats['total_billings'] > 0 ? 100 : 0;
                $stats['paid_change'] = $stats['total_paid'] > 0 ? 100 : 0;
                $stats['pending_change'] = $stats['pending_invoices'] > 0 ? 100 : 0;
                $stats['overdue_change'] = $stats['overdue_invoices'] > 0 ? 100 : 0;
            }
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
            'stats' => $stats
        ];
    }

    /**
     * Get billing details by ID
     */
    public function getBillingById($id)
    {
        $id = $this->db->real_escape_string($id);
        $query = "
            SELECT b.*, s.first_name, s.last_name, u.email, s.phone_number, 
                    p.purpose, p.payment_method, p.payment_date, p.amount as payment_amount
            FROM billing b
            LEFT JOIN students s ON b.student_id = s.student_id
            LEFT JOIN allocations a ON b.allocation_id = a.allocation_id
            LEFT JOIN payments p ON b.billing_id = p.billing_id
            LEFT JOIN users u ON s.user_id = u.user_id
            WHERE b.billing_id = '$id'
        ";

        $result = $this->db->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data['details'] = [
                'billing_id' => $row['billing_id'],
                'student_name' => $row['first_name'] . ' ' . $row['last_name'],
                'student_id' => $row['student_id'],
                'student_email' => $row['email'],
                'student_phone' => $row['phone_number'],
                'amount' => $row['amount'],
                'description' => $row['description'],
                'date_issued' => $row['date_issued'],
                'date_due' => $row['date_due'],
                'status' => $row['status'],
                'paid_amount' => $row['paid_amount'],
                'purpose' => $row['purpose'] ?? 'Hostel Fee',
                'academic_period' => $row['academic_period'] ?? 'Not specified',
            ];
            if ($row['payment_date']) {
                $data['details']['transactions'][] = [
                    'payment_date' => $row['payment_date'],
                    'payment_method' => $row['payment_method'],
                    'amount' => $row['payment_amount'],
                ];
            }
        }

        return $data['details'] ?? null;
    }

    /**
     * Get payment history for a billing ID
     */
    public function getPaymentHistory($billingId)
    {
        $billingId = $this->db->real_escape_string($billingId);
        $query = "
            SELECT b.billing_id, b.amount, b.date_issued, b.date_due, b.paid_amount,
                   s.first_name, s.last_name, s.student_id,
                   p.payment_id, p.amount as payment_amount, p.payment_date, p.transaction_reference,
                   p.payment_method, p.purpose, p.status, p.payment_notes
            FROM billing b
            LEFT JOIN students s ON b.student_id = s.student_id
            LEFT JOIN payments p ON b.billing_id = p.billing_id
            WHERE b.billing_id = '$billingId'
        ";

        $result = $this->db->query($query);
        $data = ['details' => [], 'payments' => []];
        while ($row = $result->fetch_assoc()) {
            $data['details'] = [
                'billing_id' => $row['billing_id'],
                'student_name' => $row['first_name'] . ' ' . $row['last_name'],
                'student_id' => $row['student_id'],
                'amount' => $row['amount'],
                'date_issued' => $row['date_issued'],
                'date_due' => $row['date_due'],
                'paid_amount' => $row['paid_amount'],
            ];
            if ($row['payment_id']) {
                $data['payments'][] = [
                    'payment_id' => $row['payment_id'],
                    'amount' => $row['payment_amount'],
                    'payment_date' => $row['payment_date'],
                    'transaction_reference' => $row['transaction_reference'],
                    'payment_method' => $row['payment_method'],
                    'purpose' => $row['purpose'],
                    'status' => $row['status'],
                    'payment_notes' => $row['payment_notes'],
                    'recorded_by' => 'System',
                ];
            }
        }

        return $data;
    }

    /**
     * Create a new invoice
     */
    public function createInvoice($data)
    {
        $student_id = $this->db->real_escape_string($data['student_id']);
        $amount = floatval($data['amount']);
        $description = $this->db->real_escape_string($data['description']);
        $date_due = $this->db->real_escape_string($data['date_due']);
        $purpose = $this->db->real_escape_string($data['purpose']);
        $academic_period = $this->db->real_escape_string($data['academic_period'] ?? '');
        $payment_terms = $this->db->real_escape_string($data['payment_terms'] ?? 'net30');
        $send_notification = isset($data['send_notification']) ? 1 : 0;

        $date_issued = date('Y-m-d');
        $status = 'Unpaid';

        $query = "
            INSERT INTO billing (student_id, amount, description, date_issued, date_due, status, purpose, academic_period, payment_terms)
            VALUES ('$student_id', '$amount', '$description', '$date_issued', '$date_due', '$status', '$purpose', '$academic_period', '$payment_terms')
        ";

        if ($this->db->query($query)) {
            $billing_id = $this->db->insert_id;
            if ($send_notification) {
                $this->sendNotification($billing_id, $student_id, $amount, $date_due);
            }
            return ['success' => true, 'billing_id' => $billing_id];
        }

        return ['success' => false, 'error' => 'Failed to create invoice'];
    }

    /**
     * Record a payment
     */
    public function recordPayment($data)
    {
        $billing_id = $this->db->real_escape_string($data['billing_id']);
        $amount = floatval($data['amount']);
        $payment_date = $this->db->real_escape_string($data['payment_date']);
        $payment_method = $this->db->real_escape_string($data['payment_method']);
        $transaction_reference = $this->db->real_escape_string($data['transaction_reference']);
        $payment_notes = $this->db->real_escape_string($data['payment_notes'] ?? '');
        $status = 'Completed';

        // Get billing details
        $billingQuery = "SELECT amount, paid_amount, student_id FROM billing WHERE billing_id = '$billing_id'";
        $billingResult = $this->db->query($billingQuery);
        if (!$billingResult || $billingResult->num_rows == 0) {
            return ['success' => false, 'error' => 'Invoice not found'];
        }
        $billing = $billingResult->fetch_assoc();
        $student_id = $billing['student_id'];
        $new_paid_amount = floatval($billing['paid_amount']) + $amount;

        // Update billing status
        $total_amount = floatval($billing['amount']);
        $new_status = $new_paid_amount >= $total_amount ? 'Fully Paid' : 'Partially Paid';

        $this->db->begin_transaction();
        try {
            // Insert payment
            $paymentQuery = "
                INSERT INTO payments (student_id, billing_id, amount, payment_date, transaction_reference, payment_method, status, payment_notes)
                VALUES ('$student_id', '$billing_id', '$amount', '$payment_date', '$transaction_reference', '$payment_method', '$status', '$payment_notes')
            ";
            $this->db->query($paymentQuery);

            // Update billing
            $updateBillingQuery = "
                UPDATE billing 
                SET paid_amount = '$new_paid_amount', status = '$new_status'
                WHERE billing_id = '$billing_id'
            ";
            $this->db->query($updateBillingQuery);

            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'error' => 'Failed to record payment: ' . $e->getMessage()];
        }
    }

    /**
     * Send payment reminder
     */
    public function sendReminder($data)
    {
        $billing_id = $this->db->real_escape_string($data['billing_id']);
        $subject = $this->db->real_escape_string($data['subject']);
        $message = $this->db->real_escape_string($data['message']);
        $attach_invoice = isset($data['attach_invoice']) ? 1 : 0;

        // Get billing and student details
        $query = "
            SELECT b.amount, b.paid_amount, b.date_due, s.email, s.first_name, s.last_name
            FROM billing b
            JOIN students s ON b.student_id = s.student_id
            WHERE b.billing_id = '$billing_id'
        ";
        $result = $this->db->query($query);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->sendEmail($row['email'], $subject, $message, $billing_id, $attach_invoice);
            return ['success' => true];
        }

        return ['success' => false, 'error' => 'Failed to send reminder'];
    }

    /**
     * Bulk send reminders
     */
    public function bulkSendReminders($billing_ids)
    {
        $success_count = 0;
        foreach ($billing_ids as $billing_id) {
            $billing_id = $this->db->real_escape_string($billing_id);
            $query = "
                SELECT b.amount, b.paid_amount, b.date_due, s.email, s.first_name, s.last_name
                FROM billing b
                JOIN students s ON b.student_id = s.student_id
                WHERE b.billing_id = '$billing_id' AND b.status IN ('Unpaid', 'Partially Paid', 'Overdue')
            ";
            $result = $this->db->query($query);
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $subject = "Payment Reminder: Invoice #INV-" . str_pad($billing_id, 5, '0', STR_PAD_LEFT);
                $message = "Dear {$row['first_name']} {$row['last_name']},\n\nThis is a reminder that your payment of $" .
                    ($row['amount'] - $row['paid_amount']) . " for invoice #INV-" .
                    str_pad($billing_id, 5, '0', STR_PAD_LEFT) . " is due on " .
                    date('M d, Y', strtotime($row['date_due'])) . ".\n\nThank you,\nKings Hostel Management";
                $this->sendEmail($row['email'], $subject, $message, $billing_id, true);
                $success_count++;
            }
        }

        return ['success' => true, 'sent' => $success_count];
    }

    /**
     * Get all buildings
     */
    public function getBuildings()
    {
        $query = "SELECT DISTINCT building FROM rooms WHERE building IS NOT NULL";
        $result = $this->db->query($query);
        $buildings = [];
        while ($row = $result->fetch_assoc()) {
            $buildings[] = $row['building'];
        }
        return $buildings;
    }

    /**
     * Get all students
     */
    public function getStudents()
    {
        $query = "SELECT student_id, first_name, last_name FROM students";
        $result = $this->db->query($query);
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = [
                'student_id' => $row['student_id'],
                'name' => $row['first_name'] . ' ' . $row['last_name']
            ];
        }
        return $students;
    }

    /**
     * Send email notification (placeholder)
     */
    private function sendNotification($billing_id, $student_id, $amount, $date_due)
    {
        $query = "SELECT email, first_name, last_name FROM students WHERE student_id = '$student_id'";
        $result = $this->db->query($query);
        if ($result && $result->num_rows > 0) {
            $student = $result->fetch_assoc();
            $subject = "New Invoice #INV-" . str_pad($billing_id, 5, '0', STR_PAD_LEFT);
            $message = "Dear {$student['first_name']} {$student['last_name']},\n\nA new invoice (#INV-" .
                str_pad($billing_id, 5, '0', STR_PAD_LEFT) . ") for $$amount has been issued. " .
                "It is due on " . date('M d, Y', strtotime($date_due)) . ".\n\nThank you,\nKings Hostel Management";
            $this->sendEmail($student['email'], $subject, $message, $billing_id, true);
        }
    }

    /**
     * Send email (placeholder for actual implementation)
     */
    private function sendEmail($to, $subject, $message, $billing_id, $attach_invoice)
    {
        error_log("Sending email to $to: Subject: $subject, Message: $message, Attach Invoice: " . ($attach_invoice ? 'Yes' : 'No'));
    }
}
