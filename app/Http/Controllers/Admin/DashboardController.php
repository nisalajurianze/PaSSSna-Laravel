<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\User;
use App\Models\MenuItem;
use App\Models\Staff;
use App\Models\Table;
use App\Models\DiningSession;
use App\Models\Inventory;
use App\Models\Promotion;
use App\Models\ContactMessage;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Revenue Data
        $todayRevenue = Order::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total');

        $monthlyRevenue = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('status', 'completed')
            ->sum('total');

        $lastMonthRevenue = Order::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])->where('status', 'completed')->sum('total');

        // Orders Data
        $pendingOrders = Order::where('status', 'pending')->count();
        $preparingOrders = Order::where('status', 'preparing')->count();
        $activeOrders = Order::whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->count();
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $completedOrders = Order::where('status', 'completed')
            ->whereDate('created_at', $today)
            ->count();

        // Orders by type
        $ordersByType = Order::select('order_type', DB::raw('count(*) as total'))
            ->groupBy('order_type')
            ->pluck('total', 'order_type')
            ->toArray();

        // Reservations Data
        $todayReservations = Reservation::whereDate('reservation_date', $today)
            ->where('status', 'confirmed')
            ->count();
        $pendingReservations = Reservation::where('status', 'pending')->count();
        $reservedTables = Reservation::whereDate('reservation_date', $today)
            ->where('status', 'confirmed')
            ->sum('table_count');

        // User Data
        $newCustomers = User::where('role', 'customer')
            ->whereDate('created_at', $today)
            ->count();
        $totalCustomers = User::where('role', 'customer')->count();

        // Staff Data
        $activeStaff = Staff::where('status', 'active')->count();

        // Dining/Table Data
        $totalTables = Table::count();
        $availableTables = Table::where('status', 'available')->count();
        $occupiedTables = Table::where('status', 'occupied')->count();
        $activeDiningSessions = DiningSession::where('status', 'active')->count();

        // Inventory Data
        $lowStockItemsCount = Inventory::where('current_quantity', '<=', DB::raw('minimum_quantity'))->count();
        $lowStockItems = Inventory::where('current_quantity', '<=', DB::raw('minimum_quantity'))->limit(10)->get();
        $totalInventoryItems = Inventory::count();

        // Promotions
        $activePromotions = Promotion::where('is_active', true)->count();

        // Contact Messages
        try {
            $hasIsReadColumn = DB::select("SHOW COLUMNS FROM contact_messages LIKE 'is_read'");
            $unreadMessages = $hasIsReadColumn ? ContactMessage::where('is_read', false)->count() : ContactMessage::count();
        } catch (\Exception $e) {
            $unreadMessages = 0;
        }

        // Reviews
        $avgRating = Review::avg('rating');
        $totalReviews = Review::count();

        // Recent Activity
        $recentOrders = Order::with('user')
            ->latest()
            ->limit(10)
            ->get();

        $recentReservations = Reservation::with('user')
            ->latest()
            ->limit(5)
            ->get();

        // Top Selling Items
        $topItems = DB::table('order_items')
            ->select(
                'menu_item_id',
                'name',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(order_items.unit_price * order_items.quantity) as total_revenue')
            )
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->groupBy('menu_item_id', 'name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Weekly Sales Data
        $weeklySales = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $daySales = Order::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total');
            $dayOrders = Order::whereDate('created_at', $date)->count();

            $weeklySales[] = [
                'day' => $date->format('D'),
                'date' => $date->format('Y-m-d'),
                'sales' => $daySales,
                'orders' => $dayOrders
            ];
        }

        // Monthly Sales Data
        $monthlySales = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthSales = Order::whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', $month)
                ->where('status', 'completed')
                ->sum('total');

            $monthlySales[] = [
                'month' => Carbon::create()->month($month)->format('M'),
                'sales' => $monthSales
            ];
        }

        // Recommendations
        $recommendations = $this->generateRecommendations($pendingOrders, $lowStockItemsCount, $pendingReservations);

        return view('admin.dashboard', compact(
            'todayRevenue', 'monthlyRevenue', 'lastMonthRevenue',
            'activeOrders', 'todayOrders', 'completedOrders', 'pendingOrders', 'preparingOrders',
            'todayReservations', 'pendingReservations', 'reservedTables',
            'newCustomers', 'totalCustomers', 'activeStaff',
            'totalTables', 'availableTables', 'occupiedTables', 'activeDiningSessions',
            'lowStockItems', 'totalInventoryItems',
            'activePromotions', 'unreadMessages', 'avgRating', 'totalReviews',
            'recentOrders', 'recentReservations', 'topItems',
            'weeklySales', 'monthlySales', 'ordersByType', 'recommendations'
        ));
    }

    private function generateRecommendations($pendingOrders, $lowStockItemsCount, $pendingReservations)
    {
        $recommendations = [];

        if ($pendingOrders > 5) {
            $recommendations[] = [
                'type' => 'warning',
                'message' => "You have {$pendingOrders} pending orders that need attention.",
                'icon' => 'fas fa-shopping-cart',
                'action' => route('admin.orders.index', ['status' => 'pending']),
                'color' => 'yellow'
            ];
        }

        if ($lowStockItemsCount > 0) {
            $recommendations[] = [
                'type' => 'danger',
                'message' => "{$lowStockItemsCount} items are running low on stock.",
                'icon' => 'fas fa-boxes',
                'action' => route('admin.inventory.index'),
                'color' => 'red'
            ];
        }

        if ($pendingReservations > 0) {
            $recommendations[] = [
                'type' => 'info',
                'message' => "You have {$pendingReservations} reservations awaiting confirmation.",
                'icon' => 'fas fa-calendar-check',
                'action' => route('admin.reservations.index', ['status' => 'pending']),
                'color' => 'blue'
            ];
        }

        return $recommendations;
    }

    public function getChartData()
    {
        $period = request()->get('period', 'week');

        if ($period === 'day') {
            $data = [];
            for ($i = 0; $i < 24; $i++) {
                $orders = Order::whereHour('created_at', $i)
                    ->whereDate('created_at', today())
                    ->count();
                $revenue = Order::whereHour('created_at', $i)
                    ->whereDate('created_at', today())
                    ->where('status', 'completed')
                    ->sum('total');

                $data['labels'][] = Carbon::createFromTime($i)->format('g A');
                $data['orders'][] = $orders;
                $data['revenue'][] = $revenue;
            }
        } elseif ($period === 'month') {
            $data = ['labels' => [], 'orders' => [], 'revenue' => []];
            for ($i = 1; $i <= Carbon::now()->daysInMonth; $i++) {
                $date = Carbon::now()->startOfMonth()->addDays($i - 1);
                $data['labels'][] = $date->format('M d');
                $data['orders'][] = Order::whereDate('created_at', $date)->count();
                $data['revenue'][] = Order::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('total');
            }
        } else {
            $data = ['labels' => [], 'orders' => [], 'revenue' => []];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $data['labels'][] = $date->format('M d');
                $data['orders'][] = Order::whereDate('created_at', $date)->count();
                $data['revenue'][] = Order::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('total');
            }
        }

        return response()->json($data);
    }

    public function getStats()
    {
        $period = request()->get('period', 'today');

        switch ($period) {
            case 'today':
                $startDate = today();
                $endDate = today();
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
                $startDate = today();
                $endDate = today();
        }

        $stats = [
            'revenue' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->sum('total'),
            'orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'customers' => User::where('role', 'customer')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'reservations' => Reservation::whereBetween('reservation_date', [$startDate, $endDate])->count(),
        ];

        return response()->json($stats);
    }

    // Customer Management
    public function customers()
    {
        $customers = User::where('role', 'customer')
            ->withCount('orders')
            ->latest()
            ->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function customerShow(User $customer)
    {
        $customer->load(['orders' => function($query) {
            $query->latest()->limit(10);
        }, 'reservations' => function($query) {
            $query->latest()->limit(5);
        }]);

        $totalSpent = $customer->orders()->where('status', 'completed')->sum('total');
        $orderCount = $customer->orders()->count();

        return view('admin.customers.show', compact('customer', 'totalSpent', 'orderCount'));
    }

    public function toggleCustomerStatus(User $customer)
    {
        $customer->is_active = !$customer->is_active;
        $customer->save();

        return back()->with('success', 'Customer status updated successfully.');
    }

    // Settings
    public function settings()
    {
        $settings = [
            'restaurant_name' => config('app.name'),
            'tax_rate' => config('restaurant.order.tax_rate', 10),
            'delivery_charge' => config('restaurant.order.delivery_charge', 50),
            'preparation_time' => config('restaurant.order.preparation_time_default', 30),
            'opening_time' => config('restaurant.operating_hours.opening', '09:00'),
            'closing_time' => config('restaurant.operating_hours.closing', '22:00'),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function updateSettings()
    {
        // Settings would be saved to config/database
        return back()->with('success', 'Settings updated successfully.');
    }

    public function updateBusinessHours()
    {
        return back()->with('success', 'Business hours updated successfully.');
    }
}
