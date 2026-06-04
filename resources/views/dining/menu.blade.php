@extends('layouts.app', ['kiosk' => true])

@section('title', 'Dining Menu')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-[#F8F3ED] via-white to-[#F3EEE7]">
    <div class="px-4 py-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-500">Dining Session</p>
                <h1 class="text-2xl font-bold text-gray-800">Table #{{ $session->table_number }}</h1>
                <p class="text-xs text-gray-500">Session {{ $session->session_code }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('dining.custom') }}" class="px-4 py-2 rounded-xl bg-primary-yellow text-gray-800 font-semibold hover:bg-yellow-400 transition">
                    <i class="fas fa-magic mr-2"></i>Build Custom Meal
                </a>
                <button type="button" onclick="openAdminModal()" class="px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
                    <i class="fas fa-door-closed mr-2"></i>Close Table (Admin)
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <form class="flex flex-wrap gap-3 items-center" method="GET" action="{{ route('dining.menu') }}">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search dishes..."
                               class="flex-1 px-4 py-2 rounded-xl border border-gray-200 focus:border-primary-red focus:ring-2 focus:ring-primary-red/20">
                        <select name="category" class="px-4 py-2 rounded-xl border border-gray-200">
                            <option value="all">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $category)) }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-primary-red text-white hover:bg-red-700 transition">
                            Filter
                        </button>
                    </form>
                </div>

                @if($recommendations->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-800">Smart Recommendations</h2>
                            <span class="text-xs text-gray-500">Updated live</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($recommendations as $item)
                                <div class="flex items-center gap-4 bg-gray-50 rounded-xl p-3">
                                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-16 h-16 rounded-lg object-cover">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-800">{{ $item->name }}</p>
                                        <p class="text-sm text-gray-500">{{ Str::limit($item->description, 60) }}</p>
                                        <div class="flex items-center justify-between mt-2">
                                            <span class="text-primary-red font-bold">
                                                {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->current_price ?? $item->price, 2) }}
                                            </span>
                                            <form method="POST" action="{{ route('dining.cart.add') }}">
                                                @csrf
                                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button class="px-3 py-1.5 rounded-lg bg-primary-red text-white text-sm hover:bg-red-700 transition">
                                                    Add
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @forelse($menuItems as $item)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="h-40 w-full object-cover">
                            <div class="p-4">
                                <div class="flex items-start justify-between">
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $item->name }}</h3>
                                    <span class="text-primary-red font-bold text-lg">
                                        {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->current_price ?? $item->price, 2) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mt-2">{{ Str::limit($item->description, 80) }}</p>
                                <div class="flex items-center justify-between mt-4">
                                    <span class="text-xs text-gray-400 capitalize">{{ str_replace('_', ' ', $item->category) }}</span>
                                    <form method="POST" action="{{ route('dining.cart.add') }}">
                                        @csrf
                                        <input type="hidden" name="item_id" value="{{ $item->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button class="px-3 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800 transition">
                                            Add to Order
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center text-gray-500">
                            No items found. Try a different search or category.
                        </div>
                    @endforelse
                </div>

                @if($menuItems->hasPages())
                    <div class="mt-4">
                        {{ $menuItems->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sticky top-6 space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Table Order</h2>

                        @if(count($cart) > 0)
                            <div class="space-y-4">
                                @foreach($cart as $key => $item)
                                    <div class="border border-gray-100 rounded-xl p-3">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <p class="font-semibold text-gray-800">{{ $item['name'] }}</p>
                                                <p class="text-xs text-gray-500">{{ $item['description'] ?? '' }}</p>
                                            </div>
                                            <form method="POST" action="{{ route('dining.cart.remove', $key) }}">
                                                @csrf
                                                <button class="text-gray-400 hover:text-red-600">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="flex items-center justify-between mt-3">
                                            <form method="POST" action="{{ route('dining.cart.update', $key) }}" class="flex items-center gap-2">
                                                @csrf
                                                <input type="number" name="quantity" min="1" max="10" value="{{ $item['quantity'] }}" class="w-16 px-2 py-1 border border-gray-200 rounded-lg text-sm">
                                                <button class="text-xs px-2 py-1 rounded-lg bg-gray-100 hover:bg-gray-200">Update</button>
                                            </form>
                                            <span class="font-semibold text-gray-700">
                                                {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item['total'], 2) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="border-t border-gray-100 mt-4 pt-4 space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Subtotal</span>
                                    <span class="font-semibold text-gray-700">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Tax</span>
                                    <span class="font-semibold text-gray-700">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($tax, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Service Charge</span>
                                    <span class="font-semibold text-gray-700">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($serviceCharge, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-base font-semibold">
                                    <span>Total</span>
                                    <span>{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($total, 2) }}</span>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('dining.order.place') }}" class="mt-4 space-y-3">
                                @csrf
                                <textarea name="special_instructions" rows="2" placeholder="Special instructions (optional)"
                                          class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"></textarea>
                                <button type="submit" class="w-full py-2.5 rounded-xl bg-primary-red text-white font-semibold hover:bg-red-700 transition">
                                    Place Order
                                </button>
                            </form>

                            <form method="POST" action="{{ route('dining.cart.clear') }}" class="mt-2">
                                @csrf
                                <button type="submit" class="w-full py-2 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition text-sm">
                                    Clear Order
                                </button>
                            </form>
                        @else
                            <p class="text-sm text-gray-500">No items in the table order yet.</p>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-base font-semibold text-gray-800 mb-3">Placed Orders</h3>
                        @if($orders->count() > 0)
                            <div class="space-y-3">
                                @foreach($orders as $order)
                                    <div class="border border-gray-100 rounded-xl p-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-800">{{ $order->order_number }}</p>
                                                <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, H:i') }}</p>
                                            </div>
                                            <span class="text-xs font-semibold px-2 py-1 rounded-full
                                                @switch($order->status)
                                                    @case('pending') bg-yellow-100 text-yellow-700 @break
                                                    @case('confirmed') bg-blue-100 text-blue-700 @break
                                                    @case('preparing') bg-orange-100 text-orange-700 @break
                                                    @case('ready') bg-green-100 text-green-700 @break
                                                    @case('served') bg-purple-100 text-purple-700 @break
                                                    @case('completed') bg-gray-100 text-gray-700 @break
                                                    @case('cancelled') bg-red-100 text-red-700 @break
                                                @endswitch
                                            ">
                                                {{ $order->status_text }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between mt-2 text-xs text-gray-500">
                                            <span>{{ $order->items->count() }} items</span>
                                            <span class="font-semibold text-gray-700">
                                                {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->total, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No orders placed yet for this table.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="adminModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Close Table</h3>
        <p class="text-sm text-gray-500 mb-4">Admin password required to end the dining session.</p>
        <form method="POST" action="{{ route('dining.close') }}" class="space-y-4">
            @csrf
            <input type="password" name="admin_password" required placeholder="Admin password"
                   class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:border-primary-red focus:ring-2 focus:ring-primary-red/20">
            <div class="flex items-center justify-end gap-2">
                <button type="button" onclick="closeAdminModal()" class="px-4 py-2 rounded-xl bg-gray-100 text-gray-600">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-gray-900 text-white">Close Table</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openAdminModal() {
        document.getElementById('adminModal').classList.remove('hidden');
        document.getElementById('adminModal').classList.add('flex');
    }

    function closeAdminModal() {
        document.getElementById('adminModal').classList.add('hidden');
        document.getElementById('adminModal').classList.remove('flex');
    }

    let lastCheck = new Date().toISOString();
    setInterval(() => {
        fetch('{{ route('dining.updates') }}?last_check=' + encodeURIComponent(lastCheck))
            .then(response => response.json())
            .then(data => {
                if (!data) return;
                if (data.success === false) {
                    window.location.href = '{{ route('dining.login') }}';
                    return;
                }
                lastCheck = data.timestamp || lastCheck;
                if (data.menu_updated || data.recommendations_updated || data.stock_updated) {
                    showToast('Menu or stock updated. Refreshing...', 'info');
                    setTimeout(() => window.location.reload(), 1200);
                }
            })
            .catch(() => {});
    }, 15000);
</script>
@endsection
