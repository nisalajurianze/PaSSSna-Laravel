<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Reservation;
use App\Models\Inventory;
use App\Models\Staff;
use App\Models\Table;
use App\Models\User;
use App\Models\MenuItem;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    public function generateSalesReport($startDate, $endDate, $format = 'pdf')
    {
        $orders = Order::with(['user', 'items.menuItem'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $reportData = [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'summary' => $this->calculateSalesSummary($orders),
            'daily_sales' => $this->getDailySales($startDate, $endDate),
            'category_sales' => $this->getCategorySales($startDate, $endDate),
            'payment_methods' => $this->getPaymentMethods($startDate, $endDate),
            'top_items' => $this->getTopItems($startDate, $endDate),
            'peak_hours' => $this->getPeakHours($startDate, $endDate),
            'orders' => $orders,
        ];

        if ($format === 'pdf') {
            return $this->generatePDF($reportData, 'sales-report');
        }

        return $reportData;
    }

    public function generateInventoryReport($startDate, $endDate, $format = 'pdf')
    {
        $inventoryService = new InventoryService();
        $reportData = $inventoryService->generateInventoryReport($startDate, $endDate);

        $summary = [
            'total_items' => count($reportData),
            'total_value' => array_sum(array_column($reportData, 'total_value')),
            'low_stock_items' => count(array_filter($reportData, fn($item) => $item['is_low_stock'])),
            'out_of_stock_items' => count(array_filter($reportData, fn($item) => $item['status'] === 'Out of Stock')),
        ];

        $fullReport = [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'summary' => $summary,
            'inventory_items' => $reportData,
            'category_distribution' => $this->getInventoryByCategory($reportData),
            'value_distribution' => $this->getInventoryValueDistribution($reportData),
        ];

        if ($format === 'pdf') {
            return $this->generatePDF($fullReport, 'inventory-report');
        }

        return $fullReport;
    }

    public function generateReservationReport($startDate, $endDate, $format = 'pdf')
    {
        $reservationService = new ReservationService();
        $reportData = $reservationService->getReservationStatistics('custom');

        $reservations = Reservation::with(['user', 'table'])
            ->whereBetween('reservation_date', [$startDate, $endDate])
            ->get();

        $fullReport = [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'summary' => $reportData,
            'daily_reservations' => $this->getDailyReservations($startDate, $endDate),
            'table_utilization' => $reservationService->getTableUtilization($startDate, $endDate),
            'customer_type' => $this->getReservationCustomerType($reservations),
            'reservations' => $reservations,
        ];

        if ($format === 'pdf') {
            return $this->generatePDF($fullReport, 'reservation-report');
        }

        return $fullReport;
    }

    public function generateStaffReport($startDate, $endDate, $format = 'pdf')
    {
        $staff = Staff::with(['user', 'shiftSchedules'])
            ->whereBetween('hire_date', [$startDate, $endDate])
            ->orWhereHas('shiftSchedules', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('shift_date', [$startDate, $endDate]);
            })
            ->get();

        $reportData = [];
        foreach ($staff as $employee) {
            $hoursWorked = $employee->getTotalHoursWorked($startDate, $endDate);
            $wages = $employee->calculateWages($startDate, $endDate);

            $reportData[] = [
                'staff' => $employee,
                'hours_worked' => $hoursWorked,
                'wages' => $wages,
                'shifts_completed' => $employee->shiftSchedules()
                    ->whereBetween('shift_date', [$startDate, $endDate])
                    ->where('status', 'completed')
                    ->count(),
                'attendance_rate' => $this->calculateAttendanceRate($employee, $startDate, $endDate),
            ];
        }

        $summary = [
            'total_staff' => count($reportData),
            'total_hours_worked' => array_sum(array_column($reportData, 'hours_worked')),
            'total_wages' => array_sum(array_column($reportData, 'wages')),
            'average_attendance' => array_sum(array_column($reportData, 'attendance_rate')) / count($reportData),
        ];

        $fullReport = [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'summary' => $summary,
            'staff_data' => $reportData,
            'role_distribution' => $this->getStaffByRole($staff),
            'department_distribution' => $this->getStaffByDepartment($staff),
        ];

        if ($format === 'pdf') {
            return $this->generatePDF($fullReport, 'staff-report');
        }

        return $fullReport;
    }

    public function generateCustomerReport($startDate, $endDate, $format = 'pdf')
    {
        $customers = User::where('role', 'customer')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->withCount(['orders', 'reservations'])
            ->with(['orders', 'reservations'])
            ->get();

        $reportData = [];
        foreach ($customers as $customer) {
            $totalSpent = $customer->orders->sum('total');
            $averageOrderValue = $customer->orders->count() > 0 ? $totalSpent / $customer->orders->count() : 0;

            $reportData[] = [
                'customer' => $customer,
                'total_orders' => $customer->orders_count,
                'total_reservations' => $customer->reservations_count,
                'total_spent' => $totalSpent,
                'average_order_value' => $averageOrderValue,
                'last_order_date' => $customer->orders->max('created_at'),
                'customer_since' => $customer->created_at->format('Y-m-d'),
            ];
        }

        $summary = [
            'total_customers' => count($reportData),
            'new_customers' => count(array_filter($reportData, fn($c) =>
                Carbon::parse($c['customer_since'])->between($startDate, $endDate))),
            'total_revenue' => array_sum(array_column($reportData, 'total_spent')),
            'average_orders_per_customer' => array_sum(array_column($reportData, 'total_orders')) / count($reportData),
            'repeat_customers' => count(array_filter($reportData, fn($c) => $c['total_orders'] > 1)),
        ];

        $fullReport = [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'summary' => $summary,
            'customer_data' => $reportData,
            'customer_acquisition' => $this->getCustomerAcquisition($startDate, $endDate),
            'customer_segments' => $this->segmentCustomers($reportData),
        ];

        if ($format === 'pdf') {
            return $this->generatePDF($fullReport, 'customer-report');
        }

        return $fullReport;
    }

    public function generateFinancialReport($startDate, $endDate, $format = 'pdf')
    {
        $orderService = new OrderService();
        $inventoryService = new InventoryService();
        $staffService = new StaffService();

        $salesData = $orderService->getOrderStatistics('custom');
        $inventoryData = $inventoryService->getInventoryTurnover('custom');
        $staffCosts = $staffService->calculateTotalWages($startDate, $endDate);

        // Calculate other costs (simplified)
        $otherCosts = [
            'rent' => 50000, // Example fixed cost
            'utilities' => 15000,
            'marketing' => 10000,
            'maintenance' => 5000,
            'other' => 10000,
        ];

        $totalRevenue = $salesData['total_revenue'];
        $totalCosts = $staffCosts + array_sum($otherCosts);
        $grossProfit = $totalRevenue - $totalCosts;
        $profitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        $reportData = [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'revenue' => [
                'total' => $totalRevenue,
                'breakdown' => [
                    'dine_in' => Order::where('order_type', 'dine_in')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->sum('total'),
                    'takeaway' => Order::where('order_type', 'takeaway')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->sum('total'),
                    'delivery' => Order::where('order_type', 'delivery')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->sum('total'),
                ],
            ],
            'costs' => [
                'staff' => $staffCosts,
                'inventory' => $inventoryData['usage_cost'],
                'other' => $otherCosts,
                'total' => $totalCosts,
            ],
            'profitability' => [
                'gross_profit' => $grossProfit,
                'profit_margin' => $profitMargin,
                'break_even_point' => $this->calculateBreakEvenPoint($totalRevenue, $totalCosts),
            ],
            'efficiency_metrics' => [
                'inventory_turnover' => $inventoryData['turnover_ratio'],
                'staff_productivity' => $totalRevenue / $staffCosts,
                'table_turnover' => $this->calculateTableTurnover($startDate, $endDate),
            ],
            'cash_flow' => $this->calculateCashFlow($startDate, $endDate),
        ];

        if ($format === 'pdf') {
            return $this->generatePDF($reportData, 'financial-report');
        }

        return $reportData;
    }

    private function calculateSalesSummary($orders)
    {
        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $orderTypes = [
            'dine_in' => $orders->where('order_type', 'dine_in')->count(),
            'takeaway' => $orders->where('order_type', 'takeaway')->count(),
            'delivery' => $orders->where('order_type', 'delivery')->count(),
        ];

        $statusCount = $orders->groupBy('status')->map->count();

        return [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'average_order_value' => $averageOrderValue,
            'order_types' => $orderTypes,
            'status_distribution' => $statusCount,
        ];
    }

    private function getDailySales($startDate, $endDate)
    {
        $sales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $sales;
    }

    private function getCategorySales($startDate, $endDate)
    {
        $categorySales = OrderItem::join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->selectRaw('menu_items.category, SUM(order_items.quantity) as quantity, SUM(order_items.subtotal) as revenue')
            ->groupBy('menu_items.category')
            ->orderBy('revenue', 'desc')
            ->get();

        return $categorySales;
    }

    private function getPaymentMethods($startDate, $endDate)
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as amount')
            ->groupBy('payment_method')
            ->get();
    }

    private function getTopItems($startDate, $endDate, $limit = 10)
    {
        return OrderItem::join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->selectRaw('menu_items.name, SUM(order_items.quantity) as quantity, SUM(order_items.subtotal) as revenue')
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderBy('revenue', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getPeakHours($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->get();

        return $orders;
    }

    private function getInventoryByCategory($inventoryData)
    {
        $categories = [];
        foreach ($inventoryData as $item) {
            $category = $item['category'];
            if (!isset($categories[$category])) {
                $categories[$category] = [
                    'count' => 0,
                    'value' => 0,
                ];
            }
            $categories[$category]['count']++;
            $categories[$category]['value'] += $item['total_value'];
        }

        return $categories;
    }

    private function getInventoryValueDistribution($inventoryData)
    {
        $distribution = [
            'high_value' => 0,
            'medium_value' => 0,
            'low_value' => 0,
        ];

        $totalValue = array_sum(array_column($inventoryData, 'total_value'));

        foreach ($inventoryData as $item) {
            $percentage = ($item['total_value'] / $totalValue) * 100;

            if ($percentage > 10) {
                $distribution['high_value']++;
            } elseif ($percentage > 1) {
                $distribution['medium_value']++;
            } else {
                $distribution['low_value']++;
            }
        }

        return $distribution;
    }

    private function getDailyReservations($startDate, $endDate)
    {
        return Reservation::whereBetween('reservation_date', [$startDate, $endDate])
            ->selectRaw('reservation_date as date, COUNT(*) as count, SUM(number_of_people) as people')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getReservationCustomerType($reservations)
    {
        $types = [
            'new' => 0,
            'returning' => 0,
            'walk_in' => 0,
        ];

        foreach ($reservations as $reservation) {
            if ($reservation->user_id) {
                // Check if this user has previous reservations
                $previousReservations = Reservation::where('user_id', $reservation->user_id)
                    ->where('id', '<', $reservation->id)
                    ->count();

                if ($previousReservations > 0) {
                    $types['returning']++;
                } else {
                    $types['new']++;
                }
            } else {
                $types['walk_in']++;
            }
        }

        return $types;
    }

    private function calculateAttendanceRate($staff, $startDate, $endDate)
    {
        $scheduledShifts = $staff->shiftSchedules()
            ->whereBetween('shift_date', [$startDate, $endDate])
            ->whereIn('status', ['approved', 'completed'])
            ->count();

        $attendedShifts = $staff->shiftSchedules()
            ->whereBetween('shift_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        return $scheduledShifts > 0 ? ($attendedShifts / $scheduledShifts) * 100 : 0;
    }

    private function getStaffByRole($staff)
    {
        $roles = [];
        foreach ($staff as $employee) {
            $role = $employee->role_text;
            if (!isset($roles[$role])) {
                $roles[$role] = 0;
            }
            $roles[$role]++;
        }

        return $roles;
    }

    private function getStaffByDepartment($staff)
    {
        $departments = [];
        foreach ($staff as $employee) {
            $dept = $employee->department_text;
            if (!isset($departments[$dept])) {
                $departments[$dept] = 0;
            }
            $departments[$dept]++;
        }

        return $departments;
    }

    private function getCustomerAcquisition($startDate, $endDate)
    {
        $acquisition = [];
        $currentDate = Carbon::parse($startDate);

        while ($currentDate <= $endDate) {
            $newCustomers = User::where('role', 'customer')
                ->whereDate('created_at', $currentDate)
                ->count();

            $acquisition[$currentDate->format('Y-m-d')] = $newCustomers;
            $currentDate->addDay();
        }

        return $acquisition;
    }

    private function segmentCustomers($customerData)
    {
        $segments = [
            'vip' => [],
            'regular' => [],
            'occasional' => [],
            'new' => [],
        ];

        foreach ($customerData as $customer) {
            $totalOrders = $customer['total_orders'];
            $totalSpent = $customer['total_spent'];

            if ($totalSpent > 10000 || $totalOrders > 20) {
                $segments['vip'][] = $customer;
            } elseif ($totalSpent > 5000 || $totalOrders > 10) {
                $segments['regular'][] = $customer;
            } elseif ($totalOrders > 1) {
                $segments['occasional'][] = $customer;
            } else {
                $segments['new'][] = $customer;
            }
        }

        return [
            'vip_count' => count($segments['vip']),
            'regular_count' => count($segments['regular']),
            'occasional_count' => count($segments['occasional']),
            'new_count' => count($segments['new']),
            'total_customers' => array_sum([
                count($segments['vip']),
                count($segments['regular']),
                count($segments['occasional']),
                count($segments['new']),
            ]),
        ];
    }

    private function calculateBreakEvenPoint($revenue, $costs)
    {
        // Simplified calculation
        $dailyRevenue = $revenue / 30; // Assuming monthly report
        $dailyCosts = $costs / 30;

        return $dailyCosts > 0 ? $dailyRevenue / $dailyCosts : 0;
    }

    private function calculateTableTurnover($startDate, $endDate)
    {
        $reservations = Reservation::whereBetween('reservation_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $totalTables = Table::where('is_active', true)->count();
        $totalDays = Carbon::parse($endDate)->diffInDays($startDate) + 1;

        $totalReservations = $reservations->count();
        $totalPossibleReservations = $totalTables * $totalDays * 3; // Assuming 3 time slots per day

        return $totalPossibleReservations > 0 ? ($totalReservations / $totalPossibleReservations) * 100 : 0;
    }

    private function calculateCashFlow($startDate, $endDate)
    {
        // Simplified cash flow calculation
        $revenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'completed')
            ->sum('total');

        $expenses = 0; // This should include all expenses

        return [
            'inflow' => $revenue,
            'outflow' => $expenses,
            'net_cash_flow' => $revenue - $expenses,
        ];
    }

    private function generatePDF($data, $reportType)
    {
        $pdf = Pdf::loadView('pdf.report', [
            'data' => $data,
            'reportType' => $reportType,
            'generatedAt' => now(),
            'restaurantName' => 'PaSSSna Restaurant',
        ]);

        $filename = $reportType . '-' . now()->format('Y-m-d-H-i-s') . '.pdf';
        $path = 'reports/' . $filename;

        Storage::put($path, $pdf->output());

        return [
            'path' => $path,
            'filename' => $filename,
            'url' => Storage::url($path),
        ];
    }
}
