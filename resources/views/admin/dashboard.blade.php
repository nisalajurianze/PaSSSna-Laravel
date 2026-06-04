@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Revenue Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Today's Revenue</p>
                    <p class="text-2xl font-bold text-gray-800">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($stats['todayRevenue'] ?? 0, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600">
                    <i class="fas fa-arrow-up"></i> {{ $stats['revenueGrowth'] ?? 0 }}%
                </span>
                <span class="text-gray-500 ml-2">vs last week</span>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Today's Orders</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['todayOrders'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-blue-600">
                    <i class="fas fa-clock"></i> {{ $stats['pendingOrders'] ?? 0 }} pending
                </span>
            </div>
        </div>

        <!-- Reservations Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Today's Reservations</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['todayReservations'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-check text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-purple-600">
                    <i class="fas fa-users"></i> {{ $stats['upcomingReservations'] ?? 0 }} upcoming
                </span>
            </div>
        </div>

        <!-- Tables Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Available Tables</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['availableTables'] ?? 0 }} / {{ $stats['totalTables'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-chair text-orange-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-orange-600">
                    <i class="fas fa-user-check"></i> {{ $stats['occupiedTables'] ?? 0 }} occupied
                </span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Weekly Sales Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Weekly Sales</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <div class="text-center text-gray-500">
                    <i class="fas fa-chart-line text-4xl mb-2"></i>
                    <p>Sales Chart Placeholder</p>
                    <p class="text-sm">Chart.js would render here</p>
                </div>
            </div>
        </div>

        <!-- Orders by Type -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Orders by Type</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <div class="text-center text-gray-500">
                    <i class="fas fa-chart-pie text-4xl mb-2"></i>
                    <p>Orders by Type Chart</p>
                    <p class="text-sm">Chart.js would render here</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Orders -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Recent Orders</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-red-600 hover:underline">View All</a>
            </div>
            <div class="p-4 space-y-4 max-h-80 overflow-y-auto">
                @forelse($recentOrders ?? [] as $order)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">Order #{{ $order->id }}</p>
                            <p class="text-sm text-gray-500">{{ $order->user->name ?? 'Guest' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-800">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->total, 2) }}</p>
                            <span class="text-xs px-2 py-1 rounded-full
                                @switch($order->status)
                                    @case('pending') bg-yellow-100 text-yellow-700 @break
                                    @case('confirmed') bg-blue-100 text-blue-700 @break
                                    @case('preparing') bg-orange-100 text-orange-700 @break
                                    @case('ready') bg-green-100 text-green-700 @break
                                    @case('completed') bg-gray-100 text-gray-700 @break
                                    @default bg-red-100 text-red-700
                                @endswitch
                            ">{{ ucfirst($order->status) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No recent orders</p>
                @endforelse
            </div>
        </div>

        <!-- Upcoming Reservations -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Upcoming Reservations</h3>
                <a href="{{ route('admin.reservations.index') }}" class="text-sm text-red-600 hover:underline">View All</a>
            </div>
            <div class="p-4 space-y-4 max-h-80 overflow-y-auto">
                @forelse($upcomingReservations ?? [] as $reservation)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">{{ $reservation->user->name ?? $reservation->guest_name }}</p>
                            <p class="text-sm text-gray-500">{{ $reservation->guests }} guests - Table {{ $reservation->table->table_number ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('M d') }}</p>
                            <p class="text-sm text-gray-500">{{ $reservation->reservation_time }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No upcoming reservations</p>
                @endforelse
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Low Stock Alerts</h3>
                <a href="{{ route('admin.inventory.index') }}" class="text-sm text-red-600 hover:underline">View All</a>
            </div>
            <div class="p-4 space-y-4 max-h-80 overflow-y-auto">
                @forelse($lowStockItems ?? [] as $item)
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">{{ $item->name }}</p>
                            <p class="text-sm text-gray-500">{{ $item->category }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-red-600">{{ $item->current_quantity }} {{ $item->unit }}</p>
                            <p class="text-xs text-gray-500">Min: {{ $item->minimum_quantity }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">All items in stock</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.orders.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>New Order
            </a>
            <a href="{{ route('admin.reservations.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-calendar-plus mr-2"></i>New Reservation
            </a>
            <a href="{{ route('admin.menu.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-utensils mr-2"></i>Add Menu Item
            </a>
            <a href="{{ route('admin.promotions.create') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                <i class="fas fa-tag mr-2"></i>Create Promotion
            </a>
            <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                <i class="fas fa-download mr-2"></i>Export Report
            </a>
        </div>
    </div>
</div>
@endsection

