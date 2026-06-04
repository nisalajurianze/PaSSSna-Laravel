@extends('layouts.admin')

@section('title', 'Orders')
@section('header', 'Order Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">Orders List</h2>
        <a href="{{ route('admin.orders.create') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
            <i class="fas fa-plus mr-2"></i>New Order
        </a>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
        <form class="flex flex-wrap gap-4 items-end">
            <div class="w-full md:w-auto">
                <label class="block text-sm text-gray-600 mb-1">Search</label>
                <input type="text" name="search" placeholder="Order ID or customer..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    value="{{ request('search') }}">
            </div>
            <div class="w-full md:w-auto">
                <label class="block text-sm text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>Preparing</option>
                    <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Ready</option>
                    <option value="served" {{ request('status') == 'served' ? 'selected' : '' }}>Served</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="w-full md:w-auto">
                <label class="block text-sm text-gray-600 mb-1">Order Type</label>
                <select name="order_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Types</option>
                    <option value="dine_in" {{ request('order_type') == 'dine_in' ? 'selected' : '' }}>Dine In</option>
                    <option value="takeaway" {{ request('order_type') == 'takeaway' ? 'selected' : '' }}>Takeaway</option>
                    <option value="delivery" {{ request('order_type') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                </select>
            </div>
            <div class="w-full md:w-auto">
                <label class="block text-sm text-gray-600 mb-1">Date</label>
                <input type="date" name="date"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    value="{{ request('date') }}">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
        @php $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'served', 'completed', 'cancelled']; @endphp
        @foreach($statuses as $status)
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 text-center cursor-pointer hover:shadow-md transition"
                 onclick="filterByStatus('{{ $status }}')">
                <p class="text-2xl font-bold text-gray-800">{{ $orderStats[$status] ?? 0 }}</p>
                <p class="text-sm text-gray-500 capitalize">{{ $status }}</p>
            </div>
        @endforeach
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-gray-800">#{{ $order->id }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-800">{{ $order->user->name ?? $order->guest_name ?? 'Guest' }}</p>
                            @if($order->table)
                                <p class="text-sm text-gray-500">Table {{ $order->table->table_number }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full
                                @switch($order->order_type)
                                    @case('dine_in') bg-blue-100 text-blue-700 @break
                                    @case('takeaway') bg-yellow-100 text-yellow-700 @break
                                    @case('delivery') bg-green-100 text-green-700 @break
                                @endswitch
                            ">{{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->items->count() }} items
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-800">
                            {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->total, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <select onchange="updateStatus({{ $order->id }}, this.value)"
                                class="text-xs px-2 py-1 rounded-full border-0 cursor-pointer
                                @switch($order->status)
                                    @case('pending') bg-yellow-100 text-yellow-700 @break
                                    @case('confirmed') bg-blue-100 text-blue-700 @break
                                    @case('preparing') bg-orange-100 text-orange-700 @break
                                    @case('ready') bg-green-100 text-green-700 @break
                                    @case('served') bg-purple-100 text-purple-700 @break
                                    @case('completed') bg-gray-100 text-gray-700 @break
                                    @case('cancelled') bg-red-100 text-red-700 @break
                                @endswitch">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>Preparing</option>
                                <option value="ready" {{ $order->status == 'ready' ? 'selected' : '' }}>Ready</option>
                                <option value="served" {{ $order->status == 'served' ? 'selected' : '' }}>Served</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->created_at->format('M d, H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-red-600 hover:text-red-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.orders.edit', $order) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-shopping-cart text-4xl mb-4 text-gray-300"></i>
                            <p>No orders found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
    </div>
</div>

<script>
function filterByStatus(status) {
    const url = new URL(window.location);
    url.searchParams.set('status', status);
    window.location.href = url.toString();
}

function updateStatus(orderId, status) {
    fetch(`/admin/orders/${orderId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endsection

