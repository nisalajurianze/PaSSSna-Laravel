@extends('layouts.admin')

@section('title', 'Reports')
@section('header', 'Reports & Analytics')

@section('content')
<div class="space-y-6">
    <!-- Period Selector -->
    <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
        <form class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Period</label>
                <select name="period" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>This Year</option>
                </select>
            </div>
            <div>
                <span class="text-sm text-gray-500">
                    {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                </span>
            </div>
        </form>
    </div>

    <!-- Revenue Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Revenue</p>
                    <p class="text-3xl font-bold text-gray-800">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Orders</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $orderCount }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Avg Order Value</p>
                    <p class="text-3xl font-bold text-gray-800">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($avgOrderValue, 2) }}</p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">New Customers</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $newCustomers }}</p>
                </div>
                <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-orange-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Daily Sales Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Daily Sales</h3>
            <div class="h-80 flex items-center justify-center bg-gray-50 rounded-lg">
                <div class="text-center text-gray-500">
                    <i class="fas fa-chart-bar text-4xl mb-2"></i>
                    <p>Sales Chart</p>
                    <p class="text-sm">Daily revenue for selected period</p>
                </div>
            </div>
        </div>

        <!-- Orders by Type -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Orders by Type</h3>
            <div class="space-y-4">
                @forelse($ordersByType as $orderType)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="capitalize">{{ $orderType->order_type ?? 'Unknown' }}</span>
                            <span class="font-medium">{{ $orderType->count }} orders</span>
                        </div>
                        <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-red-600 rounded-full" style="width: {{ ($orderType->count / max($orderCount, 1)) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">No data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top Items & Reservations -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Selling Items -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Selling Items</h3>
            <div class="space-y-3">
                @forelse($topItems as $index => $item)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-sm font-medium">
                                {{ $index + 1 }}
                            </span>
                            <span class="font-medium text-gray-800">{{ $item->item_name }}</span>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-800">{{ $item->total_sold }} sold</p>
                            <p class="text-sm text-gray-500">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->total_revenue, 2) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">No data available</p>
                @endforelse
            </div>
        </div>

        <!-- Reservation Stats -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Reservations</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg text-center">
                    <p class="text-3xl font-bold text-blue-800">{{ $totalReservations }}</p>
                    <p class="text-sm text-blue-600">Total</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg text-center">
                    <p class="text-3xl font-bold text-green-800">{{ $confirmedReservations }}</p>
                    <p class="text-sm text-green-600">Confirmed</p>
                </div>
            </div>

            <div class="mt-6">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Peak Hours</h4>
                <div class="space-y-2">
                    @forelse($peakHours as $hour)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">{{ sprintf('%02d:00', $hour->hour) }}</span>
                            <span class="font-medium">{{ $hour->count }} orders</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No peak hour data</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Satisfaction -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Satisfaction</h3>
        <div class="flex items-center gap-6">
            <div class="text-center">
                <div class="text-4xl font-bold text-gray-800">{{ number_format($avgRating, 1) }}</div>
                <div class="flex items-center gap-1 mt-1">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= round($avgRating) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                    @endfor
                </div>
                <p class="text-sm text-gray-500 mt-1">{{ $totalReviews }} reviews</p>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Export Reports</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.reports.export', ['type' => 'sales', 'period' => request('period', 'month')]) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-file-csv mr-2"></i>Export Sales CSV
            </a>
            <a href="{{ route('admin.reports.generate', ['type' => 'inventory', 'period' => request('period', 'month')]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-boxes mr-2"></i>Inventory Report
            </a>
            <a href="{{ route('admin.reports.generate', ['type' => 'staff', 'period' => request('period', 'month')]) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-users mr-2"></i>Staff Report
            </a>
        </div>
    </div>
</div>
@endsection

