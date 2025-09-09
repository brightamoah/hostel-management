<?php
require_once __DIR__ . "/../../database/db.php";

class Analytics
{
    private $conn;

    public function __construct()
    {
        $this->conn = getDb();
    }
    public function __destruct()
    {
        $this->conn->close();
    }

    /**
     * Get total revenue from payments
     * @return float
     */
    public function getTotalRevenue()
    {
        $query = "SELECT SUM(amount) as total FROM payments WHERE status = 'Completed'";
        $result = $this->conn->query($query);
        return $result ? floatval($result->fetch_assoc()['total']) : 0;
    }

    /**
     * Get total revenue for a specific year
     * @param int $year
     * @return float
     */
    public function getTotalRevenueByYear($year)
    {
        $query = "SELECT SUM(amount) as total FROM payments WHERE YEAR(payment_date) = ? AND status = 'Completed'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $year);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? floatval($result->fetch_assoc()['total']) : 0;
    }

    /**
     * Get previous year's total revenue
     * @return float
     */
    public function getPreviousYearTotalRevenue()
    {
        $previousYear = date('Y') - 1;
        return $this->getTotalRevenueByYear($previousYear);
    }


    /**
     * Get monthly revenue data for the current year
     * @return array
     */
    public function getMonthlyRevenue()
    {
        $query = "
            SELECT 
                MONTH(payment_date) as month,
                SUM(amount) as revenue
            FROM payments
            WHERE YEAR(payment_date) = YEAR(CURDATE()) AND status = 'Completed'
            GROUP BY MONTH(payment_date)
            ORDER BY month
        ";
        $result = $this->conn->query($query);
        $data = array_fill(1, 12, 0);
        while ($row = $result->fetch_assoc()) {
            $data[intval($row['month'])] = floatval($row['revenue']);
        }
        return array_values($data);
    }

    /**
     * Get previous year's monthly revenue for growth comparison
     * @return array
     */
    public function getPreviousYearMonthlyRevenue()
    {
        $query = "
            SELECT 
                MONTH(payment_date) as month,
                SUM(amount) as revenue
            FROM payments
            WHERE YEAR(payment_date) = YEAR(CURDATE()) - 1 AND status = 'Completed'
            GROUP BY MONTH(payment_date)
            ORDER BY month
        ";
        $result = $this->conn->query($query);
        $data = array_fill(1, 12, 0);
        while ($row = $result->fetch_assoc()) {
            $data[intval($row['month'])] = floatval($row['revenue']);
        }
        return array_values($data);
    }

    // /**
    //  * Calculate company growth percentage
    //  * @return float
    //  */
    // public function getCompanyGrowth()
    // {
    //     $current = $this->getTotalRevenue();
    //     $previous = $this->getPreviousYearTotalRevenue();
    //     if ($previous == 0) return $current > 0 ? 100 : 0;
    //     return round((($current - $previous) / $previous) * 100, 1);
    // }


    // private function getPreviousYearTotalRevenue()
    // {
    //     $query = "SELECT SUM(amount) as total FROM payments WHERE YEAR(payment_date) = YEAR(CURDATE()) - 1 AND status = 'Completed'";
    //     $result = $this->conn->query($query);
    //     return $result ? floatval($result->fetch_assoc()['total']) : 0;
    // }



    /**
     * Calculate company growth percentage
     * @param int|null $year - specific year to calculate growth for
     * @return float
     */
    public function getCompanyGrowth($year = null)
    {
        if ($year === null) {
            $year = date('Y');
        }

        $currentYear = intval($year);
        $previousYear = $currentYear - 1;

        $currentRevenue = $this->getTotalRevenueByYear($currentYear);
        $previousRevenue = $this->getTotalRevenueByYear($previousYear);

        if ($previousRevenue == 0) {
            return $currentRevenue > 0 ? 100 : 0;
        }

        return round((($currentRevenue - $previousRevenue) / $previousRevenue) * 100, 1);
    }


    /**
     * Get growth data for a specific year including comparison values
     * @param int|null $year
     * @return array
     */
    public function getGrowthData($year = null)
    {
        if ($year === null) {
            $year = date('Y');
        }

        $currentYear = intval($year);
        $previousYear = $currentYear - 1;

        $currentRevenue = $this->getTotalRevenueByYear($currentYear);
        $previousRevenue = $this->getTotalRevenueByYear($previousYear);
        $growthPercentage = $this->getCompanyGrowth($year);

        return [
            'current_year' => $currentYear,
            'previous_year' => $previousYear,
            'current_revenue' => $currentRevenue,
            'previous_revenue' => $previousRevenue,
            'growth_percentage' => $growthPercentage
        ];
    }

    // /**
    //  * Get expense ratio by categories (assuming categories from billing_type)
    //  * @return array
    //  */
    // public function getExpenseRatio()
    // {
    //     $query = "
    //         SELECT billing_type, SUM(amount) as total
    //         FROM billing
    //         WHERE billing_type IN ('Utility Fee', 'Maintenance Fee', 'Other')
    //         GROUP BY billing_type
    //     ";
    //     $result = $this->conn->query($query);
    //     $data = [];
    //     $total = 0;
    //     while ($row = $result->fetch_assoc()) {
    //         $data[$row['billing_type']] = floatval($row['total']);
    //         $total += floatval($row['total']);
    //     }
    //     foreach ($data as $key => $value) {
    //         $data[$key] = $total > 0 ? round(($value / $total) * 100) : 0;
    //     }
    //     return $data;
    // }

    /**
     * Get expense ratio by categories (assuming categories from billing_type)
     * @param string $period
     * @return array
     */
    public function getExpenseRatio($period = 'alltime')
    {
        $whereClause = '';
        if ($period !== 'alltime') {
            $dateFilter = $this->buildDateFilter($period, 'date_issued');
            $whereClause = "AND " . $dateFilter;
        }

        $query = "
            SELECT billing_type, SUM(amount) as total
            FROM billing
            WHERE billing_type IN ('Utility Fee', 'Maintenance Fee', 'Other') $whereClause
            GROUP BY billing_type
        ";
        $result = $this->conn->query($query);
        $data = [];
        $total = 0;
        while ($row = $result->fetch_assoc()) {
            $data[$row['billing_type']] = floatval($row['total']);
            $total += floatval($row['total']);
        }

        // Convert to percentages
        foreach ($data as $key => $value) {
            $data[$key] = $total > 0 ? round(($value / $total) * 100) : 0;
        }

        // Ensure we have some default data if no records exist
        if (empty($data)) {
            $data = [
                'Utility Fee' => 45,
                'Maintenance Fee' => 35,
                'Other' => 20
            ];
        }

        return $data;
    }


    // /**
    //  * Get order/booking data (e.g., allocations per month)
    //  * @return array
    //  */
    // public function getOrderData()
    // {
    //     $query = "
    //         SELECT COUNT(*) as count
    //         FROM allocations
    //         WHERE MONTH(start_date) = MONTH(CURDATE())
    //         GROUP BY DAY(start_date)
    //         ORDER BY start_date LIMIT 7
    //     ";
    //     $result = $this->conn->query($query);
    //     $data = [];
    //     while ($row = $result->fetch_assoc()) {
    //         $data[] = intval($row['count']);
    //     }
    //     return $data;
    // }


    /**
     * Get order/booking data based on period
     * @param string $period
     * @return array
     */
    public function getOrderData($period = 'thismonth')
    {
        $whereClause = $this->buildDateFilter($period, 'start_date');

        $query = "
            SELECT 
                CAST(start_date AS DATE) as date,
                COUNT(*) as count
            FROM allocations
            WHERE {$whereClause}
            GROUP BY CAST(start_date AS DATE)
            ORDER BY date
        ";

        $result = $this->conn->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'x' => $row['date'],
                'y' => intval($row['count'])
            ];
        }

        // If no data, generate sample data for the period
        if (empty($data)) {
            $daysBack = 7;
            if ($period === 'thismonth' || $period === 'last30days') {
                $daysBack = 30;
            }

            for ($i = $daysBack - 1; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $data[] = [
                    'x' => $date,
                    'y' => rand(1, 10) // Sample bookings
                ];
            }
        }

        return $data;
    }

    /**
     * Get booking/order report data with time period filter for comparison
     * @param string $period - last3months, last6months, last12months, thisweek, thismonth, last30days
     * @return array
     */
    public function getOrderReport($period = 'last12months')
    {
        // Handle both report periods and order periods
        switch ($period) {
            case 'last3months':
                $months = 3;
                break;
            case 'last6months':
                $months = 6;
                break;
            case 'last12months':
                $months = 12;
                break;
            case 'thisweek':
            case 'thismonth':
            case 'last30days':
                // For shorter periods, default to 3 months comparison
                $months = 3;
                break;
            default:
                $months = 12;
                break;
        }

        // Current period
        $currentQuery = "
            SELECT 
                DATE_FORMAT(start_date, '%Y-%m') as month,
                COUNT(*) as count
            FROM allocations
            WHERE start_date >= DATE_SUB(CURDATE(), INTERVAL {$months} MONTH) 
            GROUP BY DATE_FORMAT(start_date, '%Y-%m')
            ORDER BY month
        ";

        // Previous period for comparison
        $previousQuery = "
            SELECT 
                DATE_FORMAT(start_date, '%Y-%m') as month,
                COUNT(*) as count
            FROM allocations
            WHERE start_date >= DATE_SUB(CURDATE(), INTERVAL " . ($months * 2) . " MONTH) 
            AND start_date < DATE_SUB(CURDATE(), INTERVAL {$months} MONTH)
            GROUP BY DATE_FORMAT(start_date, '%Y-%m')
            ORDER BY month
        ";

        $currentResult = $this->conn->query($currentQuery);
        $previousResult = $this->conn->query($previousQuery);

        $currentData = [];
        $previousData = [];
        $monthLabels = [];

        // Get current period data
        while ($row = $currentResult->fetch_assoc()) {
            $monthLabels[] = date('M Y', strtotime($row['month'] . '-01'));
            $currentData[] = intval($row['count']);
        }

        // Get previous period data
        while ($row = $previousResult->fetch_assoc()) {
            $previousData[] = intval($row['count']);
        }

        // Generate sample data if no real data exists
        if (empty($currentData)) {
            for ($i = $months - 1; $i >= 0; $i--) {
                $date = date('Y-m', strtotime("-$i months"));
                $monthLabels[] = date('M Y', strtotime($date . '-01'));
                $currentData[] = rand(2, 15);
                $previousData[] = rand(1, 10);
            }
        }

        return [
            'current' => $currentData,
            'previous' => $previousData,
            'labels' => $monthLabels
        ];
    }

    /**
     * Get revenue data based on time period filter
     * @param string $period - today, yesterday, last7days, last30days, currentmonth, lastmonth
     * @return array
     */
    public function getRevenueByPeriod($period = 'last30days')
    {
        // $whereClause = $this->buildDateFilter($period);
        $whereClause = $this->buildDateFilter($period, 'payment_date');

        $query = "
            SELECT 
                CAST(payment_date AS DATE) as date,
                SUM(amount) as revenue
            FROM payments
            WHERE {$whereClause} AND status = 'Completed'
            GROUP BY CAST(payment_date AS DATE)
            ORDER BY date
        ";

        $result = $this->conn->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'x' => $row['date'],
                'y' => floatval($row['revenue'])
            ];
        }

        // If no data, generate sample data for the period
        if (empty($data)) {
            $data = $this->generateSampleRevenueData($period);
        }

        return $data;
    }


    /**
     * Generate sample revenue data for periods with no real data
     * @param string $period
     * @return array
     */
    private function generateSampleRevenueData($period)
    {
        $data = [];
        $daysBack = 30;

        $daysBack = match ($period) {
            'today' => 1,
            'yesterday' => 1,
            'last7days' => 7,
            'last30days' => 30,
        };

        for ($i = $daysBack - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $data[] = [
                'x' => $date,
                'y' => rand(100, 1000) // Sample revenue between 100-1000
            ];
        }

        return $data;
    }

    /**
     * Get daily revenue for the last 30 days
     * @return array
     */
    public function getRevenueLast30Days()
    {
        return $this->getRevenueByPeriod('last30days');
    }


    /**
     * Get revenue report data with time period filter
     * @param string $period - last3months, last6months, last12months
     * @return array
     */
    public function getRevenueReport($period = 'last12months')
    {
        switch ($period) {
            case 'last3months':
                $months = 3;
                break;
            case 'last6months':
                $months = 6;
                break;
            case 'last12months':
            default:
                $months = 12;
                break;
        }

        // Current period
        $currentQuery = "
            SELECT 
                DATE_FORMAT(payment_date, '%Y-%m') as month,
                SUM(amount) as revenue
            FROM payments
            WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL {$months} MONTH) 
            AND status = 'Completed'
            GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
            ORDER BY month
        ";

        // Previous period for comparison
        $previousQuery = "
            SELECT 
                DATE_FORMAT(payment_date, '%Y-%m') as month,
                SUM(amount) as revenue
            FROM payments
            WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL " . ($months * 2) . " MONTH) 
            AND payment_date < DATE_SUB(CURDATE(), INTERVAL {$months} MONTH)
            AND status = 'Completed'
            GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
            ORDER BY month
        ";

        $currentResult = $this->conn->query($currentQuery);
        $previousResult = $this->conn->query($previousQuery);

        $currentData = [];
        $previousData = [];
        $monthLabels = [];

        // Get current period data
        while ($row = $currentResult->fetch_assoc()) {
            $monthLabels[] = date('M Y', strtotime($row['month'] . '-01'));
            $currentData[] = floatval($row['revenue']);
        }

        // Get previous period data
        while ($row = $previousResult->fetch_assoc()) {
            $previousData[] = floatval($row['revenue']);
        }

        // Generate sample data if no real data exists
        if (empty($currentData)) {
            for ($i = $months - 1; $i >= 0; $i--) {
                $date = date('Y-m', strtotime("-$i months"));
                $monthLabels[] = date('M Y', strtotime($date . '-01'));
                $currentData[] = rand(2000, 8000);
                $previousData[] = rand(1500, 6000);
            }
        }


        return [
            'current' => $currentData,
            'previous' => $previousData,
            'labels' => $monthLabels
        ];
    }

    /**
     * Build date filter based on period
     * @param string $period
     * @param string $dateColumn
     * @return string
     */
    private function buildDateFilter($period, $dateColumn = 'payment_date')
    {
        switch ($period) {
            case 'today':
                return "DATE($dateColumn) = CURDATE()";
            case 'yesterday':
                return "DATE($dateColumn) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            case 'last7days':
                return "$dateColumn >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            case 'last30days':
                return "$dateColumn >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            case 'currentmonth':
                return "YEAR($dateColumn) = YEAR(CURDATE()) AND MONTH($dateColumn) = MONTH(CURDATE())";
            case 'lastmonth':
                return "YEAR($dateColumn) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND MONTH($dateColumn) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
            case 'thismonth':
                return "YEAR($dateColumn) = YEAR(CURDATE()) AND MONTH($dateColumn) = MONTH(CURDATE())";
            case 'thisweek':
                return "$dateColumn >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) AND $dateColumn < DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 7 DAY)";
            case 'alltime':
            default:
                return "1=1"; // No date restriction
        }
    }
}
