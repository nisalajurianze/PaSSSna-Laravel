@extends('layouts.app')

@section('title', 'Customer Dashboard - PaSSSna Restaurant')

@section('styles')
<style>
    .dashboard-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 1px solid #e9ecef;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        background: linear-gradient(45deg, #DC2626, #FBBF24);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .pulse-animation {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-yellow-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Welcome Section -->
        <div class="mb-8 animate-slide-down">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('PASSSNA.png') }}" alt="PaSSSna Logo" class="h-24 w-auto hidden sm:block">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">
                            Welcome back, {{ Auth::user()->name }}!
                        </h1>
                        <p class="text-gray-600">Manage your orders, reservations, and dining experience</p>
                    </div>
                </div>
                <div class="mt-4 md:mt-0 flex items-center space-x-4">
                    <!-- Live Updates Indicator -->
                    <div class="flex items-center text-green-600 text-sm">
                        <span class="relative flex h-3 w-3 mr-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        Live Updates
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Table Status</p>
                        <p class="font-semibold text-primary-red">
                            @if($activeDiningSession)
                                Table #{{ $activeDiningSession->table_number }}
                                <span class="text-green-600 ml-2">● Active</span>
                            @else
                                <span class="text-gray-500">Not in dining</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Active Orders -->
            <div class="dashboard-card p-6 animate-fade-in" style="animation-delay: 0.1s">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-primary-red text-xl"></i>
                    </div>
                    <span class="bg-red-100 text-primary-red px-3 py-1 rounded-full text-sm font-semibold">
                        {{ $activeOrdersCount }}
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Active Orders</h3>
                <p class="text-gray-600 text-sm mb-4">Orders being prepared or delivered</p>
                <a href="{{ route('customer.orders') }}" class="text-primary-red hover:text-red-700 font-medium inline-flex items-center">
                    View Details <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>

            <!-- Reservations -->
            <div class="dashboard-card p-6 animate-fade-in" style="animation-delay: 0.2s">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-primary-yellow text-xl"></i>
                    </div>
                    <span class="bg-yellow-100 text-primary-yellow px-3 py-1 rounded-full text-sm font-semibold">
                        {{ $upcomingReservationsCount }}
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Upcoming Reservations</h3>
                <p class="text-gray-600 text-sm mb-4">Your booked tables</p>
                <a href="{{ route('customer.reservations') }}" class="text-primary-yellow hover:text-yellow-700 font-medium inline-flex items-center">
                    View Details <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>

            <!-- Loyalty Points -->
            <div class="dashboard-card p-6 animate-fade-in" style="animation-delay: 0.3s">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-crown text-navy-blue text-xl"></i>
                    </div>
                    <span class="bg-blue-100 text-navy-blue px-3 py-1 rounded-full text-sm font-semibold">
                        {{ Auth::user()->loyalty_points }}
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Loyalty Points</h3>
                <p class="text-gray-600 text-sm mb-4">Earn rewards on every order</p>
                <a href="{{ route('customer.loyalty') }}" class="text-navy-blue hover:text-blue-800 font-medium inline-flex items-center">
                    Redeem <i class="fas fa-gift ml-2"></i>
                </a>
            </div>

            <!-- Total Spent -->
            <div class="dashboard-card p-6 animate-fade-in" style="animation-delay: 0.4s">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-wallet text-green-600 text-xl"></i>
                    </div>
                    <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm font-semibold">
                        {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($totalSpent, 2) }}
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Total Spent</h3>
                <p class="text-gray-600 text-sm mb-4">All-time spending</p>
                <a href="{{ route('customer.total-spent') }}" class="text-green-600 hover:text-green-800 font-medium inline-flex items-center">
                    View History <i class="fas fa-chart-line ml-2"></i>
                </a>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <a href="{{ route('menu') }}"
               class="bg-gradient-to-r from-primary-red to-red-600 text-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex flex-col items-center justify-center group">
                <i class="fas fa-utensils text-3xl mb-3 group-hover:rotate-12 transition-transform duration-300"></i>
                <h3 class="text-xl font-bold mb-2">Order Food</h3>
                <p class="text-red-100 text-center">Browse our delicious menu</p>
            </a>

            <a href="{{ route('reservation.create') }}"
               class="bg-gradient-to-r from-primary-yellow to-yellow-600 text-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex flex-col items-center justify-center group">
                <i class="fas fa-calendar-check text-3xl mb-3 group-hover:rotate-12 transition-transform duration-300"></i>
                <h3 class="text-xl font-bold mb-2">Book Table</h3>
                <p class="text-yellow-800 text-center">Reserve your dining experience</p>
            </a>


            <a href="{{ route('contact') }}"
               class="bg-gradient-to-r from-navy-blue to-blue-800 text-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex flex-col items-center justify-center group">
                <i class="fas fa-headset text-3xl mb-3 group-hover:rotate-12 transition-transform duration-300"></i>
                <h3 class="text-xl font-bold mb-2">Need Help?</h3>
                <p class="text-blue-200 text-center">Contact us for assistance</p>
            </a>
        </div>

        <!-- Recent Orders -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Orders -->
            <div class="bg-white rounded-xl shadow-lg p-6 animate-slide-up">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Recent Orders</h2>
                    <a href="{{ route('customer.orders') }}" class="text-primary-red hover:text-red-700 font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($recentOrders as $order)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-red transition duration-300">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-gray-800">Order #{{ $order->order_number }}</h4>
                                <p class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y - h:i A') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-gray-800">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->total, 2) }}</span>
                                <div class="mt-1">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        @if($order->status == 'completed') bg-green-100 text-green-800
                                        @elseif($order->status == 'preparing') bg-yellow-100 text-yellow-800
                                        @elseif($order->status == 'pending') bg-blue-100 text-blue-800
                                        @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-{{ $order->order_type == 'delivery' ? 'truck' : (($order->order_type == 'dine_in') ? 'chair' : 'takeaway') }} mr-2"></i>
                                {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}
                                @if($order->order_type == 'dine_in' && $order->table_number)
                                    - Table #{{ $order->table_number }}
                                @endif
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-shopping-bag text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">No orders yet</p>
                        <a href="{{ route('menu') }}" class="text-primary-red hover:text-red-700 font-medium mt-2 inline-block">
                            Start Ordering <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Notifications Section -->
            @php
                $notifications = \App\Models\Notification::where('user_id', Auth::id())->latest()->take(5)->get();
                $unreadCount = \App\Models\Notification::where('user_id', Auth::id())->unread()->count();
            @endphp
            <div class="bg-white rounded-xl shadow-lg p-6 animate-slide-up" id="notifications">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        Notifications
                        @if($unreadCount > 0)
                            <span class="bg-primary-red text-white text-sm px-2 py-1 rounded-full ml-2">{{ $unreadCount }}</span>
                        @endif
                    </h2>
                    @if($unreadCount > 0)
                        <form action="{{ route('customer.notifications.read-all') }}" method="POST" onsubmit="return confirm('Mark all notifications as read?');" style="display: inline;">
                            @csrf
                            <button type="submit" class="text-primary-red hover:text-red-700 font-medium text-sm" style="background: none; border: none; cursor: pointer;">
                                <i class="fas fa-check-double mr-1"></i>Mark all as read
                            </button>
                        </form>
                    @endif
                </div>

                <div class="space-y-3">
                    @forelse($notifications as $notification)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-red transition duration-300 {{ $notification->is_read ? 'bg-white' : 'bg-blue-50' }}">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3 {{ $notification->is_read ? 'bg-gray-100' : 'bg-primary-red' }}">
                                <i class="fas {{ $notification->is_read ? 'fa-bell text-gray-500' : 'fa-bell text-white' }}"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800">{{ $notification->title }}</h4>
                                <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                <p class="text-xs text-gray-500 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            @if(!$notification->is_read)
                                <form action="{{ route('customer.notifications.read') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="notification_id" value="{{ $notification->id }}">
                                    <button type="submit" class="text-primary-red hover:text-red-700 text-sm" style="background: none; border: none; cursor: pointer;">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-bell-slash text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">No notifications yet</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Reservations -->
            <div class="space-y-8">
                <!-- Upcoming Reservations -->
                <div class="bg-white rounded-xl shadow-lg p-6 animate-slide-up" style="animation-delay: 0.1s">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Upcoming Reservations</h2>
                    <a href="{{ route('customer.reservations') }}" class="text-primary-red hover:text-red-700 font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($upcomingReservations as $reservation)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-yellow transition duration-300">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-gray-800">Table #{{ $reservation->table_number }}</h4>
                                <p class="text-sm text-gray-500">
                                    {{ $reservation->reservation_date->format('M d, Y') }} at {{ $reservation->reservation_time }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($reservation->status == 'confirmed') bg-green-100 text-green-800
                                    @elseif($reservation->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($reservation->status == 'cancelled') bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                                <p class="text-sm text-gray-600 mt-1">{{ $reservation->number_of_guests }} guests</p>
                            </div>
                        </div>
                        @if($reservation->special_requests)
                        <div class="mt-3 p-2 bg-gray-50 rounded">
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-sticky-note mr-1"></i>
                                {{ Str::limit($reservation->special_requests, 80) }}
                            </p>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">No upcoming reservations</p>
                        <a href="{{ route('reservation.create') }}" class="text-primary-yellow hover:text-yellow-700 font-medium mt-2 inline-block">
                            Book Now <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recommended Items -->
        @if(count($recommendedItems) > 0)
        <div class="mt-8 animate-fade-in" style="animation-delay: 0.3s">
            <div class="bg-white rounded-2xl p-6 shadow-xl border border-amber-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Recommended For You</h2>
                        <p class="text-sm text-gray-500">Hand-picked dishes to match your taste</p>
                    </div>
                    <div class="hidden md:flex items-center space-x-2 text-sm text-amber-700 bg-amber-50 px-3 py-1 rounded-full">
                        <i class="fas fa-fire"></i>
                        <span>Trending now</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($recommendedItems as $item)
                    <div class="group rounded-2xl overflow-hidden border border-amber-100 bg-gradient-to-br from-white to-amber-50/70 shadow-sm hover:shadow-lg transition duration-300">
                        <div class="relative h-44 bg-gray-100">
                            <img src="{{ $item->image_url }}"
                                 alt="{{ $item->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute top-3 left-3 bg-white/90 text-primary-red text-xs font-semibold px-3 py-1 rounded-full shadow">
                                Chef Pick
                            </div>
                            <div class="absolute bottom-3 right-3 bg-gray-900/80 text-white text-sm font-bold px-3 py-1 rounded-full">
                                @if($item->offer_price)
                                    {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->offer_price, 2) }}
                                @else
                                    {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->price, 2) }}
                                @endif
                            </div>
                        </div>

                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <h4 class="text-lg font-semibold text-gray-800 leading-tight">{{ $item->name }}</h4>
                                <div class="flex items-center text-yellow-400 text-xs">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= 4 ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                            </div>

                            <p class="text-sm text-gray-600 mb-4">
                                {{ \Illuminate\Support\Str::limit($item->short_description ?? $item->description, 80) }}
                            </p>

                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-500">
                                    @if($item->offer_price)
                                        <span class="line-through mr-2">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->price, 2) }}</span>
                                        <span class="text-primary-red font-semibold">Save {{ $item->getDiscountPercentage() }}%</span>
                                    @else
                                        <span>Best value</span>
                                    @endif
                                </div>
                                <button onclick="addToCart({{ $item->id }})"
                                        class="bg-gradient-to-r from-primary-red to-red-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:from-red-700 hover:to-red-800 transition duration-300 shadow-sm">
                                    <i class="fas fa-plus mr-1"></i>Add
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
    function addToCart(itemId) {
        fetch('{{ route("cart.add", [], false) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                item_id: itemId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Added to Cart!',
                    text: 'Item added to your cart successfully',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }

    function markAsRead(notificationId) {
        fetch('{{ route("customer.notifications.read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                notification_id: notificationId
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
            alert('Error: ' + error.message);
        });
    }

    function markAllAsRead() {
        console.log('Mark all as read clicked');
        if (!confirm('Mark all notifications as read?')) {
            return;
        }
        console.log('Proceeding to mark all as read...');
        fetch('{{ route("customer.notifications.read-all") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if(data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
            alert('Error: ' + error.message);
        });
    }

    // Real-time notification polling for dashboard
    // lastNotificationCount is already declared in the layout
    lastNotificationCount = {{ \App\Models\Notification::where('user_id', Auth::id())->unread()->count() }};

    function checkDashboardNotifications() {
        fetch('{{ route("customer.notifications.check") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.unread_count !== lastNotificationCount) {
                    // New notifications detected, refresh the page
                    lastNotificationCount = data.unread_count;
                    showToast('New notification received!', 'info');
                    // Refresh after a short delay to show the toast
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            });
    }

    // Start polling every 3 seconds when on dashboard
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(checkDashboardNotifications, 3000);
        setInterval(checkDashboardNotifications, 3000);
    });
</script>
@endsection

