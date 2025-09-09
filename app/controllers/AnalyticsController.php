<?php

require_once __DIR__ . "/../../app/models/Analytics.php";

class AnalyticsController
{
    private $analyticsModel;

    public function __construct()
    {
        $this->analyticsModel = new Analytics();
    }

    private function requireAdmin()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Unauthorized'], JSON_PRETTY_PRINT);
            http_response_code(401);
            exit();
        }
    }

    public function getAnalyticsData()
    {
        $this->requireAdmin();

        header('Content-Type: application/json');

        try {

            // Get filter parameters
            $revenuePeriod = $_GET['revenue_period'] ?? 'last30days';
            $reportPeriod = $_GET['report_period'] ?? 'last12months';
            $expensePeriod = $_GET['expense_period'] ?? 'alltime';
            $orderPeriod = $_GET['order_period'] ?? 'thismonth';
            $growthYear = $_GET['growth_year'] ?? date('Y');

            // Get growth data for the specified year
            $growthData = $this->analyticsModel->getGrowthData($growthYear);

            $data = [
                'total_revenue' => $growthData['current_revenue'],
                'previous_year_revenue' => $growthData['previous_revenue'],
                'monthly_revenue' => $this->analyticsModel->getMonthlyRevenue(),
                'previous_year_monthly_revenue' => $this->analyticsModel->getPreviousYearMonthlyRevenue(),
                'company_growth' => $growthData['growth_percentage'],
                'growth_data' => $growthData,
                'expense_ratio' => $this->analyticsModel->getExpenseRatio($expensePeriod),
                'order_data' => $this->analyticsModel->getOrderData($orderPeriod),
                'order_report' => $this->analyticsModel->getOrderReport($orderPeriod),
                'revenue_last_30_days' => $this->analyticsModel->getRevenueByPeriod($revenuePeriod),
                'revenue_report' => $this->analyticsModel->getRevenueReport($reportPeriod)
            ];
            echo json_encode(['success' => true, 'data' => $data], JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_PRETTY_PRINT);
            http_response_code(500);
        }
        exit();
    }
}
