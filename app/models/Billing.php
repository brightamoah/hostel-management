<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../../services/EmailService.php";
require_once __DIR__ . "/BaseModel.php";

class Billing extends BaseModel
{
    private $emailService;

    public function __construct()
    {
        parent::__construct();
        $this->emailService = new EmailService();
    }

    public function __destruct()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }


    /**
     * Get billing details by ID (with hostel access validation)
     */
    public function getBillingById($id)
    {
        $id = (int)$id;

        // First check if billing exists and get its hostel_id
        $check_query = "SELECT hostel_id FROM billing WHERE billing_id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows === 0) {
            $check_stmt->close();
            return null; // Billing not found
        }

        $billing_hostel = $check_result->fetch_assoc();
        $check_stmt->close();

        // Validate hostel access for non-super admins
        try {
            $this->validateHostelAccess($billing_hostel['hostel_id']);
        } catch (Exception $e) {
            error_log("Hostel access denied for billing ID $id: " . $e->getMessage());
            return null; // Access denied
        }

        $query = "
           SELECT b.*, s.first_name, s.last_name, u.email, s.phone_number, 
                    p.purpose, p.payment_method, p.payment_date, p.amount as payment_amount
            FROM billing b
            LEFT JOIN students s ON b.student_id = s.student_id
            LEFT JOIN allocations a ON b.allocation_id = a.allocation_id
            LEFT JOIN payments p ON b.billing_id = p.billing_id AND p.status = 'Completed'
            LEFT JOIN users u ON s.user_id = u.user_id
            WHERE b.billing_id = ?
            ORDER BY p.payment_date DESC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = null;
        $transactions = [];

        while ($row = $result->fetch_assoc()) {
            if ($data === null) {
                // Map billing_type to payment purpose
                $billing_type_to_purpose = [
                    'Hostel Fee' => 'Hostel Fee',
                    'Security Deposit' => 'Security Deposit',
                    'Utility Fee' => 'Other',
                    'Maintenance Fee' => 'Maintenance Charge',
                    'Late Payment Penalty' => 'Penalty',
                    'Other' => 'Other'
                ];

                $billing_type = $row['billing_type'];
                $mapped_purpose = $billing_type_to_purpose[$billing_type] ?? 'Other';

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
                    'billing_type' => $billing_type,
                    'mapped_purpose' => $mapped_purpose,
                    'outstanding_balance' => $row['outstanding_amount'],
                    'late_payment_fee' => $row['late_fee'],
                    'purpose' => $row['purpose'] ?? 'Hostel Fee',
                    'academic_period' => $row['academic_period'] ?? 'Not specified',
                    'payment_terms' => $row['payment_terms'],
                ];
            }


            // Add transaction if payment data exists
            if ($row['payment_date']) {
                $transactions[] = [
                    'payment_date' => $row['payment_date'],
                    'payment_method' => $row['payment_method'],
                    'amount' => $row['payment_amount'],
                ];
            }
        }

        $stmt->close();

        // Add all transactions to the data
        if ($data !== null) {
            return [
                'details' => $data['details'],
                'transactions' => $transactions
            ];
        }

        return null;
    }



    /**
     * Get payment history for a billing ID
     */
    public function getPaymentHistory($billingId)
    {
        $billingId = $this->conn->real_escape_string($billingId);
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

        $result = $this->conn->query($query);
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
     * Build the base query for fetching billings
     */
    private function buildBaseQuery()
    {
        $query = "
            SELECT b.billing_id, b.student_id, b.allocation_id, b.hostel_id, b.amount, b.description, 
                   b.date_issued, b.date_due, b.status, b.paid_amount, 
                   s.first_name, s.last_name, r.building, h.hostel_name
            FROM billing b
            LEFT JOIN students s ON b.student_id = s.student_id
            LEFT JOIN allocations a ON b.allocation_id = a.allocation_id
            LEFT JOIN rooms r ON a.room_id = r.room_id
            LEFT JOIN hostels h ON b.hostel_id = h.hostel_id
        ";
        return $query;
    }

    /** 
     * Build the WHERE clause for filtering billings
     */
    private function buildWhereClause(array $request): string
    {
        $search = isset($request['search']['value']) ? $this->conn->real_escape_string($request['search']['value']) : '';
        $status = isset($request['columns'][6]['search']['value']) ? $this->conn->real_escape_string($request['columns'][6]['search']['value']) : '';
        $building = isset($request['columns'][7]['search']['value']) ? $this->conn->real_escape_string($request['columns'][7]['search']['value']) : '';

        $where = ["1=1"]; // Base condition to ensure valid SQL

        if ($search) {
            $where[] = "(b.billing_id LIKE '%$search%' OR CONCAT(s.first_name, ' ', s.last_name) LIKE '%$search%' OR b.description LIKE '%$search%')";
        }
        if ($status) {
            $where[] = "b.status = '$status'";
        }
        if ($building) {
            $where[] = "r.building = '$building'";
        }

        return " WHERE " . implode(" AND ", $where);
    }


    /**
     * Apply ordering to the query
     */
    private function buildOrderClause(array $request): string
    {
        $orderColumnIndex = isset($request['order'][0]['column']) ? intval($request['order'][0]['column']) : 4;
        $orderDir = $request['order'][0]['dir'] ?? 'desc';
        $columns = ['b.billing_id', 'b.billing_id', 's.first_name', 'b.amount', 'b.date_issued', 'b.date_due', 'b.status', 'b.paid_amount'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'b.date_issued';
        return " ORDER BY $orderColumn $orderDir";
    }

    /**
     * Get total record count
     */
    private function getTotalRecords(): int
    {
        $totalRecordsQuery = "SELECT COUNT(*) as total FROM billing b WHERE 1=1";
        // Apply hostel filtering for non-super admins
        $totalRecordsQuery = $this->addHostelFilter($totalRecordsQuery, 'b');

        $totalRecordsResult = $this->conn->query($totalRecordsQuery);
        return $totalRecordsResult->fetch_assoc()['total'];
    }

    /**
     * Get filtered record count
     */
    private function getFilteredRecords(string $whereClause): int
    {
        $filteredQuery = "
            SELECT COUNT(*) as total 
            FROM billing b
            LEFT JOIN students s ON b.student_id = s.student_id
            LEFT JOIN allocations a ON b.allocation_id = a.allocation_id
            LEFT JOIN rooms r ON a.room_id = r.room_id
            LEFT JOIN hostels h ON b.hostel_id = h.hostel_id
            $whereClause
        ";
        $filteredResult = $this->conn->query($filteredQuery);
        return $filteredResult->fetch_assoc()['total'];
    }


    /**
     * Fetch billing data
     */
    private function fetchBillingData(string $query): array
    {
        $result = $this->conn->query($query);
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
        return $data;
    }

    /**
     * Calculate billing statistics for current period
     */
    private function calculateCurrentStats(string $whereClause): array
    {
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
            LEFT JOIN hostels h ON b.hostel_id = h.hostel_id
            $whereClause
        ";

        $statsResult = $this->conn->query($statsQuery);
        if (!$statsResult) {
            error_log("Stats query error: " . $this->conn->error);
            error_log("Stats query: $statsQuery");
            return [
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
        }

        $stats = $statsResult->fetch_assoc();
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

        return $stats;
    }

    /**
     * Calculate statistics for the previous period
     */
    private function calculatePreviousStats(string $whereClause): array
    {
        // Create the previous period WHERE clause by combining date filter with existing filters
        $prevWhereClause = str_replace(
            "WHERE 1=1",
            "WHERE b.date_issued >= DATE_SUB(LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 2 MONTH)), INTERVAL 1 MONTH)
             AND b.date_issued < LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))",
            $whereClause
        );

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
            LEFT JOIN hostels h ON b.hostel_id = h.hostel_id
            $prevWhereClause
        ";

        $prevStatsResult = $this->conn->query($prevStatsQuery);
        if (!$prevStatsResult) {
            return [
                'total_billings' => 0,
                'total_paid' => 0,
                'pending_invoices' => 0,
                'overdue_invoices' => 0
            ];
        }

        return $prevStatsResult->fetch_assoc();
    }


    /**
     * Combine current and previous stats to calculate changes
     */
    private function combineStats(array $currentStats, array $prevStats): array
    {
        $prev_total_billings = floatval($prevStats['total_billings']);
        $prev_total_paid = floatval($prevStats['total_paid']);
        $prev_pending_invoices = floatval($prevStats['pending_invoices']);
        $prev_overdue_invoices = floatval($prevStats['overdue_invoices']);

        $currentStats['total_change'] = $prev_total_billings > 0
            ? round((($currentStats['total_billings'] - $prev_total_billings) / $prev_total_billings) * 100, 1)
            : ($currentStats['total_billings'] > 0 ? 100 : 0);
        $currentStats['paid_change'] = $prev_total_paid > 0
            ? round((($currentStats['total_paid'] - $prev_total_paid) / $prev_total_paid) * 100, 1)
            : ($currentStats['total_paid'] > 0 ? 100 : 0);
        $currentStats['pending_change'] = $prev_pending_invoices > 0
            ? round((($currentStats['pending_invoices'] - $prev_pending_invoices) / $prev_pending_invoices) * 100, 1)
            : ($currentStats['pending_invoices'] > 0 ? 100 : 0);
        $currentStats['overdue_change'] = $prev_overdue_invoices > 0
            ? round((($currentStats['overdue_invoices'] - $prev_overdue_invoices) / $prev_overdue_invoices) * 100, 1)
            : ($currentStats['overdue_invoices'] > 0 ? 100 : 0);

        return $currentStats;
    }

    /**
     * Get all billings for DataTables server-side processing
     */
    public function getBillings($request)
    {
        $draw = isset($request['draw']) ? intval($request['draw']) : 1;

        // Build query components
        $baseQuery = $this->buildBaseQuery();
        $whereClause = $this->buildWhereClause($request);

        // Apply hostel filtering for non-super admins
        $whereClause = $this->addHostelFilter($whereClause, 'b');

        $orderClause = $this->buildOrderClause($request);

        // Construct final query without pagination
        $query = $baseQuery . $whereClause . $orderClause;

        // Get total and filtered record counts
        $totalRecords = $this->getTotalRecords();
        $totalFiltered = $this->getFilteredRecords($whereClause);

        // Fetch billing data
        $data = $this->fetchBillingData($query);

        // Calculate statistics
        $currentStats = $this->calculateCurrentStats($whereClause);
        $prevStats = $this->calculatePreviousStats($whereClause);
        $stats = $this->combineStats($currentStats, $prevStats);

        return [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
            'stats' => $stats
        ];
    }


    /**
     * Create a new invoice (with hostel validation and auto-assignment)
     */
    public function createInvoice($data)
    {
        $student_id = (int)$data['student_id'];
        $amount = floatval($data['amount']);
        $description = $this->conn->real_escape_string($data['description']);
        $due_date = $this->conn->real_escape_string($data['due_date']);
        $billing_type = $this->conn->real_escape_string($data['purpose']);
        $academic_period = $this->conn->real_escape_string($data['academic_period'] ?? '');
        $payment_terms = $this->conn->real_escape_string($data['payment_terms'] ?? '30');
        $send_notification = isset($data['send_notification']) && $data['send_notification'] === 'on' ? 1 : 0;

        // Validate student exists and get their hostel info
        $studentQuery = "
            SELECT s.student_id, r.hostel_id 
            FROM students s
            LEFT JOIN allocations a ON s.student_id = a.student_id AND a.status = 'Active'
            LEFT JOIN rooms r ON a.room_id = r.room_id
            WHERE s.student_id = ? AND s.resident_status = 'Active'
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($studentQuery);
        $stmt->bind_param('i', $student_id);
        $stmt->execute();
        $studentResult = $stmt->get_result();

        if ($studentResult->num_rows === 0) {
            $stmt->close();
            return ['success' => false, 'error' => 'Student not found or not active'];
        }

        $studentData = $studentResult->fetch_assoc();
        $student_hostel_id = $studentData['hostel_id'];
        $stmt->close();

        // Validate hostel access for non-super admins
        if (!$this->isSuperAdmin()) {
            $admin_hostel_id = $this->getCurrentAdminHostelId();
            if (!$admin_hostel_id) {
                return ['success' => false, 'error' => 'Admin not assigned to any hostel'];
            }

            if ($student_hostel_id && $student_hostel_id != $admin_hostel_id) {
                return ['success' => false, 'error' => 'Cannot create billing for student from different hostel'];
            }

            // Use admin's hostel_id if student doesn't have one
            if (!$student_hostel_id) {
                $student_hostel_id = $admin_hostel_id;
            }
        }

        // Get student's active allocation
        $allocationQuery = "SELECT allocation_id FROM allocations WHERE student_id = ? AND status = 'Active' LIMIT 1";
        $stmt = $this->conn->prepare($allocationQuery);
        $stmt->bind_param('i', $student_id);
        $stmt->execute();
        $allocationResult = $stmt->get_result();
        $allocation_id = null;

        if ($allocationResult && $allocationResult->num_rows > 0) {
            $allocation = $allocationResult->fetch_assoc();
            $allocation_id = $allocation['allocation_id'];
        }
        $stmt->close();


        // Validate inputs
        $errors = [];
        if (!$student_id) $errors[] = 'Student ID';
        if (!$amount || $amount <= 0) $errors[] = 'Amount (must be greater than 0)';
        if (!$due_date) $errors[] = 'Due Date';
        if (!$billing_type) $errors[] = 'Purpose';
        if (!$academic_period) $errors[] = 'Academic Period';
        if (!$payment_terms) $errors[] = 'Payment Terms';
        if (!$description) $errors[] = 'Description';

        if (!empty($errors)) {
            return ['success' => false, 'error' => 'The following fields are required: ' . implode(', ', $errors)];
        }

        // Validate due_date format (Y-m-d H:i:s)
        $due_date_obj = DateTime::createFromFormat('Y-m-d H:i:s', $due_date);
        if (!$due_date_obj) {
            return ['success' => false, 'error' => 'Invalid due date format. Expected Y-m-d H:i:s or Y-m-d H:i, received: ' . $due_date];
        }

        $due_date_formatted = $due_date_obj->format('Y-m-d H:i:s');

        // Map academic_period to database ENUM
        $academic_period_map = [
            'first_semester' => 'Semester 1',
            'second_semester' => 'Semester 2',
            'entire_year' => 'Entire Year',
            'vacation_period' => 'Vacation Period',
        ];
        $academic_period = $academic_period_map[strtolower($academic_period)] ?? 'Semester 1';

        // Map payment_terms to database ENUM
        $payment_terms_map = [
            '15' => 'Net 15 Days',
            '30' => 'Net 30 Days',
            '45' => 'Net 45 Days',
            'immediate' => 'Immediate Payment',
        ];
        $payment_terms = $payment_terms_map[strtolower($payment_terms)] ?? 'Net 30 Days';

        $date_issued = date('Y-m-d H:i:s');
        $status = 'Unpaid';

        $query = "
            INSERT INTO billing (student_id, allocation_id, hostel_id, amount, description, date_issued, date_due, status, billing_type, academic_period, payment_terms)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return ['success' => false, 'error' => 'Failed to prepare statement: ' . $this->conn->error];
        }

        $stmt->bind_param(
            'iiidsssssss',
            $student_id,
            $allocation_id,
            $student_hostel_id,
            $amount,
            $description,
            $date_issued,
            $due_date_formatted,
            $status,
            $billing_type,
            $academic_period,
            $payment_terms
        );

        if ($stmt->execute()) {
            $billing_id = $this->conn->insert_id;
            $email_result = ['success' => false];

            if ($send_notification) {
                $email_result = $this->sendNotification(
                    $billing_id,
                    $student_id,
                    $amount,
                    $due_date_formatted,
                    $description,
                    $billing_type
                );
            }
            $stmt->close();
            return [
                'success' => true,
                'billing_id' => $billing_id,
                'email_result' => $email_result
            ];
        }

        $stmt->close();
        return ['success' => false, 'error' => 'Failed to create invoice: ' . $this->conn->error];
    }


    /**
     * Update an existing invoice (with hostel access validation)
     */
    public function updateInvoice($billing_id, $data)
    {
        $billing_id = (int)$billing_id;

        $currentQuery = "SELECT * FROM billing WHERE billing_id = ?";
        $stmt = $this->conn->prepare($currentQuery);
        $stmt->bind_param('i', $billing_id);
        $stmt->execute();
        $currentResult = $stmt->get_result();

        if (!$currentResult || $currentResult->num_rows === 0) {
            $stmt->close();
            return ['success' => false, 'error' => 'Billing record not found'];
        }

        $currentData = $currentResult->fetch_assoc();
        $stmt->close();

        // Validate hostel access for non-super admins
        try {
            $this->validateHostelAccess($currentData['hostel_id']);
        } catch (Exception $e) {
            error_log("Hostel access denied for update billing ID $billing_id: " . $e->getMessage());
            return ['success' => false, 'error' => 'Access denied: Invoice not found'];
        }

        $student_id = $this->conn->real_escape_string($data['student_id']);
        $amount = floatval($data['amount']);
        $description = $this->conn->real_escape_string($data['description']);
        $due_date = $this->conn->real_escape_string($data['date_due'] ?? $data['due_date'] ?? '');
        $billing_type = $this->conn->real_escape_string($data['billing_type'] ?? $data['purpose'] ?? '');
        $academic_period = $this->conn->real_escape_string($data['academic_period']);
        $payment_terms = $this->conn->real_escape_string($data['payment_terms']);

        $errors = [];
        if (!$student_id) $errors[] = 'Student ID';
        if (!$amount || $amount <= 0) $errors[] = 'Amount (must be greater than 0)';
        if (!$due_date) $errors[] = 'Due Date';
        if (!$billing_type) $errors[] = 'Billing Type';
        if (!$academic_period) $errors[] = 'Academic Period';
        if (!$payment_terms) $errors[] = 'Payment Terms';
        if (!$description) $errors[] = 'Description';

        if (!empty($errors)) {
            return ['success' => false, 'error' => 'The following fields are required: ' . implode(', ', $errors)];
        }

        $due_date_formatted = null;

        // Try HTML5 datetime-local format first (Y-m-d\TH:i)
        $due_date_obj = DateTime::createFromFormat('Y-m-d\TH:i', $due_date);

        if (!$due_date_obj) {
            // Try full datetime format (Y-m-d H:i:s)
            $due_date_obj = DateTime::createFromFormat('Y-m-d H:i:s', $due_date);
        }

        if (!$due_date_obj) {
            // Try without seconds (Y-m-d H:i)
            $due_date_obj = DateTime::createFromFormat('Y-m-d H:i', $due_date);
        }

        if (!$due_date_obj) {
            return ['success' => false, 'error' => "Invalid due date format. Expected Y-m-d H:i:s or Y-m-dTH:i, received: $due_date"];
        }

        $due_date_formatted = $due_date_obj->format('Y-m-d H:i:s');

        $academic_period_map = [
            'first_semester' => 'Semester 1',
            'second_semester' => 'Semester 2',
            'entire_year' => 'Entire Year',
            'vacation_period' => 'Vacation Period',
        ];

        if (isset($academic_period_map[strtolower($academic_period)])) {
            $academic_period = $academic_period_map[strtolower($academic_period)];
        }

        // Map payment_terms to database ENUM if needed
        $payment_terms_map = [
            '15' => 'Net 15 Days',
            '30' => 'Net 30 Days',
            '45' => 'Net 45 Days',
            'immediate' => 'Immediate Payment',
        ];

        if (isset($payment_terms_map[strtolower($payment_terms)])) {
            $payment_terms = $payment_terms_map[strtolower($payment_terms)];
        }

        // Check if there are any changes
        $hasChanges = false;
        $changes = [];

        if ($currentData['student_id'] != $student_id) {
            $hasChanges = true;
            $changes[] = 'Student ID';
        }
        if (floatval($currentData['amount']) != $amount) {
            $hasChanges = true;
            $changes[] = 'Amount';
        }
        if ($currentData['description'] != $description) {
            $hasChanges = true;
            $changes[] = 'Description';
        }
        if ($currentData['date_due'] != $due_date_formatted) {
            $hasChanges = true;
            $changes[] = 'Due Date';
        }
        if ($currentData['billing_type'] != $billing_type) {
            $hasChanges = true;
            $changes[] = 'Billing Type';
        }
        if ($currentData['academic_period'] != $academic_period) {
            $hasChanges = true;
            $changes[] = 'Academic Period';
        }
        if ($currentData['payment_terms'] != $payment_terms) {
            $hasChanges = true;
            $changes[] = 'Payment Terms';
        }

        // If no changes detected, return success without updating
        if (!$hasChanges) {
            return ['success' => true, 'message' => 'No changes detected. Billing is already up to date.', 'no_changes' => true];
        }

        $allocationQuery = "SELECT allocation_id FROM allocations WHERE student_id = ? AND status = 'Active' LIMIT 1";
        $stmt = $this->conn->prepare($allocationQuery);
        $stmt->bind_param('i', $student_id);
        $stmt->execute();
        $allocationResult = $stmt->get_result();
        $allocation_id = null;

        if ($allocationResult && $allocationResult->num_rows > 0) {
            $allocation = $allocationResult->fetch_assoc();
            $allocation_id = $allocation['allocation_id'];
        }
        $stmt->close();

        $query = "
        UPDATE billing 
        SET student_id = ?, allocation_id = ?, amount = ?, description = ?, 
            date_due = ?, billing_type = ?, academic_period = ?, payment_terms = ?
        WHERE billing_id = ?
    ";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Failed to prepare statement: ' . $this->conn->error];
        }

        $stmt->bind_param(
            'iidsssssi',
            $student_id,
            $allocation_id,
            $amount,
            $description,
            $due_date_formatted,
            $billing_type,
            $academic_period,
            $payment_terms,
            $billing_id
        );

        if ($stmt->execute()) {
            $stmt->close();
            return [
                'success' => true,
                'message' => 'Billing updated successfully',
                'changes' => $changes,
                'updated' => true
            ];
        }

        $stmt->close();
        return ['success' => false, 'error' => 'Failed to update billing: ' . $this->conn->error];
    }


    /**
     * Delete an invoice
     */

    public function deleteInvoice($billing_id)
    {
        $billing_id = (int)$billing_id;

        // Check if the billing record exists and get hostel info
        $query = "SELECT billing_id, status, hostel_id FROM billing WHERE billing_id = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return ['success' => false, 'error' => 'Failed to prepare statement: ' . $this->conn->error];
        }
        $stmt->bind_param('i', $billing_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result || $result->num_rows === 0) {
            $stmt->close();
            return ['success' => false, 'error' => 'Billing record not found'];
        }

        $billing = $result->fetch_assoc();
        $stmt->close();

        // Validate hostel access for non-super admins
        try {
            $this->validateHostelAccess($billing['hostel_id']);
        } catch (Exception $e) {
            error_log("Hostel access denied for delete billing ID $billing_id: " . $e->getMessage());
            return ['success' => false, 'error' => 'Access denied: Invoice not found'];
        }

        // Prevent deletion of paid or partially paid invoices
        if ($billing['status'] === 'Fully Paid' || $billing['status'] === 'Partially Paid') {
            return ['success' => false, 'error' => 'Cannot delete a paid or partially paid invoice'];
        }

        $this->conn->begin_transaction();

        try {
            // Delete related payments (if any, though should be none due to status check)
            $paymentQuery = "DELETE FROM payments WHERE billing_id = ?";
            $stmt = $this->conn->prepare($paymentQuery);
            $stmt->bind_param('i', $billing_id);
            $stmt->execute();
            $stmt->close();

            // Delete the billing record
            $deleteQuery = "DELETE FROM billing WHERE billing_id = ?";
            $stmt = $this->conn->prepare($deleteQuery);
            if (!$stmt) {
                $this->conn->rollback();
                return ['success' => false, 'error' => 'Failed to prepare delete statement: ' . $this->conn->error];
            }
            $stmt->bind_param('i', $billing_id);
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();
            return ['success' => true, 'message' => 'Invoice deleted successfully'];
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'error' => 'Failed to delete invoice: ' . $e->getMessage()];
        }
    }


    /**
     * Record a payment (with hostel access validation)
     */
    public function recordPayment($data)
    {
        $billing_id = (int)$data['billing_id'];
        $amount = floatval($data['amount']);
        $payment_date = $this->conn->real_escape_string($data['payment_date']);
        $payment_method = $this->conn->real_escape_string($data['payment_method']);
        $transaction_reference = $this->conn->real_escape_string($data['transaction_reference']);
        $status = 'Completed';

        // Validate required fields
        if (!$billing_id || !$amount || !$payment_date || !$payment_method || !$transaction_reference) {
            return ['success' => false, 'error' => 'All fields are required'];
        }

        if ($amount <= 0) {
            return ['success' => false, 'error' => 'Payment amount must be greater than zero'];
        }

        // Get billing details and validate hostel access
        $billingQuery = "SELECT amount, paid_amount, student_id, billing_type, hostel_id FROM billing WHERE billing_id = ?";

        $stmt = $this->conn->prepare($billingQuery);
        $stmt->bind_param('i', $billing_id);
        $stmt->execute();
        $billingResult = $stmt->get_result();
        if (!$billingResult || $billingResult->num_rows == 0) {
            $stmt->close();
            return ['success' => false, 'error' => 'Invoice not found'];
        }
        $billing = $billingResult->fetch_assoc();
        $stmt->close();

        // Validate hostel access for non-super admins
        try {
            $this->validateHostelAccess($billing['hostel_id']);
        } catch (Exception $e) {
            error_log("Hostel access denied for record payment billing ID $billing_id: " . $e->getMessage());
            return ['success' => false, 'error' => 'Access denied: Invoice not found'];
        }

        $student_id = $billing['student_id'];
        $current_paid_amount = floatval($billing['paid_amount']);
        $total_amount = floatval($billing['amount']);
        $new_paid_amount = $current_paid_amount + $amount;

        // Map billing_type to payment purpose
        $billing_type_to_purpose = [
            'Hostel Fee' => 'Hostel Fee',
            'Security Deposit' => 'Security Deposit',
            'Utility Fee' => 'Other',
            'Maintenance Fee' => 'Maintenance Charge',
            'Late Payment Penalty' => 'Penalty',
            'Other' => 'Other'
        ];

        $billing_type = $billing['billing_type'];
        $mapped_purpose = $billing_type_to_purpose[$billing_type] ?? 'Other';

        // Check if payment exceeds outstanding balance
        $outstanding_balance = $total_amount - $current_paid_amount;
        if ($amount > $outstanding_balance) {
            return ['success' => false, 'error' => 'Payment amount exceeds outstanding balance'];
        }

        // Update billing status
        $new_status = $new_paid_amount >= $total_amount ? 'Fully Paid' : 'Partially Paid';

        $this->conn->begin_transaction();
        try {
            // Insert payment using mapped purpose from billing type
            $paymentQuery = "
                INSERT INTO payments (student_id, billing_id, amount, payment_date, transaction_reference, payment_method, purpose, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ";
            $stmt = $this->conn->prepare($paymentQuery);
            $stmt->bind_param(
                'iidsssss',
                $student_id,
                $billing_id,
                $amount,
                $payment_date,
                $transaction_reference,
                $payment_method,
                $mapped_purpose,
                $status
            );
            $stmt->execute();
            $stmt->close();

            // Update billing
            $updateBillingQuery = "
                UPDATE billing 
                SET paid_amount = ?, status = ?
                WHERE billing_id = ?
            ";
            $stmt = $this->conn->prepare($updateBillingQuery);
            $stmt->bind_param('dsi', $new_paid_amount, $new_status, $billing_id);
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();
            return ['success' => true, 'message' => 'Payment recorded successfully', 'billing_id' => $billing_id];
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'error' => 'Failed to record payment: ' . $e->getMessage()];
        }
    }

    /**
     * Send payment reminder
     */
    public function sendReminder($data)
    {
        $billing_id = $this->conn->real_escape_string($data['billing_id']);
        $subject = $this->conn->real_escape_string($data['subject']);
        $message = $data['message'];
        $attach_invoice = isset($data['attach_invoice']) ? 1 : 0;

        // Get billing and student details
        $query = "
        SELECT b.amount, b.paid_amount, b.date_due, b.status, u.email, s.first_name, s.last_name
        FROM billing b
        JOIN students s ON b.student_id = s.student_id
        JOIN users u ON s.user_id = u.user_id
        WHERE b.billing_id = ?
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $billing_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Check if bill is fully paid
            if ($row['status'] === 'Fully Paid') {
                $stmt->close();
                return [
                    'success' => false,
                    'error' => 'Cannot send reminder for fully paid invoice'
                ];
            }

            // Generate PDF if attachment is requested
            $attachment_path = null;
            if ($attach_invoice) {
                try {
                    require_once __DIR__ . "/../models/PDFGenerator.php";
                    $pdfGenerator = new PDFGenerator();
                    $attachment_path = $pdfGenerator->generateInvoicePDFFile($billing_id);
                } catch (Exception $e) {
                    error_log("Failed to generate PDF for reminder: " . $e->getMessage());
                    // Continue without attachment
                }
            }

            // ASSIGN the result to $email_result variable
            $email_result = $this->emailService->sendEmail(
                $row['email'],
                $subject,
                $message,
                $billing_id,
                $attach_invoice,
                $attachment_path
            );

            // Clean up temporary PDF file
            if ($attachment_path && file_exists($attachment_path)) {
                unlink($attachment_path);
            }

            $stmt->close();
            return $email_result;
        }

        $stmt->close();
        return ['success' => false, 'error' => 'Failed to send reminder - billing record not found'];
    }

    /**
     * Bulk send reminders
     */
    public function bulkSendReminders($billing_ids)
    {
        $success_count = 0;
        foreach ($billing_ids as $billing_id) {
            $billing_id = $this->conn->real_escape_string($billing_id);
            $query = "
                SELECT b.amount, b.paid_amount, b.date_due, s.email, s.first_name, s.last_name
                FROM billing b
                JOIN students s ON b.student_id = s.student_id
                WHERE b.billing_id = ? AND b.status IN ('Unpaid', 'Partially Paid', 'Overdue')
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $billing_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $subject = "Payment Reminder: Invoice #INV-" . str_pad($billing_id, 6, '0', STR_PAD_LEFT);
                $message = "Dear {$row['first_name']} {$row['last_name']},\n\nThis is a reminder that your payment of $" .
                    ($row['amount'] - $row['paid_amount']) . " for invoice #INV-" .
                    str_pad($billing_id, 6, '0', STR_PAD_LEFT) . " is due on " .
                    date('M d, Y', strtotime($row['date_due'])) . ".\n\nThank you,\nKings Hostel Management";
                $this->emailService->sendEmail($row['email'], $subject, $message, $billing_id, true);
                $success_count++;
            }
            $stmt->close();
        }

        return [
            'success' => true,
            'sent' => $success_count,
            'total' => count($billing_ids),
            'failed' => count($billing_ids) - $success_count,
            'message' => "Successfully sent $success_count out of " . count($billing_ids) . " reminders"
        ];
    }

    /**
     * Get all buildings (filtered by hostel for non-super admins)
     */
    public function getBuildings()
    {
        $query = "SELECT DISTINCT r.building FROM rooms r WHERE r.building IS NOT NULL";

        // Apply hostel filtering for non-super admins
        $query = $this->addHostelFilter($query, 'r');
        $query .= " ORDER BY r.building";

        $result = $this->conn->query($query);
        if (!$result) {
            error_log("Error in getBuildings query: " . $this->conn->error);
            return [];
        }

        $buildings = [];
        while ($row = $result->fetch_assoc()) {
            $buildings[] = $row['building'];
        }
        return $buildings;
    }

    /**
     * Get all students (filtered by hostel for non-super admins)
     */
    public function getStudents()
    {
        $query = "
            SELECT DISTINCT s.student_id, s.first_name, s.last_name 
            FROM students s
            LEFT JOIN allocations a ON s.student_id = a.student_id 
            LEFT JOIN rooms r ON a.room_id = r.room_id
            WHERE s.resident_status = 'Active'
        ";

        // Apply hostel filtering for non-super admins
        $query = $this->addHostelFilter($query, 'r');
        $query .= " ORDER BY s.first_name, s.last_name";

        $result = $this->conn->query($query);
        if (!$result) {
            error_log("Error in getStudents query: " . $this->conn->error);
            return [];
        }

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
    private function sendNotification($billing_id, $student_id, $amount, $date_due, $description, $billing_type)
    {
        $query = "SELECT email, first_name, last_name FROM students s JOIN users u ON s.user_id = u.user_id WHERE student_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $student = $result->fetch_assoc();

            $email_result = $this->emailService->sendInvoiceNotification(
                $student['email'],
                $billing_id,
                $student['first_name'] . ' ' . $student['last_name'],
                $amount,
                $date_due,
                $description,
                $billing_type
            );
            $stmt->close();
            return $email_result;
        }
        $stmt->close();
        return ['success' => false, 'error' => 'Student email not found'];
    }

    /**
     * Get billing details for payment modal (with hostel access validation)
     */
    public function getBillingDetails($billing_id)
    {
        $billing_id = (int)$billing_id;

        // First check if billing exists and validate hostel access
        $query = "SELECT billing_id, amount, paid_amount, billing_type, hostel_id FROM billing WHERE billing_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $billing_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result || $result->num_rows == 0) {
            $stmt->close();
            return ['success' => false, 'error' => 'Invoice not found'];
        }

        $billing = $result->fetch_assoc();
        $stmt->close();

        // Validate hostel access for non-super admins
        try {
            $this->validateHostelAccess($billing['hostel_id']);
        } catch (Exception $e) {
            error_log("Hostel access denied for billing details ID $billing_id: " . $e->getMessage());
            return ['success' => false, 'error' => 'Access denied: Invoice not found'];
        }

        // Map billing_type to payment purpose
        $billing_type_to_purpose = [
            'Hostel Fee' => 'Hostel Fee',
            'Security Deposit' => 'Security Deposit',
            'Utility Fee' => 'Other',
            'Maintenance Fee' => 'Maintenance Charge',
            'Late Payment Penalty' => 'Penalty',
            'Other' => 'Other'
        ];

        $billing_type = $billing['billing_type'];
        $mapped_purpose = $billing_type_to_purpose[$billing_type] ?? 'Other';

        $total_amount = floatval($billing['amount']);
        $paid_amount = floatval($billing['paid_amount']);
        $outstanding_balance = $total_amount - $paid_amount;

        return [
            'success' => true,
            'data' => [
                'billing_id' => $billing['billing_id'],
                'total_amount' => $total_amount,
                'paid_amount' => $paid_amount,
                'outstanding_balance' => $outstanding_balance,
                'purpose' => $mapped_purpose
            ]
        ];
    }
}
