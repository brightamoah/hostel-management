<?php
require_once __DIR__ . "/../../app/models/User.php";
require_once __DIR__ . "/../../database/db.php"; // Database connection
require_once __DIR__ . "/../../utils/hostel_helpers.php";

$db = null;
$conn = null;
$userController = null;
$totalUsers = 0;
$totalStudents = 0;
$totalAdmins = 0;
$activeStudents = 0;

try {
    $db = new Database();
    $conn = $db->connect();
    $userController = new User($conn);


    function fetchCount($connection, $sql, $params = [], $types = "")
    {
        $stmt = $connection->prepare($sql);

        if (!$stmt) {
            error_log("Prepare failed for count query ($sql): {$connection->error}");
            return 0;
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            error_log("Execute failed for count query ($sql): {$stmt->error}");
            $stmt->close();
            return 0;
        }

        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['count'] ?? 0;
    }

    // Apply hostel filtering for regular admins
    $hostelFilter = "";
    $hostelJoinStudents = "";
    $hostelJoinAdmins = "";

    if (!isSuperAdmin()) {
        $admin_hostel_id = getCurrentAdminHostelId();
        if ($admin_hostel_id) {
            // For students: join through allocations -> rooms -> hostels
            $hostelJoinStudents = "
                LEFT JOIN allocations a ON s.student_id = a.student_id AND a.status = 'Active'
                LEFT JOIN rooms r ON a.room_id = r.room_id
            ";
            $hostelFilter = " AND r.hostel_id = " . intval($admin_hostel_id);

            // For admins: direct hostel_id filter
            $hostelJoinAdmins = " AND ad.hostel_id = " . intval($admin_hostel_id);
        }
    }

    // Fetch statistics using the helper and single connection with hostel filtering
    $totalUsers = fetchCount($conn, "
        SELECT COUNT(DISTINCT u.user_id) as count 
        FROM users u
        LEFT JOIN students s ON u.user_id = s.user_id
        LEFT JOIN admins ad ON u.user_id = ad.user_id
        $hostelJoinStudents
        WHERE 1=1 
        " . ($hostelFilter ? "AND ((u.role = 'Student'" . $hostelFilter . ") OR (u.role = 'Admin'" . $hostelJoinAdmins . "))" : ""));

    $totalStudents = fetchCount($conn, "
        SELECT COUNT(*) as count 
        FROM students s 
        $hostelJoinStudents
        WHERE 1=1" . $hostelFilter);

    $totalAdmins = fetchCount($conn, "
        SELECT COUNT(*) as count 
        FROM admins ad 
        WHERE 1=1" . $hostelJoinAdmins);

    $activeStudents = fetchCount($conn, "
        SELECT COUNT(*) as count 
        FROM students s 
        $hostelJoinStudents
        WHERE s.resident_status = ?" . $hostelFilter, ['Active'], 's');
} catch (Exception $e) {
    error_log("Error initializing user page data: " . $e->getMessage());
    // Set defaults or display an error message
    $totalUsers = $totalStudents = $totalAdmins = $activeStudents = 'Error';
}
