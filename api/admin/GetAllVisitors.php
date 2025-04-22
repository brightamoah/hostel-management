<?php
// require_once __DIR__ . "/../../database/db.php";
// require_once __DIR__ . "/../../app/models/Visitor.php";
require_once __DIR__ . "/../../app/controllers/VisitorController.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}



try {
    $controller = new VisitorController();
    $controller->getAllVisitors();
} catch (Exception $e) {
    error_log("Error in GetAllVisitors: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while fetching visitor data.']);
    exit;
}


// try {
//     $model = new Visitor();
//     $visitors = $model->getAllVisitors();
//     echo $visitors;
// } catch (Exception $e) {
//     error_log("Error in visitors-data: " . $e->getMessage());
//     echo json_encode(['data' => []]);
// }

// try {
//     $db = new Database();
//     $conn = $db->connect();
//     $query = "SELECT visitor_id, visitor_name, relation, phone_number, visit_date, status, check_in_time, check_out_time FROM visitors ORDER BY visit_date DESC";
//     $result = $conn->query($query);
//     $visitors = [];
//     while ($row = $result->fetch_assoc()) {
//         $visitors[] = $row;
//     }
//     echo json_encode(['data' => $visitors]);
//     $db->close();
// } catch (Exception $e) {
//     error_log("Error in visitors-data: " . $e->getMessage());
//     echo json_encode(['data' => []]);
// }
