<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../../utils/hostel_helpers.php";

header('Content-Type: application/json');

// Check if user can assign hostels (only Super Admin)
if (!canAssignHostels()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Only Super Admins can assign hostels']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $admin_id = (int)($input['admin_id'] ?? 0);
    $hostel_id = $input['hostel_id'] ?? null;

    if (!$admin_id) {
        throw new Exception('Admin ID is required');
    }

    // Convert empty string to null for no hostel assignment
    $hostel_id = ($hostel_id === '' || $hostel_id === 'null') ? null : (int) $hostel_id;

    $conn = getDb();

    // Verify admin exists
    $check_stmt = $conn->prepare("SELECT admin_id, user_id FROM admins WHERE admin_id = ?");
    $check_stmt->bind_param("i", $admin_id);
    $check_stmt->execute();
    $admin = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();

    if (!$admin) {
        throw new Exception('Admin not found');
    }

    // If assigning to a hostel, verify hostel exists
    if ($hostel_id) {
        $hostel_stmt = $conn->prepare("SELECT hostel_id FROM hostels WHERE hostel_id = ?");
        $hostel_stmt->bind_param("i", $hostel_id);
        $hostel_stmt->execute();
        $hostel = $hostel_stmt->get_result()->fetch_assoc();
        $hostel_stmt->close();

        if (!$hostel) {
            throw new Exception('Hostel not found');
        }
    }

    // Update admin hostel assignment
    if ($hostel_id) {
        $update_stmt = $conn->prepare("UPDATE admins SET hostel_id = ? WHERE admin_id = ?");
        $update_stmt->bind_param("ii", $hostel_id, $admin_id);
    } else {
        $update_stmt = $conn->prepare("UPDATE admins SET hostel_id = NULL WHERE admin_id = ?");
        $update_stmt->bind_param("i", $admin_id);
    }

    $result = $update_stmt->execute();
    $update_stmt->close();

    if ($result) {
        $message = $hostel_id ?
            "Admin assigned to hostel successfully" :
            "Admin hostel assignment removed successfully";
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        throw new Exception('Failed to update admin hostel assignment');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
