<?php
require_once "./database/db.php";
require_once "./app/models/Room.php";
require_once "./app/models/Student.php";
require_once "./app/models/MaintenanceRequest.php";

// Initialize classes
$db = new Database();
$conn = $db->connect();
if (!$conn) {
    die("Database connection failed: {$db->connect()->connect_error}");
}

$rooms = new Rooms();
$student = new Student($conn);
$maintenance = new MaintenanceRequest();

// Fetch statistics
$total_rooms = count($rooms->getAllRooms());
$available_rooms = count($rooms->getAvailableRooms());
$occupied_rooms = $total_rooms - $available_rooms;
$occupancy_rate = $total_rooms > 0 ? round(($occupied_rooms / $total_rooms) * 100) : 0;

// Fetch historical occupancy data (past 8 months)
$historical_occupancy = [];
$current_date = new DateTime();
for ($i = 7; $i >= 0; $i--) {
    $date = clone $current_date;
    $date->modify("-$i months");
    $month_start = $date->format('Y-m-01');
    $month_end = $date->format('Y-m-t');

    $occupied_query = "
        SELECT COUNT(DISTINCT a.room_id) as occupied
        FROM allocations a
        WHERE a.status = 'Active'
        AND a.start_date <= ?
        AND (a.end_date IS NULL OR a.end_date >= ?)";
    $stmt = $conn->prepare($occupied_query);
    $stmt->bind_param("ss", $month_end, $month_start);
    $stmt->execute();
    $occupied_result = $stmt->get_result()->fetch_assoc();
    $occupied = $occupied_result['occupied'] ?? 0;

    $rate = $total_rooms > 0 ? round(($occupied / $total_rooms) * 100) : 0;
    $historical_occupancy[] = [
        'month' => $date->format('M Y'),
        'rate' => $rate
    ];
    $stmt->close();
}

// Fetch pending maintenance requests
$pending_maintenance_query = "SELECT COUNT(*) as count FROM maintenance_requests WHERE status = 'Pending'";
$stmt = $conn->prepare($pending_maintenance_query);
$stmt->execute();
$pending_maintenance_result = $stmt->get_result()->fetch_assoc();
$pending_maintenance = $pending_maintenance_result['count'];
$stmt->close();

// Fetch in-progress maintenance requests
$in_progress_maintenance_query = "SELECT COUNT(*) as count FROM maintenance_requests WHERE status = 'In-Progress'";
$stmt = $conn->prepare($in_progress_maintenance_query);
$stmt->execute();
$in_progress_maintenance_result = $stmt->get_result()->fetch_assoc();
$in_progress_maintenance = $in_progress_maintenance_result['count'];
$stmt->close();

// Fetch completed maintenance requests
$completed_maintenance_query = "SELECT COUNT(*) as count FROM maintenance_requests WHERE status = 'Completed'";
$stmt = $conn->prepare($completed_maintenance_query);
$stmt->execute();
$completed_maintenance_result = $stmt->get_result()->fetch_assoc();
$completed_maintenance = $completed_maintenance_result['count'];
$stmt->close();

// Fetch pending visitor requests
$pending_visitors_query = "SELECT COUNT(*) as count FROM visitors WHERE status = 'Pending' AND visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
$stmt = $conn->prepare($pending_visitors_query);
$stmt->execute();
$pending_visitors_result = $stmt->get_result()->fetch_assoc();
$pending_visitors = $pending_visitors_result['count'];
$stmt->close();

// Count total students
$total_students_query = "SELECT COUNT(*) as count FROM students";
$stmt = $conn->prepare($total_students_query);
$stmt->execute();
$total_students_result = $stmt->get_result()->fetch_assoc();
$total_students = $total_students_result['count'];
$stmt->close();

// Count recent payments
$recent_payments_count_query = "SELECT COUNT(*) as count FROM payments WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
$stmt = $conn->prepare($recent_payments_count_query);
$stmt->execute();
$recent_payments_count_result = $stmt->get_result()->fetch_assoc();
$recent_payments_count = $recent_payments_count_result['count'];
$stmt->close();

// Sum of recent payments
$recent_payments_sum_query = "SELECT SUM(amount) as total FROM payments WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND status = 'Completed'";
$stmt = $conn->prepare($recent_payments_sum_query);
$stmt->execute();
$recent_payments_sum_result = $stmt->get_result()->fetch_assoc();
$recent_payments_sum = $recent_payments_sum_result['total'] ?? 0;
$stmt->close();

// Fetch recent bookings
$recent_bookings_query = "
    SELECT a.*, r.room_number, r.building, CONCAT(s.first_name, ' ', s.last_name) as student_name
    FROM allocations a
    JOIN rooms r ON a.room_id = r.room_id
    JOIN students s ON a.student_id = s.student_id
    WHERE a.status = 'Active'
    ORDER BY a.start_date DESC
    LIMIT 5";
$stmt = $conn->prepare($recent_bookings_query);
$stmt->execute();
$recent_bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch recent maintenance requests
$recent_maintenance_query = "
    SELECT mr.*, r.room_number, r.building, CONCAT(s.first_name, ' ', s.last_name) as student_name
    FROM maintenance_requests mr
    LEFT JOIN rooms r ON mr.room_id = r.room_id
    JOIN students s ON mr.student_id = s.student_id
    ORDER BY mr.request_date DESC
    LIMIT 5";
$stmt = $conn->prepare($recent_maintenance_query);
$stmt->execute();
$recent_maintenance = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch recent payments
$recent_payments_query = "
    SELECT p.*, CONCAT(s.first_name, ' ', s.last_name) as student_name
    FROM payments p
    JOIN students s ON p.student_id = s.student_id
    ORDER BY p.payment_date DESC
    LIMIT 5";
$stmt = $conn->prepare($recent_payments_query);
$stmt->execute();
$recent_payments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get admin's information
$user = $_SESSION['user'] ?? null;
$user_id = $user['user_id'] ;
$admin_query = "SELECT name, role, last_login FROM users WHERE user_id = ?";
$stmt = $conn->prepare($admin_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$admin_result = $stmt->get_result()->fetch_assoc();
$admin_name = $admin_result['name'] ?? 'Admin';
$admin_role = $user['role'] ?? 'Administrator';
$last_login = $admin_result['last_login'] ?? 'N/A';
$first_name = explode(' ', $admin_name)[0];
$stmt->close();


//query the admin table and get all the information
// $admin_query = "SELECT * FROM admins WHERE user_id = ?";
// $stmt = $conn->prepare($admin_query);
// $user_id = $_SESSION['user_id'];
// $stmt->bind_param("i", $user_id);
// $stmt->execute();
// $admin_result = $stmt->get_result()->fetch_assoc();
// $stmt->close();


// echo "<pre>";
// print_r($admin_result);
// print_r($_SESSION['user']);
// echo "</pre>";

