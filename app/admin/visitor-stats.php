<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../../app/models/Visitor.php";
require_once __DIR__ . "/../../utils/hostel_helpers.php";

$visitorModel = new Visitor();
$totalVisitors = 0;
$approvedVisitors = 0;
$checkedInVisitors = 0;
$pendingVisitors = 0;

try {
    $conn = getDb();

    // Base query with hostel filtering for regular admins
    $baseWhere = "1=1";
    $hostelJoin = "";

    if (!isSuperAdmin()) {
        $admin_hostel_id = getCurrentAdminHostelId();
        if ($admin_hostel_id) {
            $hostelJoin = "
                JOIN students s ON v.student_id = s.student_id
                LEFT JOIN allocations a ON s.student_id = a.student_id AND a.status = 'Active'
                LEFT JOIN rooms r ON a.room_id = r.room_id
            ";
            $baseWhere = "r.hostel_id = " . intval($admin_hostel_id);
        }
    }

    // Total visitors
    $query = "SELECT COUNT(*) as count FROM visitors v $hostelJoin WHERE $baseWhere";
    $result = $conn->query($query);
    $totalVisitors = $result->fetch_assoc()['count'] ?? 0;

    // Approved visitors
    $query = "SELECT COUNT(*) as count FROM visitors v $hostelJoin WHERE $baseWhere AND v.status = 'Approved'";
    $result = $conn->query($query);
    $approvedVisitors = $result->fetch_assoc()['count'] ?? 0;

    // Checked-In visitors
    $query = "SELECT COUNT(*) as count FROM visitors v $hostelJoin WHERE $baseWhere AND v.status = 'Checked-In'";
    $result = $conn->query($query);
    $checkedInVisitors = $result->fetch_assoc()['count'] ?? 0;

    // Pending visitors
    $query = "SELECT COUNT(*) as count FROM visitors v $hostelJoin WHERE $baseWhere AND v.status = 'Pending'";
    $result = $conn->query($query);
    $pendingVisitors = $result->fetch_assoc()['count'] ?? 0;
} catch (Exception $e) {
    error_log("Error in visitor_stats: " . $e->getMessage());
}
