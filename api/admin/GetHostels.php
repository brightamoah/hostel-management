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
            h.hostel_id,
            h.hostel_name,
            h.hostel_code,
            h.address,
            h.contact_phone,
            h.contact_email,
            h.status,
            COUNT(r.room_id) as total_rooms,
            COUNT(CASE WHEN r.status = 'Fully Occupied' THEN 1 END) as occupied_rooms,
            COUNT(a.admin_id) as assigned_admins
        FROM hostels h
        LEFT JOIN rooms r ON h.hostel_id = r.hostel_id
        LEFT JOIN admins a ON h.hostel_id = a.hostel_id
        WHERE h.status = 'Active'
        GROUP BY h.hostel_id
        ORDER BY h.hostel_name
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();

    $hostels = [];
    while ($row = $result->fetch_assoc()) {
        $hostels[] = [
            'hostel_id' => $row['hostel_id'],
            'hostel_name' => $row['hostel_name'],
            'hostel_code' => $row['hostel_code'],
            'address' => $row['address'],
            'contact_phone' => $row['contact_phone'],
            'contact_email' => $row['contact_email'],
            'status' => $row['status'],
            'total_rooms' => (int)$row['total_rooms'],
            'occupied_rooms' => (int)$row['occupied_rooms'],
            'assigned_admins' => (int)$row['assigned_admins']
        ];
    }

    $stmt->close();

    echo json_encode(['success' => true, 'data' => $hostels]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
