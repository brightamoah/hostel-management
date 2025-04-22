<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../../app/models/Visitor.php";

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['visitor_id']) || !isset($input['action']) || !isset($input['csrf'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Manual CSRF validation for JSON requests
if (!isset($_SESSION['csrf']) || !isset($input['csrf']) || $_SESSION['csrf'] !== $input['csrf']) {
    error_log("CSRF validation failed - Session: " . ($_SESSION['csrf'] ?? 'NULL') . ", Posted: " . ($input['csrf'] ?? 'NULL'));
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin access required']);
    exit;
}

$visitor_id = (int)$input['visitor_id'];
$action = $input['action'];
$visitorModel = new Visitor();

try {
    $db = new Database();
    $conn = $db->connect();
    $conn->begin_transaction();

    switch ($action) {
        case 'approve':
            $result = $visitorModel->approve($visitor_id);
            $message = $result ? 'Visitor request approved' : 'Failed to approve visitor request';
            break;
        case 'deny':
            $result = $visitorModel->deny($visitor_id);
            $message = $result ? 'Visitor request denied' : 'Failed to deny visitor request';
            break;
        case 'check_in':
            $result = $visitorModel->checkIn($visitor_id);
            $message = $result ? 'Visitor checked in' : 'Failed to check in visitor';
            break;
        case 'check_out':
            $result = $visitorModel->checkOut($visitor_id);
            $message = $result ? 'Visitor checked out' : 'Failed to check out visitor or no open check-in found';
            break;
        default:
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            exit;
    }

    if ($result) {
        $conn->commit();
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $message]);
    }
} catch (Exception $e) {
    $conn->rollback();
    error_log("Visitor action error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
