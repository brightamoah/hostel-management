<?php

require_once __DIR__ . "/../../database/db.php";


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
        $studentId = $_SESSION['user']['student_id'] ?? 0;

        $query = "
        SELECT a.announcement_id, a.title, a.content, a.priority, a.date_posted, 
               a.target_audience,
               CASE WHEN ar.read_id IS NOT NULL THEN 1 ELSE 0 END as is_read
        FROM announcements a
        LEFT JOIN announcement_reads ar ON a.announcement_id = ar.announcement_id 
                                      AND ar.student_id = ?
        WHERE a.target_audience IN ('Students', 'All')
        ORDER BY a.date_posted DESC
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $announcements = [];
        while ($row = $result->fetch_assoc()) {
            $announcements[] = $row;
        }

        return $announcements;
    }

    public function markAnnouncementAsRead($announcementId, $studentId)
    {
        // First check if already marked as read
        $checkQuery = "
            SELECT * FROM announcement_reads 
            WHERE announcement_id = ? AND student_id = ?
        ";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bind_param("ii", $announcementId, $studentId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            return true; // Already marked as read
        }

        // Mark as read
        $query = "
            INSERT INTO announcement_reads (announcement_id, student_id, read_date)
            VALUES (?, ?, NOW())
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $announcementId, $studentId);

        return $stmt->execute();
    }

    public function getUnreadAnnouncementCount($studentId)
    {
        $query = "
            SELECT COUNT(*) as count
            FROM announcements a
            WHERE a.target_audience IN ('Students', 'All')
            AND NOT EXISTS (
                SELECT 1 FROM announcement_reads ar
                WHERE ar.announcement_id = a.announcement_id
                AND ar.student_id = ?
            )
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['count'];
    }


    public function getAnnouncementByTitleAndDate($title, $date)
    {
        $query = "
        SELECT announcement_id, title, date_posted
        FROM announcements
        WHERE title = ? AND date_posted = ?
        LIMIT 1
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $title, $date);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }
}
