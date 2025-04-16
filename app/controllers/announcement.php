<?php

require_once "./database/db.php";

class AnnouncementController
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function getAnnouncements()
    {
        $query = "
            SELECT title, content, priority, date_posted
            FROM announcements
            WHERE target_audience IN ('Students', 'All')
            ORDER BY date_posted DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $announcements = [];
        while ($row = $result->fetch_assoc()) {
            $announcements[] = $row;
        }

        return $announcements;
        
    }
}

$controller = new AnnouncementController();
// $data = $controller->getAnnouncements();
header('Content-Type: application/json');
$data = $controller->getAnnouncements();
$_SESSION['totalAnnouncements'] = count($data);
echo json_encode($data);
