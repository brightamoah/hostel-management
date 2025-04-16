<?php
require_once "./database/db.php";
header('Content-Type: application/json');

function getVisitorByID($id) {
{
    $visitorId = $id;

    $db = new Database();
    $conn = $db->connect();

    if ($visitorId) {
        $query = "SELECT * FROM visitors WHERE visitor_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $visitorId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $visitor = $result->fetch_assoc();
            echo json_encode($visitor);
            return;
        }
    }

    // Return an error message if no visitor is found
    echo json_encode(["error" => "Visitor not found"]);
    }

    $conn->close();
}


getVisitorByID($id);
