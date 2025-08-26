<?php
require_once __DIR__ . "/../../database/db.php";
require_once __DIR__ . "/../../utils/hostel_helpers.php";

abstract class BaseModel
{
    protected $conn;

    public function __construct()
    {
        $this->conn = getDb();
    }

    /**
     * Add hostel filtering to queries for non-super admins
     */
    protected function addHostelFilter($query, $table_alias = '', $hostel_column = 'hostel_id')
    {
        return addHostelFilter($query, $table_alias, $hostel_column);
    }

    /**
     * Get hostel filter condition for prepared statements
     */
    protected function getHostelFilterCondition($table_alias = '', $hostel_column = 'hostel_id')
    {
        return getHostelFilterCondition($table_alias, $hostel_column);
    }

    /**
     * Validate that a resource belongs to the current admin's hostel
     */
    protected function validateHostelAccess($resource_hostel_id)
    {
        return validateHostelAccess($resource_hostel_id);
    }

    /**
     * Get current admin's hostel ID
     */
    protected function getCurrentAdminHostelId()
    {
        return getCurrentAdminHostelId();
    }

    /**
     * Check if current user is Super Admin
     */
    protected function isSuperAdmin()
    {
        return isSuperAdmin();
    }
}
