<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../../utils/hostel_helpers.php";

header('Content-Type: application/json');

// Check if user is Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin access required']);
    exit;
}

try {
    $conn = getDb();

    // Get all active hostels
    $query = "
        SELECT hostel_id, hostel_name, hostel_code, address, contact_phone, contact_email, status
        FROM hostels 
        WHERE status = 'Active'
        ORDER BY hostel_name ASC
    ";

    $result = $conn->query($query);
    $hostels = [];

    while ($row = $result->fetch_assoc()) {
        $hostels[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $hostels]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
