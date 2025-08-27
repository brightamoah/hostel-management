<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../../app/models/Student.php";

header('Content-Type: application/json');


// Check if user is authenticated and has Admin role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: You must be logged in as an Admin']);
    exit();
}

// Get payment ID from URL parameter
$payment_id = $id;
if ($payment_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment ID']);
    exit();
}

try {
    $conn = getDb();

    // Fetch payment details with student name
    $query = "
        SELECT p.*, CONCAT(s.first_name, ' ', s.last_name) as student_name
        FROM payments p
        JOIN students s ON p.student_id = s.student_id
        WHERE p.payment_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $payment_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $payment = $result->num_rows > 0 ? $result->fetch_assoc() : null;
    $stmt->close();

    if ($payment) {
        // Normalize status to title case
        $payment['status'] = ucfirst(strtolower(trim($payment['status'])));
        // Ensure amount is a float
        $payment['amount'] = (float)$payment['amount'];
        echo json_encode(['success' => true, 'data' => $payment]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Payment not found']);
    }
} catch (Exception $e) {
    error_log("Error in GetPaymentById: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while fetching payment details']);
}

$conn->close();
exit();
