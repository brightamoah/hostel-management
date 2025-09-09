<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../../utils/hostel_helpers.php";

header('Content-Type: application/json');

// Check if user is Super Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin' || !isSuperAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Super Admin access required']);
    exit;
}

try {
    $conn = getDb();

    $query = "
        SELECT 
            a.admin_id,
            a.user_id,
            a.first_name,
            a.last_name,
            a.department,
            a.access_level,
            a.hostel_id,
            h.hostel_name,
            h.hostel_code,
            u.email,
            u.last_login
        FROM admins a
        JOIN users u ON a.user_id = u.user_id
        LEFT JOIN hostels h ON a.hostel_id = h.hostel_id
        ORDER BY a.access_level DESC, a.first_name, a.last_name
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();

    $admins = [];
    while ($row = $result->fetch_assoc()) {
        $admins[] = [
            'admin_id' => $row['admin_id'],
            'user_id' => $row['user_id'],
            'full_name' => trim($row['first_name'] . ' ' . $row['last_name']),
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
            'department' => $row['department'],
            'access_level' => $row['access_level'],
            'hostel_id' => $row['hostel_id'],
            'hostel_name' => $row['hostel_name'],
            'hostel_code' => $row['hostel_code'],
            'last_login' => $row['last_login'],
            'hostel_assignment' => $row['hostel_name'] ? $row['hostel_name'] : 'Not Assigned'
        ];
    }

    $stmt->close();

    echo json_encode(['success' => true, 'data' => $admins]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
