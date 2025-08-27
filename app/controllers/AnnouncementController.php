<?php

require_once __DIR__ . "/../../database/db.php";

class AnnouncementController
{

    private $conn;

    public function __construct()
    {
        $this->conn = getDb();
    }

    /**
     * Get announcements for students
     */
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
           OR (a.target_audience = 'Specific' 
               AND EXISTS (
                   SELECT 1 FROM announcement_specific_targets ast 
                   WHERE ast.announcement_id = a.announcement_id 
                   AND ast.target_type = 'student' 
                   AND ast.target_id = ?
               ))
        ORDER BY a.date_posted DESC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $studentId, $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $announcements = [];
        while ($row = $result->fetch_assoc()) {
            $announcements[] = $row;
        }

        return $announcements;
    }

    /**
     * Get all announcements for admin view
     */
    public function getAllAnnouncements()
    {
        $query = "
        SELECT a.announcement_id, a.title, a.content, a.priority, 
               a.target_audience, a.date_posted, 
               CONCAT(adm.first_name, ' ', adm.last_name) as posted_by_name,
               (SELECT GROUP_CONCAT(CONCAT(ast.target_type, ':', 
                   CASE 
                       WHEN ast.target_type = 'student' THEN (SELECT CONCAT(first_name, ' ', last_name) FROM students WHERE student_id = ast.target_id)
                       WHEN ast.target_type = 'admin' THEN (SELECT CONCAT(first_name, ' ', last_name) FROM admins WHERE admin_id = ast.target_id)
                       WHEN ast.target_type = 'building' THEN ast.target_id
                       WHEN ast.target_type = 'room' THEN (SELECT CONCAT(building, ' ', room_number) FROM rooms WHERE room_id = ast.target_id)
                   END) SEPARATOR ', ')
                FROM announcement_specific_targets ast 
                WHERE ast.announcement_id = a.announcement_id) as specific_targets
        FROM announcements a
        JOIN admins adm ON a.posted_by = adm.admin_id
        ORDER BY a.date_posted DESC
        ";

        $result = $this->conn->query($query);
        $announcements = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $announcements[] = $row;
            }
        }

        return $announcements;
    }

    /**
     * Create a new announcement
     */
    public function createAnnouncement($adminId, $title, $content, $priority, $targetAudience, $specificTargetType = null, $specificTargetId = null)
    {
        $this->conn->begin_transaction();

        try {
            $query = "
            INSERT INTO announcements 
            (posted_by, title, content, priority, target_audience) 
            VALUES (?, ?, ?, ?, ?)
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("issss", $adminId, $title, $content, $priority, $targetAudience);
            $stmt->execute();
            $announcementId = $this->conn->insert_id;

            if ($targetAudience === 'Specific' && $specificTargetType && $specificTargetId) {
                $query = "
                INSERT INTO announcement_specific_targets 
                (announcement_id, target_type, target_id) 
                VALUES (?, ?, ?)
                ";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("isi", $announcementId, $specificTargetType, $specificTargetId);
                $stmt->execute();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    /**
     * Update an existing announcement
     */
    public function updateAnnouncement($announcementId, $title, $content, $priority, $targetAudience)
    {
        $query = "
        UPDATE announcements 
        SET title = ?, content = ?, priority = ?, target_audience = ? 
        WHERE announcement_id = ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssi", $title, $content, $priority, $targetAudience, $announcementId);

        return $stmt->execute();
    }

    /**
     * Delete an announcement and its associated read records
     */
    public function deleteAnnouncement($announcementId)
    {
        $this->conn->begin_transaction();

        try {
            $deleteReadsQuery = "DELETE FROM announcement_reads WHERE announcement_id = ?";
            $stmt = $this->conn->prepare($deleteReadsQuery);
            $stmt->bind_param("i", $announcementId);
            $stmt->execute();

            $deleteTargetsQuery = "DELETE FROM announcement_specific_targets WHERE announcement_id = ?";
            $stmt = $this->conn->prepare($deleteTargetsQuery);
            $stmt->bind_param("i", $announcementId);
            $stmt->execute();

            $deleteQuery = "DELETE FROM announcements WHERE announcement_id = ?";
            $stmt = $this->conn->prepare($deleteQuery);
            $stmt->bind_param("i", $announcementId);
            $result = $stmt->execute();

            $this->conn->commit();
            return $result;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    /**
     * Mark an announcement as read by a student
     */
    public function markAnnouncementAsRead($announcementId, $studentId)
    {
        $checkQuery = "
            SELECT * FROM announcement_reads 
            WHERE announcement_id = ? AND student_id = ?
        ";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bind_param("ii", $announcementId, $studentId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            return true;
        }

        $query = "
            INSERT INTO announcement_reads (announcement_id, student_id, read_date)
            VALUES (?, ?, NOW())
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $announcementId, $studentId);

        return $stmt->execute();
    }

    /**
     * Get count of unread announcements for a student
     */
    public function getUnreadAnnouncementCount($studentId)
    {
        $query = "
            SELECT COUNT(*) as count
            FROM announcements a
            WHERE a.target_audience IN ('Students', 'All')
               OR (a.target_audience = 'Specific' 
                   AND EXISTS (
                       SELECT 1 FROM announcement_specific_targets ast 
                       WHERE ast.announcement_id = a.announcement_id 
                       AND ast.target_type = 'student' 
                       AND ast.target_id = ?
                   ))
            AND NOT EXISTS (
                SELECT 1 FROM announcement_reads ar
                WHERE ar.announcement_id = a.announcement_id
                AND ar.student_id = ?
            )
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $studentId, $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['count'];
    }

    /**
     * Get announcement by ID
     */
    public function getAnnouncementById($announcementId)
    {
        $query = "
        SELECT * FROM announcements
        WHERE announcement_id = ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $announcementId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    /**
     * Get announcement by title and date
     */
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

    /**
     * Get read statistics for announcements
     */
    public function getAnnouncementReadStats($announcementId)
    {
        $query = "
        SELECT 
            COUNT(DISTINCT s.student_id) as total_students,
            COUNT(DISTINCT ar.student_id) as read_count
        FROM 
            students s
        LEFT JOIN 
            announcement_reads ar ON s.student_id = ar.student_id AND ar.announcement_id = ?
        WHERE 
            s.resident_status = 'Active'
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $announcementId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Get list of students who have/haven't read an announcement
     */
    public function getStudentReadStatus($announcementId, $readStatus = true)
    {
        $operator = $readStatus ? "IN" : "NOT IN";

        $query = "
        SELECT 
            s.student_id,
            CONCAT(s.first_name, ' ', s.last_name) as student_name,
            ar.read_date
        FROM 
            students s
        LEFT JOIN 
            announcement_reads ar ON s.student_id = ar.student_id AND ar.announcement_id = ?
        WHERE 
            s.resident_status = 'Active'
            AND s.student_id $operator (
                SELECT student_id FROM announcement_reads WHERE announcement_id = ?
            )
        ORDER BY 
            s.last_name, s.first_name
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $announcementId, $announcementId);
        $stmt->execute();
        $result = $stmt->get_result();

        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }

        return $students;
    }

    /**
     * Fetch specific targets for dropdown
     */
    public function fetchSpecificTargets($targetType)
    {
        $targets = [];

        switch ($targetType) {
            case 'student':
                $query = "
                SELECT student_id as id, CONCAT(first_name, ' ', last_name) as name
                FROM students
                WHERE resident_status = 'Active'
                ORDER BY first_name, last_name
                ";
                break;
            case 'admin':
                $query = "
                SELECT admin_id as id, CONCAT(first_name, ' ', last_name) as name
                FROM admins
                ORDER BY first_name, last_name
                ";
                break;
            case 'building':
                $query = "
                SELECT DISTINCT building as id, building as name
                FROM rooms
                ORDER BY building
                ";
                break;
            case 'room':
                $query = "
                SELECT room_id as id, CONCAT(building, ' ', room_number) as name
                FROM rooms
                ORDER BY building, room_number
                ";
                break;
            default:
                return [];
        }

        $result = $this->conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $targets[] = $row;
            }
        }

        return $targets;
    }
}
