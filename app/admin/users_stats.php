<?php
require_once __DIR__ . "/../../app/models/User.php";
require_once __DIR__ . "/../../database/db.php"; // Database connection

$db = null;
$conn = null;
$userController = null;
$totalUsers = 0;
$totalStudents = 0;
$totalAdmins = 0;
$activeStudents = 0;

try {
    $db = new Database();
    $conn = $db->connect();
    $userController = new User($conn);


    function fetchCount($connection, $sql, $params = [], $types = "")
    {
        $stmt = $connection->prepare($sql);

        if (!$stmt) {
            error_log("Prepare failed for count query ($sql): {$connection->error}");
            return 0;
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            error_log("Execute failed for count query ($sql): {$stmt->error}");
            $stmt->close();
            return 0;
        }

        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['count'] ?? 0;
    }

    // Fetch statistics using the helper and single connection
    $totalUsers = fetchCount($conn, "SELECT COUNT(*) as count FROM users");
    $totalStudents = fetchCount($conn, "SELECT COUNT(*) as count FROM students");
    $totalAdmins = fetchCount($conn, "SELECT COUNT(*) as count FROM admins");
    $activeStudents = fetchCount($conn, "SELECT COUNT(*) as count FROM students WHERE resident_status = ?", ['Active'], 's');
} catch (Exception $e) {
    error_log("Error initializing user page data: " . $e->getMessage());
    // Set defaults or display an error message
    $totalUsers = $totalStudents = $totalAdmins = $activeStudents = 'Error';
}