<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../../app/models/Student.php";

header('Content-Type: application/json');



// Check if user is authenticated and has Admin role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: You must be logged in as an Admin']);
    exit();
}

try {
    
    $conn = getDb();
    $student = new Student($conn);

    // Fetch recent payments (last 30 days, limited to 5)
    $query = "
        SELECT p.*, CONCAT(s.first_name, ' ', s.last_name) as student_name
        FROM payments p
        JOIN students s ON p.student_id = s.student_id
        WHERE p.payment_date >= DATE_SUB(CURDATE(), INTERVAL 40 DAY)
        ORDER BY p.payment_date DESC
        LIMIT 5";
    $result = $conn->query($query);

    if (!$result) {
        throw new Exception("Query failed: {$conn->error}");
    }

    $payments = [];
    while ($row = $result->fetch_assoc()) {
        // Normalize status to title case
        $row['status'] = ucfirst(strtolower(trim($row['status'])));
        // Ensure amount is a float
        $row['amount'] = (float) $row['amount'];
        $payments[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $payments]);
} catch (Exception $e) {
    error_log("Error in GetRecentPayments: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while fetching payments']);
}

$conn->close();
exit();
