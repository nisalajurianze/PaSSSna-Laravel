<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Report;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Staff;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\DailyReport;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class GenerateDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:generate-daily';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Generate daily report for restaurant operations';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Generating daily report...');

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $adminUsers = User::where('role', 'admin')
            ->where('is_active', true)
            ->get();

        // Collect daily statistics
        $stats = $this->collectDailyStats($today);
        $comparison = $this->collectComparisonStats($yesterday);

        // Generate PDF report
        $pdfData = [
            'date' => $today->format('F d, Y'),
            'stats' => $stats,
            'comparison' => $comparison,
            'top_items' => $this->getTopSellingItems($today),
            'peak_hours' => $this->getPeakHours($today),
            'reservation_stats' => $this->getReservationStats($today),
            'staff_performance' => $this->getStaffPerformance($today),
        ];

        // Generate PDF
        $pdf = PDF::loadView('pdf.daily-report', $pdfData);
        $fileName = 'daily-report-' . $today->format('Y-m-d') . '.pdf';
        $filePath = storage_path('app/reports/' . $fileName);

        // Ensure directory exists
        if (!file_exists(storage_path('app/reports'))) {
            mkdir(storage_path('app/reports'), 0755, true);
        }

        $pdf->save($filePath);

        // Send email to admin
        $adminEmails = $adminUsers->pluck('email')->filter()->values()->toArray();

        foreach ($adminEmails as $email) {
            Mail::to($email)->send(new DailyReport($stats, $filePath));
            $this->info('Daily report sent to: ' . $email);
        }

        // Store report record in database
        $generatedById = $adminUsers->first()?->id;
        if (!$generatedById) {
            $this->warn('No active admin users found. Skipping report record creation.');
            $this->info('Daily report generated successfully: ' . $fileName);
            return;
        }

        $report = Report::create([
            'report_number' => Report::generateReportNumber('DR'),
            'report_type' => Report::TYPE_DAILY,
            'title' => 'Daily Operations Report - ' . $today->format('Y-m-d'),
            'start_date' => $today->toDateString(),
            'end_date' => $today->toDateString(),
            'generated_by' => $generatedById,
            'data' => $stats,
            'summary' => [
                'total_revenue' => $stats['total_revenue'] ?? 0,
                'order_count' => $stats['order_count'] ?? 0,
                'reservation_count' => $stats['reservation_count'] ?? 0,
                'new_customers' => $stats['new_customers'] ?? 0,
                'low_stock_items' => $stats['low_stock_items'] ?? 0,
            ],
            'file_path' => $filePath,
        ]);

        // Create notifications for admin users
        foreach ($adminUsers as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => Notification::TYPE_SYSTEM,
                'title' => 'Daily Report Generated',
                'message' => 'A daily report for ' . $today->format('F d, Y') . ' has been generated and is available for download.',
                'data' => ['report_id' => $report->id],
                'action_url' => route('admin.reports.index'),
                'priority' => Notification::PRIORITY_MEDIUM,
                'channel' => Notification::CHANNEL_IN_APP,
                'is_sent' => false,
            ]);
        }

        $this->info('Daily report generated successfully: ' . $fileName);
    }

    /**
     * Collect daily statistics
     */
    private function collectDailyStats($date)
    {
        return [
            // Revenue Statistics
            'total_revenue' => Order::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total'),

            'order_count' => Order::whereDate('created_at', $date)->count(),

            'average_order_value' => Order::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->avg('total'),

            // Order Type Breakdown
            'order_types' => [
                'dine_in' => Order::whereDate('created_at', $date)
                    ->where('order_type', 'dine_in')
                    ->count(),
                'takeaway' => Order::whereDate('created_at', $date)
                    ->where('order_type', 'takeaway')
                    ->count(),
                'delivery' => Order::whereDate('created_at', $date)
                    ->where('order_type', 'delivery')
                    ->count(),
            ],

            // Reservation Statistics
            'reservation_count' => Reservation::whereDate('reservation_date', $date)->count(),
            'confirmed_reservations' => Reservation::whereDate('reservation_date', $date)
                ->where('status', 'confirmed')
                ->count(),

            // Customer Statistics
            'new_customers' => User::whereDate('created_at', $date)
                ->where('role', 'customer')
                ->count(),

            'returning_customers' => $this->getReturningCustomers($date),

            // Inventory Statistics
            'low_stock_items' => Inventory::whereColumn('current_quantity', '<=', 'minimum_quantity')
                ->where('is_active', true)
                ->count(),

            'out_of_stock_items' => Inventory::where('current_quantity', '<=', 0)
                ->where('is_active', true)
                ->count(),

            // Staff Statistics
            'active_staff' => Staff::where('status', Staff::STATUS_ACTIVE)->count(),
            'staff_on_leave' => Staff::where('status', Staff::STATUS_ON_LEAVE)->count(),
        ];
    }

    /**
     * Get returning customers
     */
    private function getReturningCustomers($date)
    {
        $returning = 0;
        $customersToday = User::whereDate('created_at', '<', $date)
            ->where('role', 'customer')
            ->pluck('id');

        $ordersToday = Order::whereDate('created_at', $date)
            ->whereIn('user_id', $customersToday)
            ->get()
            ->groupBy('user_id');

        return $ordersToday->count();
    }

    /**
     * Collect comparison statistics
     */
    private function collectComparisonStats($yesterday)
    {
        $yesterdayRevenue = Order::whereDate('created_at', $yesterday)
            ->where('status', 'completed')
            ->sum('total');

        $todayRevenue = Order::whereDate('created_at', now())
            ->where('status', 'completed')
            ->sum('total');

        if ($yesterdayRevenue == 0) {
            $revenue_growth = 100;
        } else {
            $revenue_growth = (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100;
        }

        return [
            'revenue_growth' => round($revenue_growth, 2),
            'yesterday_revenue' => $yesterdayRevenue,
            'order_count_growth' => $this->calculateGrowth('orders', $yesterday),
            'customer_growth' => $this->calculateGrowth('customers', $yesterday),
        ];
    }

    /**
     * Calculate growth percentage
     */
    private function calculateGrowth($type, $yesterday)
    {
        switch ($type) {
            case 'orders':
                $yesterdayCount = Order::whereDate('created_at', $yesterday)->count();
                $todayCount = Order::whereDate('created_at', now())->count();
                break;
            case 'customers':
                $yesterdayCount = User::whereDate('created_at', $yesterday)
                    ->where('role', 'customer')
                    ->count();
                $todayCount = User::whereDate('created_at', now())
                    ->where('role', 'customer')
                    ->count();
                break;
            default:
                return 0.0;
        }

        if ($yesterdayCount == 0) {
            return 100.0;
        }

        return (float) round((($todayCount - $yesterdayCount) / $yesterdayCount) * 100, 2);
    }

    /**
     * Get top selling items
     */
    private function getTopSellingItems($date)
    {
        return DB::table('order_items')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereDate('orders.created_at', $date)
            ->where('orders.status', 'completed')
            ->select(
                'menu_items.name',
                'menu_items.category',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->groupBy('menu_items.id', 'menu_items.name', 'menu_items.category')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get peak hours
     */
    private function getPeakHours($date)
    {
        $driver = DB::connection()->getDriverName();
        $hourExpression = $driver === 'sqlite'
            ? "CAST(strftime('%H', created_at) as integer)"
            : 'HOUR(created_at)';

        $peakHours = DB::table('orders')
            ->whereDate('created_at', $date)
            ->where('status', 'completed')
            ->select(
                DB::raw($hourExpression . ' as hour'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_revenue')
            )
            ->groupBy(DB::raw($hourExpression))
            ->orderBy('order_count', 'desc')
            ->limit(6)
            ->get();

        return $peakHours;
    }

    /**
     * Get reservation statistics
     */
    private function getReservationStats($date)
    {
        return [
            'by_status' => Reservation::whereDate('reservation_date', $date)
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get(),
            'by_time_slot' => Reservation::whereDate('reservation_date', $date)
                ->select('reservation_time', DB::raw('COUNT(*) as count'))
                ->groupBy('reservation_time')
                ->get(),
            'average_party_size' => Reservation::whereDate('reservation_date', $date)
                ->avg('number_of_people'),
        ];
    }

    /**
     * Get staff performance
     */
    private function getStaffPerformance($date)
    {
        return Staff::whereIn('status', [Staff::STATUS_ACTIVE, Staff::STATUS_ON_LEAVE])
            ->withCount(['assignedOrders as orders_count' => function ($query) use ($date) {
                $query->whereDate('created_at', $date)
                    ->where('status', 'completed');
            }])
            ->orderBy('orders_count', 'desc')
            ->limit(5)
            ->get();
    }
}
