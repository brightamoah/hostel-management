<?php

require_once __DIR__ . "/../../database/db.php";

class NotificationController
{
    private $conn;

    public function __construct()
    {
        $this->conn = getDb();
    }

    /**
     * Get notifications for the current user (based on role)
     * @return array
     */
    public function getNotifications()
    {
        if (!isset($_SESSION['user'])) {
            return [];
        }

        $user = $_SESSION['user'];
        $role = $user['role'];

        if ($role === 'Admin') {
            return $this->getAdminNotifications();
        } else {
            return $this->getStudentNotifications();
        }
    }

    /**
     * Get notifications for students (announcements)
     * @return array
     */
    private function getStudentNotifications()
    {
        $studentId = $_SESSION['user']['student_id'] ?? 0;

        if (!$studentId) {
            return [];
        }

        $query = "
        SELECT 
            a.announcement_id,
            a.title,
            a.priority,
            a.date_posted,
            CASE WHEN ar.read_id IS NOT NULL THEN 1 ELSE 0 END as is_read,
            'announcement' as type
        FROM announcements a
        LEFT JOIN announcement_reads ar ON a.announcement_id = ar.announcement_id 
                                      AND ar.student_id = ?
        WHERE (a.target_audience IN ('Students', 'All')
           OR (a.target_audience = 'Specific' 
               AND EXISTS (
                   SELECT 1 FROM announcement_specific_targets ast 
                   WHERE ast.announcement_id = a.announcement_id 
                   AND ast.target_type = 'student' 
                   AND ast.target_id = ?
               )))
        ORDER BY a.date_posted DESC
        LIMIT 10
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $studentId, $studentId);
        $stmt->execute();
        $result = $stmt->get_result();

        $notifications = [];
        while ($row = $result->fetch_assoc()) {
            $notifications[] = [
                'id' => $row['announcement_id'],
                'title' => $row['title'],
                'priority' => $row['priority'],
                'date' => $this->formatRelativeTime($row['date_posted']),
                'is_read' => (bool)$row['is_read'],
                'type' => $row['type'],
                'url' => '/student/announcements',
                'icon' => $this->getPriorityIcon($row['priority']),
                'badge_class' => $this->getPriorityBadgeClass($row['priority'])
            ];
        }

        return $notifications;
    }

    /**
     * Get notifications for admins (system events, new requests, etc.)
     * @return array
     */
    private function getAdminNotifications()
    {
        $notifications = [];

        // Get recent maintenance requests
        $maintenanceQuery = "
        SELECT 
            mr.request_id,
            CONCAT('Maintenance Request - ', mr.issue_type) as title,
            mr.priority,
            mr.request_date,
            'maintenance' as type
        FROM maintenance_requests mr
        WHERE mr.status = 'Pending'
        ORDER BY mr.request_date DESC
        LIMIT 5
        ";

        $result = $this->conn->query($maintenanceQuery);
        while ($row = $result->fetch_assoc()) {
            $notifications[] = [
                'id' => $row['request_id'],
                'title' => $row['title'],
                'priority' => $row['priority'],
                'date' => $this->formatRelativeTime($row['request_date']),
                'is_read' => false, // Admin notifications don't have read status yet
                'type' => $row['type'],
                'url' => '/admin/maintenance',
                'icon' => 'bx-wrench',
                'badge_class' => $this->getPriorityBadgeClass($row['priority'])
            ];
        }

        // Get recent visitor approvals needed
        $visitorQuery = "
        SELECT 
            v.visitor_id,
            CONCAT('Visitor Approval - ', v.visitor_name) as title,
            'Medium' as priority,
            v.registered_at,
            'visitor' as type
        FROM visitors v
        WHERE v.status = 'Pending'
        ORDER BY v.registered_at DESC
        LIMIT 5
        ";

        $result = $this->conn->query($visitorQuery);
        while ($row = $result->fetch_assoc()) {
            $notifications[] = [
                'id' => $row['visitor_id'],
                'title' => $row['title'],
                'priority' => $row['priority'],
                'date' => $this->formatRelativeTime($row['registered_at']),
                'is_read' => false,
                'type' => $row['type'],
                'url' => '/admin/visitors',
                'icon' => 'bx-user-check',
                'badge_class' => 'bg-label-info'
            ];
        }

        // Sort by date (newest first)
        usort($notifications, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($notifications, 0, 10);
    }

    /**
     * Get notification count for current user
     * @return int
     */
    public function getNotificationCount()
    {
        if (!isset($_SESSION['user'])) {
            return 0;
        }

        $user = $_SESSION['user'];
        $role = $user['role'];

        if ($role === 'Admin') {
            return $this->getAdminNotificationCount();
        } else {
            return $this->getStudentNotificationCount();
        }
    }

    /**
     * Get unread notification count for students
     * @return int
     */
    private function getStudentNotificationCount()
    {
        $studentId = $_SESSION['user']['student_id'] ?? 0;

        if (!$studentId) {
            return 0;
        }

        $query = "
            SELECT COUNT(*) as count
            FROM announcements a
            WHERE (a.target_audience IN ('Students', 'All')
               OR (a.target_audience = 'Specific' 
                   AND EXISTS (
                       SELECT 1 FROM announcement_specific_targets ast 
                       WHERE ast.announcement_id = a.announcement_id 
                       AND ast.target_type = 'student' 
                       AND ast.target_id = ?
                   )))
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

        return (int)$row['count'];
    }

    /**
     * Get notification count for admins
     * @return int
     */
    private function getAdminNotificationCount()
    {
        $count = 0;

        // Count pending maintenance requests
        $maintenanceQuery = "SELECT COUNT(*) as count FROM maintenance_requests WHERE status = 'Pending'";
        $result = $this->conn->query($maintenanceQuery);
        $row = $result->fetch_assoc();
        $count += (int)$row['count'];

        // Count pending visitor approvals
        $visitorQuery = "SELECT COUNT(*) as count FROM visitors WHERE status = 'Pending'";
        $result = $this->conn->query($visitorQuery);
        $row = $result->fetch_assoc();
        $count += (int)$row['count'];

        return $count;
    }

    /**
     * Format timestamp to relative time (e.g., "2 hours ago")
     * @param string $timestamp
     * @return string
     */
    private function formatRelativeTime($timestamp)
    {
        $time = time() - strtotime($timestamp);

        if ($time < 60) {
            return 'just now';
        } elseif ($time < 3600) {
            $minutes = floor($time / 60);
            return $minutes . 'm ago';
        } elseif ($time < 86400) {
            $hours = floor($time / 3600);
            return $hours . 'h ago';
        } elseif ($time < 2592000) {
            $days = floor($time / 86400);
            return $days . 'd ago';
        } else {
            return date('M d', strtotime($timestamp));
        }
    }

    /**
     * Get icon based on priority
     * @param string $priority
     * @return string
     */
    private function getPriorityIcon($priority)
    {
        switch (strtolower($priority)) {
            case 'urgent':
                return 'bx-error-circle';
            case 'high':
                return 'bx-info-circle';
            case 'medium':
                return 'bx-bell';
            case 'low':
            default:
                return 'bx-message-dots';
        }
    }

    /**
     * Get badge class based on priority
     * @param string $priority
     * @return string
     */
    private function getPriorityBadgeClass($priority)
    {
        switch (strtolower($priority)) {
            case 'urgent':
                return 'bg-label-danger';
            case 'high':
                return 'bg-label-warning';
            case 'medium':
                return 'bg-label-info';
            case 'low':
            default:
                return 'bg-label-success';
        }
    }
}
