<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../../utils/hostel_helpers.php";

header('Content-Type: application/json');

// Check if user is Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin access required']);
    exit;
}

$user_id = (int)($id ?? 0);
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

try {
    $conn = getDb();

    // Get admin details with hostel information
    $query = "
        SELECT 
            a.admin_id, a.user_id, a.first_name, a.last_name, a.department, 
            a.access_level, a.hostel_id,
            u.name, u.email, u.last_login, u.is_email_verified,
            h.hostel_name, h.hostel_code, h.status as hostel_status
        FROM admins a
        JOIN users u ON a.user_id = u.user_id
        LEFT JOIN hostels h ON a.hostel_id = h.hostel_id
        WHERE a.user_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();

    if (!$admin) {
        echo json_encode(['success' => false, 'message' => 'Admin not found']);
        exit;
    }

    echo json_encode(['success' => true, 'data' => $admin]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
