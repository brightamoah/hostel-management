<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../../utils/hostel_helpers.php";

header('Content-Type: application/json');

// Check if user is authenticated and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin access required']);
    exit;
}

try {
    $conn = getDb();

    // Base query - include hostel information for filtering
    $query = "
        SELECT 
            u.user_id, 
            u.name, 
            u.email, 
            u.role, 
            u.is_email_verified, 
            s.resident_status,
            r.hostel_id as student_hostel_id,
            a.hostel_id as admin_hostel_id,
            a.access_level
        FROM users u
        LEFT JOIN students s ON u.user_id = s.user_id
        LEFT JOIN allocations al ON s.student_id = al.student_id AND al.status = 'Active'
        LEFT JOIN rooms r ON al.room_id = r.room_id
        LEFT JOIN admins a ON u.user_id = a.user_id
        WHERE 1=1
    ";

    // Apply hostel filtering for regular admins
    if (!isSuperAdmin()) {
        $admin_hostel_id = getCurrentAdminHostelId();
        if ($admin_hostel_id) {
            $query .= " AND (
                (u.role = 'Student' AND r.hostel_id = $admin_hostel_id) OR
                (u.role = 'Admin' AND a.hostel_id = $admin_hostel_id)
            )";
        }
    }

    $query .= " ORDER BY u.role DESC, u.name ASC";

    $result = $conn->query($query);

    if ($result === false) {
        echo json_encode([
            'success' => false,
            'message' => "Database query failed: {$conn->error}",
            'data' => []
        ]);
        exit;
    }

    $users = [];
    while ($row = $result->fetch_assoc()) {
        // Clean up the response - remove internal hostel IDs
        unset($row['student_hostel_id']);
        unset($row['admin_hostel_id']);
        $users[] = $row;
    }

    // Include access level information for frontend
    $response = [
        'success' => true,
        'data' => $users,
        'admin_access' => getAdminAccessLevel()
    ];

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => []
    ]);
}
