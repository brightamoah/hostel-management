<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ ."/../../app/models/Visitor.php";

$visitorModel = new Visitor();
$totalVisitors = 0;
$approvedVisitors = 0;
$checkedInVisitors = 0;
$pendingVisitors = 0;

try {
    $db = $db->connect();

    // Total visitors
    $query = "SELECT COUNT(*) as count FROM visitors";
    $result = $db->query($query);
    $totalVisitors = $result->fetch_assoc()['count'] ?? 0;

    // Approved visitors
    $query = "SELECT COUNT(*) as count FROM visitors WHERE status = 'Approved'";
    $result = $db->query($query);
    $approvedVisitors = $result->fetch_assoc()['count'] ?? 0;

    // Checked-In visitors
    $query = "SELECT COUNT(*) as count FROM visitors WHERE status = 'Checked-In'";
    $result = $db->query($query);
    $checkedInVisitors = $result->fetch_assoc()['count'] ?? 0;

    // Pending visitors
    $query = "SELECT COUNT(*) as count FROM visitors WHERE status = 'Pending'";
    $result = $db->query($query);
    $pendingVisitors = $result->fetch_assoc()['count'] ?? 0;

    $db->close();
} catch (Exception $e) {
    error_log("Error in visitor_stats: " . $e->getMessage());
}
