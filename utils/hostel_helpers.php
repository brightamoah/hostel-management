<?php
require_once __DIR__ . "/../database/db.php";


/**
 * Check if current user is Super Admin
 */
function isSuperAdmin()
{
    return isset($_SESSION['user']['access_level']) &&
        $_SESSION['user']['access_level'] === 'Super Admin';
}

/**
 * Get current admin's hostel ID
 */
function getCurrentAdminHostelId()
{
    if (isSuperAdmin()) {
        return null; // Super admin can access all hostels
    }

    return $_SESSION['user']['hostel_id'] ?? null;
}

/**
 * Check if admin can access a specific hostel
 */
function canAccessHostel($hostel_id)
{
    if (isSuperAdmin()) {
        return true;
    }

    return getCurrentAdminHostelId() == $hostel_id;
}

/**
 * Add hostel filter to SQL query for non-super admins
 */
function addHostelFilter($query, $table_alias = '', $hostel_column = 'hostel_id')
{
    // Don't apply hostel filtering if user is not logged in or not an admin
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
        return $query; // Students and non-logged users see all available data
    }

    if (isSuperAdmin()) {
        return $query; // No filtering for super admin
    }

    $admin_hostel_id = getCurrentAdminHostelId();
    if (!$admin_hostel_id) {
        // For now, if admin has no hostel assignment, show all data
        // This handles the migration period before hostel assignments are made
        error_log("Warning: Admin {$_SESSION['user']['user_id']} not assigned to any hostel");
        return $query;
    }

    $prefix = $table_alias ? "$table_alias." : '';
    $filter = " AND {$prefix}{$hostel_column} = {$admin_hostel_id}";

    return $query . $filter;
}

/**
 * Get hostel filter condition for prepared statements
 */
function getHostelFilterCondition($table_alias = '', $hostel_column = 'hostel_id')
{
    // Don't apply hostel filtering if user is not logged in or not an admin
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
        return ['condition' => '', 'params' => [], 'types' => ''];
    }

    if (isSuperAdmin()) {
        return ['condition' => '', 'params' => [], 'types' => ''];
    }

    $admin_hostel_id = getCurrentAdminHostelId();
    if (!$admin_hostel_id) {
        // For now, if admin has no hostel assignment, return empty condition
        error_log("Warning: Admin {$_SESSION['user']['user_id']} not assigned to any hostel");
        return ['condition' => '', 'params' => [], 'types' => ''];
    }

    $prefix = $table_alias ? "$table_alias." : '';
    $condition = " AND {$prefix}{$hostel_column} = ?";

    return [
        'condition' => $condition,
        'params' => [$admin_hostel_id],
        'types' => 'i'
    ];
}

/**
 * Validate hostel access for a resource
 */
function validateHostelAccess($resource_hostel_id)
{
    // Don't validate if user is not logged in or not an admin
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
        return true; // Students can access any hostel's resources
    }

    if (isSuperAdmin()) {
        return true;
    }

    $admin_hostel_id = getCurrentAdminHostelId();
    if (!$admin_hostel_id) {
        // For now, allow access if admin has no hostel assignment
        error_log("Warning: Admin {$_SESSION['user']['user_id']} not assigned to any hostel");
        return true;
    }

    if ($resource_hostel_id != $admin_hostel_id) {
        throw new Exception('Access denied: Resource belongs to different hostel');
    }

    return true;
}

/**
 * Get all hostels (for super admin dropdowns)
 */
function getAllHostels()
{
    if (!isSuperAdmin()) {
        return [];
    }

    $conn = getDb();

    $query = "SELECT hostel_id, hostel_name, hostel_code, status FROM hostels ORDER BY hostel_name";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();

    $hostels = [];
    while ($row = $result->fetch_assoc()) {
        $hostels[] = $row;
    }

    $stmt->close();
    return $hostels;
}

/**
 * Get current admin's hostel details
 */
function getCurrentHostelDetails()
{
    $hostel_id = getCurrentAdminHostelId();
    if (!$hostel_id) {
        return null;
    }

    $conn = getDb();

    $query = "SELECT * FROM hostels WHERE hostel_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $hostel_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $hostel = $result->fetch_assoc();
    $stmt->close();

    return $hostel;
}

/**
 * Check if current admin can manage a specific user
 */
function canManageUser($target_user_id)
{
    if (isSuperAdmin()) {
        return true; // Super admin can manage any user
    }

    $conn = getDb();

    // Get target user info
    $query = "SELECT u.role, s.hostel_id as student_hostel, a.hostel_id as admin_hostel 
              FROM users u 
              LEFT JOIN students s ON u.user_id = s.user_id 
              LEFT JOIN admins a ON u.user_id = a.user_id 
              WHERE u.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $target_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $target_user = $result->fetch_assoc();
    $stmt->close();

    if (!$target_user) {
        return false;
    }

    $current_admin_hostel = getCurrentAdminHostelId();

    // If target is a student, check if they're in the same hostel
    if ($target_user['role'] === 'Student') {
        return $target_user['student_hostel'] == $current_admin_hostel;
    }

    // If target is an admin, regular admins can only manage admins in their hostel
    if ($target_user['role'] === 'Admin') {
        return $target_user['admin_hostel'] == $current_admin_hostel;
    }

    return false;
}

/**
 * Check if current admin can perform role changes
 */
function canChangeRole($target_user_id, $new_role)
{
    if (isSuperAdmin()) {
        return true; // Super admin can change any role
    }

    // Regular admins can only promote students to regular admin
    $conn = getDb();
    $query = "SELECT role FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $target_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_role = $result->fetch_assoc()['role'] ?? null;
    $stmt->close();

    // Regular admin can only promote Student → Admin, not demote Admin → Student
    if ($current_role === 'Student' && $new_role === 'Admin') {
        return canManageUser($target_user_id);
    }

    // Cannot demote admins or change admin roles
    return false;
}

/**
 * Check if current admin can delete users
 */
function canDeleteUser($target_user_id)
{
    // Only Super Admin can delete users
    return isSuperAdmin();
}

/**
 * Check if current admin can assign hostels
 */
function canAssignHostels()
{
    // Only Super Admin can assign hostels
    return isSuperAdmin();
}

/**
 * Get current admin's access level for frontend
 */
function getAdminAccessLevel()
{
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
        return null;
    }

    return [
        'is_super_admin' => isSuperAdmin(),
        'hostel_id' => getCurrentAdminHostelId(),
        'can_delete_users' => canDeleteUser(0), // Pass dummy ID since this just checks super admin
        'can_assign_hostels' => canAssignHostels(),
        'access_level' => $_SESSION['user']['access_level'] ?? 'Regular Admin'
    ];
}
