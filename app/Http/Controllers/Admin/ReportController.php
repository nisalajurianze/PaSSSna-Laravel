<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Staff;
use App\Models\OrderItem;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $period = $request->get('period', 'month');

        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
        }

        // Revenue Metrics
        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('total');

        $orderCount = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $avgOrderValue = $orderCount > 0 ? $totalRevenue / $orderCount : 0;

        // Orders by Type
        $ordersByType = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('order_type', DB::raw('count(*) as count'), DB::raw('sum(total) as revenue'))
            ->groupBy('order_type')
            ->get();

        // Daily Sales Trend
        $dailySales = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dayRevenue = Order::whereDate('created_at', $current)
                ->where('status', 'completed')
                ->sum('total');
            $dayOrders = Order::whereDate('created_at', $current)->count();

            $dailySales[] = [
                'date' => $current->format('Y-m-d'),
                'day' => $current->format('D'),
                'revenue' => $dayRevenue,
                'orders' => $dayOrders
            ];
            $current->addDay();
        }

        // Top Selling Items
        $topItems = OrderItem::whereHas('order', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed');
            })
            ->select('menu_item_id', 'item_name', DB::raw('sum(quantity) as total_sold'), DB::raw('sum(total_price) as total_revenue'))
            ->groupBy('menu_item_id', 'item_name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        // Customer Metrics
        $newCustomers = User::where('role', 'customer')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $totalCustomers = User::where('role', 'customer')->count();

        // Reservation Metrics
        $totalReservations = Reservation::whereBetween('reservation_date', [$startDate, $endDate])->count();
        $confirmedReservations = Reservation::whereBetween('reservation_date', [$startDate, $endDate])
            ->where('status', 'confirmed')
            ->count();

        // Peak Hours
        $peakHours = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('hour(created_at) as hour'), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Customer Satisfaction
        $avgRating = Review::avg('rating');
        $totalReviews = Review::count();

        return view('admin.reports.index', compact(
            'period', 'startDate', 'endDate',
            'totalRevenue', 'orderCount', 'avgOrderValue',
            'ordersByType', 'dailySales', 'topItems',
            'newCustomers', 'totalCustomers',
            'totalReservations', 'confirmedReservations',
            'peakHours', 'avgRating', 'totalReviews'
        ));
    }

    public function salesReport(Request $request)
    {
        return $this->generateReport($request, 'sales');
    }

    public function inventoryReport(Request $request)
    {
        $inventory = Inventory::all();

        $totalValue = Inventory::sum(DB::raw('current_quantity * unit_cost'));
        $lowStock = Inventory::whereRaw('current_quantity <= minimum_quantity')->count();
        $outOfStock = Inventory::where('current_quantity', 0)->count();

        return view('admin.reports.inventory', compact('inventory', 'totalValue', 'lowStock', 'outOfStock'));
    }

    public function staffReport(Request $request)
    {
        $staff = Staff::all();

        $activeStaff = Staff::where('status', 'active')->count();
        $onLeave = Staff::where('status', 'on_leave')->count();

        // Staff by role
        $staffByRole = Staff::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        return view('admin.reports.staff', compact('staff', 'activeStaff', 'onLeave', 'staffByRole'));
    }

    public function generatePDF(Request $request)
    {
        $type = $request->get('type', 'sales');
        $period = $request->get('period', 'month');

        // Get report data based on type
        $data = $this->getReportData($type, $period);

        $pdf = Pdf::loadView("admin.reports.pdf.{$type}", $data);

        return $pdf->download("{$type}-report-{$period}.pdf");
    }

    public function generate(Request $request)
    {
        $type = $request->get('type', 'sales');
        $period = $request->get('period', 'month');

        return $this->generateReport($request, $type);
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'sales');
        $period = $request->get('period', 'month');

        $data = $this->getReportData($type, $period);

        $csv = $this->generateCSV($type, $data);

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$type}-report-{$period}.csv\"");
    }

    private function generateReport(Request $request, $type)
    {
        return $this->index($request);
    }

    private function getReportData($type, $period)
    {
        // Similar to index method - return data for PDF generation
        return [];
    }

    private function generateCSV($type, $data)
    {
        $csv = "";

        switch ($type) {
            case 'sales':
                $csv = "Date,Orders,Revenue,Average\n";
                foreach ($data['dailySales'] ?? [] as $day) {
                    $csv .= "{$day['date']},{$day['orders']},{$day['revenue']}," . ($day['orders'] > 0 ? $day['revenue'] / $day['orders'] : 0) . "\n";
                }
                break;
            case 'inventory':
                $csv = "Name,Category,Quantity,Unit,Value\n";
                foreach ($data['inventory'] ?? [] as $item) {
                    $value = $item->current_quantity * $item->unit_price;
                    $csv .= "{$item->name},{$item->category},{$item->current_quantity},{$item->unit},{$value}\n";
                }
                break;
            case 'staff':
                $csv = "Name,Role,Status,Salary,Hire Date\n";
                foreach ($data['staff'] ?? [] as $member) {
                    $csv .= "{$member->first_name} {$member->last_name},{$member->role},{$member->status},{$member->salary},{$member->hire_date}\n";
                }
                break;
        }

        return $csv;
    }
}
